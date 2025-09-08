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
                    <div class="product-card">
                        @if ($product->images->where('is_main', true)->first())
                            <img src="{{ Storage::url($product->images->where('is_main', true)->first()->path) }}" alt="{{ $product->nome }}">
                        @else
                            <p>Sem Imagem</p>
                        @endif
                        <h5>{{ $product->nome }}</h5>
                        <p>Marca: {{ $product->marca ?? 'Sem marca' }}</p>
                        <p>Preço: R$ {{ number_format($price, 2, ',', '.') }}</p>
                        <form action="{{ route('products.addToCart', $product->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="variation_id" value="{{ $firstVariation->id ?? '' }}">
                            <div class="mb-2">
                                <label for="quantity_{{ $product->id }}" class="form-label">Quantidade:</label>
                                <input type="number" id="quantity_{{ $product->id }}" name="quantity" min="1" value="1" class="form-control w-50">
                            </div>
                            <button type="submit" class="btn btn-primary">Adicionar ao Carrinho</button>
                        </form>
                    </div>
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