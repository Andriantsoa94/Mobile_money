<?php

namespace App\Controllers;

use App\Models\EmpruntModel;
use App\Models\LivreModel;
use App\Models\ReservationModel;
use CodeIgniter\HTTP\ResponseInterface;

class EmpruntController extends BaseController
{
    private LivreModel $livreModel;

    private EmpruntModel $empruntModel;

    private ReservationModel $reservationModel;

    public function __construct()
    {
        $this->livreModel = new LivreModel();
        $this->empruntModel = new EmpruntModel();
        $this->reservationModel = new ReservationModel();
    }

    public function preter(int $livreId): ResponseInterface
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $user = session()->get('user');

        if (! is_array($user) || ! isset($user['id'], $user['nom'])) {
            return redirect()->to('/login')->with('error', 'Session utilisateur invalide.');
        }

        $livre = $this->livreModel->getLivreById($livreId);

        if ($livre === null) {
            return redirect()->to('/')->with('error', 'Livre introuvable.');
        }

        if (($livre['statut'] ?? 'disponible') !== 'disponible') {
            return redirect()->to('/')->with('error', 'Ce livre n\'est pas disponible pour le pret.');
        }

        $dateRetourPrevue = date('Y-m-d H:i:s', strtotime('+14 days'));

        if (! $this->empruntModel->createEmprunt((int) $livreId, (int) $user['id'], (string) $user['nom'], $dateRetourPrevue)) {
            return redirect()->to('/')->with('error', 'Enregistrement de l\'emprunt impossible.');
        }

        $this->reservationModel->resolveForUser((int) $livreId, (int) $user['id']);

        if (! $this->livreModel->updateStatut($livreId, 'prete')) {
            return redirect()->to('/')->with('error', 'Mise a jour du statut du livre impossible.');
        }

        return redirect()->to('/livres')->with('success', 'Livre prete avec succes. Retour prevu le ' . date('d/m/Y', strtotime($dateRetourPrevue)) . '.');
    }

    public function retourner(int $livreId): ResponseInterface
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $livre = $this->livreModel->getLivreById($livreId);

        if ($livre === null) {
            return redirect()->to('/')->with('error', 'Livre introuvable.');
        }

        $empruntActif = $this->empruntModel->getEmpruntActifByLivreId($livreId);

        if ($empruntActif === null) {
            return redirect()->to('/')->with('error', 'Aucun emprunt actif trouve pour ce livre.');
        }

        if (! $this->empruntModel->closeEmprunt((int) $empruntActif['id'])) {
            return redirect()->to('/')->with('error', 'Mise a jour de la date de retour impossible.');
        }

        if (! $this->livreModel->updateStatut($livreId, 'disponible')) {
            return redirect()->to('/')->with('error', 'Mise a jour du statut du livre impossible.');
        }

        $nextReservation = $this->reservationModel->popNextReservation((int) $livreId);
        $message = 'Livre retourne avec succes.';

        if ($nextReservation !== null) {
            $message .= ' Un lecteur en file d\'attente a ete notifie.';
        }

        return redirect()->to('/livres')->with('success', $message);
    }

    public function reserver(int $livreId): ResponseInterface
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $user = session()->get('user');

        if (! is_array($user) || ! isset($user['id'])) {
            return redirect()->to('/login')->with('error', 'Session utilisateur invalide.');
        }

        $livre = $this->livreModel->getLivreById($livreId);

        if ($livre === null) {
            return redirect()->to('/livres')->with('error', 'Livre introuvable.');
        }

        if (($livre['statut'] ?? 'disponible') === 'disponible') {
            return redirect()->to('/livres')->with('error', 'Ce livre est disponible, vous pouvez le preter directement.');
        }

        if ($this->reservationModel->hasActiveReservation((int) $livreId, (int) $user['id'])) {
            return redirect()->to('/livres')->with('error', 'Vous avez deja une reservation active pour ce livre.');
        }

        if (! $this->reservationModel->createReservation((int) $livreId, (int) $user['id'])) {
            return redirect()->to('/livres')->with('error', 'Impossible de creer la reservation.');
        }

        $position = $this->reservationModel->getPositionForUser((int) $livreId, (int) $user['id']);
        $positionLabel = $position !== null ? (string) $position : '?';

        return redirect()->to('/livres')->with('success', 'Reservation enregistree. Votre position dans la file: ' . $positionLabel . '.');
    }

    public function annulerReservation(int $livreId): ResponseInterface
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $user = session()->get('user');

        if (! is_array($user) || ! isset($user['id'])) {
            return redirect()->to('/login')->with('error', 'Session utilisateur invalide.');
        }

        if (! $this->reservationModel->cancelForUser((int) $livreId, (int) $user['id'])) {
            return redirect()->to('/livres')->with('error', 'Aucune reservation active a annuler.');
        }

        return redirect()->to('/livres')->with('success', 'Reservation annulee.');
    }
}
