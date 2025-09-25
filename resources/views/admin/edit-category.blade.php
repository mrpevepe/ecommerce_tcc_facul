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
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $category->name) }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="ativo" {{ $category->status === 'ativo' ? 'selected' : '' }}>Ativo</option>
                <option value="inativo" {{ $category->status === 'inativo' ? 'selected' : '' }}>Inativo</option>
            </select>
            @error('status')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Atualizar Categoria</button>
    </form>

    <h2 class="mt-5">Categorias Existentes</h2>
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
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection