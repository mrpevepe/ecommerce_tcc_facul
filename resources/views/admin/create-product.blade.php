@extends('layouts.main')

@section('title', 'Criar Produto')

@section('content')
<div class="create-product-container">
    <h1 class="create-product-title">Criar Novo Produto</h1>
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="create-product-form">
        @csrf

        <div class="form-group">
            <label for="nome" class="form-label">Nome do Produto</label>
            <input type="text" class="form-control" id="nome" name="nome" 
                   maxlength="60" 
                   oninput="this.value = this.value.slice(0, 60)" required>
            <small class="text-muted"><span id="nome-count">0</span>/60 caracteres</small>
            @error('nome')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control descricao-textarea" id="descricao" name="descricao" 
                      maxlength="255"
                      oninput="this.value = this.value.slice(0, 255)"></textarea>
            <small class="text-muted"><span id="descricao-count">0</span>/255 caracteres</small>
            @error('descricao')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="marca" class="form-label">Marca</label>
            <input type="text" class="form-control" id="marca" name="marca" 
                   maxlength="64"
                   oninput="this.value = this.value.slice(0, 64)">
            <small class="text-muted"><span id="marca-count">0</span>/64 caracteres</small>
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
    console.log('create-product.blade.php script loaded');

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
                        <input type="number" class="stock-input" 
                               name="variations[${variacaoIndex}][quantidade_estoque][${variacaoIndex}_${size.id}][quantity]" 
                               min="0" max="99999999"
                               oninput="this.value = this.value.slice(0, 10)" required>
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
                    <input type="text" class="form-control" 
                           name="variations[${variacaoIndex}][nome_variacao]" 
                           maxlength="60"
                           oninput="this.value = this.value.slice(0, 60)" required>
                    <small class="text-muted"><span id="nome_variacao-count-${variacaoIndex}">0</span>/60 caracteres</small>
                </div>
                <div class="form-group">
                    <label for="variations[${variacaoIndex}][preco]" class="form-label">Preço</label>
                    <input type="number" step="0.01" class="form-control" 
                           name="variations[${variacaoIndex}][preco]" 
                           maxlength="8"
                           oninput="this.value = this.value.slice(0, 10)" required>
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
        
        // Configurar contador de caracteres para o novo campo de nome_variacao
        const nomeVariacaoInput = document.querySelector(`input[name="variations[${variacaoIndex}][nome_variacao]"]`);
        const nomeVariacaoCount = document.getElementById(`nome_variacao-count-${variacaoIndex}`);
        if (nomeVariacaoInput && nomeVariacaoCount) {
            nomeVariacaoInput.addEventListener('input', function() {
                nomeVariacaoCount.textContent = this.value.length;
            });
        }
        
        variacaoIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variacao')) {
            e.target.closest('.variation-card').remove();
        }
    });

    // Validação em tempo real para todos os campos
    document.addEventListener('input', function(e) {
        // Validação para campos de preço
        if (e.target.name.includes('[preco]')) {
            const maxLength = 8;
            if (e.target.value.length > maxLength) {
                e.target.value = e.target.value.slice(0, maxLength);
            }
        }
        
        // Validação para campos de estoque
        if (e.target.name.includes('[quantidade_estoque]') && e.target.name.includes('[quantity]')) {
            const maxLength = 8;
            if (e.target.value.length > maxLength) {
                e.target.value = e.target.value.slice(0, maxLength);
            }
        }
        
        // Validação para nome do produto
        if (e.target.name === 'nome') {
            const maxLength = 60;
            if (e.target.value.length > maxLength) {
                e.target.value = e.target.value.slice(0, maxLength);
            }
        }
        
        // Validação para descrição
        if (e.target.name === 'descricao') {
            const maxLength = 255;
            if (e.target.value.length > maxLength) {
                e.target.value = e.target.value.slice(0, maxLength);
            }
        }
        
        // Validação para marca
        if (e.target.name === 'marca') {
            const maxLength = 64;
            if (e.target.value.length > maxLength) {
                e.target.value = e.target.value.slice(0, maxLength);
            }
        }
        
        // Validação para nome da variação
        if (e.target.name.includes('[nome_variacao]')) {
            const maxLength = 60;
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
            
            const fileName = event.target.files[0].name;
            const label = event.target.nextElementSibling;
            if (label && label.classList.contains('file-input-label')) {
                label.innerHTML = `<i class="fas fa-file-image"></i> ${fileName}`;
                label.classList.add('has-file');
            }
        }
    }

    // Configurar contadores de caracteres iniciais
    document.addEventListener('DOMContentLoaded', function() {
        // Campos principais
        const nomeInput = document.getElementById('nome');
        const descricaoInput = document.getElementById('descricao');
        const marcaInput = document.getElementById('marca');
        const nomeCount = document.getElementById('nome-count');
        const descricaoCount = document.getElementById('descricao-count');
        const marcaCount = document.getElementById('marca-count');

        if (nomeInput && nomeCount) {
            nomeInput.addEventListener('input', function() {
                nomeCount.textContent = this.value.length;
            });
        }

        if (descricaoInput && descricaoCount) {
            descricaoInput.addEventListener('input', function() {
                descricaoCount.textContent = this.value.length;
            });
        }

        if (marcaInput && marcaCount) {
            marcaInput.addEventListener('input', function() {
                marcaCount.textContent = this.value.length;
            });
        }

        // File inputs
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

    // Validação da imagem principal do produto <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    document.querySelector('.create-product-form').addEventListener('submit', function(e) {
        const imagemInput = document.getElementById('imagem');
        const previewMain = document.getElementById('preview-main');
        let isValid = true;
        
        // Remove mensagens de erro anteriores
        const existingError = document.querySelector('.image-required-error');
        if (existingError) {
            existingError.remove();
        }
        
        // Verifica se não há arquivo selecionado E não há preview visível
        if (!imagemInput.files.length && (!previewMain || previewMain.style.display === 'none' || !previewMain.src || previewMain.src === '#')) {
            isValid = false;
            
            // Cria e exibe a mensagem de erro
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message image-required-error';
            errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Selecione uma imagem principal para o produto.';
            errorDiv.style.color = '#dc3545';
            errorDiv.style.marginTop = '8px';
            errorDiv.style.padding = '8px 12px';
            errorDiv.style.borderRadius = '4px';
            errorDiv.style.backgroundColor = '#f8d7da';
            errorDiv.style.border = '1px solid #f5c6cb';
            
            // Insere a mensagem após o file input wrapper
            const fileInputWrapper = document.querySelector('.file-input-wrapper');
            fileInputWrapper.parentNode.insertBefore(errorDiv, fileInputWrapper.nextSibling);
            
            // Destaca visualmente o campo
            imagemInput.style.borderColor = '#dc3545';
            const label = document.getElementById('imagem-label');
            if (label) {
                label.style.borderColor = '#dc3545';
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            // Rola a página até o campo com erro
            imagemInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }
    });

    // Remove a mensagem de erro quando uma imagem é selecionada
    document.getElementById('imagem').addEventListener('change', function() {
        const existingError = document.querySelector('.image-required-error');
        if (existingError) {
            existingError.remove();
        }
        this.style.borderColor = '';
        const label = document.getElementById('imagem-label');
        if (label) {
            label.style.borderColor = '';
        }
    });
</script>

<style>
    .create-product-container {
        margin: 1rem auto;
        max-width: 900px;
    }

    .create-product-form {
        padding: 1.5rem;
    }

    .descricao-textarea {
        min-height: 80px;
        max-height: 150px;
        resize: vertical;
    }

    .image-preview-container {
        text-align: center;
        margin-top: 0.5rem;
    }

    .image-preview {
        max-width: 150px;
        max-height: 150px;
        border-radius: 8px;
        border: 2px solid rgba(0, 212, 170, 0.3);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    .variation-card {
        padding: 1.2rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .variations-section {
        margin: 1.5rem 0;
    }

    .stock-table table {
        font-size: 0.9rem;
    }

    .stock-table th,
    .stock-table td {
        padding: 0.5rem;
    }

    .btn {
        padding: 0.6rem 1rem;
    }

    .form-actions {
        margin-top: 1.5rem;
        padding-top: 1.2rem;
    }
</style>
@endsection