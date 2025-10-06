@extends('layouts.main')
@section('title', $product->nome)
@section('content')

<div class="product-show-container">
    <!-- Botão Voltar -->
    <a href="{{ route('home') }}" class="back-btn">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>

    <div class="row">
        <!-- Carrossel de Imagens -->
        <div class="col-md-6">
            <div class="product-carousel">
                <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @php
                            $allImages = [];
                            
                            // Adiciona imagem principal do produto
                            if ($product->images->where('is_main', true)->first()) {
                                $allImages[] = [
                                    'path' => Storage::url($product->images->where('is_main', true)->first()->path),
                                    'type' => 'produto',
                                    'name' => $product->nome,
                                    'badge_text' => 'PRODUTO'
                                ];
                            }
                            
                            // Adiciona imagens das variações ativas
                            foreach ($product->variations->where('status', 'ativo') as $variation) {
                                if ($variation->images->where('is_main', true)->first()) {
                                    $allImages[] = [
                                        'path' => Storage::url($variation->images->where('is_main', true)->first()->path),
                                        'type' => 'variação',
                                        'name' => $variation->nome_variacao,
                                        'badge_text' => 'VARIAÇÃO: ' . $variation->nome_variacao
                                    ];
                                }
                            }
                            
                            // Se não houver nenhuma imagem, usa uma placeholder
                            if (empty($allImages)) {
                                $allImages[] = [
                                    'path' => 'https://via.placeholder.com/500x500/1f2937/00d4aa?text=Sem+Imagem',
                                    'type' => 'placeholder',
                                    'name' => 'Imagem não disponível',
                                    'badge_text' => 'SEM IMAGEM'
                                ];
                            }
                        @endphp

                        @foreach ($allImages as $index => $image)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ $image['path'] }}" 
                                     alt="{{ $image['name'] }}" 
                                     class="d-block w-100">
                                <div class="carousel-caption d-none d-md-block">
                                    <small class="image-type-badge">{{ $image['badge_text'] }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if(count($allImages) > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                        <div class="carousel-indicators">
                            @foreach ($allImages as $index => $image)
                                <button type="button" data-bs-target="#productCarousel" 
                                        data-bs-slide-to="{{ $index }}" 
                                        class="{{ $index === 0 ? 'active' : '' }}"
                                        aria-label="Slide {{ $index + 1 }}"></button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informações do Produto -->
        <div class="col-md-6">
            <div class="product-info-section">
                <h1 class="product-title">{{ $product->nome }}</h1>
                
                <div class="product-detail">
                    <strong>Categoria:</strong> {{ $product->category->name ?? 'Sem categoria' }}
                </div>
                
                <div class="product-detail">
                    <strong>Descrição:</strong> {{ $product->descricao ?? 'Sem descrição' }}
                </div>
                
                <div class="product-detail">
                    <strong>Marca:</strong> {{ $product->marca ?? 'Sem marca' }}
                </div>
                
                <div class="product-price" id="productPrice">
                    R$ {{ number_format($product->variations->where('status', 'ativo')->first()->preco ?? 0, 2, ',', '.') }}
                </div>

                <!-- Seção de Variações -->
                <div class="variation-section">
                    <div class="section-label">Variações:</div>
                    <div class="variation-buttons">
                        @foreach ($product->variations->where('status', 'ativo') as $variation)
                            @php
                                $variationImage = $variation->images->where('is_main', true)->first();
                                $imageUrl = $variationImage ? Storage::url($variationImage->path) : 
                                            ($product->images->where('is_main', true)->first() ? 
                                            Storage::url($product->images->where('is_main', true)->first()->path) : 
                                            'https://via.placeholder.com/500x500/1f2937/00d4aa?text=Sem+Imagem');
                            @endphp
                            <button class="btn variation-btn" 
                                    data-variation-id="{{ $variation->id }}" 
                                    data-price="{{ $variation->preco }}" 
                                    data-image="{{ $imageUrl }}"
                                    data-name="{{ $variation->nome_variacao }}">
                                {{ $variation->nome_variacao }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Detalhes da Variação Selecionada -->
                <div class="variation-details">
                    <div class="variation-detail">
                        <strong>Variação Selecionada:</strong> 
                        <span id="variationName">{{ $product->variations->where('status', 'ativo')->first()->nome_variacao ?? 'Nenhuma' }}</span>
                    </div>
                    <div class="variation-detail">
                        <strong>Estoque Disponível:</strong> 
                        <span id="variationStock">0</span> unidades
                    </div>
                </div>

                <!-- Seleção de Tamanho -->
                <div class="size-section">
                    <div class="section-label">Tamanho:</div>
                    <div class="size-buttons" id="sizeButtons">
                        @foreach ($product->variations->where('status', 'ativo')->first()->sizes as $size)
                            <button class="btn size-btn {{ $size->pivot->quantity > 0 ? '' : 'disabled' }}" 
                                    data-size-id="{{ $size->id }}" 
                                    data-size-name="{{ $size->name }}" 
                                    data-stock="{{ $size->pivot->quantity }}">
                                {{ $size->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Formulário do Carrinho -->
                <div class="cart-form">
                    <form action="{{ route('products.addToCart', $product->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="variation_id" id="selectedVariationId" value="{{ $product->variations->where('status', 'ativo')->first()->id ?? '' }}">
                        <input type="hidden" name="size_id" id="selectedSizeId" value="">
                        
                        <div class="quantity-control">
                            <label class="quantity-label">Quantidade:</label>
                            <div class="quantity-wrapper">
                                <button type="button" class="quantity-btn quantity-minus" onclick="decrementQuantity()">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="quantity" name="quantity" min="1" max="1" value="1" class="quantity-input">
                                <button type="button" class="quantity-btn quantity-plus" onclick="incrementQuantity()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="add-to-cart-btn" id="addToCartBtn" disabled>
                            <i class="fas fa-shopping-cart"></i> Adicionar ao Carrinho
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Avaliações -->
    <div class="comments-section">
        <h3 class="comments-title">Avaliações do Produto</h3>
        
        @if ($product->comments->isEmpty())
            <p class="text-center">Sem avaliações para este produto.</p>
        @else
            @foreach ($product->comments as $comment)
                <div class="comment" id="comment-{{ $comment->id }}">
                    <div class="comment-display" id="display-{{ $comment->id }}">
                        <h5>{{ $comment->titulo }}</h5>
                        <p>{{ $comment->descricao }}</p>
                        <small>Por: {{ $comment->user->name }} em {{ $comment->created_at->format('d/m/Y H:i') }}</small>
                        
                        @if (Auth::check() && Auth::id() === $comment->user_id)
                            <div class="comment-actions">
                                <button class="btn comment-btn btn-outline-primary" onclick="toggleEditComment({{ $comment->id }})">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <form action="{{ route('comments.destroy', [$product->id, $comment->id]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn comment-btn btn-outline-danger" onclick="return confirm('Tem certeza que deseja excluir esta avaliação?')">
                                        <i class="fas fa-trash"></i> Excluir
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    @if (Auth::check() && Auth::id() === $comment->user_id)
                        <div class="comment-edit" id="edit-{{ $comment->id }}" style="display: none;">
                            <form action="{{ route('comments.update', [$product->id, $comment->id]) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="titulo-edit-{{ $comment->id }}" class="form-label">Título (máx. 40 caracteres)</label>
                                    <input type="text" class="form-control" id="titulo-edit-{{ $comment->id }}" 
                                           name="titulo" value="{{ $comment->titulo }}" 
                                           maxlength="40" required>
                                    <small class="text-muted"><span id="titulo-count-{{ $comment->id }}">{{ strlen($comment->titulo) }}</span>/40 caracteres</small>
                                </div>
                                <div class="mb-3">
                                    <label for="descricao-edit-{{ $comment->id }}" class="form-label">Descrição (máx. 140 caracteres)</label>
                                    <textarea class="form-control" id="descricao-edit-{{ $comment->id }}" 
                                              name="descricao" rows="4" maxlength="140" required>{{ $comment->descricao }}</textarea>
                                    <small class="text-muted"><span id="descricao-count-{{ $comment->id }}">{{ strlen($comment->descricao) }}</span>/140 caracteres</small>
                                </div>
                                <div class="comment-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Salvar
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="toggleEditComment({{ $comment->id }})">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        @endif

        <!-- Formulário para Adicionar Avaliação -->
        @if (Auth::check())
            @php
                $canComment = App\Models\Order::where('user_id', Auth::id())
                    ->where('status', 'delivered')
                    ->whereHas('items', function ($query) use ($product) {
                        $query->where('product_id', $product->id);
                    })
                    ->exists();
            @endphp
            @if ($canComment)
                <div class="comment-form">
                    <h4>Adicionar Avaliação</h4>
                    <form action="{{ route('comments.store', $product->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título (máx. 40 caracteres)</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" 
                                   maxlength="40" required>
                            <small class="text-muted"><span id="titulo-count">0</span>/40 caracteres</small>
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição (máx. 140 caracteres)</label>
                            <textarea class="form-control" id="descricao" name="descricao" 
                                      rows="4" maxlength="140" required></textarea>
                            <small class="text-muted"><span id="descricao-count">0</span>/140 caracteres</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Enviar Avaliação
                        </button>
                    </form>
                </div>
            @else
                <p class="text-center">Você precisa ter comprado e recebido este produto para adicionar uma avaliação.</p>
            @endif
        @else
            <p class="text-center">Faça login para adicionar uma avaliação.</p>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Simple product functionality without conflicts
document.addEventListener('DOMContentLoaded', function() {
    // Product variation functionality
    let sizesData = {};

    // Initialize size data for each variation
    @foreach ($product->variations->where('status', 'ativo') as $variation)
        sizesData[{{ $variation->id }}] = [
            @foreach ($variation->sizes as $size)
                { id: {{ $size->id }}, name: "{{ $size->name }}", stock: {{ $size->pivot->quantity }}},
            @endforeach
        ];
    @endforeach

    function updateSizeButtons(variationId) {
        const sizeButtonsContainer = document.getElementById('sizeButtons');
        if (!sizeButtonsContainer) return;
        
        sizeButtonsContainer.innerHTML = '';
        const sizes = sizesData[variationId] || [];
        
        sizes.forEach(size => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `btn size-btn ${size.stock > 0 ? '' : 'disabled'}`;
            button.dataset.sizeId = size.id;
            button.dataset.sizeName = size.name;
            button.dataset.stock = size.stock;
            button.textContent = size.name;
            sizeButtonsContainer.appendChild(button);
        });
        
        // Select first available size
        const availableSize = Array.from(sizeButtonsContainer.querySelectorAll('.size-btn'))
            .find(btn => !btn.classList.contains('disabled'));
            
        if (availableSize) {
            availableSize.classList.add('active');
            document.getElementById('selectedSizeId').value = availableSize.dataset.sizeId;
            updateStockDisplay(availableSize);
        } else {
            updateStockDisplay(null);
        }

        // Attach listeners to new size buttons
        attachSizeButtonListeners();
    }

    function updateStockDisplay(sizeButton) {
        const stockDisplay = document.getElementById('variationStock');
        const quantityInput = document.getElementById('quantity');
        const addToCartBtn = document.getElementById('addToCartBtn');
        
        if (stockDisplay && quantityInput && addToCartBtn) {
            if (sizeButton && sizeButton.dataset.stock) {
                stockDisplay.textContent = sizeButton.dataset.stock;
                quantityInput.max = sizeButton.dataset.stock;
                quantityInput.value = Math.min(parseInt(quantityInput.value), parseInt(sizeButton.dataset.stock));
                addToCartBtn.disabled = parseInt(sizeButton.dataset.stock) === 0;
            } else {
                stockDisplay.textContent = '0';
                quantityInput.max = '1';
                quantityInput.value = '1';
                addToCartBtn.disabled = true;
            }
        }
    }

    function attachSizeButtonListeners() {
        document.querySelectorAll('.size-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (this.classList.contains('disabled')) return;
                
                document.querySelectorAll('.size-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                this.classList.add('active');
                document.getElementById('selectedSizeId').value = this.dataset.sizeId;
                updateStockDisplay(this);
            });
        });
    }

    function findImageIndexInCarousel(imageUrl) {
        const carouselItems = document.querySelectorAll('.carousel-item');
        // Normalizar URLs para comparação
        const normalizedSearchUrl = imageUrl.split('?')[0].trim();
        
        for (let i = 0; i < carouselItems.length; i++) {
            const img = carouselItems[i].querySelector('img');
            if (img) {
                const normalizedImgSrc = img.src.split('?')[0].trim();
                if (normalizedImgSrc === normalizedSearchUrl || 
                    normalizedImgSrc.endsWith(normalizedSearchUrl.split('/').pop())) {
                    return i;
                }
            }
        }
        return -1;
    }

    function goToCarouselSlide(index) {
        const carouselElement = document.getElementById('productCarousel');
        if (carouselElement) {
            const carousel = bootstrap.Carousel.getOrCreateInstance(carouselElement);
            carousel.to(index);
        }
    }

    // Attach variation button listeners
    document.querySelectorAll('.variation-btn').forEach(button => {
        button.addEventListener('click', function() {
            const price = this.getAttribute('data-price');
            const name = this.getAttribute('data-name');
            const variationId = this.getAttribute('data-variation-id');
            const imageUrl = this.getAttribute('data-image');

            // Update product info
            const priceElement = document.getElementById('productPrice');
            const nameElement = document.getElementById('variationName');
            const variationIdElement = document.getElementById('selectedVariationId');
            
            if (priceElement) priceElement.textContent = `R$ ${parseFloat(price).toFixed(2).replace('.', ',')}`;
            if (nameElement) nameElement.textContent = name;
            if (variationIdElement) variationIdElement.value = variationId;
            
            // Update active variation button
            document.querySelectorAll('.variation-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');

            // Find and activate corresponding image in carousel
            const imageIndex = findImageIndexInCarousel(imageUrl);
            if (imageIndex !== -1) {
                setTimeout(() => goToCarouselSlide(imageIndex), 50);
            }

            updateSizeButtons(variationId);
        });
    });

    // Initialize with first variation
    const firstVariationBtn = document.querySelector('.variation-btn');
    if (firstVariationBtn) {
        firstVariationBtn.classList.add('active');
        updateSizeButtons(firstVariationBtn.getAttribute('data-variation-id'));
        
        // Also activate the corresponding image
        const firstVariationImageUrl = firstVariationBtn.getAttribute('data-image');
        const imageIndex = findImageIndexInCarousel(firstVariationImageUrl);
        if (imageIndex !== -1) {
            setTimeout(() => goToCarouselSlide(imageIndex), 100);
        }
    }

    // Quantity validation
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantityInput.addEventListener('input', function() {
            const max = parseInt(this.max) || 1;
            if (this.value > max) this.value = max;
            if (this.value < 1) this.value = 1;
        });
    }

    // Attach size button listeners initially
    attachSizeButtonListeners();

    // Character counters for comment forms
    function setupCharacterCounters() {
        // New comment form
        const tituloInput = document.getElementById('titulo');
        const descricaoInput = document.getElementById('descricao');
        const tituloCount = document.getElementById('titulo-count');
        const descricaoCount = document.getElementById('descricao-count');

        if (tituloInput && tituloCount) {
            tituloInput.addEventListener('input', function() {
                tituloCount.textContent = this.value.length;
            });
        }

        if (descricaoInput && descricaoCount) {
            descricaoInput.addEventListener('input', function() {
                descricaoCount.textContent = this.value.length;
            });
        }

        // Edit comment forms
        @foreach ($product->comments as $comment)
            @if (Auth::check() && Auth::id() === $comment->user_id)
                (function() {
                    const tituloEdit = document.getElementById('titulo-edit-{{ $comment->id }}');
                    const descricaoEdit = document.getElementById('descricao-edit-{{ $comment->id }}');
                    const tituloCountEdit = document.getElementById('titulo-count-{{ $comment->id }}');
                    const descricaoCountEdit = document.getElementById('descricao-count-{{ $comment->id }}');

                    if (tituloEdit && tituloCountEdit) {
                        tituloEdit.addEventListener('input', function() {
                            tituloCountEdit.textContent = this.value.length;
                        });
                    }

                    if (descricaoEdit && descricaoCountEdit) {
                        descricaoEdit.addEventListener('input', function() {
                            descricaoCountEdit.textContent = this.value.length;
                        });
                    }
                })();
            @endif
        @endforeach
    }

    setupCharacterCounters();
});

// Função para alternar edição de comentário
function toggleEditComment(commentId) {
    const displayDiv = document.getElementById('display-' + commentId);
    const editDiv = document.getElementById('edit-' + commentId);
    
    if (displayDiv && editDiv) {
        if (displayDiv.style.display === 'none') {
            displayDiv.style.display = 'block';
            editDiv.style.display = 'none';
        } else {
            displayDiv.style.display = 'none';
            editDiv.style.display = 'block';
        }
    }
}

// Funções para incrementar/decrementar quantidade
window.incrementQuantity = function() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.max) || 1;
    const current = parseInt(input.value) || 1;
    if (current < max) {
        input.value = current + 1;
    }
};

window.decrementQuantity = function() {
    const input = document.getElementById('quantity');
    const current = parseInt(input.value) || 1;
    if (current > 1) {
        input.value = current - 1;
    }
};
</script>
@endsection