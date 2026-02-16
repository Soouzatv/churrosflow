<?php

class ProductsController
{
    private ProductModel $products;
    private PDO $pdo;

    public function __construct()
    {
        $this->products = new ProductModel();
        $this->pdo = getPDO();
    }

    public function index(): void
    {
        requireTenant();
        $search = trim((string) ($_GET['q'] ?? ''));
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $total = $this->products->countByTenant(tenantId(), $search);
        $offset = ($page - 1) * $perPage;
        $items = $this->products->paginateByTenant(tenantId(), $search, $perPage, $offset);

        foreach ($items as &$item) {
            $item['metrics'] = ProductModel::calculateMetrics($item);
        }

        view('products/index', [
            'items' => $items,
            'search' => $search,
            'page' => $page,
            'pages' => max(1, (int) ceil($total / $perPage)),
        ]);
    }

    public function create(): void
    {
        requireTenant();
        if (isPost()) {
            csrf_check();
            $data = $this->validate($_POST);
            if ($data['errors']) {
                set_old($_POST);
                flash('error', implode(' ', $data['errors']));
                redirect('products/create');
            }
            $id = $this->products->create($data['values'], tenantId());
            auditLog($this->pdo, tenantId(), (int) currentUser()['id'], 'products.create', ['id' => $id]);
            clear_old();
            flash('success', 'Produto cadastrado com sucesso.');
            redirect('products/index');
        }
        view('products/create');
    }

    public function edit(): void
    {
        requireTenant();
        $id = (int) ($_GET['id'] ?? 0);
        $product = $this->products->find($id, tenantId());
        if (!$product) {
            flash('error', 'Produto não encontrado.');
            redirect('products/index');
        }

        if (isPost()) {
            csrf_check();
            $data = $this->validate($_POST);
            if ($data['errors']) {
                set_old($_POST);
                flash('error', implode(' ', $data['errors']));
                redirect('products/edit', ['id' => $id]);
            }
            $this->products->update($id, tenantId(), $data['values']);
            auditLog($this->pdo, tenantId(), (int) currentUser()['id'], 'products.update', ['id' => $id]);
            clear_old();
            flash('success', 'Produto atualizado.');
            redirect('products/index');
        }

        view('products/edit', ['product' => $product]);
    }

    public function delete(): void
    {
        requireTenant();
        if (!isPost()) {
            redirect('products/index');
        }
        csrf_check();
        $id = (int) ($_GET['id'] ?? 0);
        $this->products->delete($id, tenantId());
        auditLog($this->pdo, tenantId(), (int) currentUser()['id'], 'products.delete', ['id' => $id]);
        flash('success', 'Produto removido.');
        redirect('products/index');
    }

    private function validate(array $input): array
    {
        $name = trim((string) ($input['name'] ?? ''));
        $category = trim((string) ($input['category'] ?? 'Geral'));
        $costProduction = (float) str_replace(',', '.', (string) ($input['cost_production'] ?? 0));
        $fixedExpenses = (float) str_replace(',', '.', (string) ($input['fixed_expenses_unit'] ?? 0));
        $desiredMargin = (float) str_replace(',', '.', (string) ($input['desired_margin_pct'] ?? 0));
        $currentPrice = (float) str_replace(',', '.', (string) ($input['current_price'] ?? 0));

        $errors = [];
        if ($name === '') {
            $errors[] = 'Nome é obrigatório.';
        }
        if ($category === '') {
            $errors[] = 'Categoria é obrigatória.';
        }
        if ($desiredMargin >= 100) {
            $errors[] = 'Margem desejada deve ser menor que 100%.';
        }

        return [
            'errors' => $errors,
            'values' => [
                'name' => $name,
                'category' => $category,
                'cost_production' => max(0, $costProduction),
                'fixed_expenses_unit' => max(0, $fixedExpenses),
                'desired_margin_pct' => max(0, $desiredMargin),
                'current_price' => max(0, $currentPrice),
            ],
        ];
    }
}
