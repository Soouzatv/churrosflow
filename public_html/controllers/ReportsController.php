<?php

class ReportsController
{
    private ReportModel $reports;

    public function __construct()
    {
        $this->reports = new ReportModel();
    }

    public function index(): void
    {
        requireTenant();
        $data = $this->reports->build(tenantId());
        view('reports/index', $data);
    }
}
