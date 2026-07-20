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

    // Colonnes : idUser (client), idOperateur, idTypeOperation
    // (dépôt/retrait/transfert), gain (frais/commission perçu).
    protected $allowedFields = ['idOperateur', 'idTypeOperation', 'gain', 'idUser'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * "transaction" est un mot réservé SQLite (utilisé dans BEGIN/COMMIT
     * TRANSACTION). Dès qu'on l'utilise dans une chaîne brute (jointure,
     * fonction SUM/COUNT...), l'échappement automatique de CodeIgniter
     * peut échouer et produire "near transaction: syntax error".
     * On passe donc systématiquement par un alias ("tr") dans ces
     * requêtes personnalisées plutôt que par le nom de table brut.
     */
    private function builderAvecAlias()
    {
        return $this->db->table('transaction tr');
    }

    /**
     * Transactions d'un utilisateur, sans filtre, les plus récentes en premier.
     */
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

    /**
     * Les dernières transactions toutes confondues, avec le nom du client
     * et le type d'opération (utilisé par le dashboard admin).
     */
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

    /**
     * Nombre de transactions enregistrées à la date du jour.
     */
    public function nombreAujourdhui(): int
    {
        return $this->builderAvecAlias()
            ->where('DATE(tr.created_at)', date('Y-m-d'))
            ->countAllResults();
    }

    /**
     * Total des gains sur une période (filtres optionnels dateDebut/dateFin,
     * format Y-m-d).
     */
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

    /**
     * Gains agrégés par type d'opération sur une période.
     * Retour : liste de ['typeNom', 'total', 'nombre'].
     */
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
