@extends('layouts.main')

@section('title', 'Avaliações do Produto')

@section('content')
<div class="comments-container">
    <div class="comments-header-actions">
        <a href="{{ route('admin.products.index') }}" class="back-btn-comments">
            <i class="fas fa-arrow-left"></i> Voltar para Produtos
        </a>
        
        <h1 class="comments-title">Avaliações: {{ Str::limit($product->nome, 40) }}</h1>
    </div>

    <!-- Barra de Pesquisa e Estatísticas -->
    <div class="comments-search-section">
        <div class="comments-search-container">
            <form action="{{ route('admin.comments.index', $product->id) }}" method="GET" class="comments-search-form">
                <i class="fas fa-search comments-search-icon"></i>
                <input type="text" name="search" class="comments-search-input" 
                       placeholder="Pesquisar por ID, usuário, título ou data..." 
                       value="{{ request('search') }}">
            </form>
        </div>
        
        <div class="comments-stats">
            <span class="comments-count">{{ $comments->total() }} avaliação(ões) encontrada(s)</span>
        </div>
    </div>

    @if ($comments->isEmpty())
        <div class="no-comments-message">
            <i class="fas fa-comments"></i>
            <p>Nenhuma avaliação encontrada para este produto.</p>
            @if(request('search'))
                <a href="{{ route('admin.comments.index', $product->id) }}" class="btn-clear-search">
                    Limpar busca
                </a>
            @endif
        </div>
    @else
        <div class="comments-table-container">
            <table class="comments-table">
                <thead>
                    <tr>
                        <th class="col-id">ID</th>
                        <th class="col-image">Imagem</th>
                        <th class="col-product">Produto</th>
                        <th class="col-title">Título</th>
                        <th class="col-description">Descrição</th>
                        <th class="col-user">Usuário</th>
                        <th class="col-date">Data</th>
                        <th class="col-actions">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($comments as $comment)
                        <tr>
                            <td class="col-id">
                                <span class="comment-id-badge">#{{ $comment->id }}</span>
                            </td>
                            <td class="col-image">
                                @if ($product->images->where('is_main', true)->first())
                                    <img src="{{ Storage::url($product->images->where('is_main', true)->first()->path) }}" 
                                         alt="{{ $product->nome }}" 
                                         class="product-image-comment">
                                @else
                                    <span class="no-product-image">Sem Imagem</span>
                                @endif
                            </td>
                            <td class="col-product">
                                <div class="product-name">{{ $product->nome }}</div>
                            </td>
                            <td class="col-title">
                                <div class="comment-title">
                                    {{ $comment->titulo }}
                                </div>
                            </td>
                            <td class="col-description">
                                <div class="comment-description">
                                    {{ $comment->descricao }}
                                </div>
                            </td>
                            <td class="col-user">
                                <div class="comment-user">
                                    <i class="fas fa-user"></i>
                                    {{ $comment->user->name }}
                                </div>
                            </td>
                            <td class="col-date">
                                <div class="comment-date">
                                    <i class="fas fa-calendar"></i>
                                    {{ $comment->created_at->format('d/m/Y') }}
                                </div>
                                <div class="comment-time">
                                    {{ $comment->created_at->format('H:i') }}
                                </div>
                            </td>
                            <td class="col-actions">
                                <div class="comment-actions">
                                    <form action="{{ route('admin.comments.destroy', [$product->id, $comment->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="comment-action-btn btn-delete-comment" 
                                                onclick="return confirm('Tem certeza que deseja excluir esta avaliação?')">
                                            <i class="fas fa-trash"></i>
                                            <span class="btn-text">Excluir</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        @if($comments->hasPages())
        <div class="comments-pagination-wrapper">
            <div class="comments-pagination-info">
                Mostrando {{ $comments->firstItem() }} a {{ $comments->lastItem() }} de {{ $comments->total() }} avaliações
            </div>
            <div class="comments-pagination">
                {{ $comments->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    @endif
</div>

<script>
// Busca em tempo real
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.comments-search-input');
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