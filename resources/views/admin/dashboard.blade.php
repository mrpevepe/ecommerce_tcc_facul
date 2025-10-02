@extends('layouts.main')

@section('title', 'Dashboard Admin')

@section('content')

<div class="admin-dashboard-container">
    <h1 class="admin-dashboard-title">Dashboard do Administrador</h1>
    
    <div class="admin-dashboard-grid">
        <div class="admin-dashboard-card">
            <i class="fas fa-rocket"></i>
            <h3>Criar Novo Produto</h3>
            <a href="{{ route('admin.products.create') }}" class="admin-dashboard-btn">
                <i class="fas fa-plus"></i> Criar Produto
            </a>
        </div>
        
        <div class="admin-dashboard-card">
            <i class="fas fa-boxes"></i>
            <h3>Listar Produtos</h3>
            <a href="{{ route('admin.products.index') }}" class="admin-dashboard-btn">
                <i class="fas fa-list"></i> Ver Produtos
            </a>
        </div>
        
        <div class="admin-dashboard-card">
            <i class="fas fa-shipping-fast"></i>
            <h3>Gerenciar Pedidos</h3>
            <a href="{{ route('admin.orders.index') }}" class="admin-dashboard-btn">
                <i class="fas fa-clipboard-list"></i> Ver Pedidos
            </a>
        </div>
        
        <div class="admin-dashboard-card">
            <i class="fas fa-tags"></i>
            <h3>Gerenciar Categorias</h3>
            <a href="{{ route('admin.categories.create') }}" class="admin-dashboard-btn">
                <i class="fas fa-folder"></i> Ver Categorias
            </a>
        </div>
    </div>
</div>

@endsection