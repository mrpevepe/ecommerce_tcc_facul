@extends('layouts.main')

@section('title', 'Listar Produtos')

@section('content')
<div class="products-container">
    <h1 class="products-title">Listagem de Produtos</h1>
        <a href="{{ route('admin.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
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

    <!-- Paginação no topo -->
    @if($products->hasPages())
    <div class="comments-pagination-wrapper">
        <div class="comments-pagination-info">
            Mostrando {{ $products->firstItem() }} a {{ $products->lastItem() }} de {{ $products->total() }} produtos
        </div>
        <div class="comments-pagination">
            {{ $products->links('pagination::bootstrap-4') }}
        </div>
    </div>
    @endif
    
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
                        <td>
                            <div class="product-name" data-full-description="{{ $product->nome }}">
                                {{ $product->nome }}
                            </div>
                        </td>
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

    <!-- Paginação no rodapé -->
    @if($products->hasPages())
    <div class="comments-pagination-wrapper">
        <div class="comments-pagination-info">
            Mostrando {{ $products->firstItem() }} a {{ $products->lastItem() }} de {{ $products->total() }} produtos
        </div>
        <div class="comments-pagination">
            {{ $products->links('pagination::bootstrap-4') }}
        </div>
    </div>
    @endif
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

    // Tooltip para nomes e descrições longas
    document.addEventListener('DOMContentLoaded', function() {
        const elements = document.querySelectorAll('.product-description, .product-name');
        
        elements.forEach(element => {
            const fullText = element.getAttribute('data-full-description');
            const displayText = element.textContent.trim();
            
            // Se o texto for muito longo (mais de 20 caracteres), aplicar truncamento
            if (displayText.length > 20) {
                element.textContent = displayText.substring(0, 20) + '...';
                
                // Criar tooltip
                element.classList.add('has-tooltip');
                
                element.addEventListener('mouseenter', function(e) {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'description-tooltip';
                    tooltip.textContent = fullText;
                    
                    document.body.appendChild(tooltip);
                    
                    // Posicionar tooltip
                    const rect = element.getBoundingClientRect();
                    tooltip.style.left = rect.left + 'px';
                    tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
                });
                
                element.addEventListener('mouseleave', function() {
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
    /* ==================== SOBRESCREVER VARIÁVEIS DO BOOTSTRAP ==================== */
    .table {
        --bs-table-bg: transparent;
        --bs-table-striped-bg: rgba(15, 23, 42, 0.4);
        --bs-table-striped-color: #e5e7eb;
        --bs-table-active-bg: rgba(15, 23, 42, 0.6);
        --bs-table-active-color: #e5e7eb;
        --bs-table-hover-bg: rgba(15, 23, 42, 0.9);
        --bs-table-hover-color: #e5e7eb;
    }

    /* Forçar as cores do tema dark */
    .table-striped>tbody>tr:nth-of-type(odd)>* {
        --bs-table-color-type: var(--bs-table-striped-color) !important;
        --bs-table-bg-type: var(--bs-table-striped-bg) !important;
        color: var(--bs-table-color-type) !important;
        background-color: var(--bs-table-bg-type) !important;
    }

    .table-striped>tbody>tr:nth-of-type(even)>* {
        --bs-table-color-type: var(--bs-table-striped-color) !important;
        --bs-table-bg-type: rgba(15, 23, 42, 0.6) !important;
        color: var(--bs-table-color-type) !important;
        background-color: var(--bs-table-bg-type) !important;
    }

    /* Estilos específicos para nossa tabela */
    .products-table-container .table tbody tr {
        background: rgba(15, 23, 42, 0.6) !important;
    }

    .products-table-container .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(15, 23, 42, 0.4) !important;
    }

    .products-table-container .table-striped tbody tr:nth-of-type(even) {
        background-color: rgba(15, 23, 42, 0.6) !important;
    }

    .products-table-container .table tbody tr:hover {
        background-color: rgba(15, 23, 42, 0.9) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 212, 170, 0.2);
    }

    /* Garantir que o texto fique legível */
    .table td, .table th {
        color: #e5e7eb !important;
        border-color: rgba(0, 212, 170, 0.1) !important;
    }

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

    /* Estilos para paginação */
    .comments-pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
        padding: 1rem 0;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .comments-pagination-info {
        color: var(--light-text);
        font-size: 0.9rem;
        font-weight: 600;
    }

    .comments-pagination {
        display: flex;
        gap: 0.5rem;
    }

    .comments-pagination .pagination {
        margin: 0;
    }

    .comments-pagination .page-link {
        background: rgba(31, 41, 55, 0.8);
        border: 1px solid rgba(0, 212, 170, 0.3);
        color: var(--accent);
        padding: 0.5rem 0.8rem;
    }

    .comments-pagination .page-item.active .page-link {
        background: var(--accent);
        border-color: var(--accent);
        color: #1a202c;
    }

    .comments-pagination .page-link:hover {
        background: rgba(0, 212, 170, 0.1);
        border-color: var(--accent);
        color: var(--accent);
    }

    /* Tooltip para nomes e descrições longas */
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
        white-space: normal;
        word-wrap: break-word;
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
        content: "🔍";
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.8rem;
        opacity: 0.7;
    }

    @media (max-width: 768px) {
        .comments-pagination-wrapper {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
@endsection