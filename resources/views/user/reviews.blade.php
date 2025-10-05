@extends('layouts.main')
@section('title', 'Minhas Avaliações')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reviews.css') }}">
@endpush
@section('content')
<div class="reviews-container">
    <h1 class="reviews-title">Minhas Avaliações</h1>
    
    <div class="reviews-card">
        <div class="reviews-header">
            <div class="reviews-search-container">
                <form action="{{ route('user.reviews') }}" method="GET" class="reviews-search-form">
                    <i class="fas fa-search reviews-search-icon"></i>
                    <input type="text" name="search" class="reviews-search-input" 
                           placeholder="Buscar por nome do produto..." 
                           value="{{ request('search') }}">
                </form>
                
                @if($reviews->hasPages())
                <div class="reviews-pagination-top">
                    {{ $reviews->links('pagination::simple-bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
        
        <div class="reviews-card-body">
            @if ($reviews->isEmpty())
                <div class="no-reviews-message">
                    <p>Você ainda não fez nenhuma avaliação.</p>
                    <a href="{{ route('home') }}" class="back-to-account-btn">
                        <i class="fas fa-shopping-bag"></i> Ir para Loja
                    </a>
                </div>
            @else
                <div class="reviews-table-container">
                    <table class="reviews-table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Avaliação</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reviews as $review)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @php
                                                $image = $review->product->images->where('is_main', true)->first();
                                            @endphp
                                            @if ($image)
                                                <img src="{{ Storage::url($image->path) }}" 
                                                     alt="{{ $review->product->nome }}" 
                                                     class="review-product-image">
                                            @else
                                                <div class="no-review-image">Sem Imagem</div>
                                            @endif
                                            <div>
                                                <strong>{{ $review->product->nome }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $review->product->category->name ?? 'Sem categoria' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="review-content" id="display-{{ $review->id }}">
                                            <div class="review-title">{{ $review->titulo }}</div>
                                            <p class="review-description">{{ $review->descricao }}</p>
                                        </div>
                                        
                                        <div class="review-edit-form" id="edit-{{ $review->id }}" style="display: none;">
                                            <form action="{{ route('comments.update', [$review->product_id, $review->id]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="review-form-group">
                                                    <label for="titulo-edit-{{ $review->id }}" class="review-form-label">Título</label>
                                                    <input type="text" class="review-form-control" 
                                                           id="titulo-edit-{{ $review->id }}" 
                                                           name="titulo" value="{{ $review->titulo }}" required>
                                                </div>
                                                <div class="review-form-group">
                                                    <label for="descricao-edit-{{ $review->id }}" class="review-form-label">Descrição</label>
                                                    <textarea class="review-form-control" 
                                                              id="descricao-edit-{{ $review->id }}" 
                                                              name="descricao" rows="3" required>{{ $review->descricao }}</textarea>
                                                </div>
                                                <div class="review-form-actions">
                                                    <button type="submit" class="review-save-btn">
                                                        <i class="fas fa-save"></i> Salvar
                                                    </button>
                                                    <button type="button" class="review-cancel-btn" 
                                                            onclick="toggleEditReview({{ $review->id }})">
                                                        <i class="fas fa-times"></i> Cancelar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="review-date">
                                            {{ $review->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="review-actions">
                                            <button class="review-action-btn btn-edit-review" 
                                                    onclick="toggleEditReview({{ $review->id }})"
                                                    id="edit-btn-{{ $review->id }}">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <form action="{{ route('comments.destroy', [$review->product_id, $review->id]) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Tem certeza que deseja excluir esta avaliação?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="review-action-btn btn-delete-review">
                                                    <i class="fas fa-trash"></i> Excluir
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação no rodapé -->
                @if($reviews->hasPages())
                <div class="reviews-pagination-wrapper">
                    <div class="reviews-pagination">
                        {{ $reviews->links('pagination::simple-bootstrap-5') }}
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>

<script>
// Busca em tempo real
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.reviews-search-input');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            this.form.submit();
        }, 500);
    });
});

// Função para alternar edição de avaliação
function toggleEditReview(reviewId) {
    const displayDiv = document.getElementById('display-' + reviewId);
    const editDiv = document.getElementById('edit-' + reviewId);
    const editBtn = document.getElementById('edit-btn-' + reviewId);
    
    if (displayDiv && editDiv) {
        if (displayDiv.style.display === 'none') {
            // Sair do modo edição
            displayDiv.style.display = 'block';
            editDiv.style.display = 'none';
            editBtn.innerHTML = '<i class="fas fa-edit"></i> Editar';
        } else {
            // Entrar no modo edição
            displayDiv.style.display = 'none';
            editDiv.style.display = 'block';
            editBtn.innerHTML = '<i class="fas fa-times"></i> Cancelar';
        }
    }
}
</script>
@endsection