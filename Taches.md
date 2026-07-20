# Todo - Mobile Money (etat du projet)

## Version 1 - TERMINEE

---

## Base commune

- [x] Setup projet CodeIgniter 4 (structure, config SQLite)
- [x] Schema de base : role, user, operateur, prefixe, typeOperation, config, transaction, solde, numero
- [x] Migrations dans le bon ordre
- [x] Seeder AdminUserSeeder : role admin/client, user admin (0330000000), operateur, prefixes 033/037
- [x] RoleModel, UserModel, NumeroModel, PrefixeModel fonctionnels
- [x] LoginController complet : verif prefixe, recherche/creation auto client, login admin, gestion session
- [x] Routes /login (GET+POST), /logout, groupes client (role:client) et admin (role:admin)
- [x] AuthFilter / RoleFilter fonctionnels, guard par role
- [x] Vue login stylee (Bootstrap)
- [x] Dashboard client fonctionnel (layout + affichage solde/historique)
- [x] Dashboard admin reel (cartes gains/transactions/clients)

---

## Bloc 1 - Espace Client (Jean Pierre)

- [x] SoldeModel : depot(), retrait(), transferer(), creation automatique d'une ligne solde = 0 a la creation d'un client
- [x] Client\DepotController : calcul frais via ConfigModel, mise a jour solde, insertion transaction
- [x] Client\RetraitController : idem + verification solde suffisant
- [x] Client\TransfertController : verif numero destinataire + prefixe valide, transfert entre 2 soldes
- [x] Client\HistoriqueController : liste des transactions du user connecte (sans filtres)
- [x] Vues depot.php, retrait.php, transfert.php, historique.php
- [x] Composant modal de confirmation reutilisable (components/modal_confirmation.php)
- [x] TransactionModel adapte : allowedFields corrects, colonnes idOperateur / idTypeOperation / gain / idUser
- [x] Seeder TypeOperationSeeder : Depot, Retrait, Transfert
- [x] idTypeOperation renseigne a chaque insertion de transaction (depot/retrait/transfert)

---

## Bloc 2 - Espace Operateur / Admin (Andriantsoa)

- [x] Admin\DashboardController reel (cartes resume gains/transactions/clients + dernieres transactions avec type)
- [x] Admin\PrefixeController : CRUD table prefixe
- [x] Admin\TypeOperationController : CRUD typeOperation + toggle actif/inactif
- [x] Admin\ConfigController : CRUD bareme (config: min/max/gain), sans lien a un type d'operation
- [x] Admin\GainController : situation des gains, filtres periode/type, agregation
- [x] Admin\ClientController : liste clients + recherche (nom, CIN, numero) + detail client (solde, numeros, historique)
- [x] Vues correspondantes : prefixes.php, prefixeForm.php, typesOperation.php, typeOperationForm.php, configBareme.php, configForm.php, gains.php, clientsListe.php, clientDetail.php
- [x] Layout layouts/layoutAdmin.php (Bootstrap, sans JS)
- [x] Seeder ConfigBaremeSeeder : bareme de frais par tranche de montant fourni

---

## Corrections apportees apres tests (Andiantsoa)

- [x] Fix bug "Undefined variable $modalId" (remplacement de $this->include() par view())
- [x] Fix erreur DatabaseException "near transaction: syntax error" (alias de table pour eviter le mot reserve SQLite "transaction")
- [x] Ajout du numero client dans le tableau admin clients
- [x] Simplification du bareme (retrait du champ type d'operation dans le formulaire)
- [x] Fix affichage du type d'operation vide dans le tableau de bord (seed des types + idTypeOperation renseigne a l'insertion)
- [x] Suppression des filtres dans l'historique client
- [x] Style ajoute sur la page login


## Version 2

### Cote Operateur

- [ ] Modification au niveau table prefixe : ajout d'un colonne appartenance (1 si moi sinon 0)
- [ ] Creation de table : commission (idOperateur , pourcentage)
- [ ] Dans la page Situation gain : Ajout d'autre Contenu : 
    - [ ] affichage (Total des commissions)
    - [ ] table (transaction (commission))
- [ ] Page pour afficher les listes de montant a envoyer pour chaque client
    - [ ] Affichage : Operateur , Montant
    - [ ] Fonction : sommeMontant(idOperateur) 

### Cote Client (Andriantsoa)

- [ ] Inclure un frais de retrait
  - [ ] dans view
    - [ ] ajout d'une input radio si ajoute frais
      - si oui
        - [ ] prendre la fonctoin frais()
      - si non (rien)
- [ ] Envoye multiple (Transfert)
    - [ ] Affichage  
        - [ ] Boutton pour ajouter un autre numero
        - [ ] Fonction js qui va inserer une autre formulaire de numero et de montant
        - [ ] Verification via js que les numeros sont le meme operateur 
    - [ ] Backend : 
        - [ ] Validation multiple