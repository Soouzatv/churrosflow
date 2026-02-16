<div class="flex items-center justify-between mb-4">
    <h2 class="text-2xl font-bold">Novo Produto</h2>
    <a class="btn btn-light" href="index.php?r=products/index">Voltar</a>
</div>

<div class="card">
    <form method="post" action="index.php?r=products/create" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="hidden" name="_csrf" value="<?= escape(csrf_token()) ?>">
        <div>
            <label class="label">Nome</label>
            <input class="input" type="text" name="name" value="<?= escape(old('name')) ?>" required>
        </div>
        <div>
            <label class="label">Categoria</label>
            <input class="input" type="text" name="category" value="<?= escape(old('category', 'Geral')) ?>" required>
        </div>
        <div>
            <label class="label">Custo de produção (R$)</label>
            <input class="input" type="number" step="0.01" min="0" name="cost_production" value="<?= escape(old('cost_production', '0')) ?>" required>
        </div>
        <div>
            <label class="label">Despesas fixas por unidade (R$)</label>
            <input class="input" type="number" step="0.01" min="0" name="fixed_expenses_unit" value="<?= escape(old('fixed_expenses_unit', '0')) ?>" required>
        </div>
        <div>
            <label class="label">Margem desejada (%)</label>
            <input class="input" type="number" step="0.01" min="0" max="99.99" name="desired_margin_pct" value="<?= escape(old('desired_margin_pct', '35')) ?>" required>
        </div>
        <div>
            <label class="label">Preço atual praticado (R$)</label>
            <input class="input" type="number" step="0.01" min="0" name="current_price" value="<?= escape(old('current_price', '0')) ?>" required>
        </div>
        <div class="md:col-span-2">
            <button class="btn btn-primary" type="submit">Salvar produto</button>
        </div>
    </form>
</div>
