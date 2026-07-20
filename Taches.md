# Todo — Répartition des tâches

## Version 1

### Base commune (à faire en tout premier, avant la répartition)

- [ ] Setup projet (repo, structure app unique, connexion DB) (Andriantsoa)
- [x] Création du schéma de base (`user`, `prefixe`, `typeOperation`, `config`, `transaction`, `solde`) (Jean Pierre)
- [x] Seed du user admin (numéro + role `operateur` + password) (Jean Pierre)
- [ ] Page Login unique (numéro de téléphone + branchement client/operateur) (Andriantsoa)
- [x] Middleware/guard de routing par `role` (Andriantsoa)

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

- [ ] Dashboard Opérateur (`/admin`) — cartes résumé, raccourcis (Jean Pierre)
- [ ] Gestion des Préfixes (`/admin/prefixes`) — liste + CRUD (Jean Pierre)
- [ ] Gestion des Types d'Opération (`/admin/types-operation`) — liste + toggle actif/inactif (Jean Pierre)
- [ ] Configuration des Barèmes (`/admin/types-operation/:id/config`) — tranches min/max/gain (Jean Pierre)
- [ ] Situation des Gains (`/admin/gains`) — filtres + tableau + total agrégé (Jean Pierre)
- [ ] Situation des Comptes Clients (`/admin/clients`) — liste + recherche (Jean Pierre)
- [ ] Détail Client (`/admin/clients/:id`) — infos, solde, historique complet (Jean Pierre)

### Logique / backend associé

- [ ] CRUD préfixe (validation 033/037) (Jean Pierre)
- [ ] CRUD type d'opération (Jean Pierre)
- [ ] CRUD config barème par type d'opération (Jean Pierre)
- [ ] Calcul et agrégation des gains (frais retrait/transfert) (Jean Pierre)
- [ ] Fonction liste clients + solde (Jean Pierre)


## Priorisation (délai 13h — Tag v1)

1. Base commune (les 5 points partagés)
2. En parallèle :
    - Andriantsoa → Dashboard Client + Dépôt/Retrait/Transfert + Historique
    - Jean Pierre → Dashboard Admin + Préfixes + Types d'opération + Config barème
3. Si temps restant :
    - Andriantsoa → peaufinage UI / modal de confirmation
    - Jean Pierre → Situation des gains + Situation comptes clients + Détail client
