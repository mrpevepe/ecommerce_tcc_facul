<!-- resources/views/admin/comments.blade.php -->
@extends('layouts.main')

@section('title', 'Avaliações do Produto')

@section('content')
<div class="container mt-5">
    <h1>Avaliações do Produto: {{ $product->nome }}</h1>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary mb-3">Voltar para Produtos</a>

    @if ($product->comments->isEmpty())
        <p>Sem avaliações para este produto.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Produto ID</th>
                    <th>Imagem do Produto</th>
                    <th>Nome do Produto</th>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Descrição</th>
                    <th>Usuário</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($product->comments as $comment)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
                            @if ($product->images->where('is_main', true)->first())
                                <img src="{{ Storage::url($product->images->where('is_main', true)->first()->path) }}" alt="{{ $product->nome }}" style="max-width: 50px; height: auto;">
                            @else
                                Sem Imagem
                            @endif
                        </td>
                        <td>{{ $product->nome }}</td>
                        <td>{{ $comment->id }}</td>
                        <td>{{ $comment->titulo }}</td>
                        <td>{{ $comment->descricao }}</td>
                        <td>{{ $comment->user->name }}</td>
                        <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <form action="{{ route('admin.comments.destroy', [$product->id, $comment->id]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta avaliação?')">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection