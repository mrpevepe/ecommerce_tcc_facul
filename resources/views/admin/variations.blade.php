@extends('layouts.main')

@section('title', 'Variações do Produto')

@section('content')
<div class="container mt-5">
    <h1>Variações do Produto: {{ $product->nome }}</h1>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary mb-3">Voltar</a>

    <button type="button" class="btn btn-success mb-3" id="addVariationBtn">Adicionar Variação</button>

    @if ($product->variations->isEmpty())
        <p>Sem variações cadastradas.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Status</th>
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
                        <td>{{ $product->category->name ?? 'Sem categoria' }}</td>
                        <td>R$ {{ number_format($variation->preco, 2, ',', '.') }}</td>
                        <td>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tamanho</th>
                                        <th>Quantidade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($variation->sizes as $size)
                                        <tr>
                                            <td>{{ $size->name }}</td>
                                            <td>{{ $size->pivot->quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td><strong>Total</strong></td>
                                        <td><strong>{{ $variation->sizes->sum('pivot.quantity') }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </td>
                        <td>{{ ucfirst($variation->status) }}</td>
                        <td>
                            <a href="{{ route('admin.products.editStock', $variation->id) }}" class="btn btn-sm btn-primary">Editar Estoque</a>
                            <form action="{{ route('admin.variations.updateStatus', $variation->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $variation->status === 'ativo' ? 'btn-warning' : 'btn-success' }}">
                                    {{ $variation->status === 'ativo' ? 'Inativar' : 'Ativar' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div id="addVariationForm" style="display: none;">
        <h2>Adicionar Nova Variação</h2>
        <form method="POST" action="{{ route('admin.products.storeVariation', $product->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="nome_variacao" class="form-label">Nome da Variação</label>
                <input type="text" class="form-control" id="nome_variacao" name="nome_variacao" required>
                @error('nome_variacao')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="preco" class="form-label">Preço</label>
                <input type="number" step="0.01" class="form-control" id="preco" name="preco" required>
                @error('preco')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
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
                        @foreach ($sizes as $size)
                            <tr>
                                <td>{{ $size->name }}</td>
                                <td>
                                    <input type="number" class="form-control" name="quantidade_estoque[{{ $size->id }}][quantity]" min="0" required>
                                    <input type="hidden" name="quantidade_estoque[{{ $size->id }}][size_id]" value="{{ $size->id }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @error('quantidade_estoque.*.quantity')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="imagem" class="form-label">Imagem da Variação</label>
                <input type="file" class="form-control" id="imagem" name="imagem" onchange="previewImage(event)">
                <img id="imagePreview" src="#" alt="Preview da Imagem" style="display: none; max-width: 200px; margin-top: 10px;">
                @error('imagem')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
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