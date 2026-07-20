<?php

namespace App\Models;

use CodeIgniter\Model;

class AvisLivreModel extends Model
{
    protected $table = 'avis_livres';

    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';

    protected $protectFields = true;

    protected $allowedFields = [
        'livre_id',
        'user_id',
        'note',
        'commentaire',
        'created_at',
    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';

    protected $updatedField = '';

    public function addAvis(int $livreId, int $userId, int $note, string $commentaire): bool
    {
        return $this->insert([
            'livre_id' => $livreId,
            'user_id' => $userId,
            'note' => $note,
            'commentaire' => trim($commentaire),
        ]) !== false;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getByLivreId(int $livreId): array
    {
        return $this->select('avis_livres.*, user.nom AS user_nom')
            ->join('user', 'user.id = avis_livres.user_id', 'left')
            ->where('avis_livres.livre_id', $livreId)
            ->orderBy('avis_livres.created_at', 'DESC')
            ->findAll();
    }

    public function getMoyenneByLivreId(int $livreId): float
    {
        $row = $this->selectAvg('note')
            ->where('livre_id', $livreId)
            ->first();

        return (float) ($row['note'] ?? 0);
    }
}
