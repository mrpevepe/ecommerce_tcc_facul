@extends('layouts.main')

@section('title', 'Login')

@section('content')

<div class="container mt-5">
    <h1>Login</h1>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label" for="remember">Lembrar-me</label>
        </div>

        <button type="submit" class="btn btn-primary">Entrar</button>
    </form>
</div>

@endsection