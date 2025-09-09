@extends('layouts.main')
@section('title', 'Home Sitezudo')
@section('content')
<style>
    .product-image {
        max-width: 100%;
        height: auto;
        margin-bottom: 20px;
    }
    .variation-btn {
        margin: 5px;
    }
    .variation-details {
        margin-top: 10px;
    }
</style>

<div class="container mt-5">
    <h1>{{ $product->nome }}</h1>
    <a href="{{ route('home') }}" class="btn btn-secondary mb-3">Voltar</a>
    <div class="row">
        <div class="col-md-6">
            @if ($product->images->where('is_main', true)->first())
                <img src="{{ Storage::url($product->images->where('is_main', true)->first()->path) }}" alt="{{ $product->nome }}" class="product-image" id="productImage">
            @else
                <p>Sem Imagem</p>
            @endif
        </div>
        <div class="col-md-6">
            <p><strong>Descrição:</strong> {{ $product->descricao ?? 'Sem descrição' }}</p>
            <p><strong>Marca:</strong> {{ $product->marca ?? 'Sem marca' }}</p>
            <p><strong>Preço:</strong> <span id="productPrice">R$ {{ number_format($product->variations->first()->preco ?? 0, 2, ',', '.') }}</span></p>
            <div>
                @foreach ($product->variations as $variation)
                    <button class="btn btn-outline-primary variation-btn" data-variation-id="{{ $variation->id }}" data-price="{{ $variation->preco }}" data-image="{{ Storage::url($variation->images->where('is_main', true)->first()->path ?? $product->images->where('is_main', true)->first()->path) }}" data-name="{{ $variation->nome_variacao }}" data-stock="{{ $variation->quantidade_estoque }}">
                        {{ $variation->nome_variacao }}
                    </button>
                @endforeach
            </div>
            <div class="variation-details">
                <p><strong>Variação Selecionada:</strong> <span id="variationName">{{ $product->variations->first()->nome_variacao ?? 'Nenhuma' }}</span></p>
                <p><strong>Estoque:</strong> <span id="variationStock">{{ $product->variations->first()->quantidade_estoque ?? 0 }}</span></p>
            </div>
            
            <!-- Seleção de tamanho -->
            <div class="mb-3">
                <label for="sizeSelect" class="form-label">Tamanho:</label>
                <select class="form-select" id="sizeSelect" name="size">
                    <option value="P">P</option>
                    <option value="M">M</option>
                    <option value="G">G</option>
                    <option value="GG">GG</option>
                    <option value="XG">XG</option>
                    <option value="XGG">XGG</option>
                    <option value="XXGG">XXGG</option>
                </select>
            </div>
            
            <form action="{{ route('products.addToCart', $product->id) }}" method="POST" class="mt-3">
                @csrf
                <input type="hidden" name="variation_id" id="selectedVariationId" value="{{ $product->variations->first()->id ?? '' }}">
                <input type="hidden" name="size" id="selectedSize" value="P">
                <div class="mb-2">
                    <label for="quantity" class="form-label">Quantidade:</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1" class="form-control w-50">
                </div>
                <button type="submit" class="btn btn-primary">Adicionar ao Carrinho</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.variation-btn').forEach(button => {
        button.addEventListener('click', function() {
            const imageUrl = this.getAttribute('data-image');
            const price = this.getAttribute('data-price');
            const name = this.getAttribute('data-name');
            const stock = this.getAttribute('data-stock');
            const variationId = this.getAttribute('data-variation-id');

            document.getElementById('productImage').src = imageUrl;
            document.getElementById('productPrice').textContent = `R$ ${parseFloat(price).toFixed(2).replace('.', ',')}`;
            document.getElementById('variationName').textContent = name;
            document.getElementById('variationStock').textContent = stock;
            document.getElementById('selectedVariationId').value = variationId;

            document.querySelectorAll('.variation-btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Atualizar o tamanho selecionado
    document.getElementById('sizeSelect').addEventListener('change', function() {
        document.getElementById('selectedSize').value = this.value;
    });

    document.querySelector('.variation-btn')?.classList.add('active');
</script>
@endsection