<?php

namespace App\Models;

use CodeIgniter\Model;

class PromotionModel extends Model
{
    protected $table            = 'promotions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['pourcentage'];

    public function augProm(?int $money) {
        $promotion = $this->select('pourcentage');
        $nouvelleMoney = $money * $promotion / 100;

        return $nouvelleMoney;
    }
}
