@extends('layouts.main')

@section('title', 'Política de Privacidade e Segurança')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/footer_pages.css') }}">
@endpush

@section('content')
<div class="footer-page-container">
    <h1 class="footer-page-title">Política de Privacidade e Segurança</h1>
    
    <div class="footer-page-content">
        <div class="footer-page-section">
            <h2 class="footer-page-section-title">1. Coleta de Informações</h2>
            <p class="footer-page-text">
                Coletamos informações pessoais quando você se registra em nosso site, faz uma compra, 
                preenche formulários ou utiliza nossos serviços. As informações coletadas incluem 
                nome, e-mail, endereço e dados de pagamento.
            </p>
        </div>

        <div class="footer-page-section">
            <h2 class="footer-page-section-title">2. Uso das Informações</h2>
            <p class="footer-page-text">Utilizamos suas informações para:</p>
            <ul class="footer-page-list">
                <li class="footer-page-list-item">Processar pedidos e transações</li>
                <li class="footer-page-list-item">Personalizar sua experiência</li>
                <li class="footer-page-list-item">Enviar comunicações importantes</li>
                <li class="footer-page-list-item">Melhorar nossos serviços</li>
            </ul>
        </div>

        <div class="footer-page-section">
            <h2 class="footer-page-section-title">3. Proteção de Dados</h2>
            <p class="footer-page-text">
                Implementamos medidas de segurança robustas para proteger suas informações, 
                incluindo criptografia SSL, CSRF.
            </p>
        </div>

        <div class="footer-page-section">
            <h2 class="footer-page-section-title">4. Cookies e Tecnologias Similares</h2>
            <p class="footer-page-text">
                Utilizamos cookies para melhorar a experiência do user, analisar tráfego 
                e personalizar conteúdo. Você pode controlar as configurações de cookies 
                através do seu navegador.
            </p>
        </div>
    </div>
</div>
@endsection