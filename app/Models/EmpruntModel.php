<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpruntModel extends Model
{
    protected $table = 'emprunts';

    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';

    protected $protectFields = true;

    protected $allowedFields = [
        'livre_id',
        'user_id',
        'nom_emprunteur',
        'status',
        'date_emprunt',
        'date_retour_prevue',
        'date_retour',
    ];

    public function createEmprunt(int $livreId, int $userId, string $nomEmprunteur, ?string $dateRetourPrevue = null): bool
    {
        $dateRetourPrevue = $dateRetourPrevue ?? date('Y-m-d H:i:s', strtotime('+14 days'));

        return $this->insert([
            'livre_id' => $livreId,
            'user_id' => $userId,
            'nom_emprunteur' => $nomEmprunteur,
            'status' => 1,
            'date_emprunt' => date('Y-m-d H:i:s'),
            'date_retour_prevue' => $dateRetourPrevue,
            'date_retour' => null,
        ]) !== false;
    }

    public function getDernierEmpruntByLivreId(int $livreId): ?array
    {
        return $this
            ->where('livre_id', $livreId)
            ->orderBy('date_emprunt', 'DESC')
            ->first();
    }

    public function getOpenEmpruntByLivreId(int $livreId): ?array
    {
        return $this
            ->where('livre_id', $livreId)
            ->where('status', 1)
            ->orderBy('date_emprunt', 'DESC')
            ->first();
    }

    public function getEmpruntActifByLivreId(int $livreId): ?array
    {
        return $this->getOpenEmpruntByLivreId($livreId);
    }

    public function closeEmprunt(int $empruntId): bool
    {
        return $this->update($empruntId, [
            'status' => 0,
            'date_retour' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getHistoriqueByUserId(int $userId): array
    {
        return $this->select(
            "emprunts.*, livres.titre AS livre_titre, livres.auteur AS livre_auteur,
            CASE
                WHEN emprunts.status = 0 AND emprunts.date_retour IS NOT NULL AND emprunts.date_retour > emprunts.date_retour_prevue
                    THEN DATEDIFF(emprunts.date_retour, emprunts.date_retour_prevue)
                WHEN emprunts.status = 1 AND NOW() > emprunts.date_retour_prevue
                    THEN DATEDIFF(NOW(), emprunts.date_retour_prevue)
                ELSE 0
            END AS retard_jours"
        )
            ->join('livres', 'livres.id = emprunts.livre_id', 'left')
            ->where('emprunts.user_id', $userId)
            ->orderBy('emprunts.date_emprunt', 'DESC')
            ->findAll();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getHistoriqueByLivreId(int $livreId): array
    {
        return $this->select(
            "emprunts.*, user.nom AS user_nom,
            CASE
                WHEN emprunts.status = 0 AND emprunts.date_retour IS NOT NULL AND emprunts.date_retour > emprunts.date_retour_prevue
                    THEN DATEDIFF(emprunts.date_retour, emprunts.date_retour_prevue)
                WHEN emprunts.status = 1 AND NOW() > emprunts.date_retour_prevue
                    THEN DATEDIFF(NOW(), emprunts.date_retour_prevue)
                ELSE 0
            END AS retard_jours"
        )
            ->join('user', 'user.id = emprunts.user_id', 'left')
            ->where('emprunts.livre_id', $livreId)
            ->orderBy('emprunts.date_emprunt', 'DESC')
            ->findAll();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getRetardsActifs(): array
    {
        return $this->select(
            'emprunts.*, livres.titre AS livre_titre, user.nom AS user_nom, DATEDIFF(NOW(), emprunts.date_retour_prevue) AS retard_jours'
        )
            ->join('livres', 'livres.id = emprunts.livre_id', 'left')
            ->join('user', 'user.id = emprunts.user_id', 'left')
            ->where('emprunts.status', 1)
            ->where('emprunts.date_retour_prevue <', date('Y-m-d H:i:s'))
            ->orderBy('retard_jours', 'DESC')
            ->findAll();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getMostBorrowedBooks(int $limit = 10): array
    {
        return $this->select('livres.id, livres.titre, COUNT(emprunts.id) AS total_emprunts')
            ->join('livres', 'livres.id = emprunts.livre_id', 'left')
            ->groupBy('livres.id, livres.titre')
            ->orderBy('total_emprunts', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getMostActiveBorrowers(int $limit = 10): array
    {
        return $this->select('user.id, user.nom, user.email, COUNT(emprunts.id) AS total_emprunts')
            ->join('user', 'user.id = emprunts.user_id', 'left')
            ->groupBy('user.id, user.nom, user.email')
            ->orderBy('total_emprunts', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function deleteByLivreId(int $livreId): bool
    {
        return $this->builder()
            ->where('livre_id', $livreId)
            ->delete();
    }
}