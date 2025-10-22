@extends('layouts.main')

@section('title', 'Sobre Nós')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/footer_pages.css') }}">
@endpush

@section('content')
<div class="footer-page-container">
    <h1 class="footer-page-title">Sobre a Sitezudo</h1>
    
    <div class="footer-page-content">
        <div class="footer-page-section">
            <h2 class="footer-page-section-title">Nossa História</h2>
            <p class="footer-page-text">
                Fundada em 2025, a Sitezudo nasceu da paixão por tecnologia e da vontade 
                de oferecer produtos de qualidade com excelência no atendimento. 
                Começamos como uma pequena startup e hoje somos referência no segmento.
            </p>
        </div>

        <div class="footer-page-section">
            <h2 class="footer-page-section-title">O Que Fazemos</h2>
            <p class="footer-page-text">
                Somos um e-commerce especializado em produtos tecnológicos, oferecendo 
                desde componentes eletrônicos até dispositivos inteligentes. Nossa 
                curadoria garante apenas produtos de alta qualidade.
            </p>
        </div>

        <div class="footer-page-section">
            <h2 class="footer-page-section-title">Nossos Valores</h2>
            <ul class="footer-page-list">
                <li class="footer-page-list-item">
                    <span class="footer-page-highlight">Qualidade:</span> Selecionamos rigorosamente cada produto
                </li>
                <li class="footer-page-list-item">
                    <span class="footer-page-highlight">Transparência:</span> Comunicação clara e honesta
                </li>
                <li class="footer-page-list-item">
                    <span class="footer-page-highlight">Inovação:</span> Sempre buscamos as melhores soluções
                </li>
                <li class="footer-page-list-item">
                    <span class="footer-page-highlight">Satisfação do Cliente:</span> Sua felicidade é nossa prioridade
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection