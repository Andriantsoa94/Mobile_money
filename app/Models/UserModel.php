<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'user';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['nom', 'CIN', 'idrole'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // À la création d'un utilisateur, on lui crée automatiquement une
    // ligne de solde à 0 s'il s'agit d'un client (voir creerSoldeInitial ci-dessous).
    protected $afterInsert = ['creerSoldeInitial'];

    /**
     * Callback Model : crée la ligne "solde" initiale (valeur 0) pour
     * tout nouvel utilisateur dont le rôle est "client". Si le rôle
     * n'est pas renseigné à l'insertion, on considère qu'il s'agit
     * d'un client par défaut.
     */
    protected function creerSoldeInitial(array $donnees): array
    {
        $idUser = is_array($donnees['id'] ?? null) ? ($donnees['id'][0] ?? null) : ($donnees['id'] ?? null);

        if ($idUser === null) {
            return $donnees;
        }

        $idRole = $donnees['data']['idrole'] ?? null;

        $estClient = true;
        if ($idRole !== null) {
            $role      = (new RoleModel())->find($idRole);
            $estClient = ($role['type'] ?? null) === 'client';
        }

        if ($estClient) {
            (new SoldeModel())->creerSoldeInitial((int) $idUser);
        }

        return $donnees;
    }

    public function findByRole($idRole){

        return $this->select('user.*, solde.value AS soldeValue, GROUP_CONCAT(numero.numero) AS numeros')
            ->join('solde', 'solde.idUser = user.id', 'left')
            ->join('numero', 'numero.iduser = user.id', 'left')
            ->where('user.idrole', $idRole)
            ->groupBy('user.id');
    }

    public function filtrer($builder,$recherche){
        return $builder->groupStart()
                ->like('user.nom', $recherche)
                ->orLike('user.CIN', $recherche)
                ->orLike('numero.numero', $recherche)
                ->groupEnd();
    }
}
