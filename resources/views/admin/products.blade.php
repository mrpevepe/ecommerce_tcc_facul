@extends('layouts.main')

@section('title', 'Listar Produtos')

@section('content')
<div class="products-container">
    <h1 class="products-title">Listagem de Produtos</h1>
    <a href="{{ route('admin.products.create') }}" class="create-product-btn">
        <i class="fas fa-plus"></i> Criar Novo Produto
    </a>
    
    <div class="filter-container">
        <input type="text" id="searchBar" class="form-control" placeholder="Pesquisar por nome ou marca..." value="{{ request()->query('search') }}">
        <select id="statusFilter" class="form-control">
            <option value="all" {{ request()->query('status') == 'all' ? 'selected' : '' }}>Todos os Status</option>
            <option value="ativo" {{ request()->query('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
            <option value="inativo" {{ request()->query('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
        </select>
    </div>
    
    <div class="products-table-container">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Categoria</th>
                    <th>Marca</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="productTable">
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
                            @if ($product->images->where('is_main', true)->first())
                                <img src="{{ Storage::url(str_replace('public/', '', $product->images->where('is_main', true)->first()->path)) }}" 
                                     alt="Imagem do Produto" class="product-image">
                            @else
                                <span class="no-image">Sem Imagem</span>
                            @endif
                        </td>
                        <td>{{ $product->nome }}</td>
                        <td>
                            <div class="product-description" data-full-description="{{ $product->descricao ?? 'Sem descrição' }}">
                                {{ $product->descricao ?? 'Sem descrição' }}
                            </div>
                        </td>
                        <td>{{ $product->category->name ?? 'Sem categoria' }}</td>
                        <td>{{ $product->marca }}</td>
                        <td>
                            <span class="product-status status-{{ $product->status }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="product-actions">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="action-btn btn-edit">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="{{ route('admin.products.variations', $product->id) }}" class="action-btn btn-variations">
                                    <i class="fas fa-list"></i> Variações
                                </a>
                                <a href="{{ route('admin.comments.index', $product->id) }}" class="action-btn btn-comments">
                                    <i class="fas fa-comments"></i> Avaliações
                                </a>
                                <form action="{{ route('admin.products.updateStatus', $product->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="action-btn {{ $product->status === 'ativo' ? 'btn-deactivate' : 'btn-activate' }}">
                                        <i class="fas {{ $product->status === 'ativo' ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                        {{ $product->status === 'ativo' ? 'Inativar' : 'Ativar' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('searchBar').addEventListener('input', function() {
        const url = new URL(window.location);
        url.searchParams.set('search', this.value);
        window.location = url;
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        const url = new URL(window.location);
        url.searchParams.set('status', this.value);
        window.location = url;
    });

    // Tooltip para descrições longas
    document.addEventListener('DOMContentLoaded', function() {
        const descriptions = document.querySelectorAll('.product-description');
        
        descriptions.forEach(desc => {
            const fullText = desc.getAttribute('data-full-description');
            const displayText = desc.textContent.trim();
            
            // Se o texto for muito longo, aplicar truncamento
            if (displayText.length > 80) {
                desc.textContent = displayText.substring(0, 80) + '...';
                
                // Criar tooltip
                desc.classList.add('has-tooltip');
                
                desc.addEventListener('mouseenter', function(e) {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'description-tooltip';
                    tooltip.textContent = fullText;
                    
                    document.body.appendChild(tooltip);
                    
                    // Posicionar tooltip
                    const rect = desc.getBoundingClientRect();
                    tooltip.style.left = rect.left + 'px';
                    tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
                });
                
                desc.addEventListener('mouseleave', function() {
                    const tooltip = document.querySelector('.description-tooltip');
                    if (tooltip) {
                        tooltip.remove();
                    }
                });
            }
        });
    });
</script>

<style>
    /* Estilos adicionais específicos para esta página */
    .no-image {
        color: var(--light-text);
        font-size: 0.9rem;
        font-style: italic;
    }
    
    .product-actions form {
        margin: 0;
        display: contents;
    }
    
    /* Tooltip para descrição */
    .description-tooltip {
        position: fixed;
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(10px);
        color: var(--dark-text);
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid var(--accent);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
        max-width: 400px;
        z-index: 10000;
        font-size: 0.9rem;
        line-height: 1.5;
        animation: tooltipFadeIn 0.2s ease-out;
        white-space: normal; /* Adicionado */
        word-wrap: break-word; /* Adicionado */
    }
    
    @keyframes tooltipFadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .has-tooltip {
        cursor: help;
        position: relative;
    }
    
    .has-tooltip:hover::after {
        content: '🔍';
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.8rem;
        opacity: 0.7;
    }
</style>
@endsection