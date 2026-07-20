# Todo — Mobile Money (état du projet + répartition)

## Version 1

---

## ✅ Base commune — TERMINÉ

- [x] Setup projet CodeIgniter 4 (structure, config SQLite)
- [x] Schéma de base : `role`, `user`, `operateur`, `prefixe`, `typeOperation`, `config`, `transaction`, `solde`, `numero`
- [x] Migrations corrigées et dans le bon ordre (`role` → `user` → `operateur` → `prefixe` → `typeOperation` → `config` → `transaction` → `solde` → `numero`)
- [x] Seeder `AdminUserSeeder` : rôle admin/client, user admin (`0330000000`), opérateur, préfixes `033`/`037`
- [x] `RoleModel`, `UserModel`, `NumeroModel`, `PrefixeModel` fonctionnels (allowedFields corrects)
- [x] `LoginController` complet : vérif préfixe, recherche/création auto client, login admin, gestion session
- [x] Routes `/login` (GET+POST), `/logout`, groupes `client` (`role:client`) et `admin` (`role:admin`)
- [x] `AuthFilter` / `RoleFilter` fonctionnels, guard par rôle
- [x] Vue login + helper `form` chargé (csrf_field opérationnel)
- [x] Dashboard client minimal fonctionnel (layout + affichage solde/historique)
- [x] Dashboard admin — **stub minimal seulement**, à construire

**Testé et validé** : login admin → `/admin`, login nouveau numéro (préfixe valide) → création auto + `/client`, préfixe invalide → refusé.

> ⚠️ Base locale par personne (SQLite, fichier `writable/db/mobile.db` non versionné). Chacun fait `php spark migrate` + `php spark db:seed AdminUserSeeder` de son côté après un `git pull`.

---

## Bloc 1 — Espace Client (Jean Pierre)

### Pages / logique restantes

- [ ] `SoldeModel` : implémenter `depot()`, `retrait()`, création automatique d'une ligne `solde = 0` à la création d'un client (Jean Pierre)
- [ ] `Client\DepotController` : logique complète (calcul frais via `ConfigModel`, mise à jour solde, insertion transaction) (Jean Pierre)
- [ ] `Client\RetraitController` : idem + vérification solde suffisant (Jean Pierre)
- [ ] `Client\TransfertController` : vérif numéro destinataire + préfixe valide, transfert entre 2 soldes (Jean Pierre)
- [ ] `Client\HistoriqueController` : liste des transactions du user connecté + filtres (Jean Pierre)
- [ ] Vues `depot.php`, `retrait.php`, `transfert.php`, `historique.php` (Jean Pierre)
- [ ] Composant modal de confirmation réutilisable (Jean Pierre)
- [ ] Adapter `TransactionModel` : ajouter `allowedFields`, clarifier les colonnes utilisées (`idOperateur`, `gain`, `idUser`, pas de colonne "type d'opération" actuellement → à voir avec Andriantsoa si besoin d'ajouter une colonne `idTypeOperation`) (Jean Pierre + Andriantsoa, à se coordonner sur ce point précis uniquement)

---

## Bloc 2 — Espace Opérateur / Admin (Andriantsoa)

### Pages / logique restantes

- [ ] Vrai `Admin\DashboardController` (le stub actuel n'affiche rien de réel) — cartes résumé gains/transactions/clients (Andriantsoa)
- [ ] `Admin\PrefixeController` : CRUD table `prefixe` (Andriantsoa)
- [ ] `Admin\TypeOperationController` : CRUD `typeOperation` + toggle actif/inactif (Andriantsoa)
- [ ] `Admin\ConfigController` : CRUD barèmes (`config`: min/max/gain) par type d'opération (Andriantsoa)
- [ ] `Admin\GainController` : situation des gains, filtres période/type, agrégation (Andriantsoa)
- [ ] `Admin\ClientController` : liste clients + recherche + détail client (solde, historique) (Andriantsoa)
- [ ] Vues correspondantes : `admin/prefixes.php`, `admin/types_operation.php`, `admin/config_bareme.php`, `admin/gains.php`, `admin/clients_liste.php`, `admin/client_detail.php` (Andriantsoa)
- [ ] Layout `layouts/layout_admin.php` (n'existe pas encore, comme pour le client) (Andriantsoa)

---

## Priorisation (délai 13h — Tag v1)

1. ~~Base commune~~ ✅ fait
2. En parallèle :
   - **Jean Pierre** → Solde (dépôt/retrait/transfert fonctionnels) + Historique
   - **Andriantsoa** → Dashboard admin réel + Préfixes + Types d'opération + Config barème
3. Si temps restant :
   - Jean Pierre → modal de confirmation, polish UI client
   - Andriantsoa → Situation des gains + Situation comptes clients + Détail client

---

## Notes

- Les deux blocs restent indépendants : aucun des deux n'attend l'autre pour avancer.
- Seul point de coordination : la colonne manquante pour identifier le **type** de transaction (dépôt/retrait/transfert) dans la table `transaction` — actuellement rien ne stocke ça. À décider ensemble rapidement (ajout d'une colonne `idTypeOperation` recommandé) avant que les deux commencent à écrire dans cette table.
- Ne jamais committer `writable/db/mobile.db` dans Git — seulement les migrations.