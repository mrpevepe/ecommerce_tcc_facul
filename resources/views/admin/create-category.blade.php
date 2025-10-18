@extends('layouts.main')

@section('title', 'Gerenciar Categorias')

@section('content')
<link rel="stylesheet" href="{{ asset('css/categories.css') }}">

<div class="categories-container">
    <h1 class="categories-title">Gerenciar Categorias</h1>
    
    <div class="admin-actions">
        <a href="{{ route('admin.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Formulário de Criação -->
    <div class="create-category-form">
        <h2>Criar Nova Categoria</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="form-group">
                <label for="name" class="form-label">Nome da Categoria</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="{{ old('name') }}" 
                       maxlength="60" required>
                <div class="character-count" id="name-count">
                    {{ strlen(old('name', '')) }}/60 caracteres
                </div>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn-primary">Salvar Categoria</button>
        </form>
    </div>

    <!-- Filtros -->
    <div class="categories-filters-container">
        <form method="GET" action="{{ route('admin.categories.create') }}" id="filterForm">
            <div class="categories-filters-form">
                <div class="categories-filter-group">
                    <label for="search" class="categories-filter-label">Pesquisar</label>
                    <input type="text" name="search" class="categories-filter-input" 
                           placeholder="Pesquisar por ID ou nome..." 
                           value="{{ request()->query('search', '') }}"
                           id="searchInput">
                </div>
                
                <div class="categories-filter-actions">
                    <button type="submit" class="categories-filter-btn">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    @if(request()->query('search'))
                        <a href="{{ route('admin.categories.create') }}" class="categories-clear-filter-btn">
                            <i class="fas fa-times"></i> Limpar Filtros
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Paginação no topo -->
    @if($categories->hasPages())
    <div class="pagination-info">
        <div class="pagination-results">
            Exibindo {{ $categories->firstItem() }} a {{ $categories->lastItem() }} de {{ $categories->total() }} resultados
        </div>
        <div class="pagination-links">
            {{ $categories->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>
    @endif

    <!-- Tabela de Categorias -->
    @if ($categories->isEmpty())
        <div class="categories-empty-state">
            <i class="fas fa-tags categories-empty-icon"></i>
            <h3>Nenhuma categoria encontrada</h3>
            <p>Tente ajustar os filtros para ver mais resultados.</p>
        </div>
    @else
        <div class="categories-table-container">
            <table class="categories-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>
                                <span class="category-id">#{{ $category->id }}</span>
                            </td>
                            <td>
                                <div class="category-name-display" 
                                     onclick="toggleEditForm({{ $category->id }})"
                                     id="name-display-{{ $category->id }}">
                                    {{ $category->name }}
                                </div>
                                <form class="category-edit-form" 
                                      id="edit-form-{{ $category->id }}" 
                                      style="display: none;"
                                      action="{{ route('admin.categories.update', $category->id) }}" 
                                      method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div style="flex: 1; min-width: 200px;">
                                        <input type="text" 
                                               name="name" 
                                               class="category-edit-input" 
                                               value="{{ $category->name }}" 
                                               maxlength="60" 
                                               oninput="updateEditCharacterCount({{ $category->id }})"
                                               required
                                               id="edit-input-{{ $category->id }}">
                                        <div class="character-count" id="edit-count-{{ $category->id }}">
                                            {{ strlen($category->name) }}/60 caracteres
                                        </div>
                                    </div>
                                    <input type="hidden" name="search" value="{{ request()->query('search', '') }}">
                                    <button type="submit" class="category-action-btn save-btn">
                                        <i class="fas fa-save"></i> Salvar
                                    </button>
                                    <button type="button" 
                                            class="category-action-btn cancel-btn"
                                            onclick="toggleEditForm({{ $category->id }})">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                </form>
                            </td>
                            <td>
                                <span class="category-status status-{{ $category->status }}">
                                    {{ ucfirst($category->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="category-actions">
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="search" value="{{ request()->query('search', '') }}">
                                        <button type="submit" 
                                                class="category-action-btn delete-btn"
                                                onclick="return confirm('Tem certeza que deseja excluir esta categoria?')">
                                            <i class="fas fa-trash"></i> Excluir
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
        @if($categories->hasPages())
        <div class="pagination-info">
            <div class="pagination-results">
                Exibindo {{ $categories->firstItem() }} a {{ $categories->lastItem() }} de {{ $categories->total() }} resultados
            </div>
            <div class="pagination-links">
                {{ $categories->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    @endif
</div>

<script>
// Função para alternar entre visualização e edição
function toggleEditForm(categoryId) {
    const displayElement = document.getElementById(`name-display-${categoryId}`);
    const formElement = document.getElementById(`edit-form-${categoryId}`);
    
    if (displayElement.style.display === 'none') {
        displayElement.style.display = 'block';
        formElement.style.display = 'none';
    } else {
        displayElement.style.display = 'none';
        formElement.style.display = 'flex';
        // Focar no input quando abrir o formulário
        const input = document.getElementById(`edit-input-${categoryId}`);
        input.focus();
        input.select();
    }
}

// Contador de caracteres para o input de criação
document.getElementById('name').addEventListener('input', function(e) {
    const countElement = document.getElementById('name-count');
    const currentLength = e.target.value.length;
    countElement.textContent = `${currentLength}/60 caracteres`;
    
    // Adicionar classes de warning/error baseado no comprimento
    countElement.className = 'character-count';
    if (currentLength > 50) {
        countElement.classList.add('warning');
    }
    if (currentLength > 58) {
        countElement.classList.add('error');
    }
});

// Contador de caracteres para os inputs de edição
function updateEditCharacterCount(categoryId) {
    const input = document.getElementById(`edit-input-${categoryId}`);
    const countElement = document.getElementById(`edit-count-${categoryId}`);
    const currentLength = input.value.length;
    countElement.textContent = `${currentLength}/60 caracteres`;
    
    // Adicionar classes de warning/error baseado no comprimento
    countElement.className = 'character-count';
    if (currentLength > 50) {
        countElement.classList.add('warning');
    }
    if (currentLength > 58) {
        countElement.classList.add('error');
    }
}

// Inicializar contadores na carga da página
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar contador do input de criação
    const nameInput = document.getElementById('name');
    const nameCount = document.getElementById('name-count');
    if (nameInput && nameCount) {
        nameCount.textContent = `${nameInput.value.length}/60 caracteres`;
    }
    
    // Inicializar contadores dos inputs de edição
    @foreach ($categories as $category)
        const editInput{{ $category->id }} = document.getElementById('edit-input-{{ $category->id }}');
        const editCount{{ $category->id }} = document.getElementById('edit-count-{{ $category->id }}');
        if (editInput{{ $category->id }} && editCount{{ $category->id }}) {
            editCount{{ $category->id }}.textContent = `${editInput{{ $category->id }}.value.length}/60 caracteres`;
        }
    @endforeach
});

// Validação de comprimento máximo
document.addEventListener('DOMContentLoaded', function() {
    // Para o input de criação
    const nameInput = document.getElementById('name');
    if (nameInput) {
        nameInput.addEventListener('input', function(e) {
            if (e.target.value.length > 60) {
                e.target.value = e.target.value.slice(0, 60);
            }
        });
    }
    
    // Para os inputs de edição
    @foreach ($categories as $category)
        const editInput{{ $category->id }} = document.getElementById('edit-input-{{ $category->id }}');
        if (editInput{{ $category->id }}) {
            editInput{{ $category->id }}.addEventListener('input', function(e) {
                if (e.target.value.length > 60) {
                    e.target.value = e.target.value.slice(0, 60);
                    updateEditCharacterCount({{ $category->id }});
                }
            });
        }
    @endforeach
});
</script>
@endsection