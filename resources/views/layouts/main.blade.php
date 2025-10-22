<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>
        <link rel="icon" type="image/png" href="{{ asset('img/favicon.jpg') }}">
        <!-- Fonte -->
        <link href="https://fonts.googleapis.com/css2?family=Roboto" rel="stylesheet">
        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
        <!-- CSS -->
        <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        @stack('styles')
        <script src="{{ asset('js/script.js') }}"></script>
    </head>
    <body>
        <header>
            <nav class="navbar navbar-expand-sm navbar-light sitezudo-navbar">
                <a href="/" class="navbar-brand sitezudo-navbar-brand">
                    <img src="{{ asset('img/favicon.jpg') }}" alt="logo" style="width: 40px">
                </a>
                <button class="navbar-toggler sitezudo-navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon sitezudo-navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbar">
                    <ul class="navbar-nav">
                        @if (Auth::check())
                            <li class="nav-item">
                                <a href="{{ route('user.index') }}" class="nav-link sitezudo-nav-link">Minha Conta</a>
                            </li>
                            <li class="nav-item">
                                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="nav-link btn sitezudo-nav-button">Logout</button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="nav-link sitezudo-nav-link">Entrar</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('register') }}" class="nav-link sitezudo-nav-link">Registrar</a>
                            </li>
                        @endif
                        @if (Auth::check() && Auth::user()->cargo === 'administrador')
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link sitezudo-nav-link">Painel Admin</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a href="{{ route('cart.index') }}" class="nav-link sitezudo-nav-link">Carrinho</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <main>
            @yield('content')
        </main>
        <!-- FOOTER -->
        <footer class="sitezudo-footer">
            <div class="footer-container">
                <div class="footer-section">
                    <h4>Dúvidas</h4>
                    <ul>
                        <li><a href="{{ route('footer.politica') }}">Política de privacidade e segurança</a></li>
                        <li><a href="{{ route('footer.termos') }}">Termos e condições de uso</a></li>
                        <li><a href="{{ route('footer.faq') }}">Ajuda FAQ</a></li>
                        <li><a href="{{ route('footer.trocas') }}">Trocas e devoluções</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Sobre</h4>
                    <ul>
                        <li><a href="{{ route('footer.sobre') }}">Sobre nós</a></li>
                        <li><a href="{{ route('footer.missao') }}">Nossa missão</a></li>
                        <li><a href="{{ route('footer.trabalhe') }}">Trabalhe conosco</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contato</h4>
                    <ul>
                        <li><a href="mailto:contato@sitezudo.com">contato@sitezudo.com</a></li>
                        <li>
                            <a href="#" aria-label="Facebook">
                                <i class="fab fa-facebook"></i> Facebook
                            </a>
                        </li>
                        <li>
                            <a href="#" aria-label="Instagram">
                                <i class="fab fa-instagram"></i> Instagram
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Formas de pagamento</h4>
                    <ul class="payment-methods">
                        <li>
                            <i class="fas fa-qrcode"></i>
                            <span>Pix</span>
                        </li>
                        <li>
                            <i class="fab fa-cc-visa"></i>
                            <span>Cartão de crédito</span>
                        </li>
                        <li>
                            <i class="fas fa-barcode"></i>
                            <span>Boleto</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>E-Commerce TCC &copy; 2025</p>
            </div>
        </footer>
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
@if($errors->any())
    <div class="flash-messages">
        @foreach($errors->all() as $error)
            <div class="flash-message warning">
                <div class="flash-message-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="flash-message-content">{{ $error }}</div>
                <button class="flash-message-close" onclick="this.closest('.flash-message').remove()">&times;</button>
            </div>
        @endforeach
    </div>
    <script>
        // Auto-remover após 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.querySelectorAll('.flash-message').forEach(msg => {
                    msg.classList.add('removing');
                    setTimeout(() => msg.remove(), 300);
                });
            }, 5000);
        });
    </script>
@endif

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showFlashMessage('{{ session('success') }}', 'success');
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showFlashMessage('{{ session('error') }}', 'error');
        });
    </script>
@endif

<script>
    function showFlashMessage(message, type = 'info') {
        let container = document.querySelector('.flash-messages');
        if (!container) {
            container = document.createElement('div');
            container.className = 'flash-messages';
            document.body.appendChild(container);
        }

        const icons = {
            success: '<i class="fas fa-check-circle"></i>',
            error: '<i class="fas fa-times-circle"></i>',
            warning: '<i class="fas fa-exclamation-triangle"></i>',
            info: '<i class="fas fa-info-circle"></i>'
        };

        const flashDiv = document.createElement('div');
        flashDiv.className = `flash-message ${type}`;
        flashDiv.innerHTML = `
            <div class="flash-message-icon">${icons[type]}</div>
            <div class="flash-message-content">${message}</div>
            <button class="flash-message-close" onclick="this.closest('.flash-message').remove()">&times;</button>
        `;

        container.appendChild(flashDiv);
        setTimeout(() => {
            flashDiv.classList.add('removing');
            setTimeout(() => flashDiv.remove(), 300);
        }, 5000);
    }
</script>
    </body>
</html>