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

    public function estValide(string $prefixe3chiffres): bool
    {
        return (bool) preg_match('/^0[0-9]{2}$/', $prefixe3chiffres);
    }

    public function listeActifs(): array
    {
        return $this->select('numero')->findAll();
    }

    public function parNumero(string $numero): ?array
    {
        $prefixe = substr($numero, 0, 3);
        return $this->where('numero', $prefixe)->first();
    }

    public function trouverOperateurParNumero(string $numero): ?int
    {
        $ligne = $this->parNumero($numero);

        return isset($ligne['idoperateur']) ? (int) $ligne['idoperateur'] : null;
    }

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
