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

    <form action="{{ route('user.address.save') }}" method="POST" id="addressForm">
        @csrf
        <div class="mb-3">
            <label for="logradouro" class="form-label">Logradouro *</label>
            <input type="text" class="form-control @error('logradouro') is-invalid @enderror" id="logradouro" name="logradouro" value="{{ old('logradouro', $endereco->logradouro ?? '') }}" required maxlength="40">
            <div class="invalid-feedback">
                @error('logradouro') {{ $message }} @enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="numero" class="form-label">Número *</label>
            <input type="text" class="form-control @error('numero') is-invalid @enderror" id="numero" name="numero" value="{{ old('numero', $endereco->numero ?? '') }}" required maxlength="20">
            <div class="invalid-feedback">
                @error('numero') {{ $message }} @enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="complemento" class="form-label">Complemento</label>
            <input type="text" class="form-control @error('complemento') is-invalid @enderror" id="complemento" name="complemento" value="{{ old('complemento', $endereco->complemento ?? '') }}" maxlength="100">
            <div class="invalid-feedback">
                @error('complemento') {{ $message }} @enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="bairro" class="form-label">Bairro *</label>
            <input type="text" class="form-control @error('bairro') is-invalid @enderror" id="bairro" name="bairro" value="{{ old('bairro', $endereco->bairro ?? '') }}" required maxlength="40">
            <div class="invalid-feedback">
                @error('bairro') {{ $message }} @enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="cep" class="form-label">CEP *</label>
            <input type="text" class="form-control @error('cep') is-invalid @enderror" id="cep" name="cep" value="{{ old('cep', $endereco->cep ?? '') }}" required placeholder="12345-678" maxlength="9">
            <div class="invalid-feedback">
                @error('cep') {{ $message }} @enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="nome_cidade" class="form-label">Cidade *</label>
            <input type="text" class="form-control @error('nome_cidade') is-invalid @enderror" id="nome_cidade" name="nome_cidade" value="{{ old('nome_cidade', $endereco->nome_cidade ?? '') }}" required maxlength="30">
            <div class="invalid-feedback">
                @error('nome_cidade') {{ $message }} @enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="estado" class="form-label">Estado (Sigla) *</label>
            <input type="text" class="form-control @error('estado') is-invalid @enderror" id="estado" name="estado" value="{{ old('estado', $endereco->estado ?? '') }}" required maxlength="2" placeholder="SP">
            <div class="invalid-feedback">
                @error('estado') {{ $message }} @enderror
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Salvar Endereço</button>
        <a href="{{ route('user.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
    const cepInput = document.getElementById('cep');
    const form = document.getElementById('addressForm');

    // Function to format CEP
    function formatCEP(value) {
        value = value.replace(/\D/g, '');
        if (value.length > 5) {
            value = value.substring(0, 5) + '-' + value.substring(5, 8);
        }
        return value;
    }

    // Add hyphen as user types
    cepInput.addEventListener('input', function () {
        this.value = formatCEP(this.value);
    });

    // Validate CEP on blur
    cepInput.addEventListener('blur', async function () {
        let cep = this.value.replace(/\D/g, '');
        const feedback = this.nextElementSibling;

        if (cep.length !== 8) {
            this.classList.add('is-invalid');
            feedback.textContent = 'CEP deve ter 8 dígitos.';
            this.setCustomValidity('CEP deve ter 8 dígitos.');
            return;
        }

        try {
            const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            const data = await response.json();

            if (data.erro) {
                this.classList.add('is-invalid');
                feedback.textContent = 'CEP inserido errado!';
                this.setCustomValidity('CEP inserido errado!');
            } else {
                document.getElementById('logradouro').value = data.logradouro || '';
                document.getElementById('bairro').value = data.bairro || '';
                document.getElementById('nome_cidade').value = data.localidade || '';
                document.getElementById('estado').value = data.uf || '';
                document.getElementById('complemento').value = data.complemento || '';

                this.classList.remove('is-invalid');
                feedback.textContent = '';
                this.setCustomValidity('');
            }
        } catch (error) {
            this.classList.add('is-invalid');
            feedback.textContent = 'Erro ao validar CEP.';
            this.setCustomValidity('Erro ao validar CEP.');
        }
    });
</script>
@endsection