@extends('layouts.main')
@section('title', 'Minha Conta')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
@endpush
@section('content')
<div class="user-container">
    <h1 class="user-title">Minha Conta</h1>

    <!-- Seção de Endereço -->
    <div class="user-card">
        <div class="user-card-header">
            <h2 class="user-card-title">Endereço de Entrega</h2>
        </div>
        <div class="user-card-body">
            @if ($user->endereco)
                <div class="user-address-info">
                    <div class="address-field">
                        <span class="address-label">Logradouro</span>
                        <p class="address-value">{{ $user->endereco->logradouro }}, {{ $user->endereco->numero }}</p>
                    </div>
                    <div class="address-field">
                        <span class="address-label">Bairro</span>
                        <p class="address-value">{{ $user->endereco->bairro }}</p>
                    </div>
                    <div class="address-field">
                        <span class="address-label">Cidade/Estado</span>
                        <p class="address-value">{{ $user->endereco->nome_cidade }} - {{ $user->endereco->estado }}</p>
                    </div>
                    <div class="address-field">
                        <span class="address-label">CEP</span>
                        <p class="address-value">{{ $user->endereco->cep }}</p>
                    </div>
                    @if ($user->endereco->complemento)
                    <div class="address-field">
                        <span class="address-label">Complemento</span>
                        <p class="address-value">{{ $user->endereco->complemento }}</p>
                    </div>
                    @endif
                </div>
                <a href="{{ route('user.address.form') }}" class="btn-add-address">
                    <i class="fas fa-edit"></i> Editar Endereço
                </a>
            @else
                <div class="user-no-address">
                    <p>Você não possui um endereço cadastrado.</p>
                    <a href="{{ route('user.address.form') }}" class="btn-add-address">
                        <i class="fas fa-plus"></i> Adicionar Endereço
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Seção de Pedidos -->
    <div class="user-card">
        <div class="user-card-header">
            <h2 class="user-card-title">Meus Pedidos</h2>
            <div class="user-orders-header">
                <div class="user-search-container">
                    <form action="{{ route('user.index') }}" method="GET" class="user-search-form">
                        <i class="fas fa-search user-search-icon"></i>
                        <input type="text" name="search" class="user-search-input" 
                               placeholder="Buscar por ID ou nome do produto..." 
                               value="{{ request('search') }}">
                    </form>
                </div>
                <div class="user-actions">
                    <a href="{{ route('user.reviews') }}" class="btn-my-reviews">
                        <i class="fas fa-star"></i> Minhas Avaliações
                    </a>
                </div>
            </div>
        </div>
        
        <div class="user-card-body">
            @if ($orders->isEmpty())
                <div class="user-no-orders">
                    <p>Você não possui pedidos cadastrados.</p>
                </div>
            @else
                <!-- Paginação no topo -->
                @if($orders->hasPages())
                <div class="user-pagination-wrapper">
                    <div class="user-pagination-info">
                        Exibindo {{ $orders->firstItem() }} a {{ $orders->lastItem() }} de {{ $orders->total() }} pedidos
                    </div>
                    <div class="user-pagination">
                        {{ $orders->links('pagination::bootstrap-4') }}
                    </div>
                </div>
                @endif

                <div class="user-orders-table-container">
                    <table class="user-orders-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>{{ $order->created_at->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i') }}</td>
                                    <td>R$ {{ number_format($order->total_price, 2, ',', '.') }}</td>
                                    <td>
                                        <span class="order-status status-{{ $order->status }}">
                                            {{ App\Http\Controllers\UserController::translateStatus($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="order-actions">
                                            <a href="{{ route('user.orders.show', $order->id) }}" class="order-action-btn btn-view-details">
                                                <i class="fas fa-eye"></i> Ver Detalhes
                                            </a>
                                            @if ($order->status === 'pending')
                                                <form action="{{ route('user.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar este pedido?');">
                                                    @csrf
                                                    <button type="submit" class="order-action-btn btn-cancel-order">
                                                        <i class="fas fa-times"></i> Cancelar
                                                    </button>
                                                </form>
                                            @else
                                                <button class="order-action-btn btn-cancel-order" disabled>
                                                    <i class="fas fa-times"></i> Cancelar
                                                </button>
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
                <div class="user-pagination-wrapper">
                    <div class="user-pagination-info">
                        Exibindo {{ $orders->firstItem() }} a {{ $orders->lastItem() }} de {{ $orders->total() }} pedidos
                    </div>
                    <div class="user-pagination">
                        {{ $orders->links('pagination::bootstrap-4') }}
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>

<script>
// Busca em tempo real (opcional)
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.user-search-input');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            this.form.submit();
        }, 500);
    });
});
</script>
@endsection