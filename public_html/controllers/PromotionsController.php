<?php

class PromotionsController
{
    private PromotionModel $promotions;
    private ProductModel $products;
    private PDO $pdo;

    public function __construct()
    {
        $this->promotions = new PromotionModel();
        $this->products = new ProductModel();
        $this->pdo = getPDO();
    }

    public function index(): void
    {
        requireTenant();
        $items = $this->promotions->allByTenant(tenantId());
        $sampleProduct = $this->products->allByTenant(tenantId())[0] ?? null;
        $preview = [];

        if ($sampleProduct) {
            $cost = (float) $sampleProduct['cost_production'] + (float) $sampleProduct['fixed_expenses_unit'];
            $basePrice = (float) $sampleProduct['current_price'] > 0
                ? (float) $sampleProduct['current_price']
                : (float) $sampleProduct['suggested_price'];

            foreach ($items as $item) {
                $calc = PromotionEngine::apply($basePrice, 10, $item);
                $profit = $calc['unit_price_effective'] - $cost;
                $preview[$item['id']] = [
                    'unit_effective' => $calc['unit_price_effective'],
                    'profit' => $profit,
                    'loss' => $calc['unit_price_effective'] < $cost,
                ];
            }
        }

        view('promotions/index', ['items' => $items, 'preview' => $preview, 'hasSample' => (bool) $sampleProduct]);
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
                redirect('promotions/create');
            }
            $id = $this->promotions->create(tenantId(), $data['values']);
            auditLog($this->pdo, tenantId(), (int) currentUser()['id'], 'promotions.create', ['id' => $id]);
            clear_old();
            flash('success', 'Promoção criada.');
            redirect('promotions/index');
        }
        view('promotions/create');
    }

    public function edit(): void
    {
        requireTenant();
        $id = (int) ($_GET['id'] ?? 0);
        $promotion = $this->promotions->find($id, tenantId());
        if (!$promotion) {
            flash('error', 'Promoção não encontrada.');
            redirect('promotions/index');
        }

        if (isPost()) {
            csrf_check();
            $data = $this->validate($_POST);
            if ($data['errors']) {
                set_old($_POST);
                flash('error', implode(' ', $data['errors']));
                redirect('promotions/edit', ['id' => $id]);
            }
            $this->promotions->update($id, tenantId(), $data['values']);
            auditLog($this->pdo, tenantId(), (int) currentUser()['id'], 'promotions.update', ['id' => $id]);
            clear_old();
            flash('success', 'Promoção atualizada.');
            redirect('promotions/index');
        }

        view('promotions/edit', ['promotion' => $promotion]);
    }

    public function toggle(): void
    {
        requireTenant();
        if (!isPost()) {
            redirect('promotions/index');
        }
        csrf_check();
        $id = (int) ($_GET['id'] ?? 0);
        $this->promotions->toggle($id, tenantId());
        auditLog($this->pdo, tenantId(), (int) currentUser()['id'], 'promotions.toggle', ['id' => $id]);
        flash('success', 'Status da promoção atualizado.');
        redirect('promotions/index');
    }

    public function delete(): void
    {
        requireTenant();
        if (!isPost()) {
            redirect('promotions/index');
        }
        csrf_check();
        $id = (int) ($_GET['id'] ?? 0);
        $this->promotions->delete($id, tenantId());
        auditLog($this->pdo, tenantId(), (int) currentUser()['id'], 'promotions.delete', ['id' => $id]);
        flash('success', 'Promoção removida.');
        redirect('promotions/index');
    }

    private function validate(array $input): array
    {
        $name = trim((string) ($input['name'] ?? ''));
        $type = (string) ($input['type'] ?? '');
        $errors = [];
        $rules = [];

        if ($name === '') {
            $errors[] = 'Nome é obrigatório.';
        }

        $validTypes = ['percent_discount', 'buy_x_pay_y', 'progressive_discount', 'combo_fixed_price'];
        if (!in_array($type, $validTypes, true)) {
            $errors[] = 'Tipo de promoção inválido.';
        }

        if ($type === 'percent_discount') {
            $rules['discount_pct'] = max(0, min(100, (float) str_replace(',', '.', (string) ($input['discount_pct'] ?? 0))));
        }

        if ($type === 'buy_x_pay_y') {
            $buy = max(1, (int) ($input['buy_qty'] ?? 1));
            $pay = max(1, (int) ($input['pay_qty'] ?? 1));
            if ($pay > $buy) {
                $errors[] = 'No leve X pague Y, Y não pode ser maior que X.';
            }
            $rules['buy'] = $buy;
            $rules['pay'] = $pay;
        }

        if ($type === 'progressive_discount') {
            $t1q = max(1, (int) ($input['tier1_qty'] ?? 10));
            $t1d = max(0, min(100, (float) str_replace(',', '.', (string) ($input['tier1_discount'] ?? 5))));
            $t2q = max($t1q + 1, (int) ($input['tier2_qty'] ?? 30));
            $t2d = max($t1d, min(100, (float) str_replace(',', '.', (string) ($input['tier2_discount'] ?? 10))));
            $rules['tiers'] = [
                ['min_qty' => $t1q, 'discount_pct' => $t1d],
                ['min_qty' => $t2q, 'discount_pct' => $t2d],
            ];
        }

        if ($type === 'combo_fixed_price') {
            $rules['bundle_qty'] = max(1, (int) ($input['bundle_qty'] ?? 2));
            $rules['bundle_price'] = max(0, (float) str_replace(',', '.', (string) ($input['bundle_price'] ?? 0)));
        }

        return [
            'errors' => $errors,
            'values' => [
                'name' => $name,
                'type' => $type,
                'rules_json' => json_encode($rules, JSON_UNESCAPED_UNICODE),
            ],
        ];
    }
}
