@extends('layouts.main')

@section('title', 'Editar Categoria')

@section('content')
<div class="container mt-5">
    <h1>Editar Categoria</h1>
    <a href="{{ route('admin.categories.create') }}?{{ http_build_query(request()->query()) }}" class="btn btn-secondary mb-3">Voltar</a>

    <form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Nome da Categoria</label>
            <input type="text" class="form-control" id="name" name="name" 
                   value="{{ old('name', $category->name) }}" 
                   maxlength="60"
                   oninput="this.value = this.value.slice(0, 60)" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <input type="hidden" name="search" value="{{ request()->query('search', '') }}">
        <input type="hidden" name="status" value="{{ request()->query('status', 'all') }}">
        <button type="submit" class="btn btn-primary">Atualizar Categoria</button>
    </form>

    <h2 class="mt-5">Categorias Existentes</h2>
    <form method="GET" action="{{ route('admin.categories.create') }}" id="filterForm">
        <div class="mb-3 filter-container d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Pesquisar por nome..." 
                   value="{{ request()->query('search', '') }}">
            <select name="status" class="form-control w-25">
                <option value="all" {{ request()->query('status', 'all') == 'all' ? 'selected' : '' }}>Todos os Status</option>
                <option value="ativo" {{ request()->query('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                <option value="inativo" {{ request()->query('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
            </select>
            <button type="submit" class="btn btn-primary">Filtrar</button>
            @if(request()->query('search') || request()->query('status') != 'all')
                <a href="{{ route('admin.categories.create') }}" class="btn btn-secondary">Limpar Filtros</a>
            @endif
        </div>
    </form>

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
                @foreach ($categories as $cat)
                    <tr>
                        <td>{{ $cat->id }}</td>
                        <td>{{ $cat->name }}</td>
                        <td>{{ ucfirst($cat->status) }}</td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $cat->id) }}?{{ http_build_query(request()->query()) }}" 
                               class="btn btn-sm btn-primary">Editar</a>
                            <form action="{{ route('admin.categories.updateStatus', $cat->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="search" value="{{ request()->query('search', '') }}">
                                <input type="hidden" name="status" value="{{ request()->query('status', 'all') }}">
                                <button type="submit" class="btn btn-sm {{ $cat->status === 'ativo' ? 'btn-warning' : 'btn-success' }}">
                                    {{ $cat->status === 'ativo' ? 'Inativar' : 'Ativar' }}
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
    // Validação em tempo real para o campo nome
    document.getElementById('name').addEventListener('input', function(e) {
        const maxLength = 60;
        if (e.target.value.length > maxLength) {
            e.target.value = e.target.value.slice(0, maxLength);
        }
    });

    // Submissão automática do formulário de filtro (opcional)
    document.querySelector('select[name="status"]').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
</script>
@endsection