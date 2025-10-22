@extends('layouts.main')

@section('title', 'Trocas e Devoluções')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/footer_pages.css') }}">
@endpush

@section('content')
<div class="footer-page-container">
    <h1 class="footer-page-title">Política de Trocas e Devoluções</h1>
    
    <div class="footer-page-content">
        <div class="footer-page-section">
            <h2 class="footer-page-section-title">Prazo para Trocas e Devoluções</h2>
            <p class="footer-page-text">
                Você tem <span class="footer-page-highlight">30 dias corridos</span> a partir da data de recebimento 
                do produto para solicitar troca ou devolução.
            </p>
        </div>

        <div class="footer-page-section">
            <h2 class="footer-page-section-title">Condições para Trocas</h2>
            <ul class="footer-page-list">
                <li class="footer-page-list-item">Produto deve estar na embalagem original</li>
                <li class="footer-page-list-item">Todos os acessórios devem ser incluídos</li>
                <li class="footer-page-list-item">Etiquetas e manuais preservados</li>
                <li class="footer-page-list-item">Produto sem sinais de uso</li>
            </ul>
        </div>

        <div class="footer-page-section">
            <h2 class="footer-page-section-title">Processo de Troca/Devolução</h2>
            <ol class="footer-page-list">
                <li class="footer-page-list-item">Envie um e-mail para nosso suporte: contato@sitezudo.com</li>
            </ol>
        </div>

        <div class="footer-page-section">
            <h2 class="footer-page-section-title">Reembolsos</h2>
            <p class="footer-page-text">
                O reembolso será processado em até 10 dias úteis após o recebimento 
                e análise do produto em nosso centro de distribuição.
            </p>
        </div>
    </div>
</div>
@endsection