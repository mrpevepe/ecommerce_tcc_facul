@extends('layouts.main')

@section('title', 'Variações do Produto')

@section('content')
<div class="variations-container">
    <div class="variations-header-actions">
        <a href="{{ route('admin.products.index') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Voltar para Produtos
        </a>
        
        <h1 class="variations-title">Variações do Produto: {{ $product->nome }}</h1>
        
        <button type="button" class="add-variation-main-btn" id="addVariationBtn">
            <i class="fas fa-plus"></i> Adicionar Variação
        </button>
    </div>

    @if ($variations->isEmpty())
        <div class="no-variations-message">
            <p>Nenhuma variação cadastrada para este produto.</p>
        </div>
    @else
        <!-- Paginação no topo -->
        @if($variations->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-top">
                <div class="pagination-info">
                    Exibindo {{ $variations->firstItem() }} a {{ $variations->lastItem() }} de {{ $variations->total() }} resultados
                </div>
                <div>
                    {{ $variations->links('pagination::simple-bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif

        <div class="variations-table-container">
            <table class="variations-table">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($variations as $variation)
                        <tr data-variation-id="{{ $variation->id }}" data-save-url="{{ route('admin.products.saveStock', $variation->id) }}">
                            <td>
                                @if ($variation->images->where('is_main', true)->first())
                                    <img src="{{ Storage::url($variation->images->where('is_main', true)->first()->path) }}" 
                                         alt="Imagem da Variação" class="variation-image">
                                @else
                                    <span class="no-variation-image">Sem Imagem</span>
                                @endif
                            </td>
                            <td>{{ $variation->nome_variacao }}</td>
                            <td>{{ $product->category->name ?? 'Sem categoria' }}</td>
                            <td>R$ {{ number_format($variation->preco, 2, ',', '.') }}</td>
                            <td>
                                <div class="stock-table-inner">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Tamanho</th>
                                                <th>Quantidade</th>
                                            </tr>
                                        </thead>
                                        <tbody class="stock-display">
                                            @foreach ($variation->sizes as $size)
                                                <tr>
                                                    <td>{{ $size->name }}</td>
                                                    <td class="stock-quantity" data-size-id="{{ $size->id }}">
                                                        {{ $size->pivot->quantity }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tbody class="stock-edit" style="display: none;">
                                            @foreach ($variation->sizes as $size)
                                                <tr>
                                                    <td>{{ $size->name }}</td>
                                                    <td>
                                                        <input type="number" 
                                                               class="stock-edit-input" 
                                                               data-size-id="{{ $size->id }}"
                                                               value="{{ $size->pivot->quantity }}" 
                                                               min="0" 
                                                               max="99999999"
                                                               maxlength="8"
                                                               oninput="this.value = this.value.slice(0, 8)">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td><strong>Total</strong></td>
                                                <td><strong class="stock-total">{{ $variation->sizes->sum('pivot.quantity') }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </td>
                            <td>
                                <span class="variation-status status-{{ $variation->status }}">
                                    {{ ucfirst($variation->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="variation-actions">
                                    <button type="button" class="variation-action-btn btn-edit-stock" onclick="toggleStockEdit({{ $variation->id }})">
                                        <i class="fas fa-edit"></i> Editar Estoque
                                    </button>
                                    <div class="stock-edit-actions" style="display: none;">
                                        <button type="button" class="variation-action-btn btn-save-stock" onclick="saveStock({{ $variation->id }})">
                                            <i class="fas fa-save"></i> Salvar
                                        </button>
                                        <button type="button" class="variation-action-btn btn-cancel-stock" onclick="cancelStockEdit({{ $variation->id }})">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.variations.updateStatus', $variation->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="variation-action-btn btn-toggle-status {{ $variation->status === 'ativo' ? '' : 'active' }}">
                                            <i class="fas {{ $variation->status === 'ativo' ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                            {{ $variation->status === 'ativo' ? 'Inativar' : 'Ativar' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginação no rodapé -->
        @if($variations->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-bottom">
                <div class="pagination-info">
                    Exibindo {{ $variations->firstItem() }} a {{ $variations->lastItem() }} de {{ $variations->total() }} resultados
                </div>
                <div>
                    {{ $variations->links('pagination::simple-bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    @endif

    <div class="add-variation-form-container" id="addVariationForm" style="display: none;">
        <h2 class="add-variation-form-title">Adicionar Nova Variação</h2>
        <form method="POST" action="{{ route('admin.products.storeVariation', $product->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="variation-form-group">
                <label for="nome_variacao" class="variation-form-label">Nome da Variação</label>
                <input type="text" class="variation-form-control" id="nome_variacao" name="nome_variacao" 
                       value="{{ old('nome_variacao') }}" 
                       maxlength="60"
                       oninput="this.value = this.value.slice(0, 60)" required>
                @error('nome_variacao')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="variation-form-group">
                <label for="preco" class="variation-form-label">Preço</label>
                <input type="number" step="0.01" class="variation-form-control" id="preco" name="preco" 
                       value="{{ old('preco') }}" 
                       maxlength="8"
                       oninput="this.value = this.value.slice(0, 10)" required>
                @error('preco')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="variation-form-group">
                <label class="variation-form-label">Estoque por Tamanho</label>
                <div class="variation-stock-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Tamanho</th>
                                <th>Quantidade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sizes as $size)
                                <tr>
                                    <td>{{ $size->name }}</td>
                                    <td>
                                        <input type="number" class="stock-quantity-input" 
                                               name="quantidade_estoque[{{ $size->id }}][quantity]" 
                                               value="{{ old("quantidade_estoque.{$size->id}.quantity", 0) }}" 
                                               min="0" max="99999999"
                                               oninput="this.value = this.value.slice(0, 8)" required>
                                        <input type="hidden" name="quantidade_estoque[{{ $size->id }}][size_id]" value="{{ $size->id }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @error('quantidade_estoque.*.quantity')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="variation-form-group">
                <label for="imagem" class="variation-form-label">Imagem da Variação</label>
                <div class="file-input-wrapper">
                    <input type="file" class="variation-form-control" id="imagem" name="imagem" onchange="previewImage(event)">
                    <label for="imagem" class="file-input-label" id="imagem-label">
                        <i class="fas fa-cloud-upload-alt"></i> Clique para selecionar uma imagem
                    </label>
                </div>
                <div class="image-preview-container">
                    <img id="imagePreview" src="#" alt="Preview da Imagem" class="image-preview" style="display: none;">
                </div>
                @error('imagem')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="variation-form-actions">
                <button type="submit" class="variation-submit-btn">
                    <i class="fas fa-save"></i> Salvar Variação
                </button>
                <button type="button" class="variation-cancel-btn" id="cancelVariationBtn">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Função para alternar entre modo de visualização e edição do estoque
    function toggleStockEdit(variationId) {
        const row = document.querySelector(`tr[data-variation-id="${variationId}"]`);
        const displayBody = row.querySelector('.stock-display');
        const editBody = row.querySelector('.stock-edit');
        const editBtn = row.querySelector('.btn-edit-stock');
        const editActions = row.querySelector('.stock-edit-actions');

        if (displayBody.style.display === 'none') {
            // Sair do modo de edição
            displayBody.style.display = 'table-row-group';
            editBody.style.display = 'none';
            editBtn.style.display = 'flex';
            editActions.style.display = 'none';
        } else {
            // Entrar no modo de edição
            displayBody.style.display = 'none';
            editBody.style.display = 'table-row-group';
            editBtn.style.display = 'none';
            editActions.style.display = 'flex';
        }
    }

    // Função para cancelar a edição do estoque
    function cancelStockEdit(variationId) {
        const row = document.querySelector(`tr[data-variation-id="${variationId}"]`);
        const displayBody = row.querySelector('.stock-display');
        const editBody = row.querySelector('.stock-edit');
        const editBtn = row.querySelector('.btn-edit-stock');
        const editActions = row.querySelector('.stock-edit-actions');

        // Restaurar valores originais
        const inputs = editBody.querySelectorAll('.stock-edit-input');
        inputs.forEach(input => {
            const sizeId = input.getAttribute('data-size-id');
            const originalValue = row.querySelector(`.stock-quantity[data-size-id="${sizeId}"]`).textContent;
            input.value = originalValue;
        });

        // Voltar para modo de visualização
        displayBody.style.display = 'table-row-group';
        editBody.style.display = 'none';
        editBtn.style.display = 'flex';
        editActions.style.display = 'none';
    }

    // Função para salvar o estoque
    function saveStock(variationId) {
        const row = document.querySelector(`tr[data-variation-id="${variationId}"]`);
        const saveUrl = row.getAttribute('data-save-url');
        const inputs = row.querySelectorAll('.stock-edit-input');
        const stockData = [];

        // Coletar dados do formulário
        inputs.forEach(input => {
            const sizeId = input.getAttribute('data-size-id');
            stockData.push({
                size_id: sizeId,
                quantity: input.value
            });
        });

        // Enviar requisição AJAX
        fetch(saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                quantidade_estoque: stockData
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na requisição');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Atualizar display com novos valores
                inputs.forEach(input => {
                    const sizeId = input.getAttribute('data-size-id');
                    const displayCell = row.querySelector(`.stock-quantity[data-size-id="${sizeId}"]`);
                    displayCell.textContent = input.value;
                });

                // Atualizar total
                const total = stockData.reduce((sum, item) => sum + parseInt(item.quantity), 0);
                row.querySelector('.stock-total').textContent = total;

                // Sair do modo de edição
                toggleStockEdit(variationId);

                // Mostrar mensagem de sucesso
                showFlashMessage('Estoque atualizado com sucesso!', 'success');
            } else {
                showFlashMessage('Erro ao atualizar estoque!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showFlashMessage('Erro ao atualizar estoque!', 'error');
        });
    }

    // Função para mostrar mensagens flash
    function showFlashMessage(message, type) {
        // Criar elemento de mensagem
        const flashMessage = document.createElement('div');
        flashMessage.className = `flash-message ${type}`;
        flashMessage.innerHTML = `
            <div class="flash-message-content">${message}</div>
            <button class="flash-message-close" onclick="this.parentElement.remove()">&times;</button>
        `;

        // Adicionar ao container de mensagens
        const flashContainer = document.querySelector('.flash-messages') || createFlashContainer();
        flashContainer.appendChild(flashMessage);

        // Remover automaticamente após 5 segundos
        setTimeout(() => {
            if (flashMessage.parentElement) {
                flashMessage.remove();
            }
        }, 5000);
    }

    // Criar container para mensagens flash se não existir
    function createFlashContainer() {
        const container = document.createElement('div');
        container.className = 'flash-messages';
        document.body.appendChild(container);
        return container;
    }

    // Validação em tempo real para inputs de estoque
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('stock-edit-input')) {
            const maxLength = 8;
            if (e.target.value.length > maxLength) {
                e.target.value = e.target.value.slice(0, maxLength);
            }
            
            // Garantir que não seja negativo
            if (e.target.value < 0) {
                e.target.value = 0;
            }
        }
    });

    // Código existente para adicionar variação
    document.getElementById('addVariationBtn').addEventListener('click', function() {
        document.getElementById('addVariationForm').style.display = 'block';
        this.style.display = 'none';
    });

    document.getElementById('cancelVariationBtn').addEventListener('click', function() {
        document.getElementById('addVariationForm').style.display = 'none';
        document.getElementById('addVariationBtn').style.display = 'block';
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('imagePreview').src = '#';
        
        const fileLabel = document.getElementById('imagem-label');
        fileLabel.innerHTML = '<i class="fas fa-cloud-upload-alt"></i> Clique para selecionar uma imagem';
        fileLabel.classList.remove('has-file');
    });

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('imagePreview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
            
            const fileName = event.target.files[0].name;
            const label = event.target.nextElementSibling;
            if (label && label.classList.contains('file-input-label')) {
                label.innerHTML = `<i class="fas fa-file-image"></i> ${fileName}`;
                label.classList.add('has-file');
            }
        }
    }

    // Validação em tempo real para o formulário de variação
    document.addEventListener('input', function(e) {
        // Validação para campos de preço
        if (e.target.name === 'preco') {
            const maxLength = 8;
            if (e.target.value.length > maxLength) {
                e.target.value = e.target.value.slice(0, maxLength);
            }
        }
        
        // Validação para campos de estoque
        if (e.target.name.includes('[quantidade_estoque]') && e.target.name.includes('[quantity]')) {
            const maxLength = 8;
            if (e.target.value.length > maxLength) {
                e.target.value = e.target.value.slice(0, maxLength);
            }
        }
        
        // Validação para nome da variação
        if (e.target.name === 'nome_variacao') {
            const maxLength = 60;
            if (e.target.value.length > maxLength) {
                e.target.value = e.target.value.slice(0, maxLength);
            }
        }
        
        // Update file input labels
        if (e.target.type === 'file') {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Nenhum arquivo selecionado';
            const label = e.target.nextElementSibling;
            if (label && label.classList.contains('file-input-label')) {
                label.innerHTML = `<i class="fas fa-file-image"></i> ${fileName}`;
                label.classList.add('has-file');
            }
        }
    });
</script>

<style>
    /* Estilos para edição inline de estoque */
    .stock-edit-input {
        background: rgba(31, 41, 55, 0.8);
        border: 1px solid rgba(0, 212, 170, 0.3);
        border-radius: 4px;
        padding: 0.3rem 0.5rem;
        color: var(--dark-text);
        width: 80px;
        text-align: center;
        font-weight: 600;
        transition: var(--transition);
    }

    .stock-edit-input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 2px rgba(0, 212, 170, 0.2);
        background: rgba(15, 23, 42, 1);
    }

    .stock-edit-actions {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    /* Botões específicos para edição de estoque */
    .btn-save-stock {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.4);
    }

    .btn-save-stock:hover {
        background: linear-gradient(135deg, #34d399, #10b981);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.6);
    }

    .btn-cancel-stock {
        background: rgba(31, 41, 55, 0.8);
        color: var(--accent);
        border: 1px solid rgba(0, 212, 170, 0.3);
    }

    .btn-cancel-stock:hover {
        background: rgba(0, 212, 170, 0.1);
        border-color: var(--accent);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 212, 170, 0.3);
    }

    .error-message {
        color: #ef4444;
        font-size: 0.8rem;
        margin-top: 0.3rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    
    .error-message::before {
        content: "⚠";
        font-size: 0.8rem;
    }

    /* CSS para ajustar o tamanho da paginação */
    .pagination {
        font-size: 0.9rem;
        margin: 0;
    }
    .pagination .page-link {
        padding: 0.25rem 0.5rem;
        color: #fff;
        background-color: rgba(31, 41, 55, 0.8);
        border: 1px solid rgba(0, 212, 170, 0.3);
    }
    .pagination .page-item.active .page-link {
        background-color: #00d4aa;
        border-color: #00d4aa;
        color: #1a202c;
    }
    .pagination .page-link:hover {
        background-color: rgba(0, 212, 170, 0.2);
        border-color: #00d4aa;
        color: #fff;
    }

    /* File input styling para variações */
    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
        width: 100%;
    }

    .file-input-wrapper input[type="file"] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .file-input-label {
        background: rgba(15, 23, 42, 0.8);
        border: 2px dashed rgba(0, 212, 170, 0.3);
        border-radius: 8px;
        padding: 1.2rem;
        text-align: center;
        color: var(--dark-text);
        transition: var(--transition);
        cursor: pointer;
        display: block;
        font-size: 0.9rem;
    }

    .file-input-label:hover {
        border-color: var(--accent);
        background: rgba(0, 212, 170, 0.05);
    }

    .file-input-label.has-file {
        border-color: var(--accent);
        background: rgba(0, 212, 170, 0.1);
        color: var(--accent);
    }

    /* Estilo simplificado para paginação */
    .pagination-wrapper {
        margin: 0.5rem 0;
    }

    .pagination-top, .pagination-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
    }

    .pagination-info {
        color: #fff;
        font-size: 0.9rem;
    }

    /* Remove espaçamento excessivo */
    .pagination-top {
        margin-bottom: 0.5rem;
    }

    .pagination-bottom {
        margin-top: 0.5rem;
    }

    /* Flash messages styling */
    .flash-messages {
        position: fixed;
        top: 90px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
    }

    .flash-message {
        background: rgba(31, 41, 55, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 8px;
        padding: 1rem 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        border-left: 4px solid;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        animation: slideIn 0.3s ease-out;
    }

    .flash-message.success {
        border-color: var(--accent);
    }

    .flash-message.error {
        border-color: #ef4444;
    }

    .flash-message-content {
        color: #e5e7eb;
        font-size: 0.95rem;
    }

    .flash-message-close {
        background: transparent;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        font-size: 1.2rem;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
    }

    .flash-message-close:hover {
        color: #fff;
    }

    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @media (max-width: 768px) {
        .flash-messages {
            right: 10px;
            left: 10px;
            max-width: none;
        }
    }
</style>
@endsection