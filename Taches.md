# Todo — Répartition des tâches

## Version 1

### Base commune (à faire en tout premier, avant la répartition)

- [ ] Setup projet (repo, structure app unique, connexion DB) (Andriantsoa)
- [ ] Création du schéma de base (`user`, `prefixe`, `typeOperation`, `config`, `transaction`, `solde`) (Jean)
- [ ] Seed du user admin (numéro + role `operateur` + password) (Jean)
- [ ] Page Login unique (numéro de téléphone + branchement client/operateur) (Andriantsoa)
- [ ] Middleware/guard de routing par `role` (Andriantsoa)

> Ces 5 points sont des fondations partagées. Une fois faits, les deux blocs ci-dessous sont indépendants l'un de l'autre et peuvent avancer en parallèle.

---

## Bloc 1 — Espace Client

### Pages

- [ ] Dashboard Client (`/client`) — solde, boutons raccourcis, aperçu dernières transactions (Andriantsoa)
- [ ] Page Dépôt (`/client/depot`) — montant, frais, confirmation, nouveau solde (Andriantsoa)
- [ ] Page Retrait (`/client/retrait`) — montant, vérif solde, frais, confirmation (Andriantsoa)
- [ ] Page Transfert (`/client/transfert`) — numéro destinataire, vérif préfixe, montant, frais (Andriantsoa)
- [ ] Page Historique (`/client/historique`) — liste transactions + filtres (Andriantsoa)
- [ ] Composant Modal de confirmation (réutilisable dépôt/retrait/transfert) (Andriantsoa)

### Logique / backend associé

- [ ] Fonction voir solde (Andriantsoa)
- [ ] Fonction faire dépôt (auto) (Andriantsoa)
- [ ] Fonction faire retrait (auto) (Andriantsoa)
- [ ] Fonction faire transfert (Andriantsoa)
- [ ] Fonction récupération historique par user (Andriantsoa)

---

## Bloc 2 — Espace Opérateur / Admin

### Pages

- [ ] Dashboard Opérateur (`/admin`) — cartes résumé, raccourcis (Jean)
- [ ] Gestion des Préfixes (`/admin/prefixes`) — liste + CRUD (Jean)
- [ ] Gestion des Types d'Opération (`/admin/types-operation`) — liste + toggle actif/inactif (Jean)
- [ ] Configuration des Barèmes (`/admin/types-operation/:id/config`) — tranches min/max/gain (Jean)
- [ ] Situation des Gains (`/admin/gains`) — filtres + tableau + total agrégé (Jean)
- [ ] Situation des Comptes Clients (`/admin/clients`) — liste + recherche (Jean)
- [ ] Détail Client (`/admin/clients/:id`) — infos, solde, historique complet (Jean)

### Logique / backend associé

- [ ] CRUD préfixe (validation 033/037) (Jean)
- [ ] CRUD type d'opération (Jean)
- [ ] CRUD config barème par type d'opération (Jean)
- [ ] Calcul et agrégation des gains (frais retrait/transfert) (Jean)
- [ ] Fonction liste clients + solde (Jean)


## Priorisation (délai 13h — Tag v1)

1. Base commune (les 5 points partagés)
2. En parallèle :
    - Andriantsoa → Dashboard Client + Dépôt/Retrait/Transfert + Historique
    - Jean → Dashboard Admin + Préfixes + Types d'opération + Config barème
3. Si temps restant :
    - Andriantsoa → peaufinage UI / modal de confirmation
    - Jean → Situation des gains + Situation comptes clients + Détail client
