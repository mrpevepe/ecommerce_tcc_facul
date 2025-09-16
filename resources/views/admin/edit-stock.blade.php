@extends('layouts.main')

@section('title', 'Editar Estoque da Variação')

@section('content')
<div class="container mt-5">
    <h1>Editar Estoque: {{ $variation->product->nome }} - {{ $variation->nome_variacao }}</h1>
    <a href="{{ route('admin.products.variations', $variation->product_id) }}" class="btn btn-secondary mb-3">Voltar</a>
    <p><strong>Categoria:</strong> {{ $variation->product->category->name ?? 'Sem categoria' }}</p>

    <div class="mb-3">
        @if ($variation->images->where('is_main', true)->first())
            <img src="{{ Storage::url($variation->images->where('is_main', true)->first()->path) }}" alt="Imagem da Variação" style="max-width: 200px; height: auto;">
        @else
            Sem Imagem
        @endif
    </div>

    <form action="{{ route('admin.products.saveStock', $variation->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <h3>Estoque por Tamanho</h3>
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
                                <input type="number" class="form-control" name="quantidade_estoque[{{ $size->id }}][quantity]" value="{{ $variation->sizes->find($size->id)->pivot->quantity ?? 0 }}" min="0" required>
                                <input type="hidden" name="quantidade_estoque[{{ $size->id }}][size_id]" value="{{ $size->id }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <button type="submit" class="btn btn-primary">Salvar Estoque</button>
    </form>
</div>
@endsection