<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table         = 'prefixe';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['numero', 'idoperateur', 'appartenance'];
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
     * Ligne de préfixe correspondant aux 3 premiers chiffres d'un numéro.
     */
    public function parNumero(string $numero): ?array
    {
        $prefixe = substr($numero, 0, 3);
        return $this->where('numero', $prefixe)->first();
    }

    /**
     * Résout l'id de l'opérateur correspondant au préfixe d'un numéro.
     * Retourne null si le préfixe n'est pas reconnu.
     */
    public function trouverOperateurParNumero(string $numero): ?int
    {
        $ligne = $this->parNumero($numero);

        return isset($ligne['idoperateur']) ? (int) $ligne['idoperateur'] : null;
    }

    /**
     * Indique si le numéro appartient à NOTRE opérateur (appartenance = 1)
     * ou à un autre opérateur (appartenance = 0). Retourne false si le
     * préfixe n'est pas reconnu.
     */
    public function appartientANous(string $numero): bool
    {
        $ligne = $this->parNumero($numero);

        return isset($ligne['appartenance']) && (int) $ligne['appartenance'] === 1;
    }

    public function findAllMe()
    {
        return $this
            ->select('prefixe.*, operateur.nom AS operateurNom')
            ->join('operateur', 'operateur.id = prefixe.idoperateur', 'left')
            ->orderBy('prefixe.numero', 'ASC')
            ->find();
    }
}
