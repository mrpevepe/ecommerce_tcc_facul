@extends('layouts.main')
@section('title', 'Editar Dados')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endpush

@section('content')
<div class="address-container">
    <h1 class="address-title">Editar Dados Pessoais</h1>
    
    <div class="address-card">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('user.update') }}" method="POST" id="editForm" class="address-form">
            @csrf
            <div class="form-group full-width">
                <label for="name">Nome *</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                       value="{{ old('name', $user->name) }}" required maxlength="60">
                <div class="invalid-feedback">
                    @error('name') {{ $message }} @enderror
                </div>
            </div>

            <div class="form-group full-width">
                <label for="telefone">Telefone *</label>
                <input type="text" class="form-control @error('telefone') is-invalid @enderror" id="telefone" name="telefone" 
                       value="{{ old('telefone', $user->telefone) }}" required maxlength="20">
                <div class="invalid-feedback">
                    @error('telefone') {{ $message }} @enderror
                </div>
            </div>

            <div class="form-group full-width">
                <label for="current_password">Senha Atual *</label>
                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                       id="current_password" name="current_password" required>
                <div class="invalid-feedback">
                    @error('current_password') {{ $message }} @enderror
                </div>
            </div>

            <div class="form-group full-width">
                <label for="password">Nova Senha (deixe em branco para manter a atual)</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       id="password" name="password" maxlength="60">
                <div class="invalid-feedback">
                    @error('password') {{ $message }} @enderror
                </div>
            </div>

            <div class="form-group full-width">
                <label for="password_confirmation">Confirmar Nova Senha</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" maxlength="60">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Atualizar Dados</button>
                <a href="{{ route('user.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Formatação do telefone
    document.getElementById('telefone').addEventListener('input', function (e) {
        let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,1})(\d{0,4})(\d{0,4})/);
        e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? ' ' + x[3] : '') + (x[4] ? '-' + x[4] : '');
    });

    // Validação do formulário
    document.getElementById('editForm').addEventListener('submit', function (e) {
        let isValid = true;
        const telefone = document.getElementById('telefone');
        const telefoneRegex = /^\(\d{2}\) \d \d{4}-\d{4}$/;
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');

        // Resetar erros
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');

        // Validação do telefone
        if (!telefoneRegex.test(telefone.value)) {
            telefone.classList.add('is-invalid');
            telefone.nextElementSibling.innerText = 'Formato inválido. Use (54) 9 9123-4567';
            isValid = false;
        }

        // Validação da senha
        if (password.value && password.value.length < 8) {
            password.classList.add('is-invalid');
            password.nextElementSibling.innerText = 'A senha deve ter pelo menos 8 caracteres.';
            isValid = false;
        }

        // Validação de confirmação de senha
        if (password.value && password.value !== passwordConfirmation.value) {
            passwordConfirmation.classList.add('is-invalid');
            // Criar elemento de feedback se não existir
            let feedback = passwordConfirmation.nextElementSibling;
            if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                passwordConfirmation.parentNode.appendChild(feedback);
            }
            feedback.innerText = 'A confirmação da senha não corresponde.';
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
</script>
@endsection