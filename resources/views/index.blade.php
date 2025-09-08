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
    </style>

    <div class="container mt-5">
        <h1>Bem-vindo à Loja</h1>
        <div class="mb-3">
            <input type="text" id="searchBar" class="form-control" placeholder="Pesquisar produtos...">
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
    </script>
@endsection