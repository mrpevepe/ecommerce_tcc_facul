@extends('layouts.main')

@section('title', 'Dashboard Admin')

@section('content')

<div class="container mt-5">
    <h1>Dashboard do Administrador</h1>
    <div class="row">
        <div class="col-md-4">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-lg">Criar Novo Produto</a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-lg">Listar Produtos</a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-info btn-lg">Gerenciar Pedidos</a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-lg">Gerenciar Categorias</a>
        </div>
    </div>
</div>

@endsection