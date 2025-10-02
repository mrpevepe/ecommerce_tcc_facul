<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    /**
     * Exibe a lista de todos os pedidos para o administrador.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $query = Order::with('user', 'items.product.category', 'items.variation', 'items.size');

        if (in_array($status, ['pending', 'cancelled', 'delivered'])) {
            $query->where('status', $status);
        }

        // Paginação com 10 itens por página, preservando o parâmetro status
        $orders = $query->latest()->paginate(10)->appends(['status' => $status]);

        return view('admin.orders.index', compact('orders', 'status'));
    }

    /**
     * Processa o checkout, cria o pedido e reduz o estoque.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $endereco = $user->endereco;

        if (!$endereco) {
            return redirect()->route('user.address.form')->with('error', 'Adicione um endereço antes de prosseguir com o checkout.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        $request->validate([
            'payment_method' => 'required|in:pix,boleto,cartao',
        ], [
            'payment_method.required' => 'Escolha um método de pagamento!',
        ]);

        $totalPrice = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));

        // Verificar estoque antes de criar o pedido
        foreach ($cart as $cartKey => $item) {
            $parts = explode('_', $cartKey);
            $variationId = $parts[0];
            $sizeId = $item['size_id'];

            $variation = ProductVariation::find($variationId);
            $stock = $variation->sizes()->where('size_id', $sizeId)->first()->pivot->quantity ?? 0;

            if (!$variation || $stock < $item['quantity']) {
                return redirect()->route('cart.index')->with('error', 'Uma ou mais variações/tamanhos possuem estoque insuficiente.');
            }
        }

        // Criar o pedido
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'total_price' => $totalPrice,
            'logradouro' => $endereco->logradouro,
            'numero' => $endereco->numero,
            'complemento' => $endereco->complemento,
            'bairro' => $endereco->bairro,
            'cep' => $endereco->cep,
            'nome_cidade' => $endereco->nome_cidade,
            'estado' => $endereco->estado,
        ]);

        // Criar itens do pedido e reduzir estoque
        foreach ($cart as $cartKey => $item) {
            $parts = explode('_', $cartKey);
            $variationId = $parts[0];
            $sizeId = $item['size_id'];

            $variation = ProductVariation::find($variationId);
            $stock = $variation->sizes()->where('size_id', $sizeId)->first()->pivot->quantity ?? 0;

            if ($variation && $stock >= $item['quantity']) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variation_id' => $variationId,
                    'size_id' => $sizeId,
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $item['price'],
                ]);

                // Reduzir o estoque
                DB::table('product_variation_sizes')
                    ->where('product_variation_id', $variationId)
                    ->where('size_id', $sizeId)
                    ->decrement('quantity', $item['quantity']);
            } else {
                $order->update(['status' => 'cancelled']);
                return redirect()->route('cart.index')->with('error', 'Uma ou mais variações/tamanhos são inválidos ou sem estoque.');
            }
        }

        session()->forget('cart');

        return redirect()->route('user.orders.show', $order->id)->with('success', 'Pedido realizado com sucesso!');
    }

    /**
     * Exibe os detalhes do pedido.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $order = Order::with('items.product.category', 'items.variation.images', 'items.size')->findOrFail($id);
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('user.order-details', compact('order'));
    }

    /**
     * Cancela um pedido (apenas para usuário, status pending).
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel($id)
    {
        $order = Order::findOrFail($id);
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('user.index')->with('error', 'Este pedido não pode ser cancelado.');
        }

        // Restaurar o estoque ao cancelar
        foreach ($order->items as $item) {
            DB::table('product_variation_sizes')
                ->where('product_variation_id', $item->variation_id)
                ->where('size_id', $item->size_id)
                ->increment('quantity', $item->quantity);
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('user.index')->with('success', 'Pedido cancelado com sucesso!');
    }

    /**
     * Marca um pedido como entregue (apenas para admin, status pending).
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsDelivered($id)
    {
        $order = Order::with('items.variation', 'items.size')->findOrFail($id);
        if (Auth::user()->cargo !== 'administrador') {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('admin.orders.index')->with('error', 'Este pedido não pode ser marcado como entregue.');
        }

        $order->update(['status' => 'delivered']);

        return redirect()->route('admin.orders.index')->with('success', 'Pedido marcado como entregue!');
    }
}