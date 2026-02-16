<?php

class DashboardController
{
    private DashboardModel $model;

    public function __construct()
    {
        $this->model = new DashboardModel();
    }

    public function index(): void
    {
        requireTenant();
        $stats = $this->model->stats(tenantId());
        view('dashboard/index', ['stats' => $stats]);
    }
}
