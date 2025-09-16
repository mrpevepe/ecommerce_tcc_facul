@extends('layouts.main')
@section('title', 'Carrinho - Sitezudo')
@section('content')
<style>
    .cart-item {
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
    }
    .cart-item img {
        max-width: 100px;
        height: auto;
    }
    .total-price {
        margin-top: 20px;
        font-weight: bold;
        font-size: 1.2em;
    }
</style>

<div class="container mt-5">
    <h1>Carrinho</h1>
    <a href="{{ route('home') }}" class="btn btn-secondary mb-3">Voltar</a>
    @if ($cart && count($cart) > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Tamanho</th>
                    <th>Preço Unitário</th>
                    <th>Quantidade</th>
                    <th>Subtotal</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cart as $key => $item)
                    <tr class="cart-item">
                        <td>
                            @if ($item['image'])
                                <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['name'] }}">
                            @else
                                <p>Sem Imagem</p>
                            @endif
                        </td>
                        <td>{{ $item['name'] }} - {{ $item['variation_name'] ?? 'Sem variação' }}</td>
                        <td>{{ $item['size_name'] }}</td>
                        <td>R$ {{ number_format($item['price'], 2, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('cart.updateQuantity') }}" method="POST" style="display: inline;">
                                @csrf
                                <input type="hidden" name="cart_key" value="{{ $key }}">
                                <div class="input-group" style="width: 150px;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="this.parentElement.querySelector('input[type=number]').stepDown(); this.form.submit();">-</button>
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock'] }}" class="form-control text-center" style="width: 60px;" onchange="this.form.submit()">
                                    <button type="button" class="btn btn-outline-secondary" onclick="this.parentElement.querySelector('input[type=number]').stepUp(); this.form.submit();">+</button>
                                </div>
                            </form>
                        </td>
                        <td>R$ {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('cart.remove', $key) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Remover</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="total-price">
            Preço Total: R$ <span id="totalPrice">{{ number_format(array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart ?? [])), 2, ',', '.') }}</span>
        </div>

        <!-- Endereço do usuário -->
        @auth
            @if (auth()->user()->endereco)
                <div class="mt-4">
                    <h4>Endereço de Entrega:</h4>
                    <p>{{ auth()->user()->endereco->logradouro }}, {{ auth()->user()->endereco->numero }} - {{ auth()->user()->endereco->bairro }}</p>
                    <p>{{ auth()->user()->endereco->nome_cidade }} - {{ auth()->user()->endereco->estado }}, CEP: {{ auth()->user()->endereco->cep }}</p>
                    @if (auth()->user()->endereco->complemento)
                        <p>Complemento: {{ auth()->user()->endereco->complemento }}</p>
                    @endif
                    <a href="{{ route('user.address.form') }}" class="btn btn-secondary">Editar Endereço</a>
                </div>
            @else
                <div class="mt-4 alert alert-warning">
                    <p>Você não possui um endereço cadastrado. <a href="{{ route('user.address.form') }}">Adicionar novo endereço</a></p>
                </div>
            @endif
        @else
            <div class="mt-4 alert alert-warning">
                <p>Faça login para prosseguir com o checkout.</p>
            </div>
        @endauth

        <!-- Seleção de Método de Pagamento -->
        @auth
            @if (auth()->user()->endereco)
                <div class="mt-4">
                    <h4>Selecione o Método de Pagamento:</h4>
                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="payment_method" id="pix" value="pix" autocomplete="off" required>
                            <label class="btn btn-outline-primary" for="pix">PIX</label>

                            <input type="radio" class="btn-check" name="payment_method" id="boleto" value="boleto" autocomplete="off" required>
                            <label class="btn btn-outline-primary" for="boleto">Boleto</label>

                            <input type="radio" class="btn-check" name="payment_method" id="cartao" value="cartao" autocomplete="off" required>
                            <label class="btn btn-outline-primary" for="cartao">Cartão de Crédito</label>
                        </div>
                        <button type="submit" class="btn btn-success mt-3">Finalizar Compra</button>
                    </form>
                </div>
            @endif
        @endauth
    @else
        <p>O carrinho está vazio.</p>
    @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('input[type=number][name=quantity]').forEach(input => {
        input.addEventListener('input', function() {
            const max = parseInt(this.max) || 1;
            if (this.value > max) this.value = max;
            if (this.value < 1) this.value = 1;
        });
    });
</script>
@endsection