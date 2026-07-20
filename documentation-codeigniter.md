# Documentation CodeIgniter 4 : Les essentiels

> Cette documentation couvre CodeIgniter 4 (la version actuelle). Si vous utilisez CodeIgniter 3, la syntaxe diffère sur plusieurs points (signalés dans le texte).

## Table des matières

1. [Structure d'un projet CodeIgniter](#1-structure-dun-projet-codeigniter)
2. [Les Routes](#2-les-routes)
3. [Les Controllers](#3-les-controllers)
4. [Les Models](#4-les-models)
5. [Le Query Builder (dialecte SQL)](#5-le-query-builder-dialecte-sql)
6. [Les Migrations](#6-les-migrations)
7. [Les Seeds (données de test)](#7-les-seeds-données-de-test)
8. [Les Vues](#8-les-vues)
9. [Gestion des rôles et permissions](#9-gestion-des-rôles-et-permissions)
10. [Validation des données](#10-validation-des-données)
11. [Sessions et authentification de base](#11-sessions-et-authentification-de-base)
12. [Filtres (Filters) — middlewares](#12-filtres-filters--middlewares)
13. [Configuration de la base de données](#13-configuration-de-la-base-de-données)
14. [Tous les types de retours possibles dans un Controller](#14-tous-les-types-de-retours-possibles-dans-un-controller)
15. [Toutes les façons de transférer des données vers les Vues](#15-toutes-les-façons-de-transférer-des-données-vers-les-vues)
16. [Helpers (fonctions utilitaires)](#16-helpers-fonctions-utilitaires)
17. [Libraries et Services](#17-libraries-et-services)
18. [Upload et gestion de fichiers](#18-upload-et-gestion-de-fichiers)
19. [Pagination](#19-pagination)
20. [API REST avec ResourceController](#20-api-rest-avec-resourcecontroller)
21. [Gestion des erreurs et exceptions](#21-gestion-des-erreurs-et-exceptions)
22. [Fichier .env et environnements](#22-fichier-env-et-environnements)
23. [Cache](#23-cache)
24. [Envoi d'e-mails](#24-envoi-demails)
25. [Événements (Events)](#25-événements-events)
26. [Tâches planifiées et commandes CLI (spark)](#26-tâches-planifiées-et-commandes-cli-spark)
27. [CSRF et sécurité des formulaires](#27-csrf-et-sécurité-des-formulaires)
28. [Tests unitaires de base](#28-tests-unitaires-de-base)
29. [Bonnes pratiques générales](#29-bonnes-pratiques-générales)

---

## 1. Structure d'un projet CodeIgniter

```
app/
├── Config/           → fichiers de configuration (BDD, routes, autoload...)
├── Controllers/       → contrôleurs
├── Models/            → modèles (accès aux données)
├── Views/             → vues (HTML/PHP)
├── Filters/           → middlewares (auth, rôles, CORS...)
├── Database/
│   ├── Migrations/    → fichiers de migration
│   └── Seeds/         → fichiers de seed
├── Libraries/          → classes utilitaires personnalisées
└── Helpers/            → fonctions utilitaires globales
public/
└── index.php           → point d'entrée de l'application
```

**Règle générale (architecture MVC)** :
- **Model** : tout ce qui touche aux données (requêtes SQL, validation liée aux données, relations).
- **View** : uniquement de l'affichage (HTML), pas de logique métier.
- **Controller** : reçoit la requête HTTP, appelle le Model, prépare les données, envoie à la Vue. Le controller ne doit **jamais** contenir de requêtes SQL directes.

---

## 2. Les Routes

Fichier : `app/Config/Routes.php`

### 2.1 Routes de base

```php
<?php
// app/Config/Routes.php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Home::index');
$routes->get('produits', 'ProduitController::index');
$routes->get('produits/(:num)', 'ProduitController::show/$1');
$routes->post('produits', 'ProduitController::create');
$routes->put('produits/(:num)', 'ProduitController::update/$1');
$routes->delete('produits/(:num)', 'ProduitController::delete/$1');
```

`(:num)` capture un nombre, `(:any)` capture n'importe quel segment, `(:segment)` capture un segment sans slash.

### 2.2 Routes nommées

```php
$routes->get('produits/(:num)', 'ProduitController::show/$1', ['as' => 'produit.show']);
```

Utilisation dans une vue : `<a href="<?= route_to('produit.show', $produit['id']) ?>">`

### 2.3 Groupes de routes (avec préfixe et filtre)

```php
$routes->group('admin', ['filter' => 'role:admin'], function ($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');
    $routes->get('utilisateurs', 'Admin\UtilisateurController::index');
});
```

Toutes les routes de ce groupe seront préfixées par `/admin/...` et protégées par le filtre `role:admin` (voir section 9).

### 2.4 Routes RESTful automatiques (resource)

```php
$routes->resource('produits', ['controller' => 'ProduitController']);
```

Cela crée automatiquement les routes `index`, `new`, `create`, `show`, `edit`, `update`, `delete` selon les conventions REST.

### 2.5 Routes avec espace de noms (namespace)

```php
$routes->get('api/produits', 'Api\ProduitController::index');
```

---

## 3. Les Controllers

Fichier : `app/Controllers/ProduitController.php`

### 3.1 Ce qu'un controller doit contenir

- Récupérer les données de la requête (`$this->request`)
- Appeler le(s) Model(s) pour lire/écrire des données
- Valider les entrées (ou déléguer à un service)
- Choisir la vue à afficher et lui transmettre les données
- **Ne jamais** écrire de requêtes SQL directement

### 3.2 Exemple de controller complet

```php
<?php

namespace App\Controllers;

use App\Models\ProduitModel;

class ProduitController extends BaseController
{
    protected ProduitModel $produitModel;

    public function __construct()
    {
        $this->produitModel = new ProduitModel();
    }

    // GET /produits
    public function index()
    {
        $produits = $this->produitModel->findAll();
        return view('produits/index', ['produits' => $produits]);
    }

    // GET /produits/{id}
    public function show($id)
    {
        $produit = $this->produitModel->find($id);

        if ($produit === null) {
            return redirect()->back()->with('error', 'Produit introuvable');
        }

        return view('produits/show', ['produit' => $produit]);
    }

    // POST /produits
    public function create()
    {
        $regles = [
            'nom'   => 'required|min_length[3]|max_length[100]',
            'prix'  => 'required|decimal',
        ];

        if (!$this->validate($regles)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->produitModel->save([
            'nom'  => $this->request->getPost('nom'),
            'prix' => $this->request->getPost('prix'),
        ]);

        return redirect()->to('/produits')->with('success', 'Produit créé avec succès');
    }

    // PUT /produits/{id}
    public function update($id)
    {
        $produit = $this->produitModel->find($id);
        if ($produit === null) {
            return redirect()->to('/produits')->with('error', 'Produit introuvable');
        }

        $this->produitModel->update($id, [
            'nom'  => $this->request->getVar('nom'),
            'prix' => $this->request->getVar('prix'),
        ]);

        return redirect()->to('/produits')->with('success', 'Produit mis à jour');
    }

    // DELETE /produits/{id}
    public function delete($id)
    {
        $this->produitModel->delete($id);
        return redirect()->to('/produits')->with('success', 'Produit supprimé');
    }
}
```

### 3.3 Récupérer les données de la requête

```php
$this->request->getPost('nom');       // donnée POST
$this->request->getGet('recherche');  // donnée GET (query string)
$this->request->getVar('nom');        // POST ou GET, peu importe
$this->request->getJSON();            // corps JSON (pour API)
$this->request->getFile('image');     // fichier uploadé
```

### 3.4 Réponses JSON (API)

```php
public function apiIndex()
{
    $produits = $this->produitModel->findAll();
    return $this->response->setJSON($produits);
}
```

---

## 4. Les Models

Fichier : `app/Models/ProduitModel.php`

### 4.1 Ce qu'un model doit contenir

- Le nom de la table, la clé primaire, les champs autorisés (`allowedFields`)
- Les règles de validation propres aux données
- Les méthodes personnalisées de requêtes (jointures, filtres, agrégations)
- Les callbacks (`beforeInsert`, `afterUpdate`, etc.)

### 4.2 Exemple de model

```php
<?php

namespace App\Models;

use CodeIgniter\Model;

class ProduitModel extends Model
{
    protected $table            = 'produits';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // ou 'object', ou une classe Entity
    protected $useSoftDeletes   = false;

    protected $allowedFields = ['nom', 'prix', 'categorie_id', 'stock'];

    // Timestamps automatiques
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation directement dans le model
    protected $validationRules = [
        'nom'  => 'required|min_length[3]|max_length[100]',
        'prix' => 'required|decimal',
    ];

    protected $validationMessages = [
        'nom' => [
            'required' => 'Le nom du produit est obligatoire.',
        ],
    ];

    // Méthode personnalisée : produits d'une catégorie
    public function findByCategorie(int $categorieId): array
    {
        return $this->where('categorie_id', $categorieId)->findAll();
    }

    // Méthode avec jointure
    public function findAvecCategorie(): array
    {
        return $this->select('produits.*, categories.nom as categorie_nom')
                     ->join('categories', 'categories.id = produits.categorie_id')
                     ->findAll();
    }
}
```

### 4.3 Méthodes intégrées les plus utilisées

```php
$model->find();                 // tous les enregistrements
$model->find(5);                // un enregistrement par id
$model->findAll();               // équivalent à find() sans argument
$model->first();                 // premier résultat
$model->where('stock >', 0)->findAll();
$model->save($data);             // insert ou update automatique
$model->insert($data);
$model->update($id, $data);
$model->delete($id);
$model->countAllResults();
```

### 4.4 Callbacks (hooks) du model

```php
protected $beforeInsert = ['hasherMotDePasse'];

protected function hasherMotDePasse(array $data)
{
    if (isset($data['data']['mot_de_passe'])) {
        $data['data']['mot_de_passe'] = password_hash($data['data']['mot_de_passe'], PASSWORD_DEFAULT);
    }
    return $data;
}
```

---

## 5. Le Query Builder (dialecte SQL)

CodeIgniter fournit un **Query Builder** qui génère du SQL de façon sécurisée (échappement automatique).

### 5.1 SELECT

```php
$db = \Config\Database::connect();
$builder = $db->table('produits');

$query = $builder->select('id, nom, prix')
                  ->where('prix >', 100)
                  ->orderBy('nom', 'ASC')
                  ->limit(10)
                  ->get();

$resultats = $query->getResultArray(); // ou getResult() pour objets
```

### 5.2 WHERE, jointures, groupBy

```php
$builder->where('categorie_id', 3)
        ->orWhere('stock', 0)
        ->whereIn('statut', ['actif', 'en_attente'])
        ->like('nom', 'chaise')
        ->join('categories', 'categories.id = produits.categorie_id', 'left')
        ->groupBy('categorie_id')
        ->having('COUNT(id) >', 5);
```

### 5.3 INSERT / UPDATE / DELETE

```php
$builder->insert(['nom' => 'Chaise', 'prix' => 49.99]);

$builder->where('id', 5)->update(['prix' => 59.99]);

$builder->where('id', 5)->delete();
```

### 5.4 Requêtes SQL brutes (quand nécessaire)

```php
$db = \Config\Database::connect();
$query = $db->query('SELECT * FROM produits WHERE prix > ?', [100]);
$resultats = $query->getResultArray();
```

⚠️ Toujours utiliser des **paramètres liés** (`?` ou binds nommés) plutôt que de concaténer des variables dans la requête, pour éviter les injections SQL.

### 5.5 Transactions

```php
$db = \Config\Database::connect();
$db->transStart();

$builder->insert(['nom' => 'Table']);
$builder->insert(['nom' => 'Chaise']);

$db->transComplete();

if ($db->transStatus() === false) {
    // rollback automatique en cas d'erreur
}
```

---

## 6. Les Migrations

Les migrations permettent de versionner la structure de la base de données (comme Git, mais pour le schéma SQL).

### 6.1 Créer une migration

```bash
php spark make:migration CreateProduitsTable
```

Cela crée un fichier dans `app/Database/Migrations/` nommé avec un timestamp, ex : `2026-07-19-100000_CreateProduitsTable.php`.

### 6.2 Contenu d'une migration

```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProduitsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nom' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'prix' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'categorie_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true); // clé primaire
        $this->forge->addForeignKey('categorie_id', 'categories', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('produits');
    }

    public function down()
    {
        $this->forge->dropTable('produits');
    }
}
```

- `up()` : ce qui se passe quand on applique la migration
- `down()` : comment annuler la migration (rollback)

### 6.3 Modifier une table existante

```php
public function up()
{
    $this->forge->addColumn('produits', [
        'stock' => ['type' => 'INT', 'default' => 0, 'after' => 'prix'],
    ]);
}

public function down()
{
    $this->forge->dropColumn('produits', 'stock');
}
```

### 6.4 Commandes essentielles

```bash
php spark migrate                  # applique toutes les migrations en attente
php spark migrate:rollback         # annule le dernier lot de migrations
php spark migrate:refresh          # rollback total puis ré-applique tout
php spark migrate:status           # affiche l'état des migrations
```

---

## 7. Les Seeds (données de test)

Les seeds permettent d'insérer des données de départ (ex : rôles par défaut, utilisateur admin, données de démonstration).

### 7.1 Créer un seed

```bash
php spark make:seeder ProduitSeeder
```

Fichier créé : `app/Database/Seeds/ProduitSeeder.php`

### 7.2 Contenu d'un seed

```php
<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProduitSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['nom' => 'Chaise',  'prix' => 49.99,  'stock' => 20],
            ['nom' => 'Table',   'prix' => 149.99, 'stock' => 5],
            ['nom' => 'Armoire', 'prix' => 299.99, 'stock' => 2],
        ];

        // Insertion en masse
        $this->db->table('produits')->insertBatch($data);
    }
}
```

### 7.3 Seed avec Faker (données aléatoires)

```php
public function run()
{
    $faker = \Faker\Factory::create('fr_FR');

    for ($i = 0; $i < 50; $i++) {
        $this->db->table('produits')->insert([
            'nom'  => $faker->words(2, true),
            'prix' => $faker->randomFloat(2, 5, 500),
        ]);
    }
}
```

### 7.4 Seed principal (DatabaseSeeder) qui appelle les autres

```php
<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('RoleSeeder');
        $this->call('ProduitSeeder');
        $this->call('UtilisateurSeeder');
    }
}
```

### 7.5 Exécuter les seeds

```bash
php spark db:seed DatabaseSeeder
php spark db:seed ProduitSeeder
```

---

## 8. Les Vues

Fichier : `app/Views/produits/index.php`

```php
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Produits</title></head>
<body>
    <h1>Liste des produits</h1>

    <?php if (session()->getFlashdata('success')): ?>
        <p style="color:green"><?= session()->getFlashdata('success') ?></p>
    <?php endif; ?>

    <ul>
        <?php foreach ($produits as $produit): ?>
            <li><?= esc($produit['nom']) ?> — <?= esc($produit['prix']) ?> €</li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
```

⚠️ Toujours utiliser `esc()` pour afficher des données utilisateur (protection contre les failles XSS).

### 8.1 Layouts réutilisables

```php
// app/Views/layouts/main.php
<!DOCTYPE html>
<html>
<head><title><?= $titre ?? 'Mon site' ?></title></head>
<body>
    <?= $this->renderSection('contenu') ?>
</body>
</html>
```

```php
// app/Views/produits/index.php
<?= $this->extend('layouts/main') ?>
<?= $this->section('contenu') ?>
    <h1>Produits</h1>
<?= $this->endSection() ?>
```

---

## 9. Gestion des rôles et permissions

CodeIgniter n'a pas de système de rôles intégré : on le construit soi-même (ou via une librairie comme Shield). Voici l'approche manuelle la plus courante.

### 9.1 Structure de base de données

```php
// Migration : table roles
$this->forge->addField([
    'id'   => ['type' => 'INT', 'auto_increment' => true],
    'nom'  => ['type' => 'VARCHAR', 'constraint' => 50], // ex: admin, editeur, client
]);
$this->forge->addKey('id', true);
$this->forge->createTable('roles');

// Migration : ajouter role_id sur la table utilisateurs
$this->forge->addColumn('utilisateurs', [
    'role_id' => ['type' => 'INT', 'null' => true, 'after' => 'email'],
]);
```

### 9.2 Model utilisateur avec rôle

```php
class UtilisateurModel extends Model
{
    protected $table = 'utilisateurs';
    protected $allowedFields = ['nom', 'email', 'mot_de_passe', 'role_id'];

    public function getAvecRole(int $id)
    {
        return $this->select('utilisateurs.*, roles.nom as role_nom')
                     ->join('roles', 'roles.id = utilisateurs.role_id')
                     ->find($id);
    }
}
```

### 9.3 Filtre (middleware) de vérification de rôle

Fichier : `app/Filters/RoleFilter.php`

```php
<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        $roleUtilisateur = $session->get('role');

        // $arguments contient les rôles autorisés, ex: ['admin']
        if ($arguments && !in_array($roleUtilisateur, $arguments)) {
            return redirect()->to('/')->with('error', 'Accès refusé.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // rien après la requête
    }
}
```

### 9.4 Enregistrer le filtre

Fichier : `app/Config/Filters.php`

```php
public array $aliases = [
    // ... autres filtres existants
    'role' => \App\Filters\RoleFilter::class,
];
```

### 9.5 Utiliser le filtre sur des routes

```php
// Une seule route protégée
$routes->get('admin/dashboard', 'Admin\DashboardController::index', ['filter' => 'role:admin']);

// Un groupe entier protégé, accessible par plusieurs rôles
$routes->group('gestion', ['filter' => 'role:admin,editeur'], function ($routes) {
    $routes->get('produits', 'GestionController::produits');
});
```

### 9.6 Vérifier un rôle directement dans une vue

```php
<?php if (session()->get('role') === 'admin'): ?>
    <a href="/admin/dashboard">Panneau d'administration</a>
<?php endif; ?>
```

> Pour un système plus robuste avec permissions fines (pas seulement des rôles), on utilise généralement une table `permissions` liée à `roles` via une table pivot `role_permissions`, ou la librairie officielle **CodeIgniter Shield**.

---

## 10. Validation des données

### 10.1 Dans le controller

```php
$regles = [
    'email' => 'required|valid_email|is_unique[utilisateurs.email]',
    'mot_de_passe' => 'required|min_length[8]',
];

if (!$this->validate($regles)) {
    return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
}
```

### 10.2 Règles courantes

| Règle | Description |
|-------|--------------|
| `required` | champ obligatoire |
| `min_length[n]` / `max_length[n]` | longueur |
| `valid_email` | format email valide |
| `is_unique[table.colonne]` | unicité en base |
| `numeric` / `integer` / `decimal` | type numérique |
| `matches[autre_champ]` | doit correspondre à un autre champ (ex: confirmation mot de passe) |

---

## 11. Sessions et authentification de base

### 11.1 Démarrer une session à la connexion

```php
public function login()
{
    $email = $this->request->getPost('email');
    $motDePasse = $this->request->getPost('mot_de_passe');

    $utilisateur = $this->utilisateurModel->where('email', $email)->first();

    if ($utilisateur && password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
        session()->set([
            'isLoggedIn' => true,
            'user_id'    => $utilisateur['id'],
            'role'       => $utilisateur['role_id'],
        ]);
        return redirect()->to('/dashboard');
    }

    return redirect()->back()->with('error', 'Identifiants invalides');
}
```

### 11.2 Déconnexion

```php
public function logout()
{
    session()->destroy();
    return redirect()->to('/login');
}
```

---

## 12. Filtres (Filters) — middlewares

Les filtres sont l'équivalent des **middlewares** dans d'autres frameworks. Ils interceptent une requête **avant** qu'elle n'atteigne le controller (`before`) et/ou **après** que le controller a généré sa réponse (`after`). C'est l'endroit idéal pour : l'authentification, la vérification des rôles, les logs, le CORS, le rate limiting, la compression de réponse, etc.

### 12.1 Anatomie d'un filtre

Un filtre est une classe qui implémente `FilterInterface` et possède deux méthodes obligatoires : `before()` et `after()`.

```php
<?php
// app/Filters/AuthFilter.php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Exécuté AVANT que la requête n'atteigne le controller.
     * Peut bloquer la requête en retournant une redirection ou une réponse.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            // On peut retourner une redirection : le controller ne sera jamais appelé
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        // Si on ne retourne rien, la requête continue normalement vers le controller
    }

    /**
     * Exécuté APRÈS que le controller a généré sa réponse.
     * Utile pour modifier la réponse (headers, logs, compression...).
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Exemple : ajouter un header de sécurité à toutes les réponses
        $response->setHeader('X-Frame-Options', 'DENY');
    }
}
```

- `before()` peut **stopper** l'exécution en retournant une réponse (redirection, JSON d'erreur, etc.). Si `before()` ne retourne rien (`null`), le flux continue normalement.
- `after()` reçoit en plus la réponse déjà générée et peut la modifier avant qu'elle ne soit envoyée au navigateur.
- `$arguments` contient les paramètres passés au filtre depuis les routes (voir 12.4).

### 12.2 Déclarer le filtre (alias)

Chaque filtre doit être enregistré avec un nom court (alias) dans `app/Config/Filters.php` :

```php
<?php
// app/Config/Filters.php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
    // Liste de tous les filtres disponibles avec leur alias
    public array $aliases = [
        'csrf'      => \CodeIgniter\Filters\CSRF::class,       // filtre intégré
        'toolbar'   => \CodeIgniter\Filters\DebugToolbar::class, // filtre intégré
        'honeypot'  => \CodeIgniter\Filters\Honeypot::class,     // anti-spam intégré
        'cors'      => \CodeIgniter\Filters\Cors::class,         // intégré
        'auth'      => \App\Filters\AuthFilter::class,           // personnalisé
        'role'      => \App\Filters\RoleFilter::class,           // personnalisé
        'invitesSeulement' => \App\Filters\GuestFilter::class,   // personnalisé
    ];

    // Filtres appliqués automatiquement à TOUTES les requêtes
    public array $globals = [
        'before' => [
            // 'csrf',      // décommenter pour activer la protection CSRF partout
            // 'honeypot',
        ],
        'after' => [
            'toolbar', // barre de debug, uniquement visible en dev
        ],
    ];

    // Filtres appliqués uniquement à certaines méthodes HTTP
    public array $methods = [
        'post' => ['csrf'], // CSRF uniquement sur les requêtes POST
    ];

    // Filtres appliqués à des URL précises (par motif), sans passer par les routes
    public array $filters = [
        'auth' => ['before' => ['admin/*', 'compte/*']],
        'cors' => ['before' => ['api/*'], 'after' => ['api/*']],
    ];
}
```

### 12.3 Appliquer un filtre sur une route précise

C'est la méthode la plus courante et la plus lisible :

```php
// app/Config/Routes.php

// Une seule route protégée
$routes->get('compte/parametres', 'CompteController::parametres', ['filter' => 'auth']);

// Un groupe entier de routes protégé
$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');
    $routes->get('utilisateurs', 'Admin\UtilisateurController::index');
});

// Plusieurs filtres appliqués en même temps (tableau)
$routes->group('admin', ['filter' => ['auth', 'role:admin']], function ($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');
});
```

### 12.4 Passer des arguments à un filtre

Les arguments sont passés après `:` et séparés par des virgules. Ils arrivent dans `$arguments` sous forme de tableau.

```php
$routes->get('admin/produits', 'ProduitController::index', ['filter' => 'role:admin,editeur']);
```

```php
public function before(RequestInterface $request, $arguments = null)
{
    // $arguments = ['admin', 'editeur']
    $roleUtilisateur = session()->get('role');

    if (!in_array($roleUtilisateur, $arguments ?? [])) {
        return redirect()->to('/')->with('error', 'Accès refusé.');
    }
}
```

### 12.5 Ordre d'exécution des filtres

1. Filtres **globaux** `before` (dans l'ordre déclaré dans `$globals`)
2. Filtres liés à la **méthode HTTP** (`$methods`)
3. Filtres liés au **motif d'URL** (`$filters`)
4. Filtres **de route** (déclarés dans `Routes.php`)
5. → Exécution du **controller**
6. Filtres de route `after`
7. Filtres liés au motif d'URL `after`
8. Filtres globaux `after`

### 12.6 Filtres intégrés utiles

| Alias | Rôle |
|-------|------|
| `csrf` | Protection contre les attaques CSRF sur les formulaires |
| `honeypot` | Piège anti-bot invisible sur les formulaires |
| `cors` | Gestion des en-têtes CORS pour les API |
| `throttle` | Limitation du nombre de requêtes (rate limiting) |
| `toolbar` | Barre de débogage (uniquement en développement) |
| `pagecache` | Mise en cache de pages complètes |
| `forcehttps` | Force la redirection vers HTTPS |

### 12.7 Exemple de filtre de rate limiting

```php
<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ThrottleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $limiteur = Services::throttler();

        // Autorise 10 requêtes par minute par IP
        if ($limiteur->check(md5($request->getIPAddress()), 10, MINUTE) === false) {
            return service('response')
                ->setStatusCode(429)
                ->setJSON(['erreur' => 'Trop de requêtes, réessayez plus tard.']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
```

### 12.8 Exclure une route d'un filtre global

Si un filtre est global mais que certaines routes doivent y échapper :

```php
public array $globals = [
    'before' => [
        'auth' => ['except' => ['login', 'inscription', 'mot-de-passe-oublie']],
    ],
];
```

---

## 13. Configuration de la base de données

### 13.1 Le fichier `app/Config/Database.php`

C'est le fichier central qui définit les **groupes de connexion** à la base de données. Chaque groupe est un tableau de paramètres.

```php
<?php
// app/Config/Database.php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    // Groupe utilisé par défaut dans toute l'application
    public string $defaultGroup = 'default';

    public array $default = [
        'DSN'      => '',
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'ma_base',
        'DBDriver' => 'MySQLi',   // MySQLi, Postgre, SQLite3, SQLSRV, OCI8...
        'DBPrefix' => '',          // préfixe de table optionnel, ex: 'ci_'
        'pConnect' => false,       // connexion persistante
        'DBDebug'  => true,        // affiche les erreurs SQL (à désactiver en prod)
        'charset'  => 'utf8mb4',
        'DBCollat' => 'utf8mb4_general_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 3306,
    ];

    // Groupe utilisé automatiquement pendant l'exécution des tests (php spark test)
    public array $tests = [
        'DSN'      => '',
        'hostname' => '127.0.0.1',
        'username' => '',
        'password' => '',
        'database' => ':memory:',
        'DBDriver' => 'SQLite3',
        'DBPrefix' => 'db_',
        'pConnect' => false,
        'DBDebug'  => true,
        'charset'  => 'utf8',
        'DBCollat' => '',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 3306,
    ];

    public function __construct()
    {
        parent::__construct();

        // Charge automatiquement les valeurs depuis le .env si présentes
        // (elles écrasent celles définies ci-dessus)
    }
}
```

### 13.2 Configurer via le fichier `.env` (recommandé)

Plutôt que de modifier `Database.php` directement (ce qui finit versionné dans Git avec vos identifiants), on utilise le fichier `.env` à la racine du projet, qui **surcharge automatiquement** les valeurs du groupe correspondant :

```env
# .env

database.default.hostname = localhost
database.default.database = ma_base
database.default.username = root
database.default.password = motdepasse_secret
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port     = 3306
```

La convention de nommage est : `database.<nom_du_groupe>.<parametre>`.

⚠️ Le fichier `.env` ne doit **jamais** être versionné (ajoutez-le à `.gitignore`). On versionne à la place un fichier `env` (exemple) que chaque développeur copie en `.env` localement.

### 13.3 Plusieurs connexions (groupes multiples)

Utile si votre application doit lire/écrire sur plusieurs bases (ex: base principale + base de logs, ou base externe en lecture seule) :

```env
# .env
database.default.hostname = localhost
database.default.database = ma_base

database.logs.hostname = localhost
database.logs.database = ma_base_logs
database.logs.DBDriver = MySQLi
```

Utilisation d'un groupe spécifique dans un Model :

```php
class LogModel extends Model
{
    protected $DBGroup = 'logs'; // ce model utilisera le groupe "logs" au lieu de "default"
    protected $table   = 'evenements';
}
```

Ou directement via une connexion manuelle :

```php
$dbLogs = \Config\Database::connect('logs');
```

### 13.4 Configuration des Migrations

Fichier : `app/Config/Migrations.php`

```php
<?php
// app/Config/Migrations.php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Migrations extends BaseConfig
{
    // Active/désactive complètement le système de migrations
    public bool $enabled = true;

    // Nom de la table qui stocke l'historique des migrations déjà appliquées
    public string $table = 'migrations';

    // Groupe de BDD utilisé pour exécuter les migrations
    public string $DBGroup = 'default';

    // Format du timestamp utilisé dans le nom des fichiers de migration
    // ex: 2026-07-19-143000_CreateProduitsTable.php
    public string $timestampFormat = 'Y-m-d-His_';
}
```

Points clés :
- `$table` : CodeIgniter crée automatiquement une table `migrations` qui garde la trace de ce qui a déjà été exécuté, pour ne jamais rejouer deux fois la même migration.
- `$DBGroup` : permet de faire migrer un groupe de BDD différent du groupe par défaut (utile en multi-bases).
- On peut aussi préciser un groupe au moment de l'exécution en CLI :

```bash
php spark migrate -g logs           # migre uniquement le groupe "logs"
php spark migrate --all             # migre TOUS les groupes détectés
php spark migrate -n App             # migre uniquement les migrations du namespace "App"
```

### 13.5 Migrations et namespaces (utile pour les modules ou packages)

Par défaut, CodeIgniter cherche les migrations dans `app/Database/Migrations`. Si vous développez un module ou utilisez un package tiers avec ses propres migrations, il faut déclarer le namespace correspondant dans `app/Config/Autoload.php` :

```php
public $psr4 = [
    'App'           => APPPATH,
    'Modules\Blog'  => ROOTPATH . 'modules/Blog',
];
```

CodeIgniter cherchera alors aussi dans `modules/Blog/Database/Migrations`.

### 13.6 Configuration des Seeds

Il n'existe pas de fichier de configuration dédié aux seeds comme pour les migrations : ils suivent simplement l'autoloading PSR-4 standard. Deux points de configuration à connaître :

**a) Emplacement des fichiers**

Par défaut : `app/Database/Seeds/`. Vous pouvez organiser vos seeds en sous-dossiers avec un namespace :

```php
// app/Database/Seeds/Roles/RoleSeeder.php
namespace App\Database\Seeds\Roles;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('roles')->insertBatch([
            ['nom' => 'admin'],
            ['nom' => 'editeur'],
            ['nom' => 'client'],
        ]);
    }
}
```

Exécution avec le namespace complet :

```bash
php spark db:seed "App\Database\Seeds\Roles\RoleSeeder"
```

**b) Choisir le groupe de base de données utilisé par un seed**

Chaque seeder a accès à `$this->db`, qui pointe par défaut sur le groupe `default`. Pour cibler un autre groupe :

```php
class LogSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect('logs');
        $db->table('evenements')->insert(['message' => 'Initialisation']);
    }
}
```

### 13.7 Résumé des commandes liées à la config BDD/migrations/seeds

```bash
php spark migrate                  # applique les migrations en attente (groupe par défaut)
php spark migrate -g nom_groupe    # applique sur un groupe spécifique
php spark migrate:rollback         # annule le dernier lot appliqué
php spark migrate:refresh          # rollback complet + ré-application
php spark migrate:status           # affiche l'état (appliquées / en attente)
php spark db:seed NomDuSeeder      # exécute un seeder précis
```

### 13.8 Bonnes pratiques spécifiques

- Ne jamais mettre `DBDebug => false` en développement (vous perdriez les messages d'erreur SQL utiles).
- Toujours mettre `DBDebug => false` en **production** (sinon les erreurs SQL détaillées s'affichent aux visiteurs, ce qui est une faille de sécurité).
- Utiliser un utilisateur MySQL dédié avec des droits limités (pas `root`) en production.
- Garder les migrations **petites et ordonnées** : une migration = un changement de schéma logique (une table, ou l'ajout d'une colonne), jamais un gros script fourre-tout.
- Ne jamais modifier une migration déjà appliquée en production : créer une **nouvelle** migration pour corriger.

---

## 14. Tous les types de retours possibles dans un Controller

Une méthode de controller peut retourner (ou envoyer) une réponse de plusieurs façons différentes selon le besoin.

### 14.1 Retourner une vue (HTML)

```php
public function index()
{
    return view('produits/index', ['produits' => $produits]);
}
```

### 14.2 Retourner du texte brut

```php
public function ping()
{
    return 'pong'; // CodeIgniter accepte un simple return string
}
```

### 14.3 Retourner un objet Response personnalisé

```php
public function exemple()
{
    return $this->response
                 ->setStatusCode(200)
                 ->setBody('Contenu personnalisé');
}
```

### 14.4 Retourner du JSON (API)

```php
public function apiIndex()
{
    $produits = $this->produitModel->findAll();
    return $this->response->setJSON($produits);
}

// Avec code de statut HTTP
public function apiShow($id)
{
    $produit = $this->produitModel->find($id);

    if ($produit === null) {
        return $this->response->setJSON(['erreur' => 'Introuvable'])->setStatusCode(404);
    }

    return $this->response->setJSON($produit)->setStatusCode(200);
}
```

### 14.5 Retourner du XML

```php
public function exportXml()
{
    $produits = $this->produitModel->findAll();
    return $this->response
                 ->setXML($produits) // nécessite un tableau formaté ou une lib XML
                 ->setHeader('Content-Type', 'application/xml');
}
```

### 14.6 Redirection

```php
// Redirection simple
return redirect()->to('/produits');

// Redirection vers une route nommée
return redirect()->route('produit.show', [$id]);

// Redirection vers la page précédente
return redirect()->back();

// Redirection avec données flash (message de succès/erreur)
return redirect()->to('/produits')->with('success', 'Produit ajouté');

// Redirection en conservant les anciennes valeurs du formulaire (utile après une erreur)
return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
```

### 14.7 Téléchargement de fichier

```php
public function telecharger($id)
{
    $fichier = $this->fichierModel->find($id);
    return $this->response->download($fichier['chemin'], null);
}

// Avec un contenu généré dynamiquement (ex: export CSV)
public function exportCsv()
{
    $contenu = "id,nom,prix\n1,Chaise,49.99\n";
    return $this->response
                 ->setHeader('Content-Type', 'text/csv')
                 ->setHeader('Content-Disposition', 'attachment; filename="export.csv"')
                 ->setBody($contenu);
}
```

### 14.8 Réponse avec code de statut HTTP explicite

```php
public function creerProduit()
{
    // ... création ...
    return $this->response->setStatusCode(201)->setJSON($produit); // 201 Created
}

public function nonAutorise()
{
    return $this->response->setStatusCode(403, 'Accès refusé')->setBody('Interdit');
}
```

Codes HTTP courants à utiliser :

| Code | Usage |
|------|-------|
| `200` | OK (succès général) |
| `201` | Ressource créée |
| `204` | Succès sans contenu à retourner |
| `400` | Requête invalide |
| `401` | Non authentifié |
| `403` | Non autorisé (authentifié mais accès refusé) |
| `404` | Ressource introuvable |
| `422` | Erreur de validation |
| `500` | Erreur serveur |

### 14.9 Retourner `null` / ne rien retourner

Une méthode peut aussi ne rien `return` explicitement : CodeIgniter enverra alors une réponse vide avec un code 200. À éviter sauf cas très spécifiques (ex: webhook qui doit juste accuser réception).

### 14.10 Lever une exception HTTP (pages d'erreur automatiques)

```php
use CodeIgniter\Exceptions\PageNotFoundException;

public function show($id)
{
    $produit = $this->produitModel->find($id);

    if ($produit === null) {
        throw new PageNotFoundException("Produit $id introuvable");
    }

    return view('produits/show', ['produit' => $produit]);
}
```

### 14.11 Réponse en flux (streaming) — cas avancé

```php
public function stream()
{
    return $this->response->setBody(fopen('gros_fichier.csv', 'r'));
}
```

---

## 15. Toutes les façons de transférer des données vers les Vues

### 15.1 Passer un tableau associatif (méthode standard)

```php
public function index()
{
    $data = [
        'titre'    => 'Liste des produits',
        'produits' => $this->produitModel->findAll(),
    ];
    return view('produits/index', $data);
}
```

Dans la vue, chaque clé devient une variable : `<?= $titre ?>`, `<?= $produits ?>`.

### 15.2 Passer des données directement en ligne

```php
return view('produits/index', ['produits' => $produits, 'total' => count($produits)]);
```

### 15.3 Utiliser `view_cell()` pour un composant réutilisable avec ses propres données

```php
// Dans une vue
<?= view_cell('App\Libraries\PanierCell::afficher', ['userId' => $userId]) ?>
```

```php
// app/Libraries/PanierCell.php
namespace App\Libraries;

class PanierCell
{
    public function afficher($params)
    {
        $model = new \App\Models\PanierModel();
        $items = $model->where('user_id', $params['userId'])->findAll();
        return view('composants/panier', ['items' => $items]);
    }
}
```

### 15.4 Données de session (accessibles dans toutes les vues sans les passer explicitement)

```php
// Dans le controller
session()->set('nom_utilisateur', 'Jean');

// Dans n'importe quelle vue
<?= session()->get('nom_utilisateur') ?>
```

### 15.5 Flashdata (données temporaires, disponibles une seule fois après redirection)

```php
// Controller
return redirect()->to('/produits')->with('success', 'Produit créé !');

// Vue
<?php if (session()->getFlashdata('success')): ?>
    <p><?= session()->getFlashdata('success') ?></p>
<?php endif; ?>
```

### 15.6 Variables globales partagées à toutes les vues (via BaseController)

```php
// app/Controllers/BaseController.php
protected function partagerDonnees()
{
    $this->data = [
        'nomSite' => 'Ma Boutique',
        'anneeEnCours' => date('Y'),
    ];
}
```

```php
// Dans un controller enfant
public function index()
{
    return view('produits/index', array_merge($this->data, ['produits' => $produits]));
}
```

### 15.7 Passer des données via un layout avec `sections`

```php
// Controller
return view('produits/index', ['produits' => $produits]);
```

```php
// Vue enfant (produits/index.php)
<?= $this->extend('layouts/main') ?>

<?= $this->section('contenu') ?>
    <?php foreach ($produits as $produit): ?>
        <p><?= esc($produit['nom']) ?></p>
    <?php endforeach; ?>
<?= $this->endSection() ?>
```

### 15.8 Passer des objets Entity (plutôt que des tableaux)

Si le model utilise `protected $returnType = 'App\Entities\Produit';` :

```php
public function show($id)
{
    $produit = $this->produitModel->find($id); // retourne un objet Entity
    return view('produits/show', ['produit' => $produit]);
}
```

```php
// Vue
<p><?= esc($produit->nom) ?></p>
<p><?= esc($produit->prix) ?> €</p>
```

### 15.9 Configuration globale accessible dans les vues

```php
// Dans une vue, accéder directement à un fichier de config
$config = config('App'); 
echo $config->baseURL;
```

### 15.10 Passer des données en JSON pour les utiliser en JavaScript côté vue

```php
// Controller
return view('produits/index', ['produitsJson' => json_encode($produits)]);
```

```php
// Vue
<script>
    const produits = <?= $produitsJson ?>;
    console.log(produits);
</script>
```

### 15.11 Résumé comparatif

| Méthode | Portée | Cas d'usage |
|---------|--------|--------------|
| `view($nom, $data)` | Une seule vue | Cas standard |
| Session (`session()->set()`) | Toute l'application, persistant | Infos utilisateur connecté |
| Flashdata | Une seule requête suivante | Messages de succès/erreur après redirection |
| Variables partagées (BaseController) | Toutes les vues d'un controller | Nom du site, menu, année |
| `view_cell()` | Composant isolé | Widgets réutilisables (panier, notifications) |
| Entity | Une vue | Objets avec logique métier (méthodes, casts) |

---

## 16. Helpers (fonctions utilitaires)

Les helpers sont des ensembles de fonctions globales (pas de classe) chargées à la demande.

### 16.1 Charger un helper

```php
// Dans un controller
helper('url');   // charge le helper URL
helper(['form', 'text']); // plusieurs helpers d'un coup
```

Ou pour les charger automatiquement partout : `app/Config/Autoload.php`

```php
public $helpers = ['url', 'form', 'text'];
```

### 16.2 Helpers courants

```php
site_url('produits');          // génère une URL absolue
base_url('assets/style.css');  // URL vers un fichier public
anchor('produits', 'Voir les produits'); // génère un <a>
form_open('produits/create');  // ouvre un formulaire avec CSRF automatique
form_close();
esc($valeur);                  // échapper une sortie (anti-XSS)
word_limiter($texte, 20);      // limiter un texte à N mots
time_elapsed_string($date);    // "il y a 3 jours"
```

### 16.3 Créer un helper personnalisé

Fichier : `app/Helpers/mon_helper.php`

```php
<?php

if (!function_exists('prix_formate')) {
    function prix_formate(float $prix): string
    {
        return number_format($prix, 2, ',', ' ') . ' €';
    }
}
```

```php
helper('mon');
echo prix_formate(1999.9); // "1 999,90 €"
```

---

## 17. Libraries et Services

### 17.1 Services intégrés (via `Config\Services`)

```php
$request  = service('request');
$response = service('response');
$session  = service('session');
$validation = service('validation');
```

### 17.2 Créer une Library personnalisée

Fichier : `app/Libraries/PdfGenerator.php`

```php
<?php

namespace App\Libraries;

class PdfGenerator
{
    public function generer(string $titre, array $donnees): string
    {
        // logique de génération PDF (ex: avec mPDF ou Dompdf)
        return "chemin/vers/fichier.pdf";
    }
}
```

Utilisation dans un controller :

```php
use App\Libraries\PdfGenerator;

public function exporter()
{
    $pdf = new PdfGenerator();
    $chemin = $pdf->generer('Facture', $donnees);
    return $this->response->download($chemin, null);
}
```

---

## 18. Upload et gestion de fichiers

```php
public function uploadImage()
{
    $fichier = $this->request->getFile('image');

    if ($fichier->isValid() && !$fichier->hasMoved()) {
        $nouveauNom = $fichier->getRandomName();
        $fichier->move(WRITEPATH . 'uploads', $nouveauNom);

        $this->produitModel->update($id, ['image' => $nouveauNom]);
    }

    return redirect()->back()->with('success', 'Image envoyée');
}
```

Validation d'upload dans les règles :

```php
$regles = [
    'image' => 'uploaded[image]|is_image[image]|max_size[image,2048]|mime_in[image,image/jpg,image/jpeg,image/png]',
];
```

---

## 19. Pagination

```php
// Model / Controller
public function index()
{
    $model = new \App\Models\ProduitModel();
    $data['produits'] = $model->paginate(10); // 10 par page
    $data['pager']    = $model->pager;

    return view('produits/index', $data);
}
```

```php
// Vue
<?php foreach ($produits as $produit): ?>
    <p><?= esc($produit['nom']) ?></p>
<?php endforeach; ?>

<?= $pager->links() ?>
```

---

## 20. API REST avec ResourceController

Pour construire une API REST rapidement, CodeIgniter propose `ResourceController`.

```php
<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProduitModel;

class ProduitApiController extends ResourceController
{
    protected $modelName = ProduitModel::class;
    protected $format    = 'json';

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $produit = $this->model->find($id);
        if ($produit === null) {
            return $this->failNotFound("Produit $id introuvable");
        }
        return $this->respond($produit);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        $this->model->insert($data);
        return $this->respondCreated($data);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);
        $this->model->update($id, $data);
        return $this->respondUpdated($data);
    }

    public function delete($id = null)
    {
        $this->model->delete($id);
        return $this->respondDeleted(['id' => $id]);
    }
}
```

Routes associées :

```php
$routes->resource('api/produits', ['controller' => 'Api\ProduitApiController']);
```

Méthodes de réponse utiles de `ResourceController` :

| Méthode | Code HTTP |
|---------|-----------|
| `respond($data)` | 200 |
| `respondCreated($data)` | 201 |
| `respondUpdated($data)` | 200 |
| `respondDeleted($data)` | 200 |
| `failNotFound($message)` | 404 |
| `failValidationErrors($errors)` | 400 |
| `failUnauthorized($message)` | 401 |
| `failForbidden($message)` | 403 |

---

## 21. Gestion des erreurs et exceptions

### 21.1 Try/catch classique

```php
public function create()
{
    try {
        $this->produitModel->insert($data);
    } catch (\Exception $e) {
        log_message('error', $e->getMessage());
        return redirect()->back()->with('error', 'Une erreur est survenue.');
    }
}
```

### 21.2 Logs

```php
log_message('info', 'Produit créé avec succès');
log_message('error', 'Erreur : ' . $e->getMessage());
log_message('debug', 'Valeur : ' . print_r($data, true));
```

Configuration des logs : `app/Config/Logger.php`

### 21.3 Pages d'erreur personnalisées

```
app/Views/errors/html/error_404.php
app/Views/errors/html/error_exception.php
```

---

## 22. Fichier .env et environnements

```env
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = ma_base
database.default.username = root
database.default.password = secret
```

Accéder à une variable d'environnement en PHP :

```php
$valeur = env('APP_CLE_API', 'valeur_par_defaut');
```

Adapter le comportement selon l'environnement :

```php
if (ENVIRONMENT === 'production') {
    // désactiver l'affichage des erreurs détaillées
}
```

---

## 23. Cache

```php
$cache = \Config\Services::cache();

// Enregistrer en cache pour 300 secondes
$cache->save('produits_populaires', $produits, 300);

// Récupérer
$donnees = $cache->get('produits_populaires');
if ($donnees === null) {
    $donnees = $this->produitModel->findPopulaires();
    $cache->save('produits_populaires', $donnees, 300);
}

// Supprimer
$cache->delete('produits_populaires');
```

---

## 24. Envoi d'e-mails

```php
$email = \Config\Services::email();

$email->setTo('client@example.com');
$email->setFrom('contact@monsite.com', 'Ma Boutique');
$email->setSubject('Confirmation de commande');
$email->setMessage(view('emails/confirmation', ['commande' => $commande]));

if ($email->send()) {
    // succès
} else {
    log_message('error', $email->printDebugger());
}
```

Configuration SMTP : `app/Config/Email.php`

---

## 25. Événements (Events)

Les événements permettent d'exécuter du code automatiquement à certains moments du cycle de vie de l'application.

```php
// app/Config/Events.php
use CodeIgniter\Events\Events;

Events::on('post_controller', function () {
    log_message('info', 'Un controller vient de s\'exécuter');
});
```

Déclencher un événement personnalisé :

```php
Events::trigger('produit_cree', $produit);
```

---

## 26. Tâches planifiées et commandes CLI (spark)

### 26.1 Créer une commande personnalisée

```bash
php spark make:command NettoyerLogs
```

```php
<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class NettoyerLogs extends BaseCommand
{
    protected $group       = 'app';
    protected $name        = 'app:nettoyer-logs';
    protected $description = 'Supprime les anciens fichiers de logs';

    public function run(array $params)
    {
        CLI::write('Nettoyage en cours...', 'yellow');
        // logique de nettoyage
        CLI::write('Terminé.', 'green');
    }
}
```

Exécution :

```bash
php spark app:nettoyer-logs
```

### 26.2 Commandes spark essentielles

```bash
php spark serve                     # lance le serveur de développement
php spark make:controller Nom       # créer un controller
php spark make:model Nom            # créer un model
php spark make:migration Nom        # créer une migration
php spark make:seeder Nom           # créer un seeder
php spark migrate                   # exécuter les migrations
php spark db:seed Nom               # exécuter un seeder
php spark routes                    # lister toutes les routes définies
php spark clear:logs                # vider les logs
```

---

## 27. CSRF et sécurité des formulaires

```php
// app/Config/Filters.php
public array $globals = [
    'before' => ['csrf'],
];
```

Dans chaque formulaire :

```php
<form method="post" action="/produits">
    <?= csrf_field() ?>
    <input type="text" name="nom">
</form>
```

Ou automatiquement avec `form_open()` (helper `form`), qui insère le champ CSRF pour vous.

---

## 28. Tests unitaires de base

```php
<?php

namespace Tests;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\ProduitModel;

class ProduitModelTest extends CIUnitTestCase
{
    public function testTrouveProduit()
    {
        $model = new ProduitModel();
        $produit = $model->find(1);
        $this->assertNotNull($produit);
    }
}
```

Exécution :

```bash
php spark test
```

---

## 29. Bonnes pratiques générales

- **Controllers minces, Models gras** : la logique d'accès aux données va dans le Model, pas dans le Controller.
- Toujours utiliser le **Query Builder** ou des requêtes préparées, jamais de concaténation directe de variables dans du SQL.
- Utiliser `esc()` dans les vues pour échapper les sorties (anti-XSS).
- Activer le filtre `csrf` pour tous les formulaires POST/PUT/DELETE.
- Versionner la structure de la BDD avec des **migrations**, jamais de modifications manuelles en production.
- Utiliser les **seeds** uniquement pour les environnements de développement/test (sauf données de référence comme les rôles).
- Nommer les routes et utiliser `route_to()` plutôt que des URLs codées en dur dans les vues.
- Séparer les routes par groupes/namespaces (`admin`, `api`, `client`) pour la lisibilité et la sécurité.
- Utiliser des codes HTTP cohérents dans les réponses d'API.
- Ne jamais versionner le fichier `.env` (le mettre dans `.gitignore`), utiliser `.env.example` comme modèle.
- Journaliser (`log_message`) les erreurs importantes plutôt que de les afficher directement à l'utilisateur en production.