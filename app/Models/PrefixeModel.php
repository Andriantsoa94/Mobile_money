<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table            = 'prefixe';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['num', 'idOperateur'];
    protected $useTimestamps    = false;

    public function estValide(string $prefixe3chiffres): bool
    {
        return $this->where('num', $prefixe3chiffres)->first() !== null;
    }

    public function listeActifs(): array
    {
        return $this->select('num')->findAll();
    }
}