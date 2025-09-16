@extends('layouts.main')
@section('title', 'Home Sitezudo')
@section('content')
<style>
    .product-card {
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        text-align: center;
    }
    .product-card img {
        max-width: 100%;
        height: auto;
    }
    .filter-container {
        display: flex;
        gap: 10px;
        align-items: center;
    }
</style>

<div class="container mt-5">
    <h1>Bem-vindo à Loja</h1>
    <div class="mb-3 filter-container">
        <input type="text" id="searchBar" class="form-control" placeholder="Pesquisar produtos...">
        <select id="categoryFilter" class="form-control w-25">
            <option value="all">Todas as Categorias</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ request()->query('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div id="productList" class="row">
        @foreach ($products as $product)
            @php
                $firstVariation = $product->variations->first();
                $price = $firstVariation ? $firstVariation->preco : 0;
            @endphp
            <div class="col-md-4">
                <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none text-dark">
                    <div class="product-card">
                        @if ($product->images->where('is_main', true)->first())
                            <img src="{{ Storage::url($product->images->where('is_main', true)->first()->path) }}" alt="{{ $product->nome }}">
                        @else
                            <p>Sem Imagem</p>
                        @endif
                        <h5>{{ $product->nome }}</h5>
                        <p>Marca: {{ $product->marca ?? 'Sem marca' }}</p>
                        <p>Categoria: {{ $product->category->name ?? 'Sem categoria' }}</p>
                        <p>Preço: R$ {{ number_format($price, 2, ',', '.') }}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('searchBar').addEventListener('input', function() {
        let filter = this.value.toLowerCase();
        document.querySelectorAll('#productList .col-md-4').forEach(card => {
            let text = card.textContent.toLowerCase();
            card.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    document.getElementById('categoryFilter').addEventListener('change', function() {
        const categoryId = this.value;
        const url = new URL(window.location);
        if (categoryId === 'all') {
            url.searchParams.delete('category_id');
        } else {
            url.searchParams.set('category_id', categoryId);
        }
        window.location = url;
    });
</script>
@endsection