# Todo — Répartition des tâches

## Version 1

### Base commune (à faire en tout premier, avant la répartition)

- [ ] Setup projet (repo, structure app unique, connexion DB) (Jean Pierre)
- [ ] Création du schéma de base (`user`, `prefixe`, `typeOperation`, `config`, `transaction`, `solde`) (Andriantsoa)
- [ ] Seed du user admin (numéro + role `operateur` + password) (Andriantsoa)
- [ ] Page Login unique (numéro de téléphone + branchement client/operateur) (Jean Pierre)
- [ ] Middleware/guard de routing par `role` (Jean Pierre)

> Ces 5 points sont des fondations partagées. Une fois faits, les deux blocs ci-dessous sont indépendants l'un de l'autre et peuvent avancer en parallèle.

---

## Bloc 1 — Espace Client

### Pages

- [ ] Dashboard Client (`/client`) — solde, boutons raccourcis, aperçu dernières transactions (Jean Pierre)
- [ ] Page Dépôt (`/client/depot`) — montant, frais, confirmation, nouveau solde (Jean Pierre)
- [ ] Page Retrait (`/client/retrait`) — montant, vérif solde, frais, confirmation (Jean Pierre)
- [ ] Page Transfert (`/client/transfert`) — numéro destinataire, vérif préfixe, montant, frais (Jean Pierre)
- [ ] Page Historique (`/client/historique`) — liste transactions + filtres (Jean Pierre)
- [ ] Composant Modal de confirmation (réutilisable dépôt/retrait/transfert) (Jean Pierre)

### Logique / backend associé

- [ ] Fonction voir solde (Jean Pierre)
- [ ] Fonction faire dépôt (auto) (Jean Pierre)
- [ ] Fonction faire retrait (auto) (Jean Pierre)
- [ ] Fonction faire transfert (Jean Pierre)
- [ ] Fonction récupération historique par user (Jean Pierre)

---

## Bloc 2 — Espace Opérateur / Admin

### Pages

- [ ] Dashboard Opérateur (`/admin`) — cartes résumé, raccourcis (Andriantsoa)
- [ ] Gestion des Préfixes (`/admin/prefixes`) — liste + CRUD (Andriantsoa)
- [ ] Gestion des Types d'Opération (`/admin/types-operation`) — liste + toggle actif/inactif (Andriantsoa)
- [ ] Configuration des Barèmes (`/admin/types-operation/:id/config`) — tranches min/max/gain (Andriantsoa)
- [ ] Situation des Gains (`/admin/gains`) — filtres + tableau + total agrégé (Andriantsoa)
- [ ] Situation des Comptes Clients (`/admin/clients`) — liste + recherche (Andriantsoa)
- [ ] Détail Client (`/admin/clients/:id`) — infos, solde, historique complet (Andriantsoa)

### Logique / backend associé

- [ ] CRUD préfixe (validation 033/037) (Andriantsoa)
- [ ] CRUD type d'opération (Andriantsoa)
- [ ] CRUD config barème par type d'opération (Andriantsoa)
- [ ] Calcul et agrégation des gains (frais retrait/transfert) (Andriantsoa)
- [ ] Fonction liste clients + solde (Andriantsoa)


## Priorisation (délai 13h — Tag v1)

1. Base commune (les 5 points partagés)
2. En parallèle :
    - Jean Pierre → Dashboard Client + Dépôt/Retrait/Transfert + Historique
    - Andriantsoa → Dashboard Admin + Préfixes + Types d'opération + Config barème
3. Si temps restant :
    - Jean Pierre → peaufinage UI / modal de confirmation
    - Andriantsoa → Situation des gains + Situation comptes clients + Détail client
