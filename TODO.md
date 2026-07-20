# Todo

### Version 1

**Operateur**

- [] Configuration prefixe valable (033 ,037)
- [] Creation type d'operation (depot ,retrait ,transfert)
    - Bareme voir dans le docs de Mr
- [] Gain via fraits
- [] Situation comptes clients

**Client**

- [] Login auto avec telephone (pas d'inscription)
- [] operation
    - voir solde
    - faire depot
    - faire retrait
    - faire transfert
    - voir l'historique

**Base**

- user (nom, CIN, role, idNumero)
- role (type) => client , admin
- numero (numero ,idClient)

- prefixe (num ,idOperateur) => valide sa tsia
- operateur (nom)
- typeOperation (libelle ,boolean)
- config (min ,max ,gain)
- transaction (operation ,gain ,user ,datetime)
- solde (idUser ,value)

**À faire**

- operateur
    - user admin
    - dashboard
      - 
    - crud prefixes
    - type d'operation (+ config)
    - config frais operation
    - situation gains
    - situation compte clients
- clients
    - login numero
        - dashboard
          - 
    - solde , depot ,transfert ,retrait ,voir historique

- role (opérateur, clients)
    - par rapport a son numero (faire table user (numero ,type))
- faire la config pour n'avoir que les (033, 037)

## Liste des pages à faire

### Pages publiques

- [ ] **Login** (`/login`) — unique pour tout le monde
    - champ numéro de téléphone
    - branchement conditionnel : operateur → mot de passe, client existant → dashboard, nouveau → vérif préfixe +
      création auto

### Espace Client (`/client/*`)

- [ ] **Dashboard Client** (`/client`)
    - solde affiché en gros
    - boutons raccourcis : Dépôt / Retrait / Transfert / Historique
    - aperçu des 3-5 dernières transactions
- [ ] **Dépôt** (`/client/depot`)
    - champ montant
    - affichage frais/gain selon barème
    - bouton confirmer (traitement auto)
    - résultat : nouveau solde
- [ ] **Retrait** (`/client/retrait`)
    - champ montant
    - vérification solde suffisant
    - affichage frais appliqué
    - résultat : nouveau solde
- [ ] **Transfert** (`/client/transfert`)
    - champ numéro destinataire (vérifie préfixe valide + existence)
    - champ montant
    - affichage frais
    - résultat : solde émetteur mis à jour
- [ ] **Historique** (`/client/historique`)
    - liste des transactions (type, montant, frais, date/heure)
    - filtres simples (type, période)

### Espace Opérateur/Admin (`/admin/*`)

- [ ] **Dashboard Opérateur** (`/admin`)
    - cartes résumé : gain total (jour/mois), nb transactions, nb clients actifs
    - raccourcis vers les autres sections admin
- [ ] **Gestion des Préfixes** (`/admin/prefixes`)
    - liste des préfixes (033, 037…) avec statut
    - ajout / édition / suppression
- [ ] **Gestion des Types d'Opération** (`/admin/types-operation`)
    - liste des types (Dépôt, Retrait, Transfert) avec toggle actif/inactif
    - ajout / édition
    - lien vers config barème de chaque type
- [ ] **Configuration des Barèmes** (`/admin/types-operation/:id/config`)
    - tableau des tranches (min, max, gain) pour le type sélectionné
    - ajout / édition / suppression de tranche
- [ ] **Situation des Gains** (`/admin/gains`)
    - filtres par période et type d'opération
    - tableau des transactions avec gain généré
    - total agrégé
- [ ] **Situation des Comptes Clients** (`/admin/clients`)
    - liste des clients (numéro, nom, solde)
    - recherche par numéro
    - clic → détail client
- [ ] **Détail Client** (`/admin/clients/:id`)
    - infos client (nom, CIN, numéro)
    - solde actuel
    - historique complet de ses transactions

### Composant transverse

- [ ] **Écran/Modal de confirmation** (réutilisé pour dépôt/retrait/transfert)
    - récap montant, type, frais, nouveau solde, date/heure


### Structure 
mvola-app/
├── app/
│   ├── Config/
│   │   ├── Routes.php                 # routes définies ici (Anriantsoa + Jean Pierre, sections séparées)
│   │   ├── Database.php
│   │   └── Filters.php                # enregistrement du filtre AuthFilter / RoleFilter
│   │
│   ├── Controllers/
│   │   ├── AuthController.php         # (Anriantsoa) login unique numéro + password admin
│   │   ├── Client/
│   │   │   ├── DashboardController.php    # (Anriantsoa)
│   │   │   ├── DepotController.php        # (Anriantsoa)
│   │   │   ├── RetraitController.php      # (Anriantsoa)
│   │   │   ├── TransfertController.php    # (Anriantsoa)
│   │   │   └── HistoriqueController.php   # (Anriantsoa)
│   │   └── Admin/
│   │       ├── DashboardController.php    # (Jean Pierre)
│   │       ├── PrefixeController.php      # (Jean Pierre)
│   │       ├── TypeOperationController.php # (Jean Pierre)
│   │       ├── ConfigController.php       # (Jean Pierre) barèmes
│   │       ├── GainController.php         # (Jean Pierre)
│   │       └── ClientController.php       # (Jean Pierre) situation comptes clients
│   │
│   ├── Models/
│   │   ├── UserModel.php              # (base commune)
│   │   ├── PrefixeModel.php           # (Jean Pierre)
│   │   ├── TypeOperationModel.php     # (Jean Pierre)
│   │   ├── ConfigModel.php            # (Jean Pierre)
│   │   ├── TransactionModel.php       # (partagé - lecture Anriantsoa / Jean Pierre, écriture Anriantsoa)
│   │   └── SoldeModel.php             # (Anriantsoa)
│   │
│   ├── Filters/
│   │   ├── AuthFilter.php             # (base commune) vérifie session
│   │   └── RoleFilter.php             # (base commune) vérifie role client/operateur
│   │
│   ├── Views/
│   │   ├── auth/
│   │   │   └── login.php              # (Anriantsoa)
│   │   ├── client/
│   │   │   ├── dashboard.php          # (Anriantsoa)
│   │   │   ├── depot.php              # (Anriantsoa)
│   │   │   ├── retrait.php            # (Anriantsoa)
│   │   │   ├── transfert.php          # (Anriantsoa)
│   │   │   ├── historique.php         # (Anriantsoa)
│   │   │   └── partials/
│   │   │       └── confirmation_modal.php  # (Anriantsoa)
│   │   ├── admin/
│   │   │   ├── dashboard.php          # (Jean Pierre)
│   │   │   ├── prefixes.php           # (Jean Pierre)
│   │   │   ├── types_operation.php    # (Jean Pierre)
│   │   │   ├── config_bareme.php      # (Jean Pierre)
│   │   │   ├── gains.php              # (Jean Pierre)
│   │   │   ├── clients_liste.php      # (Jean Pierre)
│   │   │   └── client_detail.php      # (Jean Pierre)
│   │   └── layouts/
│   │       ├── layout_client.php      # (Anriantsoa)
│   │       └── layout_admin.php       # (Jean Pierre)
│   │
│   └── Database/
│       ├── Migrations/
│       │   ├── 001_CreateUserTable.php        # (base commune)
│       │   ├── 002_CreatePrefixeTable.php     # (Jean Pierre)
│       │   ├── 003_CreateTypeOperationTable.php # (Jean Pierre)
│       │   ├── 004_CreateConfigTable.php      # (Jean Pierre)
│       │   ├── 005_CreateTransactionTable.php # (Anriantsoa)
│       │   └── 006_CreateSoldeTable.php       # (Anriantsoa)
│       └── Seeds/
│           └── AdminUserSeeder.php    # (Jean Pierre) crée le user admin whitelisté
│
├── public/
│   ├── index.php
│   └── assets/
│       ├── css/
│       └── js/
│
├── tests/
├── writable/
└── .env