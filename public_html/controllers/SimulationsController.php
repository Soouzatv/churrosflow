<?php

class SimulationsController
{
    private SimulationModel $simulations;
    private ProductModel $products;
    private PromotionModel $promotions;
    private PDO $pdo;

    public function __construct()
    {
        $this->simulations = new SimulationModel();
        $this->products = new ProductModel();
        $this->promotions = new PromotionModel();
        $this->pdo = getPDO();
    }

    public function index(): void
    {
        requireTenant();
        $items = $this->simulations->allByTenant(tenantId());
        view('simulations/index', ['items' => $items]);
    }

    public function create(): void
    {
        requireTenant();
        $products = $this->products->allByTenant(tenantId());
        $promotions = $this->promotions->allByTenant(tenantId(), true);

        if (isPost()) {
            csrf_check();
            $payload = $this->validate($_POST, $products, $promotions);
            if ($payload['errors']) {
                set_old($_POST);
                flash('error', implode(' ', $payload['errors']));
                redirect('simulations/create');
            }

            $id = $this->simulations->create(tenantId(), $payload['values']);
            auditLog($this->pdo, tenantId(), (int) currentUser()['id'], 'simulations.create', ['id' => $id]);
            clear_old();
            flash('success', 'Simulação criada.');
            redirect('simulations/view', ['id' => $id]);
        }

        view('simulations/create', ['products' => $products, 'promotions' => $promotions]);
    }

    public function view(): void
    {
        requireTenant();
        $id = (int) ($_GET['id'] ?? 0);
        $item = $this->simulations->find($id, tenantId());
        if (!$item) {
            flash('error', 'Simulação não encontrada.');
            redirect('simulations/index');
        }
        $pdfPublicUrl = baseUrl(trim(PDF_PUBLIC_PATH, '/') . '/churrosflow-simulacao-' . $id . '.pdf');
        $waText = rawurlencode("Olá! Aqui está a simulação do evento {$item['event_name']}. PDF: {$pdfPublicUrl}");
        $waLink = "https://wa.me/?text={$waText}";
        view('simulations/view', ['item' => $item, 'pdfPublicUrl' => $pdfPublicUrl, 'waLink' => $waLink]);
    }

    public function edit(): void
    {
        requireTenant();
        $id = (int) ($_GET['id'] ?? 0);
        $item = $this->simulations->find($id, tenantId());
        if (!$item) {
            flash('error', 'Simulação não encontrada.');
            redirect('simulations/index');
        }

        $products = $this->products->allByTenant(tenantId());
        $promotions = $this->promotions->allByTenant(tenantId(), true);

        if (isPost()) {
            csrf_check();
            $payload = $this->validate($_POST, $products, $promotions);
            if ($payload['errors']) {
                set_old($_POST);
                flash('error', implode(' ', $payload['errors']));
                redirect('simulations/edit', ['id' => $id]);
            }
            $this->simulations->update($id, tenantId(), $payload['values']);
            auditLog($this->pdo, tenantId(), (int) currentUser()['id'], 'simulations.update', ['id' => $id]);
            clear_old();
            flash('success', 'Simulação atualizada.');
            redirect('simulations/view', ['id' => $id]);
        }

        view('simulations/edit', [
            'item' => $item,
            'products' => $products,
            'promotions' => $promotions,
        ]);
    }

    public function delete(): void
    {
        requireTenant();
        if (!isPost()) {
            redirect('simulations/index');
        }
        csrf_check();
        $id = (int) ($_GET['id'] ?? 0);
        $this->simulations->delete($id, tenantId());
        auditLog($this->pdo, tenantId(), (int) currentUser()['id'], 'simulations.delete', ['id' => $id]);
        flash('success', 'Simulação removida.');
        redirect('simulations/index');
    }

    private function validate(array $input, array $products, array $promotions): array
    {
        $eventName = trim((string) ($input['event_name'] ?? ''));
        $productId = (int) ($input['product_id'] ?? 0);
        $promotionId = (int) ($input['promotion_id'] ?? 0);
        $qty = max(1, (int) ($input['estimated_qty'] ?? 1));
        $extraCost = max(0, (float) str_replace(',', '.', (string) ($input['extra_cost'] ?? 0)));
        $discountPct = max(0, min(100, (float) str_replace(',', '.', (string) ($input['discount_pct'] ?? 0))));
        $notes = trim((string) ($input['notes'] ?? ''));

        $errors = [];
        if ($eventName === '') {
            $errors[] = 'Nome do evento é obrigatório.';
        }

        $product = null;
        foreach ($products as $p) {
            if ((int) $p['id'] === $productId) {
                $product = $p;
                break;
            }
        }
        if (!$product) {
            $errors[] = 'Selecione um produto válido.';
        }

        $promotion = null;
        if ($promotionId > 0) {
            foreach ($promotions as $promo) {
                if ((int) $promo['id'] === $promotionId) {
                    $promotion = $promo;
                    break;
                }
            }
            if (!$promotion) {
                $errors[] = 'Promoção inválida para este restaurante.';
            }
        }

        if ($errors) {
            return ['errors' => $errors, 'values' => []];
        }

        $calc = $this->simulations->calculate($product, $qty, $extraCost, $discountPct, $promotion);

        return [
            'errors' => [],
            'values' => [
                'event_name' => $eventName,
                'product_id' => $productId,
                'promotion_id' => $promotionId ?: null,
                'estimated_qty' => $qty,
                'extra_cost' => $extraCost,
                'discount_pct' => $discountPct,
                'used_unit_price' => $calc['used_unit_price'],
                'gross_revenue' => $calc['gross_revenue'],
                'revenue_after_discount' => $calc['revenue_after_discount'],
                'total_cost' => $calc['total_cost'],
                'estimated_profit' => $calc['estimated_profit'],
                'break_even_qty' => $calc['break_even_qty'],
                'notes' => $notes,
            ],
        ];
    }
}
