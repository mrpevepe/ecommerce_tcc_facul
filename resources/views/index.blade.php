@extends('layouts.main')
@section('title', 'Home Sitezudo')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush
@section('content')
<div class="container home-container">
    <h1 class="home-title">Bem-vindo à Loja</h1>
    <div class="home-filter-container">
        <form method="GET" action="{{ route('home') }}" class="d-flex gap-2 align-items-center w-100">
            <input type="text" name="search" id="searchBar" class="form-control home-filter-input" placeholder="Pesquisar produtos..." value="{{ request()->query('search') }}">
            <select name="category_id" id="categoryFilter" class="form-control home-filter-select w-25" onchange="this.form.submit()">
                <option value="all" {{ request()->query('category_id', 'all') == 'all' ? 'selected' : '' }}>Todas as Categorias</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request()->query('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn home-filter-button">Pesquisar</button>
        </form>
    </div>

    <!-- Paginação no topo -->
    @if($products->hasPages())
    <div class="comments-pagination-wrapper">
        <div class="comments-pagination-info">
            Exibindo {{ $products->firstItem() }} a {{ $products->lastItem() }} de {{ $products->total() }} produtos
        </div>
        <div class="comments-pagination">
            {{ $products->links('pagination::bootstrap-4') }}
        </div>
    </div>
    @endif

    <div id="productList" class="row">
        @foreach ($products as $product)
            @if ($product->status === 'ativo' && $product->variations->where('status', 'ativo')->isNotEmpty())
                @php
                    $firstVariation = $product->variations->where('status', 'ativo')->first();
                    $price = $firstVariation ? $firstVariation->preco : 0;
                @endphp
                <div class="col-md-3">
                    <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none text-dark">
                        <div class="home-product-card">
                            @if ($product->images->where('is_main', true)->first())
                                <img src="{{ Storage::url($product->images->where('is_main', true)->first()->path) }}" alt="{{ $product->nome }}">
                            @else
                                <p class="home-product-card-text">Sem Imagem</p>
                            @endif
                            <h5 class="home-product-card-title">{{ $product->nome }}</h5>
                            <p class="home-product-card-text">Marca: {{ $product->marca ?? 'Sem marca' }}</p>
                            <p class="home-product-card-text">Categoria: {{ $product->category->name ?? 'Sem categoria' }}</p>
                            <p class="home-product-card-price">Preço: R$ {{ number_format($price, 2, ',', '.') }}</p>
                        </div>
                    </a>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Paginação no rodapé -->
    @if($products->hasPages())
    <div class="comments-pagination-wrapper">
        <div class="comments-pagination-info">
            Exibindo {{ $products->firstItem() }} a {{ $products->lastItem() }} de {{ $products->total() }} produtos
        </div>
        <div class="comments-pagination">
            {{ $products->links('pagination::bootstrap-4') }}
        </div>
    </div>
    @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
@endsection