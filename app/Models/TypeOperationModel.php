<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeOperationModel extends Model
{
    protected $table            = 'typeOperation';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nom', 'isGain', 'isActif'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Inverse l'état actif/inactif d'un type d'opération.
     */
    public function basculerActif(int $id): bool
    {
        $ligne = $this->find($id);
        if (! $ligne) {
            return false;
        }

        return (bool) $this->update($id, [
            'isActif' => empty($ligne['isActif']) ? 1 : 0,
        ]);
    }

    /**
     * Uniquement les types d'opération actifs (pour formulaires côté client).
     */
    public function listeActifs(): array
    {
        return $this->where('isActif', 1)->orderBy('nom', 'ASC')->findAll();
    }
}
