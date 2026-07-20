<?php

namespace App\Models;

use CodeIgniter\Model;

class AuteurModel extends Model
{
    protected $table = 'auteurs';

    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';

    protected $protectFields = true;

    protected $allowedFields = [
        'nom',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';

    public function findOrCreateByName(string $nom): int
    {
        $nom = trim($nom);

        $existing = $this->where('nom', $nom)->first();

        if ($existing !== null) {
            return (int) $existing['id'];
        }

        $this->insert(['nom' => $nom]);

        return (int) $this->getInsertID();
    }
}
