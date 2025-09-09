@extends('layouts.main')
@section('title', 'Gerenciar Pedidos')
@section('content')
<div class="container mt-5">
    <h1>Gerenciar Pedidos</h1>

    <!-- Filtro de Status -->
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.orders.index') }}">
            <div class="input-group">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Todos</option>
                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    <option value="delivered" {{ $status == 'delivered' ? 'selected' : '' }}>Entregue</option>
                </select>
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </form>
    </div>

    @if ($orders->isEmpty())
        <p>Nenhum pedido encontrado.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Itens</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->user->name }}</td>
                        <td>{{ $order->created_at->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i') }}</td>
                        <td>
                            <ul>
                                @foreach ($order->items as $item)
                                    <li>
                                        {{ $item->product->nome }} 
                                        ({{ $item->variation->nome_variacao ?? 'Sem variação' }}) 
                                        - Tamanho: {{ $item->product_size ?? 'N/A' }} 
                                        - Qtd: {{ $item->quantity }}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td>R$ {{ number_format($order->total_price, 2, ',', '.') }}</td>
                        <td>{{ ucfirst($order->status) }}</td>
                        <td>
                            @if ($order->status === 'pending')
                                <form action="{{ route('admin.orders.deliver', $order->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja marcar este pedido como entregue? Estoque será reduzido.');">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Enviar Pedido</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mt-3">Voltar</a>
</div>
@endsection