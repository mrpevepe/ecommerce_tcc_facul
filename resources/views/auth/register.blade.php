@extends('layouts.main')

@section('title', 'Registrar')

@section('content')

<div class="container mt-5">
    <h1>Registrar Novo Usuário</h1>
    <form method="POST" action="{{ route('register') }}" onsubmit="return validarFormulario()">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input type="text" class="form-control" id="name" name="name" maxlength="60" required>
            <div class="invalid-feedback" id="name-error"></div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" maxlength="80" required>
            <div class="invalid-feedback" id="email-error"></div>
        </div>

        <div class="mb-3">
            <label for="telefone" class="form-label">Telefone</label>
            <input type="text" class="form-control" id="telefone" name="telefone" maxlength="20" required>
            <div class="invalid-feedback" id="telefone-error"></div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input type="password" class="form-control" id="password" name="password" maxlength="60" required>
            <div class="invalid-feedback" id="password-error"></div>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmar Senha</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" maxlength="60" required>
        </div>

        <button type="submit" class="btn btn-primary">Registrar</button>
    </form>
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

<style>
.invalid-feedback {
    display: block;
}
</style>

@endsection