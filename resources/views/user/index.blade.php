@extends('layouts.main')
@section('title', 'Minha Conta')
@section('content')
<div class="container mt-5">
    <h1>Minha Conta</h1>

    <!-- Seção de Endereço -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Endereço de Entrega</h4>
        </div>
        <div class="card-body">
            @if ($user->endereco)
                <p><strong>Logradouro:</strong> {{ $user->endereco->logradouro }}, {{ $user->endereco->numero }}</p>
                <p><strong>Bairro:</strong> {{ $user->endereco->bairro }}</p>
                <p><strong>Cidade:</strong> {{ $user->endereco->nome_cidade }} - {{ $user->endereco->estado }}</p>
                <p><strong>CEP:</strong> {{ $user->endereco->cep }}</p>
                @if ($user->endereco->complemento)
                    <p><strong>Complemento:</strong> {{ $user->endereco->complemento }}</p>
                @endif
                <a href="{{ route('user.address.form') }}" class="btn btn-primary">Editar Endereço</a>
            @else
                <p>Você não possui um endereço cadastrado.</p>
                <a href="{{ route('user.address.form') }}" class="btn btn-primary">Adicionar Endereço</a>
            @endif
        </div>
    </div>

    <!-- Seção de Pedidos -->
    <div class="card">
        <div class="card-header">
            <h4>Meus Pedidos</h4>
        </div>
        <div class="card-body">
            @if ($orders->isEmpty())
                <p>Você não possui pedidos cadastrados.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>R$ {{ number_format($order->total_price, 2, ',', '.') }}</td>
                                <td>{{ ucfirst($order->status) }}</td>
                                <td>
                                    <a href="{{ route('user.orders.show', $order->id) }}" class="btn btn-info btn-sm">Ver Detalhes</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection