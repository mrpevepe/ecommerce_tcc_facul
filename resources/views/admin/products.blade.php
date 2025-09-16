<!-- resources/views/admin/products.blade.php -->
@extends('layouts.main')

@section('title', 'Listar Produtos')

@section('content')
<div class="container mt-5">
    <h1>Listagem de Produtos</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">Criar Novo Produto</a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Imagem</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Marca</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>
                        @if ($product->images->where('is_main', true)->first())
                            <img src="{{ Storage::url(str_replace('public/', '', $product->images->where('is_main', true)->first()->path)) }}" alt="Imagem do Produto" style="max-width: 100px; height: auto;">
                        @else
                            Sem Imagem
                        @endif
                    </td>
                    <td>{{ $product->nome }}</td>
                    <td>{{ $product->category->name ?? 'Sem categoria' }}</td>
                    <td>{{ $product->marca }}</td>
                    <td>{{ $product->status }}</td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning">Editar</a>
                        <a href="{{ route('admin.products.variations', $product->id) }}" class="btn btn-sm btn-info">Ver Variações</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection