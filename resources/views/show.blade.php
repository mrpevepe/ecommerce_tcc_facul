@extends('layouts.main')
@section('title', 'Camiseta')
@section('content')
<style>
    .product-image {
        max-width: 100%;
        height: auto;
        margin-bottom: 20px;
    }
    .variation-btn, .size-btn {
        margin: 5px;
    }
    .variation-details {
        margin-top: 10px;
    }
    .size-btn.disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }
    .size-btn.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
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
                    <button class="btn btn-outline-primary variation-btn" data-variation-id="{{ $variation->id }}" data-price="{{ $variation->preco }}" data-image="{{ Storage::url($variation->images->where('is_main', true)->first()->path ?? $product->images->where('is_main', true)->first()->path) }}" data-name="{{ $variation->nome_variacao }}">
                        {{ $variation->nome_variacao }}
                    </button>
                @endforeach
            </div>
            <div class="variation-details">
                <p><strong>Variação Selecionada:</strong> <span id="variationName">{{ $product->variations->first()->nome_variacao ?? 'Nenhuma' }}</span></p>
                <p><strong>Estoque:</strong> <span id="variationStock">0</span></p>
            </div>
            
            <!-- Seleção de tamanho com botões -->
            <div class="mb-3">
                <label class="form-label">Tamanho:</label>
                <div id="sizeButtons">
                    @foreach ($product->variations->first()->sizes as $size)
                        <button class="btn btn-outline-secondary size-btn" data-size-id="{{ $size->id }}" data-size-name="{{ $size->name }}" data-stock="{{ $size->pivot->quantity }}" {{ $size->pivot->quantity > 0 ? '' : 'disabled' }}>
                            {{ $size->name }}
                        </button>
                    @endforeach
                </div>
            </div>
            
            <form action="{{ route('products.addToCart', $product->id) }}" method="POST" class="mt-3">
                @csrf
                <input type="hidden" name="variation_id" id="selectedVariationId" value="{{ $product->variations->first()->id ?? '' }}">
                <input type="hidden" name="size_id" id="selectedSizeId" value="">
                <div class="mb-2">
                    <label for="quantity" class="form-label">Quantidade:</label>
                    <input type="number" id="quantity" name="quantity" min="1" max="1" value="1" class="form-control w-50">
                </div>
                <button type="submit" class="btn btn-primary" id="addToCartBtn" disabled>Adicionar ao Carrinho</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let sizesData = {};

    // Inicializar dados de tamanhos para cada variação
    @foreach ($product->variations as $variation)
        sizesData[{{ $variation->id }}] = [
            @foreach ($variation->sizes as $size)
                { id: {{ $size->id }}, name: "{{ $size->name }}", stock: {{ $size->pivot->quantity }}},
            @endforeach
        ];
    @endforeach

    function updateSizeButtons(variationId) {
        const sizeButtonsContainer = document.getElementById('sizeButtons');
        sizeButtonsContainer.innerHTML = '';
        const sizes = sizesData[variationId] || [];
        sizes.forEach(size => {
            const button = document.createElement('button');
            button.className = `btn btn-outline-secondary size-btn ${size.stock > 0 ? '' : 'disabled'}`;
            button.dataset.sizeId = size.id;
            button.dataset.sizeName = size.name;
            button.dataset.stock = size.stock;
            button.textContent = size.name;
            sizeButtonsContainer.appendChild(button);
        });
        attachSizeButtonListeners();

        // Selecionar automaticamente o tamanho "G" se disponível
        const gSizeButton = Array.from(document.querySelectorAll('.size-btn')).find(btn => btn.dataset.sizeName === 'G' && !btn.classList.contains('disabled'));
        if (gSizeButton) {
            gSizeButton.classList.add('active');
            document.getElementById('selectedSizeId').value = gSizeButton.dataset.sizeId;
            updateStockDisplay(gSizeButton);
        } else {
            updateStockDisplay(null);
        }
    }

    function updateStockDisplay(sizeButton) {
        const stockDisplay = document.getElementById('variationStock');
        const quantityInput = document.getElementById('quantity');
        const addToCartBtn = document.getElementById('addToCartBtn');
        if (sizeButton && sizeButton.dataset.stock) {
            stockDisplay.textContent = sizeButton.dataset.stock;
            quantityInput.max = sizeButton.dataset.stock;
            quantityInput.value = Math.min(quantityInput.value, sizeButton.dataset.stock);
            addToCartBtn.disabled = parseInt(sizeButton.dataset.stock) === 0;
        } else {
            stockDisplay.textContent = '0';
            quantityInput.max = '1';
            quantityInput.value = '1';
            addToCartBtn.disabled = true;
        }
    }

    function attachSizeButtonListeners() {
        document.querySelectorAll('.size-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (this.classList.contains('disabled')) return;
                document.querySelectorAll('.size-btn').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('selectedSizeId').value = this.dataset.sizeId;
                updateStockDisplay(this);
            });
        });
    }

    document.querySelectorAll('.variation-btn').forEach(button => {
        button.addEventListener('click', function() {
            const imageUrl = this.getAttribute('data-image');
            const price = this.getAttribute('data-price');
            const name = this.getAttribute('data-name');
            const variationId = this.getAttribute('data-variation-id');

            document.getElementById('productImage').src = imageUrl;
            document.getElementById('productPrice').textContent = `R$ ${parseFloat(price).toFixed(2).replace('.', ',')}`;
            document.getElementById('variationName').textContent = name;
            document.getElementById('selectedVariationId').value = variationId;
            document.querySelectorAll('.variation-btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            updateSizeButtons(variationId);
        });
    });

    // Inicia com a primeira variação ativa
    const firstVariationBtn = document.querySelector('.variation-btn');
    if (firstVariationBtn) {
        firstVariationBtn.classList.add('active');
        updateSizeButtons(firstVariationBtn.getAttribute('data-variation-id'));
    }

    // Validar quantidade ao mudar
    document.getElementById('quantity').addEventListener('input', function() {
        const max = parseInt(this.max) || 1;
        if (this.value > max) this.value = max;
        if (this.value < 1) this.value = 1;
    });
</script>
@endsection