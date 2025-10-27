@extends('layouts.main')
@section('title', 'Gerenciar Pedidos')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/orders.css') }}">
@endpush
@section('content')
<div class="orders-container">
    <h1 class="orders-title">Gerenciar Pedidos</h1>
    
    <div class="admin-actions">
        <a href="{{ route('admin.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Filtros -->
    <div class="filters-container">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="filters-form">
            <div class="filter-group">
                <label for="search" class="filter-label">Pesquisar</label>
                <input type="text" id="search" name="search" class="filter-input" 
                       placeholder="ID do pedido ou nome do cliente..." 
                       value="{{ request()->query('search') }}">
            </div>
            
            <div class="filter-group">
                <label for="status" class="filter-label">Status</label>
                <select name="status" id="status" class="filter-select">
                    <option value="all" {{ request()->query('status', 'all') == 'all' ? 'selected' : '' }}>Todos os Status</option>
                    <option value="pending" {{ request()->query('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="cancelled" {{ request()->query('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    <option value="delivered" {{ request()->query('status') == 'delivered' ? 'selected' : '' }}>Entregue</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="date" class="filter-label">Data</label>
                <input type="date" id="date" name="date" class="filter-input" 
                       value="{{ request()->query('date') }}">
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="filter-btn">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                @if(request()->query('search') || request()->query('status') != 'all' || request()->query('date'))
                <a href="{{ route('admin.orders.index') }}" class="clear-filter-btn">
                    <i class="fas fa-times"></i> Limpar
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Paginação no topo -->
    @if($orders->hasPages())
    <div class="pagination-info">
        <div class="pagination-results">
            Exibindo {{ $orders->firstItem() }} a {{ $orders->lastItem() }} de {{ $orders->total() }} resultados
        </div>
        <div class="pagination-links">
            {{ $orders->links('pagination::bootstrap-4') }}
        </div>
    </div>
    @endif

    @if ($orders->isEmpty())
        <div class="empty-state">
            <i class="fas fa-box-open empty-icon"></i>
            <h3>Nenhum pedido encontrado</h3>
            <p>Tente ajustar os filtros para ver mais resultados.</p>
        </div>
    @else
        <div class="orders-table-container">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Itens</th>
                        <th>Endereço</th>
                        <th>Total</th>
                        <th>Status & Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr class="order-row">
                            <td class="order-id">
                                <span class="id-badge">#{{ $order->id }}</span>
                            </td>
                            <td class="order-customer">
                                <div class="customer-info">
                                    <strong>{{ $order->user->name }}</strong>
                                </div>
                            </td>
                            <td class="order-date">
                                <div class="date-info">
                                    <div class="date-line">
                                        <span class="date-label">Pedido:</span>
                                        <span class="date-value">{{ $order->created_at->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if($order->status == 'delivered')
                                    <div class="date-line delivered-date">
                                        <span class="date-label">Entrega:</span>
                                        <span class="date-value">{{ $order->updated_at->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td class="order-items">
                                <div class="items-list">
                                    @foreach ($order->items as $item)
                                        <div class="order-item">
                                            <div class="item-image">
                                                @if($item->variation && $item->variation->images->where('is_main', true)->first())
                                                    <img src="{{ Storage::url($item->variation->images->where('is_main', true)->first()->path) }}" 
                                                         alt="{{ $item->product->nome }}" 
                                                         class="variation-image">
                                                @elseif($item->product->images->where('is_main', true)->first())
                                                    <img src="{{ Storage::url($item->product->images->where('is_main', true)->first()->path) }}" 
                                                         alt="{{ $item->product->nome }}" 
                                                         class="variation-image">
                                                @else
                                                    <div class="no-image-placeholder">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="item-details">
                                                <div class="item-name">{{ $item->product->nome }}</div>
                                                <div class="item-meta">
                                                    <span class="variation-name">{{ $item->variation->nome_variacao ?? 'Sem variação' }}</span>
                                                    <span class="size">Tamanho: {{ $item->size->name ?? 'N/A' }}</span>
                                                    <span class="quantity">Qtd: {{ $item->quantity }}</span>
                                                </div>
                                                <div class="item-category">
                                                    {{ $item->product->category->name ?? 'Sem categoria' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="order-address">
                                <div class="address-info">
                                    <div class="address-line">{{ $order->logradouro }}</div>
                                    <div class="address-line">Nº {{ $order->numero }}</div>
                                    @if ($order->complemento)
                                        <div class="address-line">Complemento: {{ $order->complemento }}</div>
                                    @endif
                                    <div class="address-line">Bairro: {{ $order->bairro }}</div>
                                    <div class="address-line">CEP: {{ $order->cep }}</div>
                                    <div class="address-line">{{ $order->nome_cidade }} - {{ $order->estado }}</div>
                                </div>
                            </td>
                            <td class="order-total">
                                <span class="total-price">R$ {{ number_format($order->total_price, 2, ',', '.') }}</span>
                            </td>
                            <td class="order-status">
                                <span class="status-badge status-{{ $order->status }}">
                                    {{ ['pending' => 'Pendente', 'cancelled' => 'Cancelado', 'delivered' => 'Entregue'][$order->status] ?? ucfirst($order->status) }}
                                </span>
                                <div class="action-buttons">
                                    @if ($order->status === 'pending')
                                        <form action="{{ route('admin.orders.deliver', $order->id) }}" method="POST" 
                                              onsubmit="return confirm('Deseja marcar este pedido como entregue?');">
                                            @csrf
                                            <button type="submit" class="action-btn deliver-btn">
                                                <i class="fas fa-shipping-fast"></i>
                                                <span>Entregar</span>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" 
                                              onsubmit="return confirm('Deseja cancelar este pedido?');">
                                            @csrf
                                            <button type="submit" class="cancel-order-btn">
                                                <i class="fas fa-times"></i>
                                                <span>Cancelar</span>
                                            </button>
                                        </form>
                                    @else
                                        <span class="no-action">Nenhuma ação</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginação no rodapé -->
        @if($orders->hasPages())
        <div class="pagination-info">
            <div class="pagination-results">
                Exibindo {{ $orders->firstItem() }} a {{ $orders->lastItem() }} de {{ $orders->total() }} resultados
            </div>
            <div class="pagination-links">
                {{ $orders->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    @endif
</div>

<style>
    /* Correção adicional para evitar scrollbar no hover */
    .orders-table tbody tr {
        transform: none;
        transition: none;
    }
    
    .orders-table tbody tr:hover {
        transform: none;
    }
    
    .order-item:hover {
        transform: none;
    }
</style>
@endsection