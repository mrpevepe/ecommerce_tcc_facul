@extends('layouts.main')

@section('title', 'Editar Produto')

@section('content')

<div class="edit-product-container">
    <h1 class="edit-product-title">Editar Produto: {{ $product->nome }}</h1>
    <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="edit-product-form">
        @csrf
        @method('PUT')

        <div class="edit-form-group">
            <label for="nome" class="edit-form-label">Nome do Produto</label>
            <input type="text" class="edit-form-control" id="nome" name="nome" value="{{ $product->nome }}" maxlength="60" required>
            @error('nome')
                <div class="edit-error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="edit-form-group">
            <label for="descricao" class="edit-form-label">Descrição</label>
            <textarea class="edit-form-control edit-descricao-textarea" id="descricao" name="descricao" maxlength="255">{{ $product->descricao }}</textarea>
            @error('descricao')
                <div class="edit-error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="edit-form-group">
            <label for="marca" class="edit-form-label">Marca</label>
            <input type="text" class="edit-form-control" id="marca" name="marca" value="{{ $product->marca }}" maxlength="64">
            @error('marca')
                <div class="edit-error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="edit-form-group">
            <label for="category_id" class="edit-form-label">Categoria</label>
            <select class="edit-form-control" id="category_id" name="category_id">
                <option value="">Nenhuma</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <div class="edit-error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="edit-form-group">
            <label for="imagem" class="edit-form-label">Imagem Principal do Produto (deixe em branco para manter atual)</label>
            <div class="edit-file-input-wrapper">
                <input type="file" class="edit-form-control" id="imagem" name="imagem" onchange="previewImage(event, 'preview-main')">
                <label for="imagem" class="edit-file-input-label" id="imagem-label">
                    <i class="fas fa-cloud-upload-alt"></i> Clique para selecionar uma imagem
                </label>
            </div>
            @error('imagem')
                <div class="edit-error-message">{{ $message }}</div>
            @enderror
            <div class="edit-image-preview-container">
                @if ($product->images->where('is_main', true)->first())
                    <img id="preview-main" src="{{ Storage::url($product->images->where('is_main', true)->first()->path) }}" alt="Imagem Atual" class="edit-image-preview">
                @else
                    <img id="preview-main" src="#" alt="Preview da Imagem" class="edit-image-preview" style="display: none;">
                @endif
            </div>
        </div>

        <div class="edit-variations-section">
            <h2 class="edit-variations-title">Variações</h2>
            <div id="variacoes-container">
                @foreach ($product->variations as $index => $variation)
                    <div class="edit-variation-card">
                        <div class="edit-variation-card-header">
                            <h3 class="edit-variation-card-title">Variação {{ $index + 1 }}</h3>
                        </div>
                        <input type="hidden" name="variations[{{ $index }}][id]" value="{{ $variation->id }}">
                        <div class="edit-form-group">
                            <label class="edit-form-label">Nome da Variação</label>
                            <input type="text" class="edit-form-control" name="variations[{{ $index }}][nome_variacao]" value="{{ $variation->nome_variacao }}" required maxlength="60">
                        </div>
                        <div class="edit-form-group">
                            <label class="edit-form-label">Preço</label>
                            <input type="number" step="0.01" class="edit-form-control" name="variations[{{ $index }}][preco]" value="{{ $variation->preco }}" required maxlength="10">
                        </div>
                        <div class="edit-form-group">
                            <label class="edit-form-label">Estoque por Tamanho</label>
                            <div class="edit-stock-table">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Tamanho</th>
                                            <th>Quantidade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sizes as $size)
                                            <tr>
                                                <td>{{ $size->name }}</td>
                                                <td>
                                                    <input type="number" class="edit-stock-input" name="variations[{{ $index }}][quantidade_estoque][{{ $index }}_{{ $size->id }}][quantity]" value="{{ $variation->sizes->where('id', $size->id)->first()->pivot->quantity ?? 0 }}" min="0" required>
                                                    <input type="hidden" name="variations[{{ $index }}][quantidade_estoque][{{ $index }}_{{ $size->id }}][size_id]" value="{{ $size->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="edit-form-group">
                            <label class="edit-form-label">Imagem da Variação (deixe em branco para manter atual)</label>
                            <div class="edit-file-input-wrapper">
                                <input type="file" class="edit-form-control" name="variations[{{ $index }}][imagem]" onchange="previewImage(event, 'preview-variacao-{{ $index }}')">
                                <label for="variations[{{ $index }}][imagem]" class="edit-file-input-label" id="label-variacao-{{ $index }}">
                                    <i class="fas fa-cloud-upload-alt"></i> Clique para selecionar uma imagem
                                </label>
                            </div>
                            <div class="edit-image-preview-container">
                                @if ($variation->images->where('is_main', true)->first())
                                    <img id="preview-variacao-{{ $index }}" src="{{ Storage::url($variation->images->where('is_main', true)->first()->path) }}" alt="Imagem Atual" class="edit-image-preview">
                                @else
                                    <img id="preview-variacao-{{ $index }}" src="#" alt="Preview da Imagem" class="edit-image-preview" style="display: none;">
                                @endif
                            </div>
                        </div>
                        <button type="button" class="edit-btn edit-btn-danger remove-variacao">
                            <i class="fas fa-trash"></i> Remover Variação
                        </button>
                    </div>
                @endforeach
            </div>

            <button type="button" class="edit-add-variation-btn" id="add-variacao">
                <i class="fas fa-plus"></i> Adicionar Variação
            </button>
        </div>

        <div class="edit-form-actions">
            <a href="{{ route('admin.products.index') }}" class="edit-btn edit-btn-secondary">
                <i class="fas fa-arrow-left"></i> Cancelar
            </a>
            <button type="submit" class="edit-btn edit-btn-primary">
                <i class="fas fa-save"></i> Salvar Produto
            </button>
        </div>
    </form>
</div>

<script>
    // O script JavaScript pode permanecer igual, pois não depende das classes CSS para funcionalidade.
    let variacaoIndex = {{ count($product->variations) }};
    const sizes = @json($sizes);

    document.getElementById('add-variacao').addEventListener('click', function() {
        const container = document.getElementById('variacoes-container');
        let sizeRows = '';
        sizes.forEach(size => {
            sizeRows += `
                <tr>
                    <td>${size.name}</td>
                    <td>
                        <input type="number" class="edit-stock-input" name="variations[${variacaoIndex}][quantidade_estoque][${variacaoIndex}_${size.id}][quantity]" min="0" required>
                        <input type="hidden" name="variations[${variacaoIndex}][quantidade_estoque][${variacaoIndex}_${size.id}][size_id]" value="${size.id}">
                    </td>
                </tr>
            `;
        });

        const variacaoHtml = `
            <div class="edit-variation-card">
                <div class="edit-variation-card-header">
                    <h3 class="edit-variation-card-title">Variação ${variacaoIndex + 1}</h3>
                </div>
                <div class="edit-form-group">
                    <label class="edit-form-label">Nome da Variação</label>
                    <input type="text" class="edit-form-control" name="variations[${variacaoIndex}][nome_variacao]" required maxlength="60">
                </div>
                <div class="edit-form-group">
                    <label class="edit-form-label">Preço</label>
                    <input type="number" step="0.01" class="edit-form-control" name="variations[${variacaoIndex}][preco]" required maxlength="10">
                </div>
                <div class="edit-form-group">
                    <label class="edit-form-label">Estoque por Tamanho</label>
                    <div class="edit-stock-table">
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
                <div class="edit-form-group">
                    <label class="edit-form-label">Imagem da Variação</label>
                    <div class="edit-file-input-wrapper">
                        <input type="file" class="edit-form-control" name="variations[${variacaoIndex}][imagem]" onchange="previewImage(event, 'preview-variacao-${variacaoIndex}')">
                        <label for="variations[${variacaoIndex}][imagem]" class="edit-file-input-label" id="label-variacao-${variacaoIndex}">
                            <i class="fas fa-cloud-upload-alt"></i> Clique para selecionar uma imagem
                        </label>
                    </div>
                    <div class="edit-image-preview-container">
                        <img id="preview-variacao-${variacaoIndex}" src="#" alt="Preview da Imagem" class="edit-image-preview" style="display: none;">
                    </div>
                </div>
                <button type="button" class="edit-btn edit-btn-danger remove-variacao">
                    <i class="fas fa-trash"></i> Remover Variação
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', variacaoHtml);
        variacaoIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variacao')) {
            e.target.closest('.edit-variation-card').remove();
        }
    });

    function previewImage(event, previewId) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById(previewId);
            output.src = reader.result;
            output.style.display = 'block';
            output.classList.add('edit-success-preview');
        };
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
            
            // Update the file input label
            const fileName = event.target.files[0].name;
            const label = event.target.nextElementSibling;
            if (label && label.classList.contains('edit-file-input-label')) {
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
            if (label && label.classList.contains('edit-file-input-label')) {
                if (input.files.length > 0) {
                    label.innerHTML = `<i class="fas fa-file-image"></i> ${input.files[0].name}`;
                    label.classList.add('has-file');
                }
            }
        });
    });
</script>

@endsection