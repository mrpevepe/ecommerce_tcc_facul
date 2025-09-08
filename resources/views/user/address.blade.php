@extends('layouts.main')
@section('title', 'Adicionar Endereço')
@section('content')
<div class="container mt-5">
    <h1>{{ $endereco ? 'Editar' : 'Adicionar' }} Endereço</h1>

    <!-- Exibe mensagens de erro geral -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Exibe mensagem de sucesso/erro do controller -->
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('user.address.save') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="logradouro" class="form-label">Logradouro</label>
            <input type="text" class="form-control @error('logradouro') is-invalid @enderror" id="logradouro" name="logradouro" value="{{ old('logradouro', $endereco->logradouro ?? '') }}" required>
            @error('logradouro')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="numero" class="form-label">Número</label>
            <input type="text" class="form-control @error('numero') is-invalid @enderror" id="numero" name="numero" value="{{ old('numero', $endereco->numero ?? '') }}" required>
            @error('numero')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="complemento" class="form-label">Complemento</label>
            <input type="text" class="form-control @error('complemento') is-invalid @enderror" id="complemento" name="complemento" value="{{ old('complemento', $endereco->complemento ?? '') }}">
            @error('complemento')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="bairro" class="form-label">Bairro</label>
            <input type="text" class="form-control @error('bairro') is-invalid @enderror" id="bairro" name="bairro" value="{{ old('bairro', $endereco->bairro ?? '') }}" required>
            @error('bairro')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="cep" class="form-label">CEP</label>
            <input type="text" class="form-control @error('cep') is-invalid @enderror" id="cep" name="cep" value="{{ old('cep', $endereco->cep ?? '') }}" required placeholder="12345-678">
            @error('cep')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="nome_cidade" class="form-label">Cidade</label>
            <input type="text" class="form-control @error('nome_cidade') is-invalid @enderror" id="nome_cidade" name="nome_cidade" value="{{ old('nome_cidade', $endereco->nome_cidade ?? '') }}" required>
            @error('nome_cidade')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="estado" class="form-label">Estado (Sigla)</label>
            <input type="text" class="form-control @error('estado') is-invalid @enderror" id="estado" name="estado" value="{{ old('estado', $endereco->estado ?? '') }}" required maxlength="2" placeholder="SP">
            @error('estado')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Salvar Endereço</button>
        <a href="{{ route('user.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection