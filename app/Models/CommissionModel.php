<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionModel extends Model
{
    // La table est nommée "comission" en base (orthographe de la migration).
    protected $table         = 'comission';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['idOperateur', 'commission', 'pourcentage'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ligne de commission configurée pour un opérateur donné (ou null).
     */
    public function pourOperateurLigne(?int $idOperateur): ?array
    {
        if ($idOperateur === null) {
            return null;
        }

        return $this->where('idOperateur', $idOperateur)->first();
    }

    /**
     * Commission totale à appliquer pour un transfert vers cet opérateur :
     * un montant fixe + un pourcentage du montant transféré.
     * Retourne 0 si aucune commission n'est configurée pour cet opérateur.
     */
    public function pourOperateur(?int $idOperateur, float $montant = 0): float
    {
        $ligne = $this->pourOperateurLigne($idOperateur);

        if (! $ligne) {
            return 0.0;
        }

        $fixe        = (float) ($ligne['commission'] ?? 0);
        $pourcentage = (float) ($ligne['pourcentage'] ?? 0);

        return $fixe + ($montant * $pourcentage / 100);
    }

    /**
     * Liste des commissions configurées, avec le nom de l'opérateur.
     */
    public function listeAvecOperateur(): array
    {
        return $this
            ->select('comission.*, operateur.nom AS operateurNom')
            ->join('operateur', 'operateur.id = comission.idOperateur', 'left')
            ->orderBy('operateur.nom', 'ASC')
            ->find();
    }
}
