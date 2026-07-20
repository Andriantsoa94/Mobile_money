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

    /**
     * Résout l'id de l'opérateur correspondant au préfixe (3 premiers
     * chiffres) d'un numéro de téléphone complet. Retourne null si le
     * préfixe n'est pas reconnu.
     */
    public function trouverOperateurParNumero(string $numero): ?int
    {
        $prefixe = substr($numero, 0, 3);
        $ligne   = $this->where('numero', $prefixe)->first();

        return isset($ligne['idoperateur']) ? (int) $ligne['idoperateur'] : null;
    }
}
