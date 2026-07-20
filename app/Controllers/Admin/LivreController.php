<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EmpruntModel;
use App\Models\LivreModel;
use CodeIgniter\HTTP\ResponseInterface;

class LivreController extends BaseController
{
    private LivreModel $livreModel;

    private EmpruntModel $empruntModel;

    public function __construct()
    {
        $this->livreModel = new LivreModel();
        $this->empruntModel = new EmpruntModel();
    }

    public function supprimer(int $id): ResponseInterface
    {
        $livre = $this->livreModel->getLivreById($id);

        if ($livre === null) {
            return redirect()->to('/livres')->with('error', 'Livre introuvable.');
        }

        if (($livre['statut'] ?? 'disponible') === 'prete') {
            return redirect()->to('/livres')->with('error', 'Retournez le livre avant suppression.');
        }

        $this->empruntModel->deleteByLivreId($id);

        if (! $this->livreModel->deleteLivre($id)) {
            return redirect()->to('/livres')->with('error', 'Suppression impossible.');
        }

        return redirect()->to('/livres')->with('success', 'Livre supprime par admin.');
    }
}
