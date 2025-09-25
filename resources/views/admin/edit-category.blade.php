@extends('layouts.main')

@section('title', 'Editar Categoria')

@section('content')
<div class="container mt-5">
    <h1>Editar Categoria</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-secondary mb-3">Voltar</a>

    <form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Nome da Categoria</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $category->name) }}" maxlength="60" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Atualizar Categoria</button>
    </form>

    <h2 class="mt-5">Categorias Existentes</h2>
    <div class="mb-3 filter-container">
        <input type="text" id="searchBar" class="form-control" placeholder="Pesquisar por nome..." value="{{ request()->query('search') }}">
        <select id="statusFilter" class="form-control w-25">
            <option value="all" {{ request()->query('status') == 'all' ? 'selected' : '' }}>Todos os Status</option>
            <option value="ativo" {{ request()->query('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
            <option value="inativo" {{ request()->query('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
        </select>
    </div>
    @if ($categories->isEmpty())
        <p>Nenhuma categoria cadastrada.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ ucfirst($category->status) }}</td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-primary">Editar</a>
                            <form action="{{ route('admin.categories.updateStatus', $category->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $category->status === 'ativo' ? 'btn-warning' : 'btn-success' }}">
                                    {{ $category->status === 'ativo' ? 'Inativar' : 'Ativar' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
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