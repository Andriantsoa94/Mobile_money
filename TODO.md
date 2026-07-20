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