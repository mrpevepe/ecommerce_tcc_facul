@extends('layouts.main')

@section('title', 'Nossa Missão')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/footer_pages.css') }}">
@endpush

@section('content')
<div class="footer-page-container">
    <h1 class="footer-page-title">Nossa Missão, Visão e Valores</h1>
    
    <div class="footer-page-content">
        <div class="footer-page-card">
            <h3 class="footer-page-card-title">Missão</h3>
            <p class="footer-page-text">
                <span class="footer-page-highlight">Democratizar o acesso à tecnologia de qualidade</span>, 
                oferecendo produtos inovadores com excelência no atendimento e 
                construindo relações duradouras com nossos clientes.
            </p>
        </div>

        <div class="footer-page-card">
            <h3 class="footer-page-card-title">Visão</h3>
            <p class="footer-page-text">
                Ser reconhecida como a <span class="footer-page-highlight">mais confiável plataforma 
                de e-commerce tecnológico</span> do Brasil até 2030, sendo referência 
                em inovação, sustentabilidade e satisfação do cliente.
            </p>
        </div>

        <div class="footer-page-card">
            <h3 class="footer-page-card-title">Valores</h3>
            <ul class="footer-page-list">
                <li class="footer-page-list-item">
                    <span class="footer-page-highlight">Excelência:</span> Buscamos a perfeição em tudo que fazemos
                </li>
                <li class="footer-page-list-item">
                    <span class="footer-page-highlight">Inovação:</span> Estamos sempre um passo à frente
                </li>
                <li class="footer-page-list-item">
                    <span class="footer-page-highlight">Integridade:</span> Agimos com ética e transparência
                </li>
                <li class="footer-page-list-item">
                    <span class="footer-page-highlight">Paixão:</span> Amamos o que fazemos e isso nos move
                </li>
                <li class="footer-page-list-item">
                    <span class="footer-page-highlight">Colaboração:</span> Juntos somos mais fortes
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection