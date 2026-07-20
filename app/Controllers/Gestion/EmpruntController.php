<?php

namespace App\Controllers\Gestion;

use App\Controllers\BaseController;
use App\Models\EmpruntModel;

class EmpruntController extends BaseController
{
    private EmpruntModel $empruntModel;

    public function __construct()
    {
        $this->empruntModel = new EmpruntModel();
    }

    public function index(): string
    {
        $retards = $this->empruntModel->getRetardsActifs();

        return $this->render('gestion/retards', [
            'title' => 'Gestion des retards',
            'retards' => $retards,
        ]);
    }

    public function retards(): string
    {
        return $this->index();
    }
}
