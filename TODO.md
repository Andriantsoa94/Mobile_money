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