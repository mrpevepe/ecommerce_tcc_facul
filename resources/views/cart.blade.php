@extends('layouts.main')
@section('title', 'Carrinho - Sitezudo')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
@endpush
@section('content')

<div class="container cart-container">
    <a href="{{ route('home') }}" class="cart-back-btn">← Voltar para a loja</a>

    @if ($cart && count($cart) > 0)
        <div class="cart-layout">
            <!-- Lista de Produtos -->
            <div class="cart-items-section">
                @foreach ($cart as $key => $item)
                    <div class="cart-item">
                        <div>
                            @if ($item['image'])
                                <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['name'] }}" class="cart-item-image">
                            @else
                                <div class="cart-item-image" style="background: rgba(0,212,170,0.1); display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 0.7rem;">
                                    Sem imagem
                                </div>
                            @endif
                        </div>
                        
                        <div class="cart-item-info">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <h3 class="cart-item-name">{{ $item['name'] }}</h3>
                                <p class="cart-item-price">R$ {{ number_format($item['price'], 2, ',', '.') }}</p>
                            </div>
                            <p class="cart-item-details">{{ $item['variation_name'] ?? 'Sem variação' }} • Tamanho: {{ $item['size_name'] }}</p>
                            <p class="cart-item-details">{{ $item['category'] ?? 'Sem categoria' }}</p>
                        </div>

                        <form action="{{ route('cart.updateQuantity') }}" method="POST">
                            @csrf
                            <input type="hidden" name="cart_key" value="{{ $key }}">
                            <div class="quantity-control">
                                <button type="button" class="quantity-btn" onclick="this.parentElement.querySelector('input[type=number]').stepDown(); this.form.submit();">−</button>
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock'] }}" class="quantity-input" onchange="this.form.submit()">
                                <button type="button" class="quantity-btn" onclick="this.parentElement.querySelector('input[type=number]').stepUp(); this.form.submit();">+</button>
                            </div>
                        </form>
                        
                        <form action="{{ route('cart.remove', $key) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="remove-btn">Remover</button>
                        </form>
                    </div>
                @endforeach
            </div>

            <!-- Resumo e Checkout -->
            <div class="cart-summary">
                <div class="cart-summary-box">
                    <h2 class="cart-summary-title">Resumo do Pedido</h2>

                    <!-- Endereço -->
                    @auth
                        @if (auth()->user()->endereco)
                            <div class="cart-address">
                                <h4 class="cart-address-title">Endereço de Entrega</h4>
                                <div class="cart-address-info">
                                    <p><strong>{{ auth()->user()->endereco->logradouro }}, {{ auth()->user()->endereco->numero }}</strong></p>
                                    <p>{{ auth()->user()->endereco->bairro }}</p>
                                    <p>{{ auth()->user()->endereco->nome_cidade }} - {{ auth()->user()->endereco->estado }}</p>
                                    <p>CEP: {{ auth()->user()->endereco->cep }}</p>
                                    @if (auth()->user()->endereco->complemento)
                                        <p>{{ auth()->user()->endereco->complemento }}</p>
                                    @endif
                                </div>
                                <a href="{{ route('user.address.form') }}" class="cart-edit-address">Editar endereço</a>
                            </div>
                        @else
                            <div class="cart-alert">
                                <p>Você não possui um endereço cadastrado. <a href="{{ route('user.address.form') }}">Adicionar agora</a></p>
                            </div>
                        @endif
                    @else
                        <div class="cart-alert">
                            <p>Faça login para prosseguir com o checkout.</p>
                        </div>
                    @endauth

                    <!-- Método de Pagamento -->
                    @auth
                        @if (auth()->user()->endereco)
                            <div class="cart-payment">
                                <h4 class="cart-payment-title">Método de Pagamento</h4>
                                <form action="{{ route('orders.store') }}" method="POST" id="checkoutForm">
                                    @csrf
                                    <div class="payment-options">
                                        <div class="payment-option">
                                            <input type="radio" name="payment_method" id="pix" value="pix" >
                                            <label class="payment-label" for="pix">PIX</label>
                                        </div>
                                        <div class="payment-option">
                                            <input type="radio" name="payment_method" id="boleto" value="boleto" >
                                            <label class="payment-label" for="boleto">Boleto</label>
                                        </div>
                                        <div class="payment-option">
                                            <input type="radio" name="payment_method" id="cartao" value="cartao" >
                                            <label class="payment-label" for="cartao">Cartão</label>
                                        </div>
                                    </div>

                                    <!-- Total -->
                                    <div class="cart-total">
                                        <p class="cart-total-label">Total do Pedido</p>
                                        <p class="cart-total-price">R$ {{ number_format(array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart ?? [])), 2, ',', '.') }}</p>
                                    </div>

                                    <button type="submit" class="cart-checkout-btn">Finalizar Compra</button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    @else
        <div class="cart-empty">
            <p>Seu carrinho está vazio</p>
            <a href="{{ route('home') }}" class="cart-back-btn">Ir para a loja</a>
        </div>
    @endif
</div>

<script>
    // Validação de quantidade
    document.querySelectorAll('input[type=number][name=quantity]').forEach(input => {
        input.addEventListener('input', function() {
            const max = parseInt(this.max) || 1;
            if (this.value > max) this.value = max;
            if (this.value < 1) this.value = 1;
        });
    });
</script>
@endsection