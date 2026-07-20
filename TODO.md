## Todo
-``features``

-base de donnees
    -[x] creer la base MySQL bibliotheque
    -[x] verifier que la base bibliotheque est accessible

-[x] table livres
    - creer id auto-incremente (primary key)
    - creer titre
    - creer auteur
    - creer isbn (unique)
    - creer annee_publication
    - creer categorie
    - creer resume
    - creer nom_fichier_couverture
    - creer statut (disponible / prete), defaut: disponible
    - creer created_at
    - creer updated_at

-[x] table emprunts
    - creer id (primary key)
    - creer livre_id (reference vers livres.id)
    - creer nom_emprunteur
    - creer date_emprunt
    - creer date_retour (nullable)
    - ajouter la contrainte de cle etrangère livre_id -> livres.id
    - ajouter un index sur livre_id

-[x] configuration codeigniter
    - configurer la connexion MySQL dans le fichier de configuration Database

## Partie 2 - Routes

-[x] definir les routes dans app/Config/Routes.php

-[x] 2.1 page d'accueil: liste de tous les livres
    -[x] creer la route GET / vers la methode de liste
    -[x] verifier que la route affiche bien tous les livres

-[x] 2.2 fiche detaillee d'un livre
    -[x] creer la route GET /livres/(:num)
    -[x] passer l'identifiant dans l'URL
    -[x] verifier l'affichage d'un livre par id

-[x] 2.3 ajout d'un livre (2 routes)
    -[x] creer la route GET /livres/ajouter (afficher formulaire)
    -[x] creer la route POST /livres/ajouter (traiter envoi)
    -[x] verifier l'insertion après soumission

-[x] 2.4 suppression d'un livre
    -[x] creer la route POST /livres/supprimer/(:num)
    -[x] verifier la suppression par id

-[x] 2.5 prêt et retour d'un livre
    -[x] creer la route POST /livres/preter/(:num)
    -[x] creer la route POST /livres/retourner/(:num)
    -[x] verifier le changement de statut disponible/prete

-[x] verification finale des routes
    -[x] verifier les methodes HTTP (GET/POST)
    
## Partie 2 - Implementation realisee

-[x] contrôleur principal des livres
    -[x] creer app/Controllers/Livres.php
    -[x] utiliser les modèles dans le constructeur
    -[x] implementer index() pour la liste
    -[x] implementer show($id) pour le detail
    -[x] implementer create() + store() pour l'ajout
    -[x] implementer edit($id) + update($id) pour la modification
    -[x] implementer delete($id) pour la suppression
    -[x] implementer preter($id) et retourner($id)

-[x] modèles et requêtes deplacees dans les models
    -[x] creer app/Models/LivreModel.php
    -[x] ajouter getAllLivres()
    -[x] ajouter getLivreById()
    -[x] ajouter createLivre()
    -[x] ajouter updateLivre()
    -[x] ajouter deleteLivre()
    -[x] ajouter updateStatut()
    -[x] ajouter isbnExists()
    -[x] creer app/Models/EmpruntModel.php
    -[x] ajouter createEmprunt()
    -[x] ajouter getOpenEmpruntByLivreId()
    -[x] ajouter closeEmprunt()
    -[x] ajouter deleteByLivreId()

-[x] routes CRUD complètes
    -[x] ajouter GET /livres/modifier/(:num)
    -[x] ajouter POST /livres/modifier/(:num)
    -[x] garder toutes les routes existantes de la partie 2

-[x] vues CRUD
    -[x] creer app/Views/livres/index.php
    -[x] creer app/Views/livres/create.php
    -[x] creer app/Views/livres/show.php
    -[x] creer app/Views/livres/edit.php
    -[x] ajouter les liens/actions Modifier, Supprimer, Prêter, Retourner

-[x] corrections techniques
    -[x] corriger la propriete useTimestamps dans LivreModel
    -[x] corriger les types de retour des methodes du contrôleur
    -[x] verifier les routes avec php spark routes
    -[x] verifier la syntaxe PHP des fichiers modifies

## Partie 3 - Modele Livre

-[x] 3.1 config du modele livres
    -[x] declarer la table livres
    -[x] declarer la cle primaire id
    -[x] declarer les champs autorises (allowedFields)
    -[x] activer les timestamps automatiques

-[x] 3.2 validation dans le modele
    -[x] titre obligatoire
    -[x] titre minimum 3 caracteres
    -[x] auteur obligatoire
    -[x] isbn obligatoire
    -[x] isbn unique en base
    -[x] annee obligatoire
    -[x] messages d'erreur explicites en francais

-[x] 3.3 validation metier annee
    -[x] ajouter la methode isAnneePublicationValide()
    -[x] bloquer une annee de publication dans le futur
    -[x] retourner un message metier en francais si annee invalide

-[x] 3.4 recherche livres
    -[x] ajouter rechercherLivres(motCle, categorie)
    -[x] filtrer par mot-cle sur le titre
    -[x] filtrer optionnellement par categorie
    -[x] connecter la recherche a la page liste (GET q/categorie)

-[x] 3.5 pagination
    -[x] ajouter getLivresPagines(10)
    -[x] afficher 10 resultats par page
    -[x] afficher les liens de pagination dans la vue index

-[x] integration partie 3
    -[x] deplacer la validation create/update vers le modele
    -[x] simplifier le controleur pour utiliser le modele
    -[x] conserver les routes CRUD existantes

-[x] verification manuelle partie 3
    -[x] tester le message "titre min 3 caracteres"
    -[x] tester le message "isbn deja existant"
    -[x] tester le message "annee dans le futur"
    -[x] tester la recherche par titre
    -[x] tester la recherche par categorie
    -[x] tester la pagination (10 par page)

## Partie 4 - Modele Emprunts

-[x] 4.1 modele emprunts
    -[x] declarer la table emprunts
    -[x] declarer les champs autorises

-[x] 4.2 dernier emprunt par livre
    -[x] ajouter getDernierEmpruntByLivreId($livreId)
    -[x] trier par date_emprunt DESC

## Partie 5 - Controleur principal Livres

-[x] 5.1 index
    -[x] pagination des livres (10 par page)
    -[x] recherche prioritaire si q ou categorie presents dans URL
    -[x] transmettre livres + pager + filtres + categories a la vue

-[x] 5.2 detail
    -[x] recuperer un livre par id
    -[x] declencher 404 si introuvable
        - [x] personnalise 404 
    -[x] recuperer le dernier emprunt via EmpruntModel
    -[x] transmettre livre + dernierEmprunt a la vue

-[x] 5.3 formulaire
    -[x] afficher le formulaire d'ajout

-[x] 5.4 store
    -[x] recuperer les donnees POST
    -[x] valider manuellement l'annee (pas dans le futur)
    -[x] gerer l'upload de couverture (jpeg/png/webp, max 2Mo)
    -[x] stocker la couverture dans public/uploads/
    -[x] inserer via le modele LivreModel
    -[x] reafficher le formulaire + erreurs en cas d'echec
    -[x] rediriger avec message flash en cas de succes

-[x] 5.5 suppression
    -[x] supprimer le livre
    -[x] rediriger avec message flash

## Partie 6 - Controleur mouvements

-[x] 6.1 pret
    -[x] creer app/Controllers/Emprunts.php
    -[x] verifier existence du livre
    -[x] verifier disponibilite du livre
    -[x] enregistrer un nouvel emprunt (nom + date)
    -[x] passer le statut du livre a prete

-[x] 6.2 retour
    -[x] retrouver l'emprunt actif (date_retour null)
    -[x] renseigner la date de retour
    -[x] remettre le statut du livre a disponible

## Partie 7 - Vues + Templating

-[x] 7.1 layout principal
    -[x] creer app/Views/layouts/main.php
    -[x] ajouter barre de navigation
    -[x] ajouter zone des messages flash
    -[x] ajouter zone de contenu dynamique (sections)

-[x] 7.2 vue catalogue
    -[x] formulaire de recherche (texte + liste categories)
    -[x] tableau des livres (titre lien, auteur, annee, statut)
    -[x] statut colore (vert disponible / rouge prete)
    -[x] formulaire inline Pret si disponible
    -[x] bouton Retourner si deja prete
    -[x] bouton Supprimer avec confirmation JS
    -[x] pagination affichee si necessaire

-[x] 7.3 vue detail
    -[x] afficher les infos completes du livre
    -[x] afficher le dernier emprunteur + date d'emprunt

-[x] 7.4 formulaire d'ajout
    -[x] champs demandes (titre, auteur, isbn, annee, categorie, resume, couverture)
    -[x] messages d'erreur sous chaque champ
    -[x] old() pour repopulation
    -[x] jeton CSRF inclus
    -[x] annee max = annee courante cote client

## Partie 8 - Securite

-[x] 8.1 activer CSRF globalement
    -[x] csrf active dans app/Config/Filters.php (globals before)

-[x] 8.2 verifier les formulaires POST
    -[x] tous les formulaires POST contiennent csrf_field()

-[x] 8.3 protection XSS
    -[x] utiliser esc() sur les donnees affichees dans les vues

## Criteres d'evaluation - Etat actuel

-[x] code organise selon pattern MVC
-[x] vues structurees avec layout + sections
-[x] validation fichier image (type + taille)
-[x] CSRF active et appliquee sur les routes/formulaires
-[x] page 404 personnalisee avec style coherent
-[x] fonctionnement correct des operations CRUD (test manuel complet a faire)
-[x] respect complet validation + affichage erreurs (verification manuelle a finir)
-[x] gestion correcte statut pret/retour (verification manuelle a finir)
-[x] pagination visible des 11 livres (verification manuelle a faire)

## Suite TP - Rôles
-[x] Créer table users
    -[x] id auto-incrémenté (primary key)
    -[x] nom
    -[x] role (enum: utilisateur, bibliothécaire, admin)
    

-[x] login.php
  - [x] afficher formulaire
    - [x] ajout du view
    - [x] ajout des routes (post) 
      - [x] methode post
      - [x] pour le show form
    - [x] ajout controller pour user
    - [x] ajout model pour user
    - [x] se logger a partir du fomulaire
    - 


-[x] Implémenter système d'authentification
    -[x] créer app/Controllers/Auth.php
    -[x] routes login/logout dans app/Config/Routes.php
    -[x] vues login (app/Views/auth/login.php)
    -[x] gérer la session utilisateur (login/logout)
    -[x] valider email/password lors du login

-[x] page d'ajout (admin et biblio)
    -[x] masquer les bouttons par de if
    -[x] ajout routes autorise admin et biblio
     - [x] mettre les routes pour ajouter livres dans cette routes

- [x] amelioration
  - [x] changer les emplacements de
    - [x] livres
    - [x] logout
- [x] page profil avec hisotrique emprunts
  - [x] ajout table emprunts
    - [x] id emprunts 
    - [x] id livres
    - [x] id user
    - [x] enlever inputs et changer par le session conncter
    - [x] dans le profil
        - [x] voir tous les emprunts et les anciens emprunts
        - [x] modifier la table pour stocker tous les anciens emprunts
            - [x] ajout de status dans le table emprunts avec valeur 0 et 1
- [x] page 404 enlevement de certains bouttons
- [x] reparer les bouttons
  - [x] supprimer
  - [x] modifier

## Fonctionnalites avancees

- [x] Gestion avancee des emprunts
    - [x] date de retour prevue
    - [x] calcul automatique des retards
    - [x] systeme de relance: liste des emprunts en retard
    - [x] reservation d'un livre prete (file d'attente)
    - [x] historique complet des emprunts par livre
    - [x] historique complet des emprunts par emprunteur

- [x] Catalogue
    - [x] gestion des auteurs comme entite separee
    - [x] relation N:N livres-auteurs
    - [x] export du catalogue CSV
    - [x] export du catalogue PDF
    - [x] notation et commentaires sur les livres

- [x] IHM
    - [x] tri des colonnes du tableau (titre, auteur, annee)

- [x] Statistiques (admin only)
    - [x] dashboard livres les plus empruntes
    - [x] dashboard emprunteurs les plus actifs