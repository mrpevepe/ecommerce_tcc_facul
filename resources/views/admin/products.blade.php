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
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            Exibindo {{ $products->firstItem() }} a {{ $products->lastItem() }} de {{ $products->total() }} resultados
        </div>
        <div>
            {{ $products->links('pagination::simple-bootstrap-5') }}
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
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            Exibindo {{ $products->firstItem() }} a {{ $products->lastItem() }} de {{ $products->total() }} resultados
        </div>
        <div>
            {{ $products->links('pagination::simple-bootstrap-5') }}
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

    /* CSS para ajustar o tamanho da paginação */
    .pagination {
        font-size: 0.9rem;
    }
    .pagination .page-link {
        padding: 0.25rem 0.5rem;
    }
    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }
</style>
@endsection