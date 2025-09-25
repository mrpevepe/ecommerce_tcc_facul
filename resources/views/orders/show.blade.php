@extends('layouts.main')
@section('title', 'Detalhes do Pedido')
@section('content')
<div class="container mt-5">
    <h1>Pedido #{{ $order->id }}</h1>
    <p>Status: {{ ucfirst($order->status) }}</p>
    <p>Método de Pagamento: {{ ucfirst($order->payment_method) }}</p>
    <p>Total: R$ {{ number_format($order->total_price, 2, ',', '.') }}</p>

    <h4>Endereço de Entrega:</h4>
    <p>{{ $order->logradouro }}, {{ $order->numero }} - {{ $order->bairro }}</p>
    <p>{{ $order->nome_cidade }} - {{ $order->estado }}, CEP: {{ $order->cep }}</p>
    @if ($order->complemento)
        <p>Complemento: {{ $order->complemento }}</p>
    @endif

    <h4>Itens do Pedido:</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Variação</th>
                <th>Tamanho</th>
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
                    <td>{{ $item->product_size ?? 'N/A' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>R$ {{ number_format($item->price_at_purchase, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($item->price_at_purchase * $item->quantity, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection