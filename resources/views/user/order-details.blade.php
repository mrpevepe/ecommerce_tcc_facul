@extends('layouts.main')
@section('title', 'Detalhes do Pedido')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/order-details.css') }}">
@endpush
@section('content')
@php
    use App\Http\Controllers\UserController;
@endphp
<div class="order-details-container">
    <h1 class="order-details-title">Detalhes do Pedido #{{ $order->id }}</h1>

    <div class="order-details-card">
        <div class="order-details-card-header">
            <h2 class="order-details-card-title">Informações Gerais</h2>
        </div>
        <div class="order-details-card-body">
            <div class="order-info-grid">
                <div class="order-info-field">
                    <span class="order-info-label">Status</span>
                    <p class="order-info-value">
                        <span class="order-status-badge status-{{ $order->status }}">
                            {{ UserController::translateStatus($order->status) }}
                        </span>
                    </p>
                </div>
                <div class="order-info-field">
                    <span class="order-info-label">Método de Pagamento</span>
                    <p class="order-info-value">{{ ucfirst($order->payment_method) }}</p>
                </div>
                <div class="order-info-field">
                    <span class="order-info-label">Total</span>
                    <p class="order-info-value">R$ {{ number_format($order->total_price, 2, ',', '.') }}</p>
                </div>
                <div class="order-info-field">
                    <span class="order-info-label">Data do Pedido</span>
                    <p class="order-info-value">{{ $order->created_at->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="order-details-card">
        <div class="order-details-card-header">
            <h2 class="order-details-card-title">Endereço de Entrega</h2>
        </div>
        <div class="order-details-card-body">
            <div class="order-info-grid">
                <div class="order-info-field">
                    <span class="order-info-label">Endereço</span>
                    <p class="order-info-value">{{ $order->logradouro }}, {{ $order->numero }}</p>
                </div>
                <div class="order-info-field">
                    <span class="order-info-label">Bairro</span>
                    <p class="order-info-value">{{ $order->bairro }}</p>
                </div>
                <div class="order-info-field">
                    <span class="order-info-label">Cidade/Estado</span>
                    <p class="order-info-value">{{ $order->nome_cidade }} - {{ $order->estado }}</p>
                </div>
                <div class="order-info-field">
                    <span class="order-info-label">CEP</span>
                    <p class="order-info-value">{{ $order->cep }}</p>
                </div>
                @if ($order->complemento)
                <div class="order-info-field">
                    <span class="order-info-label">Complemento</span>
                    <p class="order-info-value">{{ $order->complemento }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="order-details-card">
        <div class="order-details-card-header">
            <h2 class="order-details-card-title">Itens do Pedido</h2>
        </div>
        <div class="order-details-card-body">
            <div class="order-items-table-container">
                <table class="order-items-table">
                    <thead>
                        <tr>
                            <th>Imagem</th>
                            <th>Produto</th>
                            <th>Categoria</th>
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
                                        <img src="{{ Storage::url($image->path) }}" alt="{{ $item->product->nome }}" class="order-item-image">
                                    @else
                                        <p class="no-order-item-image">Sem Imagem</p>
                                    @endif
                                </td>
                                <td class="break-word">{{ $item->product->nome }}</td>
                                <td class="break-word">{{ $item->product->category->name ?? 'Sem categoria' }}</td>
                                <td class="break-word">{{ $item->variation->nome_variacao ?? 'Sem variação' }}</td>
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
    </div>

    <a href="{{ route('user.index') }}" class="back-to-account-btn">
        <i class="fas fa-arrow-left"></i> Voltar para Minha Conta
    </a>
</div>
@endsection