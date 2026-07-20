<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table         = 'role';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['type'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function findByType(string $type)
    {
        return $this->where('type', $type)->first();
    }

    public function totalClients()
    {
        $userModel = new UserModel();
        $roleClient = $this->findByType('client');
        if ($roleClient) {
            return $userModel->where('idrole', $roleClient['id'])->countAllResults();
        }
        return 0;
    }
}
