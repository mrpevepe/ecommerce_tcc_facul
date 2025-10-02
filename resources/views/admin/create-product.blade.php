@extends('layouts.main')

@section('title', 'Criar Produto')

@section('content')
<div class="create-product-container">
    <h1 class="create-product-title">Criar Novo Produto</h1>
    
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="create-product-form">
        @csrf

        <div class="form-group">
            <label for="nome" class="form-label">Nome do Produto</label>
            <input type="text" class="form-control" id="nome" name="nome" maxlength="60" required>
            @error('nome')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control descricao-textarea" id="descricao" name="descricao" maxlength="255"></textarea>
            @error('descricao')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="marca" class="form-label">Marca</label>
            <input type="text" class="form-control" id="marca" name="marca" maxlength="64">
            @error('marca')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="category_id" class="form-label">Categoria</label>
            <select class="form-control" id="category_id" name="category_id">
                <option value="">Nenhuma</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="imagem" class="form-label">Imagem Principal do Produto</label>
            <div class="file-input-wrapper">
                <input type="file" class="form-control" id="imagem" name="imagem" onchange="previewImage(event, 'preview-main')">
                <label for="imagem" class="file-input-label" id="imagem-label">
                    <i class="fas fa-cloud-upload-alt"></i> Clique para selecionar uma imagem
                </label>
            </div>
            @error('imagem')
                <div class="error-message">{{ $message }}</div>
            @enderror
            <div class="image-preview-container">
                <img id="preview-main" src="#" alt="Preview da Imagem" class="image-preview" style="display: none;">
            </div>
        </div>

        <div class="variations-section">
            <h2 class="variations-title">Variações</h2>
            <div id="variacoes-container">
                <!-- Variações adicionadas dinamicamente -->
            </div>

            <button type="button" class="add-variation-btn" id="add-variacao">
                <i class="fas fa-plus"></i> Adicionar Variação
            </button>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar Produto
            </button>
        </div>
    </form>
</div>

<script>
    // Debugging: Log to ensure script is running
    console.log('create-product.blade.php script loaded');

    // Pass sizes from Blade to JavaScript
    const sizes = @json($sizes);
    let variacaoIndex = 0;

    document.getElementById('add-variacao').addEventListener('click', function() {
        const container = document.getElementById('variacoes-container');
        let sizeRows = '';
        sizes.forEach(size => {
            sizeRows += `
                <tr>
                    <td>${size.name}</td>
                    <td>
                        <input type="number" class="stock-input" name="variations[${variacaoIndex}][quantidade_estoque][${variacaoIndex}_${size.id}][quantity]" min="0" required>
                        <input type="hidden" name="variations[${variacaoIndex}][quantidade_estoque][${variacaoIndex}_${size.id}][size_id]" value="${size.id}">
                    </td>
                </tr>
            `;
        });

        const variacaoHtml = `
            <div class="variation-card">
                <div class="variation-card-header">
                    <h3 class="variation-card-title">Variação ${variacaoIndex + 1}</h3>
                </div>
                <div class="form-group">
                    <label for="variations[${variacaoIndex}][nome_variacao]" class="form-label">Nome da Variação</label>
                    <input type="text" class="form-control" name="variations[${variacaoIndex}][nome_variacao]" required maxlength="60">
                </div>
                <div class="form-group">
                    <label for="variations[${variacaoIndex}][preco]" class="form-label">Preço</label>
                    <input type="number" step="0.01" class="form-control" name="variations[${variacaoIndex}][preco]" required maxlength="10">
                </div>
                <div class="form-group">
                    <label class="form-label">Estoque por Tamanho</label>
                    <div class="stock-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tamanho</th>
                                    <th>Quantidade</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${sizeRows}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group">
                    <label for="variations[${variacaoIndex}][imagem]" class="form-label">Imagem da Variação</label>
                    <div class="file-input-wrapper">
                        <input type="file" class="form-control" name="variations[${variacaoIndex}][imagem]" onchange="previewImage(event, 'preview-variacao-${variacaoIndex}')">
                        <label for="variations[${variacaoIndex}][imagem]" class="file-input-label" id="label-variacao-${variacaoIndex}">
                            <i class="fas fa-cloud-upload-alt"></i> Clique para selecionar uma imagem
                        </label>
                    </div>
                    <div class="image-preview-container">
                        <img id="preview-variacao-${variacaoIndex}" src="#" alt="Preview da Imagem" class="image-preview" style="display: none;">
                    </div>
                </div>
                <button type="button" class="btn btn-danger remove-variacao">
                    <i class="fas fa-trash"></i> Remover Variação
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', variacaoHtml);
        variacaoIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variacao')) {
            e.target.closest('.variation-card').remove();
        }
    });

    // Limitar o comprimento do campo de preço a 10 caracteres
    document.addEventListener('input', function(e) {
        if (e.target.name.includes('[preco]')) {
            const maxLength = 10;
            if (e.target.value.length > maxLength) {
                e.target.value = e.target.value.slice(0, maxLength);
            }
        }
        
        // Update file input labels
        if (e.target.type === 'file') {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Nenhum arquivo selecionado';
            const label = e.target.nextElementSibling;
            if (label && label.classList.contains('file-input-label')) {
                label.innerHTML = `<i class="fas fa-file-image"></i> ${fileName}`;
                label.classList.add('has-file');
            }
        }
    });

    function previewImage(event, previewId) {
        console.log('previewImage called for', previewId);
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById(previewId);
            output.src = reader.result;
            output.style.display = 'block';
            output.classList.add('success-preview');
        };
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
            
            // Update the file input label
            const fileName = event.target.files[0].name;
            const label = event.target.nextElementSibling;
            if (label && label.classList.contains('file-input-label')) {
                label.innerHTML = `<i class="fas fa-file-image"></i> ${fileName}`;
                label.classList.add('has-file');
            }
        }
    }

    // Initialize file input labels
    document.addEventListener('DOMContentLoaded', function() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            const label = input.nextElementSibling;
            if (label && label.classList.contains('file-input-label')) {
                if (input.files.length > 0) {
                    label.innerHTML = `<i class="fas fa-file-image"></i> ${input.files[0].name}`;
                    label.classList.add('has-file');
                }
            }
        });
    });
</script>

<style>
    /* Additional inline styles for the adjustments */
    .create-product-container {
        margin: 1rem auto;
        max-width: 900px; /* Formulário mais compacto */
    }

    .create-product-form {
        padding: 1.5rem; /* Reduzir padding do formulário */
    }

    .descricao-textarea {
        min-height: 80px; /* Altura mínima */
        max-height: 150px; /* Altura máxima */
        resize: vertical; /* Permitir redimensionamento apenas vertical */
    }

    .image-preview-container {
        text-align: center;
        margin-top: 0.5rem;
    }

    .image-preview {
        max-width: 150px; /* Preview bem menor */
        max-height: 150px;
        border-radius: 8px;
        border: 2px solid rgba(0, 212, 170, 0.3);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    .variation-card {
        padding: 1.2rem; /* Cartões de variação mais compactos */
    }

    .form-group {
        margin-bottom: 1rem; /* Menor espaçamento entre campos */
    }

    .variations-section {
        margin: 1.5rem 0; /* Menor margem na seção de variações */
    }

    /* Ajustes para a tabela de estoque */
    .stock-table table {
        font-size: 0.9rem;
    }

    .stock-table th,
    .stock-table td {
        padding: 0.5rem;
    }

    /* Botões mais compactos */
    .btn {
        padding: 0.6rem 1rem;
    }

    .form-actions {
        margin-top: 1.5rem;
        padding-top: 1.2rem;
    }
</style>
@endsection