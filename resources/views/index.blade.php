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
    .pagination {
        font-size: 0.9rem; /* Tamanho menor para os links */
    }
    .pagination .page-link {
        padding: 0.25rem 0.5rem; /* Menor padding para setas e números */
    }
    .pagination .page-item.active .page-link {
        background-color: #007bff; /* Cor do Bootstrap para página ativa */
        border-color: #007bff;
    }
</style>

<div class="container mt-5">
    <h1>Bem-vindo à Loja</h1>
    <div class="mb-3 filter-container">
        <form method="GET" action="{{ route('home') }}" class="d-flex gap-2 align-items-center w-100">
            <input type="text" name="search" id="searchBar" class="form-control" placeholder="Pesquisar produtos..." value="{{ request()->query('search') }}">
            <select name="category_id" id="categoryFilter" class="form-control w-25" onchange="this.form.submit()">
                <option value="all" {{ request()->query('category_id', 'all') == 'all' ? 'selected' : '' }}>Todas as Categorias</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request()->query('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Pesquisar</button>
        </form>
    </div>

    <!-- Paginação superior (acima dos produtos) -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            Exibindo {{ $products->firstItem() }} a {{ $products->lastItem() }} de {{ $products->total() }} produtos
        </div>
        <div>
            {{ $products->links('pagination::simple-bootstrap-5') }}
        </div>
    </div>

    <div id="productList" class="row">
        @foreach ($products as $product)
            @if ($product->status === 'ativo' && $product->variations->where('status', 'ativo')->isNotEmpty())
                @php
                    $firstVariation = $product->variations->where('status', 'ativo')->first();
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
            @endif
        @endforeach
    </div>

    <!-- Paginação inferior (abaixo dos produtos) -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            Exibindo {{ $products->firstItem() }} a {{ $products->lastItem() }} de {{ $products->total() }} produtos
        </div>
        <div>
            {{ $products->links('pagination::simple-bootstrap-5') }}
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
@endsection