<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EmpruntModel;

class DashboardController extends BaseController
{
    private EmpruntModel $empruntModel;

    public function __construct()
    {
        $this->empruntModel = new EmpruntModel();
    }

    public function index(): string
    {
        return $this->render('admin/dashboard', [
            'title' => 'Dashboard admin',
            'topLivres' => $this->empruntModel->getMostBorrowedBooks(10),
            'topEmprunteurs' => $this->empruntModel->getMostActiveBorrowers(10),
            'retards' => $this->empruntModel->getRetardsActifs(),
        ]);
    }
}
