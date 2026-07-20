<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transaction';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = ['idOperateur', 'idTypeOperation', 'gain', 'idUser' ,'valeur'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    private function builderAvecAlias()
    {
        return $this->db->table('transaction tr');
    }

    public function pourUtilisateur(int $idUser): array
    {
        return $this->builderAvecAlias()
            ->select('tr.*, typeOperation.nom AS typeNom')
            ->join('typeOperation', 'typeOperation.id = tr.idTypeOperation', 'left')
            ->where('tr.idUser', $idUser)
            ->orderBy('tr.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function dernieresAvecClient(int $limite = 8): array
    {
        return $this->builderAvecAlias()
            ->select('tr.*, user.nom AS clientNom, typeOperation.nom AS typeNom')
            ->join('user', 'user.id = tr.idUser', 'left')
            ->join('typeOperation', 'typeOperation.id = tr.idTypeOperation', 'left')
            ->orderBy('tr.created_at', 'DESC')
            ->limit($limite)
            ->get()
            ->getResultArray();
    }

    public function nombreAujourdhui(): int
    {
        return $this->builderAvecAlias()
            ->where('DATE(tr.created_at)', date('Y-m-d'))
            ->countAllResults();
    }

    public function totalGains(array $filtres = []): float
    {
        $builder = $this->builderAvecAlias()->selectSum('tr.gain', 'total');

        if (! empty($filtres['dateDebut'])) {
            $builder = $builder->where('tr.created_at >=', $filtres['dateDebut'] . ' 00:00:00');
        }
        if (! empty($filtres['dateFin'])) {
            $builder = $builder->where('tr.created_at <=', $filtres['dateFin'] . ' 23:59:59');
        }
        if (! empty($filtres['idTypeOperation'])) {
            $builder = $builder->where('tr.idTypeOperation', $filtres['idTypeOperation']);
        }

        $ligne = $builder->get()->getRowArray();

        return (float) ($ligne['total'] ?? 0);
    }

    public function gainsParType(array $filtres = []): array
    {
        $builder = $this->builderAvecAlias()
            ->select('typeOperation.nom AS typeNom, SUM(tr.gain) AS total, COUNT(tr.id) AS nombre')
            ->join('typeOperation', 'typeOperation.id = tr.idTypeOperation', 'left')
            ->groupBy('tr.idTypeOperation');

        if (! empty($filtres['dateDebut'])) {
            $builder = $builder->where('tr.created_at >=', $filtres['dateDebut'] . ' 00:00:00');
        }
        if (! empty($filtres['dateFin'])) {
            $builder = $builder->where('tr.created_at <=', $filtres['dateFin'] . ' 23:59:59');
        }

        return $builder->get()->getResultArray();
    }
}
