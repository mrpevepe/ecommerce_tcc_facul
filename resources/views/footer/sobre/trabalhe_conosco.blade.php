@extends('layouts.main')

@section('title', 'Trabalhe Conosco')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/footer_pages.css') }}">
@endpush

@section('content')
<div class="footer-page-container">
    <h1 class="footer-page-title">Trabalhe Conosco</h1>
    
    <div class="footer-page-content">
        <div class="footer-page-section">
            <h2 class="footer-page-section-title">Faça Parte do Nosso Time</h2>
            <p class="footer-page-text">
                Na Sitezudo, acreditamos que as pessoas são nosso maior patrimônio. 
                Buscamos profissionais talentosos, criativos e apaixonados por tecnologia 
                para crescer conosco.
            </p>
        </div>

        <div class="footer-page-section">
            <h2 class="footer-page-section-title">Vagas Abertas</h2>
            
            <div class="footer-page-card">
                <h3 class="footer-page-card-title">Desenvolvedor Full Stack</h3>
                <p class="footer-page-text">
                    <strong>Requisitos:</strong> PHP, Laravel, JavaScript, Vue.js, MySQL<br>
                    <strong>Benefícios:</strong> VT, VR, Plano de Saúde, Home Office
                </p>
            </div>

            <div class="footer-page-card">
                <h3 class="footer-page-card-title">Analista de Marketing Digital</h3>
                <p class="footer-page-text">
                    <strong>Requisitos:</strong> Google Ads, Meta Ads, Analytics, SEO<br>
                    <strong>Benefícios:</strong> VT, VR, Plano de Saúde, Bônus por performance
                </p>
            </div>

            <div class="footer-page-card">
                <h3 class="footer-page-card-title">Atendimento ao Cliente</h3>
                <p class="footer-page-text">
                    <strong>Requisitos:</strong> Boa comunicação, Pacote Office, CRM<br>
                    <strong>Benefícios:</strong> VT, VR, Plano de Saúde, Vale Cultura
                </p>
            </div>
        </div>

        <div class="footer-page-section">
            <h2 class="footer-page-section-title">Como se Candidatar</h2>
            <p class="footer-page-text">
                Envie seu currículo para 
                <span class="footer-page-highlight">rh@sitezudo.com</span> com o assunto 
                "Vaga - [Nome da Vaga]". Nossa equipe de RH entrará em contato com 
                os candidatos selecionados.
            </p>
        </div>

        <div class="footer-page-contact-item">
            <div class="footer-page-contact-icon">
                <i class="fas fa-heart"></i>
            </div>
            <div class="footer-page-contact-text">
                <div class="footer-page-contact-title">Venha fazer a diferença!</div>
                <div class="footer-page-contact-description">
                    Junte-se a uma empresa que valoriza sua criatividade e potencial
                </div>
            </div>
        </div>
    </div>
</div>
@endsection