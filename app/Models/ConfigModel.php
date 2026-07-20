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

    // Barème global par tranche de montant : "min"/"max" délimitent la
    // tranche, "gain" est le frais appliqué pour cette tranche.
    protected $allowedFields = ['min', 'max', 'gain'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Calcule les frais applicables pour un montant donné, en fonction
     * de la tranche [min, max] dans laquelle il tombe.
     * Retourne 0 si aucune tranche ne correspond.
     */
    public function calculerFrais(float $montant): float
    {
        $tranche = $this->where('min <=', $montant)
            ->where('max >=', $montant)
            ->first();

        return (float) ($tranche['gain'] ?? 0);
    }

    /**
     * Liste des tranches triées par montant croissant.
     */
    public function listeTriee(): array
    {
        return $this->orderBy('min', 'ASC')->findAll();
    }
}
