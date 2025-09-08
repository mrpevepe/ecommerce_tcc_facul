@extends('layouts.main')
@section('title', 'Detalhes do Pedido')
@section('content')
<div class="container mt-5">
    <h1>Pedido #{{ $order->id }}</h1>
    <p>Status: {{ ucfirst($order->status) }}</p>
    <p>Método de Pagamento: {{ ucfirst($order->payment_method) }}</p>
    <p>Total: R$ {{ number_format($order->total_price, 2, ',', '.') }}</p>

    <h4>Endereço de Entrega:</h4>
    <p>{{ $order->address->logradouro }}, {{ $order->address->numero }} - {{ $order->address->bairro }}</p>
    <p>{{ $order->address->nome_cidade }} - {{ $order->address->estado }}, CEP: {{ $order->address->cep }}</p>
    @if ($order->address->complemento)
        <p>Complemento: {{ $order->address->complemento }}</p>
    @endif

    <h4>Itens do Pedido:</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Variação</th>
                <th>Quantidade</th>
                <th>Preço</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->nome }}</td>
                    <td>{{ $item->variation->nome_variacao ?? 'Sem variação' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>R$ {{ number_format($item->price_at_purchase, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($item->price_at_purchase * $item->quantity, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection