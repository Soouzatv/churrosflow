<?php

class ReportModel extends BaseModel
{
    public function build(int $restaurantId): array
    {
        $products = $this->productsWithMetrics($restaurantId);
        if (!$products) {
            return [
                'most_profitable' => null,
                'least_profitable' => null,
                'avg_margin' => 0.0,
                'ideal_vs_current' => [],
                'recommendations' => ['Cadastre produtos para gerar análises inteligentes.'],
            ];
        }

        usort($products, fn($a, $b) => $b['profit_unit'] <=> $a['profit_unit']);
        $most = $products[0];
        $least = $products[count($products) - 1];

        $marginItems = array_filter($products, fn($item) => $item['current_price'] > 0);
        $avgMargin = 0.0;
        if ($marginItems) {
            $avgMargin = array_sum(array_column($marginItems, 'real_margin_pct')) / count($marginItems);
        }

        $recommendations = [];
        if ($avgMargin < 20) {
            $recommendations[] = 'Margem média baixa. Reavalie custos e suba preços de itens estratégicos.';
        } else {
            $recommendations[] = 'Margem média saudável. Foque em aumentar volume dos itens mais lucrativos.';
        }
        if ($least['profit_unit'] < 0) {
            $recommendations[] = 'Há produto em prejuízo. Ajuste preço atual ou revise a composição de custo.';
        }
        $overpricedCount = count(array_filter($products, fn($p) => $p['price_diff'] > 3));
        if ($overpricedCount > 0) {
            $recommendations[] = "{$overpricedCount} produto(s) estão acima do ideal em mais de R$ 3,00.";
        }

        return [
            'most_profitable' => $most,
            'least_profitable' => $least,
            'avg_margin' => $avgMargin,
            'ideal_vs_current' => $products,
            'recommendations' => $recommendations,
        ];
    }

    private function productsWithMetrics(int $restaurantId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM products WHERE restaurant_id = :restaurant_id ORDER BY name ASC
        ');
        $stmt->execute([':restaurant_id' => $restaurantId]);
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $calc = ProductModel::calculateMetrics($row);
            $result[] = [
                'id' => (int) $row['id'],
                'name' => $row['name'],
                'category' => $row['category'],
                'current_price' => (float) $row['current_price'],
                'suggested_price' => (float) $row['suggested_price'],
                'profit_unit' => $calc['lucro_unit_no_preco_atual'],
                'real_margin_pct' => $calc['margem_real_no_preco_atual'],
                'price_diff' => $calc['diferenca_preco'],
            ];
        }
        return $result;
    }
}
