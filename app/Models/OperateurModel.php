<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurModel extends Model
{
    protected $table         = 'operateur';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['nom','appartenance'];
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function listeAutreOperateurSansCommission(){
        return $this->select('operateur.*')
            ->join('comission', 'comission.idOperateur = operateur.id', 'left')
            ->where('operateur.appartenance', 0)
            ->where('comission.idOperateur IS NULL', null, false)
            ->findAll();
    }
}
