<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $query = Order::with('user', 'items.product', 'items.variation');

        if (in_array($status, ['pending', 'cancelled', 'delivered'])) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->get();

        return view('admin.orders.index', compact('orders', 'status'));
    }

    /**
     * Processa o checkout e cria o pedido sem reduzir o estoque.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->endereco_id) {
            return redirect()->route('user.address.form')->with('error', 'Adicione um endereço antes de prosseguir com o checkout.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Seu carrinho está vazio.');
        }

        $request->validate([
            'payment_method' => 'required|in:pix,boleto,cartao',
        ]);

        $totalPrice = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));

        $order = Order::create([
            'user_id' => $user->id,
            'address_id' => $user->endereco_id,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'total_price' => $totalPrice,
        ]);

        foreach ($cart as $cartKey => $item) {
            $parts = explode('_', $cartKey);
            $variationId = $parts[0];
            $size = $item['size'];
            
            $variation = ProductVariation::find($variationId);

            if ($variation) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variation_id' => $variationId,
                    'product_size' => $size, // Salvar o tamanho
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $item['price'],
                ]);
            } else {
                // Se a variação não existe, cancela o pedido
                $order->update(['status' => 'cancelled']);
                return redirect()->route('cart.index')->with('error', 'Uma ou mais variações são inválidas.');
            }
        }

        // Limpa o carrinho
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
        $order = Order::with('items.product', 'items.variation.images', 'address')->findOrFail($id);
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

        $order->update(['status' => 'cancelled']);

        return redirect()->route('user.index')->with('success', 'Pedido cancelado com sucesso!');
    }

    /**
     * Marca um pedido como entregue e reduz o estoque (apenas para admin, status pending).
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsDelivered($id)
    {
        $order = Order::with('items.variation')->findOrFail($id);
        if (Auth::user()->cargo !== 'administrador') {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('admin.orders.index')->with('error', 'Este pedido não pode ser marcado como entregue.');
        }

        // Verifica se há estoque suficiente para todos os itens
        foreach ($order->items as $item) {
            $variation = ProductVariation::find($item->variation_id);
            if (!$variation || $variation->quantidade_estoque < $item->quantity) {
                return redirect()->route('admin.orders.index')->with('error', 'Estoque insuficiente para um ou mais itens do pedido.');
            }
        }

        // Reduz o estoque
        foreach ($order->items as $item) {
            $variation = ProductVariation::find($item->variation_id);
            if ($variation) {
                $variation->quantidade_estoque -= $item->quantity;
                $variation->save();
            }
        }

        $order->update(['status' => 'delivered']);

        return redirect()->route('admin.orders.index')->with('success', 'Pedido marcado como entregue e estoque atualizado!');
    }
}