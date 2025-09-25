@extends('layouts.main')

@section('title', 'Listar Produtos')

@section('content')
<div class="container mt-5">
    <h1>Listagem de Produtos</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">Criar Novo Produto</a>
    <div class="mb-3 filter-container">
        <input type="text" id="searchBar" class="form-control" placeholder="Pesquisar por nome ou marca..." value="{{ request()->query('search') }}">
        <select id="statusFilter" class="form-control w-25">
            <option value="all" {{ request()->query('status') == 'all' ? 'selected' : '' }}>Todos os Status</option>
            <option value="ativo" {{ request()->query('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
            <option value="inativo" {{ request()->query('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
        </select>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Imagem</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Categoria</th>
                <th>Marca</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="productTable">
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
                    <td>{{ $product->descricao ?? 'Sem descrição' }}</td>
                    <td>{{ $product->category->name ?? 'Sem categoria' }}</td>
                    <td>{{ $product->marca }}</td>
                    <td>{{ ucfirst($product->status) }}</td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning">Editar</a>
                        <a href="{{ route('admin.products.variations', $product->id) }}" class="btn btn-sm btn-info">Ver Variações</a>
                        <a href="{{ route('admin.comments.index', $product->id) }}" class="btn btn-sm btn-secondary">Ver Avaliações</a>
                        <form action="{{ route('admin.products.updateStatus', $product->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $product->status === 'ativo' ? 'btn-warning' : 'btn-success' }}">
                                {{ $product->status === 'ativo' ? 'Inativar' : 'Ativar' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
    document.getElementById('searchBar').addEventListener('input', function() {
        const url = new URL(window.location);
        url.searchParams.set('search', this.value);
        window.location = url;
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        const url = new URL(window.location);
        url.searchParams.set('status', this.value);
        window.location = url;
    });
</script>
@endsection