@extends('layouts.main')

@section('title', 'Ajuda e FAQ')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/footer_pages.css') }}">
@endpush

@section('content')
<div class="footer-page-container">
    <h1 class="footer-page-title">Ajuda e Perguntas Frequentes</h1>
    
    <div class="footer-page-content">
        <div class="footer-page-card">
            <h3 class="footer-page-card-title">Como faço para me cadastrar?</h3>
            <p class="footer-page-text">
                Clique em "Registrar" no menu superior, preencha seus dados pessoais 
                e siga as instruções para confirmar seu e-mail.
            </p>
        </div>

        <div class="footer-page-card">
            <h3 class="footer-page-card-title">Quais formas de pagamento são aceitas?</h3>
            <p class="footer-page-text">
                Aceitamos PIX, cartão de crédito e boleto bancário. Todas as transações 
                são processadas com segurança.
            </p>
        </div>

        <div class="footer-page-card">
            <h3 class="footer-page-card-title">Qual o prazo de entrega?</h3>
            <p class="footer-page-text">
                O prazo varia de 3 a 10 dias úteis, dependendo da sua localização. 
                Você receberá um código de rastreamento por e-mail.
            </p>
        </div>

        <div class="footer-page-card">
            <h3 class="footer-page-card-title">Como acompanho meu pedido?</h3>
            <p class="footer-page-text">
                Acesse "Minha Conta" → "Meus Pedidos" e clique no pedido desejado 
                para ver detalhes e código de rastreamento.
            </p>
        </div>

        <div class="footer-page-contact-item">
            <div class="footer-page-contact-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="footer-page-contact-text">
                <div class="footer-page-contact-title">Ainda precisa de ajuda?</div>
                <div class="footer-page-contact-description">
                    Entre em contato conosco: contato@sitezudo.com
                </div>
            </div>
        </div>
    </div>
</div>
@endsection