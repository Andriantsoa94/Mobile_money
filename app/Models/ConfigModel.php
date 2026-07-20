<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfigModel extends Model
{
    protected $table            = 'config';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = ['min', 'max', 'frais', 'gain'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function trancheDe(float $montant): ?array
    {
        return $this->where('min <=', $montant)
            ->where('max >=', $montant)
            ->first();
    }

    public function calculerFrais(float $montant): float
    {
        return (float) ($this->trancheDe($montant)['frais'] ?? 0);
    }

    public function calculerGain(float $montant): float
    {
        return (float) ($this->trancheDe($montant)['gain'] ?? 0);
    }

    public function listeTriee(): array
    {
        return $this->orderBy('min', 'ASC')->findAll();
    }
}
