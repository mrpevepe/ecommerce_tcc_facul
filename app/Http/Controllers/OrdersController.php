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
     * Processa o checkout e cria o pedido.
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
            'payment_status' => 'pending',
            'total_price' => $totalPrice,
        ]);

        foreach ($cart as $variationId => $item) {
            $variation = ProductVariation::find($variationId);

            if ($variation && $variation->quantidade_estoque >= $item['quantity']) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variation_id' => $variationId,
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $item['price'],
                ]);

                // Atualiza o estoque
                $variation->quantidade_estoque -= $item['quantity'];
                $variation->save();
            } else {
                // Se estoque insuficiente, cancela o pedido
                $order->update(['status' => 'cancelled']);
                return redirect()->route('cart.index')->with('error', 'Estoque insuficiente para um ou mais itens.');
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
}