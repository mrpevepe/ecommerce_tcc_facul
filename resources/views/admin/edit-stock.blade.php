@extends('layouts.main')

@section('title', 'Editar Estoque da Variação')

@section('content')
<div class="edit-stock-container">
    <h1 class="edit-stock-title">Editar Estoque</h1>
    
    <a href="{{ route('admin.products.variations', $variation->product_id) }}" class="edit-stock-back-btn">
        <i class="fas fa-arrow-left"></i> Voltar para Variações
    </a>

    <div class="edit-stock-form">
        <!-- Informações do Produto -->
        <div class="edit-stock-product-info">
            <h2 class="edit-stock-product-name">{{ $variation->product->nome }} - {{ $variation->nome_variacao }}</h2>
            <p class="edit-stock-product-detail"><strong>Categoria:</strong> {{ $variation->product->category->name ?? 'Sem categoria' }}</p>
            
            <div class="edit-stock-image-container">
                @if ($variation->images->where('is_main', true)->first())
                    <img src="{{ Storage::url($variation->images->where('is_main', true)->first()->path) }}" 
                         alt="Imagem da Variação" class="edit-stock-image">
                @else
                    <p class="edit-stock-no-image">Sem Imagem</p>
                @endif
            </div>
        </div>

        <!-- Formulário de Estoque -->
        <form action="{{ route('admin.products.saveStock', $variation->id) }}" method="POST">
            @csrf
            <div class="edit-stock-table-container">
                <h3 class="edit-stock-section-title">Estoque por Tamanho</h3>
                <table class="edit-stock-table">
                    <thead>
                        <tr>
                            <th>Tamanho</th>
                            <th>Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sizes as $size)
                            <tr>
                                <td>{{ $size->name }}</td>
                                <td>
                                    <input type="number" 
                                           class="edit-stock-input" 
                                           name="quantidade_estoque[{{ $size->id }}][quantity]" 
                                           value="{{ $variation->sizes->find($size->id)->pivot->quantity ?? 0 }}" 
                                           min="0" 
                                           required>
                                    <input type="hidden" 
                                           name="quantidade_estoque[{{ $size->id }}][size_id]" 
                                           value="{{ $size->id }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="edit-stock-form-actions">
                <button type="submit" class="edit-stock-btn edit-stock-btn-primary">
                    <i class="fas fa-save"></i> Salvar Estoque
                </button>
                <a href="{{ route('admin.products.variations', $variation->product_id) }}" class="edit-stock-btn edit-stock-btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@endsection