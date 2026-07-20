<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table         = 'prefixe';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['numero', 'idoperateur'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Vérifie si un préfixe (3 premiers chiffres) existe en base
     */
    public function estValide(string $prefixe3chiffres): bool
    {
        return $this->where('numero', $prefixe3chiffres)->first() !== null;
    }

    /**
     * Récupère tous les préfixes valides (utile pour le JS côté client)
     */
    public function listeActifs(): array
    {
        return $this->select('numero')->findAll();
    }
}
