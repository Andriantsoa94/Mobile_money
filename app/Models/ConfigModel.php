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
    // tranche. "frais" est ce que paie le client, "gain" est ce que
    // garde la plateforme. Ce sont deux valeurs indépendantes, pas de
    // calcul entre elles.
    protected $allowedFields = ['min', 'max', 'frais', 'gain'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Tranche correspondant à un montant donné (ou null si aucune ne matche).
     */
    public function trancheDe(float $montant): ?array
    {
        return $this->where('min <=', $montant)
            ->where('max >=', $montant)
            ->first();
    }

    /**
     * Frais facturé au client pour ce montant (0 si aucune tranche ne correspond).
     */
    public function calculerFrais(float $montant): float
    {
        return (float) ($this->trancheDe($montant)['frais'] ?? 0);
    }

    /**
     * Gain conservé par la plateforme pour ce montant (0 si aucune tranche ne correspond).
     */
    public function calculerGain(float $montant): float
    {
        return (float) ($this->trancheDe($montant)['gain'] ?? 0);
    }

    /**
     * Liste des tranches triées par montant croissant.
     */
    public function listeTriee(): array
    {
        return $this->orderBy('min', 'ASC')->findAll();
    }
}
