<?php

namespace App\Models;

use CodeIgniter\Model;

class SoldeModel extends Model
{
    protected $table            = 'solde';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['idUser' ,'value'];

    public function getSolde($idUser) {
        return $this->where('idUser', $idUser);
    }

    public function depot($idUser ,$montant) {
        $soldeActuel = $this->where('idUser', $idUser);

    }
}
