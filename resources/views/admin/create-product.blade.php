@extends('layouts.main')

@section('title', 'Criar Produto')

@section('content')
<div class="container mt-5">
    <h1>Criar Novo Produto</h1>
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="nome" class="form-label">Nome do Produto</label>
            <input type="text" class="form-control" id="nome" name="nome" maxlength="60" required>
            @error('nome')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
            @error('descricao')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="marca" class="form-label">Marca</label>
            <input type="text" class="form-control" id="marca" name="marca">
            @error('marca')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label">Categoria</label>
            <select class="form-control" id="category_id" name="category_id">
                <option value="">Nenhuma</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="imagem" class="form-label">Imagem Principal do Produto</label>
            <input type="file" class="form-control" id="imagem" name="imagem" onchange="previewImage(event, 'preview-main')">
            @error('imagem')
                <div class="text-danger">{{ $message }}</div>
            @enderror
            <img id="preview-main" src="#" alt="Preview da Imagem" style="display: none; max-width: 200px; margin-top: 10px;">
        </div>

        <h2>Variações</h2>
        <div id="variacoes-container">
            <!-- Variações adicionadas dinamicamente -->
        </div>

        <button type="button" class="btn btn-secondary mb-3" id="add-variacao">Adicionar Variação</button>

        <button type="submit" class="btn btn-primary">Salvar Produto</button>
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
                        <input type="number" class="form-control" name="variations[${variacaoIndex}][quantidade_estoque][${variacaoIndex}_${size.id}][quantity]" min="0" required>
                        <input type="hidden" name="variations[${variacaoIndex}][quantidade_estoque][${variacaoIndex}_${size.id}][size_id]" value="${size.id}">
                    </td>
                </tr>
            `;
        });

        const variacaoHtml = `
            <div class="card mb-3 variacao">
                <div class="card-body">
                    <h5 class="card-title">Variação ${variacaoIndex + 1}</h5>
                    <div class="mb-3">
                        <label for="variations[${variacaoIndex}][nome_variacao]" class="form-label">Nome da Variação</label>
                        <input type="text" class="form-control" name="variations[${variacaoIndex}][nome_variacao]" required>
                    </div>
                    <div class="mb-3">
                        <label for="variations[${variacaoIndex}][preco]" class="form-label">Preço</label>
                        <input type="number" step="0.01" class="form-control" name="variations[${variacaoIndex}][preco]" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estoque por Tamanho</label>
                        <table class="table table-bordered">
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
                    <div class="mb-3">
                        <label for="variations[${variacaoIndex}][imagem]" class="form-label">Imagem da Variação</label>
                        <input type="file" class="form-control" name="variations[${variacaoIndex}][imagem]" onchange="previewImage(event, 'preview-variacao-${variacaoIndex}')">
                        <img id="preview-variacao-${variacaoIndex}" src="#" alt="Preview da Imagem" style="display: none; max-width: 200px; margin-top: 10px;">
                    </div>
                    <button type="button" class="btn btn-danger remove-variacao">Remover Variação</button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', variacaoHtml);
        variacaoIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variacao')) {
            e.target.closest('.variacao').remove();
        }
    });

    function previewImage(event, previewId) {
        console.log('previewImage called for', previewId);
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById(previewId);
            output.src = reader.result;
            output.style.display = 'block';
        };
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
@endsection