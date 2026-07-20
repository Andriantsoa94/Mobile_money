<?php

namespace App\Models;

use CodeIgniter\Model;

class ReservationModel extends Model
{
    protected $table = 'reservations';

    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';

    protected $protectFields = true;

    protected $allowedFields = [
        'livre_id',
        'user_id',
        'position_file',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';

    public function hasActiveReservation(int $livreId, int $userId): bool
    {
        return $this->where('livre_id', $livreId)
            ->where('user_id', $userId)
            ->whereIn('status', ['en_attente', 'notifiee'])
            ->countAllResults() > 0;
    }

    public function getNextPosition(int $livreId): int
    {
        $row = $this->selectMax('position_file')
            ->where('livre_id', $livreId)
            ->where('status', 'en_attente')
            ->first();

        $max = (int) ($row['position_file'] ?? 0);

        return $max + 1;
    }

    public function createReservation(int $livreId, int $userId): bool
    {
        return $this->insert([
            'livre_id' => $livreId,
            'user_id' => $userId,
            'position_file' => $this->getNextPosition($livreId),
            'status' => 'en_attente',
        ]) !== false;
    }

    public function getPositionForUser(int $livreId, int $userId): ?int
    {
        $row = $this->where('livre_id', $livreId)
            ->where('user_id', $userId)
            ->whereIn('status', ['en_attente', 'notifiee'])
            ->orderBy('id', 'DESC')
            ->first();

        if ($row === null) {
            return null;
        }

        return (int) $row['position_file'];
    }

    public function getActiveMapByUserId(int $userId): array
    {
        $rows = $this->select('livre_id')
            ->where('user_id', $userId)
            ->whereIn('status', ['en_attente', 'notifiee'])
            ->findAll();

        $map = [];

        foreach ($rows as $row) {
            $map[(int) $row['livre_id']] = true;
        }

        return $map;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getQueueByLivreId(int $livreId): array
    {
        return $this->select('reservations.*, user.nom AS user_nom, user.email AS user_email')
            ->join('user', 'user.id = reservations.user_id', 'left')
            ->where('reservations.livre_id', $livreId)
            ->where('reservations.status', 'en_attente')
            ->orderBy('reservations.position_file', 'ASC')
            ->findAll();
    }

    public function countActiveQueueByLivreId(int $livreId): int
    {
        return $this->where('livre_id', $livreId)
            ->where('status', 'en_attente')
            ->countAllResults();
    }

    /**
     * @param list<int> $livreIds
     * @return array<int, int>
     */
    public function getQueueCountsByLivreIds(array $livreIds): array
    {
        if ($livreIds === []) {
            return [];
        }

        $rows = $this->select('livre_id, COUNT(*) AS total')
            ->whereIn('livre_id', $livreIds)
            ->where('status', 'en_attente')
            ->groupBy('livre_id')
            ->findAll();

        $result = [];

        foreach ($rows as $row) {
            $result[(int) $row['livre_id']] = (int) $row['total'];
        }

        return $result;
    }

    public function popNextReservation(int $livreId): ?array
    {
        $next = $this->where('livre_id', $livreId)
            ->where('status', 'en_attente')
            ->orderBy('position_file', 'ASC')
            ->first();

        if ($next === null) {
            return null;
        }

        $this->update((int) $next['id'], ['status' => 'notifiee']);

        return $next;
    }

    public function cancelForUser(int $livreId, int $userId): bool
    {
        return $this->where('livre_id', $livreId)
            ->where('user_id', $userId)
            ->whereIn('status', ['en_attente', 'notifiee'])
            ->set(['status' => 'annulee'])
            ->update();
    }

    public function resolveForUser(int $livreId, int $userId): bool
    {
        return $this->where('livre_id', $livreId)
            ->where('user_id', $userId)
            ->whereIn('status', ['en_attente', 'notifiee'])
            ->set(['status' => 'terminee'])
            ->update();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getByUserId(int $userId): array
    {
        return $this->select('reservations.*, livres.titre AS livre_titre')
            ->join('livres', 'livres.id = reservations.livre_id', 'left')
            ->where('reservations.user_id', $userId)
            ->orderBy('reservations.created_at', 'DESC')
            ->findAll();
    }
}
