<?php

class PdfController
{
    private SimulationModel $simulations;

    public function __construct()
    {
        $this->simulations = new SimulationModel();
    }

    public function generate(): void
    {
        requireTenant();
        $id = (int) ($_GET['id'] ?? 0);
        $item = $this->simulations->find($id, tenantId());
        if (!$item) {
            flash('error', 'Simulação não encontrada.');
            redirect('simulations/index');
        }

        if (!file_exists(VENDOR_AUTOLOAD)) {
            flash('error', 'dompdf não instalado. Execute composer install no servidor.');
            redirect('simulations/view', ['id' => $id]);
        }

        require_once VENDOR_AUTOLOAD;
        $restaurant = currentRestaurant();

        ob_start();
        $simulation = $item;
        include VIEWS_PATH . '/simulations/pdf.php';
        $html = ob_get_clean();

        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();

        $filename = 'churrosflow-simulacao-' . $id . '.pdf';
        @file_put_contents(PDF_DIR . '/' . $filename, $output);
        @file_put_contents(EXPORT_DIR . '/' . $filename, $output);

        flash('success', 'PDF gerado com sucesso.');
        redirect('simulations/view', ['id' => $id]);
    }
}
