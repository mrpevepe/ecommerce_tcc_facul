@extends('layouts.main')
@section('title', 'Detalhes do Pedido')
@section('content')
<div class="container mt-5">
    <h1>Detalhes do Pedido #{{ $order->id }}</h1>

    <div class="card mb-4">
        <div class="card-header">
            <h4>Informações Gerais</h4>
        </div>
        <div class="card-body">
            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
            <p><strong>Método de Pagamento:</strong> {{ ucfirst($order->payment_method) }}</p>
            <p><strong>Total:</strong> R$ {{ number_format($order->total_price, 2, ',', '.') }}</p>
            <p><strong>Data do Pedido:</strong> {{ $order->created_at->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h4>Endereço de Entrega</h4>
        </div>
        <div class="card-body">
            <p>{{ $order->address->logradouro }}, {{ $order->address->numero }} - {{ $order->address->bairro }}</p>
            <p>{{ $order->address->nome_cidade }} - {{ $order->address->estado }}, CEP: {{ $order->address->cep }}</p>
            @if ($order->address->complemento)
                <p>Complemento: {{ $order->address->complemento }}</p>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Itens do Pedido</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Produto</th>
                        <th>Variação</th>
                        <th>Tamanho</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>
                                @php
                                    $image = $item->variation->images->where('is_main', true)->first() ?? $item->product->images->where('is_main', true)->first();
                                @endphp
                                @if ($image)
                                    <img src="{{ Storage::url($image->path) }}" alt="{{ $item->product->nome }}" style="max-width: 100px; height: auto;">
                                @else
                                    <p>Sem Imagem</p>
                                @endif
                            </td>
                            <td>{{ $item->product->nome }}</td>
                            <td>{{ $item->variation->nome_variacao ?? 'Sem variação' }}</td>
                            <td>{{ $item->size->name ?? 'N/A' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>R$ {{ number_format($item->price_at_purchase, 2, ',', '.') }}</td>
                            <td>R$ {{ number_format($item->price_at_purchase * $item->quantity, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('user.index') }}" class="btn btn-secondary mt-3">Voltar para Minha Conta</a>
</div>
@endsection