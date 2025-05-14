
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Novo Produto</h1>
        <a href="/products" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="/products/create" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Nome do Produto</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Preço</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="number" class="form-control" id="price" name="price"
                            step="0.01" min="0" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantidade em Estoque</label>
                    <input type="number" class="form-control" id="quantity" name="quantity"
                        min="0" value="0" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Variações</label>
                    <div id="variations">
                        <div class="variation-item mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="variations[0][name]"
                                        placeholder="Nome da Variação">
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" class="form-control" name="variations[0][price_adjustment]"
                                            step="0.01" placeholder="Ajuste de Preço">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" name="variations[0][quantity]"
                                        min="0" value="0" placeholder="Estoque">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger remove-variation">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary" id="add-variation">
                        <i class="fas fa-plus"></i> Adicionar Variação
                    </button>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let variationCount = 1;

    document.getElementById('add-variation').addEventListener('click', function() {
        const variations = document.getElementById('variations');
        const newVariation = document.createElement('div');
        newVariation.className = 'variation-item mb-3';
        newVariation.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <input type="text" class="form-control" name="variations[${variationCount}][name]" 
                       placeholder="Nome da Variação">
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="number" class="form-control" name="variations[${variationCount}][price_adjustment]" 
                           step="0.01" placeholder="Ajuste de Preço">
                </div>
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="variations[${variationCount}][quantity]" 
                       min="0" value="0" placeholder="Estoque">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-variation">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
        variations.appendChild(newVariation);
        variationCount++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variation') ||
            e.target.parentElement.classList.contains('remove-variation')) {
            const button = e.target.classList.contains('remove-variation') ? e.target : e.target.parentElement;
            button.closest('.variation-item').remove();
        }
    });
</script>