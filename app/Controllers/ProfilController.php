<?php

namespace App\Controllers;

use App\Models\EmpruntModel;
use App\Models\ReservationModel;
use CodeIgniter\HTTP\ResponseInterface;

class ProfilController extends BaseController
{
    private EmpruntModel $empruntModel;

    private ReservationModel $reservationModel;

    public function __construct()
    {
        $this->empruntModel = new EmpruntModel();
        $this->reservationModel = new ReservationModel();
    }

    public function index(): string|ResponseInterface
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $user = session()->get('user');

        if (! is_array($user) || ! isset($user['id'])) {
            return redirect()->to('/login')->with('error', 'Session utilisateur invalide.');
        }

        $historique = $this->empruntModel->getHistoriqueByUserId((int) $user['id']);
        $empruntsActifs = array_values(array_filter($historique, static fn (array $row): bool => (int) ($row['status'] ?? 0) === 1));
        $anciensEmprunts = array_values(array_filter($historique, static fn (array $row): bool => (int) ($row['status'] ?? 0) === 0));
        $reservations = $this->reservationModel->getByUserId((int) $user['id']);

        return $this->render('profil/index', [
            'title' => 'Mon profil',
            'user' => $user,
            'empruntsActifs' => $empruntsActifs,
            'anciensEmprunts' => $anciensEmprunts,
            'reservations' => $reservations,
        ]);
    }
}
