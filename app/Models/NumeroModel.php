<?php

namespace App\Models;

use CodeIgniter\Model;

class NumeroModel extends Model
{
    protected $table         = 'numero';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['numero', 'iduser'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function findByNumero(string $numero)
    {
        return $this->where('numero', $numero)->first();
    }
}
