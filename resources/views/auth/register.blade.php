@extends('layouts.main')

@section('title', 'Registrar')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-container">
    <h1 class="auth-title">Cadastro</h1>
    
    <div class="auth-card">
        <form method="POST" action="{{ route('register') }}" class="auth-form" onsubmit="return validarFormulario()">
            @csrf

            <div class="form-group">
                <label for="name">Nome</label>
                <input type="text" class="form-control" id="name" name="name" maxlength="60" required>
                <div class="invalid-feedback" id="name-error"></div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" maxlength="80" required>
                <div class="invalid-feedback" id="email-error"></div>
            </div>

            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="text" class="form-control" id="telefone" name="telefone" maxlength="20" required>
                <div class="invalid-feedback" id="telefone-error"></div>
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" class="form-control" id="password" name="password" maxlength="60" required>
                <div class="invalid-feedback" id="password-error"></div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar Senha</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" maxlength="60" required>
            </div>

            <button type="submit" class="btn btn-primary">Registrar</button>
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
function validarFormulario() {
    let isValid = true;
    const telefone = document.getElementById('telefone');
    const telefoneRegex = /^\(\d{2}\) \d \d{4}-\d{4}$/;

    // Resetar erros
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');

    // Validação do telefone
    if (!telefoneRegex.test(telefone.value)) {
        telefone.classList.add('is-invalid');
        document.getElementById('telefone-error').innerText = 'Formato inválido. Use (54) 9 9123-4567';
        isValid = false;
    }

    return isValid;
}
</script>
@endsection