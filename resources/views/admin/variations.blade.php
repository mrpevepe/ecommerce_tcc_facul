<!-- resources/views/admin/variations.blade.php -->
@extends('layouts.main')

@section('title', 'Variações do Produto')

@section('content')
<div class="container mt-5">
    <h1>Variações do Produto: {{ $product->nome }}</h1>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary mb-3">Voltar</a>

    <!-- Botão para adicionar variação -->
    <button type="button" class="btn btn-success mb-3" id="addVariationBtn">Adicionar Variação</button>

    @if ($product->variations->isEmpty())
        <p>Sem variações cadastradas.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($product->variations as $variation)
                    <tr>
                        <td>
                            @if ($variation->images->where('is_main', true)->first())
                                <img src="{{ Storage::url($variation->images->where('is_main', true)->first()->path) }}" alt="Imagem da Variação" style="max-width: 100px; height: auto;">
                            @else
                                Sem Imagem
                            @endif
                        </td>
                        <td>{{ $variation->nome_variacao }}</td>
                        <td>{{ $product->descricao ?? 'Sem descrição' }}</td>
                        <td>R$ {{ number_format($variation->preco, 2, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('admin.variations.updateStock', $variation->id) }}" method="POST" class="d-flex align-items-center">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantidade_estoque" value="{{ $variation->quantidade_estoque }}" class="form-control d-inline-block" style="width: 100px;" min="0">
                                <button type="submit" class="btn btn-sm btn-primary ms-2">Atualizar Estoque</button>
                            </form>
                        </td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Formulário dinâmico para adicionar variação -->
    <div id="addVariationForm" style="display: none;" class="mt-4">
        <h3>Adicionar Nova Variação</h3>
        <form action="{{ route('admin.products.storeVariation', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="nome_variacao" class="form-label">Nome da Variação</label>
                <input type="text" class="form-control" id="nome_variacao" name="nome_variacao" required>
            </div>
            <div class="mb-3">
                <label for="preco" class="form-label">Preço</label>
                <input type="number" step="0.01" class="form-control" id="preco" name="preco" required>
            </div>
            <div class="mb-3">
                <label for="quantidade_estoque" class="form-label">Quantidade em Estoque</label>
                <input type="number" class="form-control" id="quantidade_estoque" name="quantidade_estoque" required>
            </div>
            <div class="mb-3">
                <label for="imagem" class="form-label">Imagem da Variação</label>
                <input type="file" class="form-control" id="imagem" name="imagem" onchange="previewImage(event)">
                <img id="imagePreview" src="#" alt="Preview da Imagem" style="display: none; max-width: 200px; margin-top: 10px;">
            </div>
            <button type="submit" class="btn btn-primary">Salvar Variação</button>
            <button type="button" class="btn btn-secondary ms-2" id="cancelVariationBtn">Cancelar</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('addVariationBtn').addEventListener('click', function() {
        document.getElementById('addVariationForm').style.display = 'block';
        this.style.display = 'none';
    });

    document.getElementById('cancelVariationBtn').addEventListener('click', function() {
        document.getElementById('addVariationForm').style.display = 'none';
        document.getElementById('addVariationBtn').style.display = 'block';
        // Limpar o preview ao cancelar
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('imagePreview').src = '#';
    });

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('imagePreview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
@endsection