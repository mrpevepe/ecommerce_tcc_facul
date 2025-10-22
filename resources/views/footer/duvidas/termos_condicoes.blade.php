@extends('layouts.main')

@section('title', 'Termos e Condições de Uso')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/footer_pages.css') }}">
@endpush

@section('content')
<div class="footer-page-container">
    <h1 class="footer-page-title">Termos e Condições de Uso</h1>
    
    <div class="footer-page-content">
        <div class="footer-page-section">
            <h2 class="footer-page-section-title">1. Aceitação dos Termos</h2>
            <p class="footer-page-text">
                Ao acessar e usar nosso site, você concorda em cumprir e estar vinculado 
                a estes termos e condições. Se você não concordar com qualquer parte 
                destes termos, não poderá usar nosso site.
            </p>
        </div>

        <div class="footer-page-section">
            <h2 class="footer-page-section-title">2. Uso do Site</h2>
            <p class="footer-page-text">Você concorda em usar o site apenas para fins lícitos e de maneira que não:</p>
            <ul class="footer-page-list">
                <li class="footer-page-list-item">Infrinja direitos de terceiros</li>
                <li class="footer-page-list-item">Seja fraudulento ou ilegal</li>
                <li class="footer-page-list-item">Cause dano ou interrupção ao site</li>
                <li class="footer-page-list-item">Transmita vírus ou malware</li>
            </ul>
        </div>

        <div class="footer-page-section">
            <h2 class="footer-page-section-title">3. Propriedade Intelectual</h2>
            <p class="footer-page-text">
                Todo o conteúdo do site, incluindo textos, gráficos, logos e imagens, 
                é propriedade da Sitezudo e protegido por leis de direitos autorais.
            </p>
        </div>

        <div class="footer-page-section">
            <h2 class="footer-page-section-title">4. Limitação de Responsabilidade</h2>
            <p class="footer-page-text">
                Não seremos responsáveis por quaisquer danos diretos, indiretos ou 
                consequenciais resultantes do uso ou incapacidade de usar nosso site.
            </p>
        </div>
    </div>
</div>
@endsection