<?php

namespace App\Models;

use CodeIgniter\Model;
use RuntimeException;

class SoldeModel extends Model
{
    protected $table            = 'solde';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['idUser', 'value'];

    // La table "solde" a created_at en NOT NULL : il faut que le Model
    // le renseigne lui-même sinon l'insert échoue.
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Crée la ligne de solde initiale (0) pour un utilisateur.
     * Appelé automatiquement par UserModel::creerSoldeInitial() à la création d'un client.
     * Idempotent : ne recrée pas de ligne si une existe déjà.
     */
    public function creerSoldeInitial(int $idUser, float $valeurInitiale = 0): bool
    {
        if ($this->ligneSolde($idUser)) {
            return true;
        }

        return (bool) $this->insert([
            'idUser' => $idUser,
            'value'  => $valeurInitiale,
        ]);
    }

    /**
     * Ligne brute de solde d'un utilisateur (ou null si aucune).
     */
    public function ligneSolde(int $idUser): ?array
    {
        return $this->where('idUser', $idUser)->first();
    }

    /**
     * Valeur numérique du solde d'un utilisateur (0 si aucune ligne trouvée).
     */
    public function getValeur(int $idUser): float
    {
        return (float) ($this->ligneSolde($idUser)['value'] ?? 0);
    }

    /**
     * Vérifie que le solde couvre le montant demandé.
     */
    public function soldeSuffisant(int $idUser, float $montant): bool
    {
        return $this->getValeur($idUser) >= $montant;
    }

    /**
     * Crédite le solde d'un utilisateur (dépôt). Crée la ligne si besoin.
     * Retourne le nouveau solde.
     */
    public function depot(int $idUser, float $montant): float
    {
        if ($montant <= 0) {
            throw new RuntimeException('Le montant du dépôt doit être positif.');
        }

        $ligne = $this->ligneSolde($idUser);

        if (! $ligne) {
            $this->creerSoldeInitial($idUser);
            $ligne = $this->ligneSolde($idUser);
        }

        $nouveauSolde = (float) $ligne['value'] + $montant;

        $this->update($ligne['id'], ['value' => $nouveauSolde]);

        return $nouveauSolde;
    }

    /**
     * Débite le solde d'un utilisateur (retrait) après vérification des fonds.
     * Lève une exception si le solde est insuffisant.
     */
    public function retrait(int $idUser, float $montant): float
    {
        if ($montant <= 0) {
            throw new RuntimeException('Le montant du retrait doit être positif.');
        }

        if (! $this->soldeSuffisant($idUser, $montant)) {
            throw new RuntimeException('Solde insuffisant.');
        }

        $ligne        = $this->ligneSolde($idUser);
        $nouveauSolde = (float) $ligne['value'] - $montant;

        $this->update($ligne['id'], ['value' => $nouveauSolde]);

        return $nouveauSolde;
    }

    /**
     * Transfère un montant entre deux soldes (débit source / crédit destination),
     * dans une transaction SQL pour garantir l'atomicité.
     *
     * @param float|null $montantCredite Montant réellement crédité au destinataire
     *                                    si différent du montant débité (frais côté source).
     *                                    Par défaut, identique au montant débité.
     */
    public function transferer(int $idUserSource, int $idUserDestination, float $montantDebite, ?float $montantCredite = null): array
    {
        $montantCredite ??= $montantDebite;

        if (! $this->soldeSuffisant($idUserSource, $montantDebite)) {
            throw new RuntimeException('Solde insuffisant pour effectuer ce transfert.');
        }

        $this->db->transStart();

        $this->retrait($idUserSource, $montantDebite);
        $this->depot($idUserDestination, $montantCredite);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new RuntimeException('Le transfert a échoué, veuillez réessayer.');
        }

        return [
            'soldeSource'      => $this->getValeur($idUserSource),
            'soldeDestination' => $this->getValeur($idUserDestination),
        ];
    }
}
