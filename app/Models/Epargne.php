<?php

namespace App\Models;

use CodeIgniter\Model;

class Epargne extends Model
{
    protected $table            = 'epargne';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['idUser','pourcentage','montantTotal'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function ligneEpargne(int $idUser): ?array
    {
        return $this->where('idUser', $idUser)->first();
    }

    /**
     * Valeur numérique du solde d'un utilisateur (0 si aucune ligne trouvée).
     */
    public function getValeur(int $idUser): float
    {
        return (float) ($this->ligneEpargne($idUser)['montantTotal'] ?? 0);
    }

    public function getPourcentage(int $idUser)
    {
        return  ($this->ligneEpargne($idUser)['pourcentage'] ?? 0);
    }
}
