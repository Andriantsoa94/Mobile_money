# Documentation PHP : Manipulation de fichiers et import CSV vers une base de données

## Table des matières

1. [Manipulation de fichiers en PHP](#1-manipulation-de-fichiers-en-php)
2. [Upload de fichiers via un formulaire](#2-upload-de-fichiers-via-un-formulaire)
3. [Lecture d'un fichier CSV](#3-lecture-dun-fichier-csv)
4. [Connexion à la base de données (PDO)](#4-connexion-à-la-base-de-données-pdo)
5. [Import complet CSV → Base de données](#5-import-complet-csv--base-de-données)
6. [Gestion des erreurs et bonnes pratiques](#6-gestion-des-erreurs-et-bonnes-pratiques)
7. [Exemple complet fonctionnel](#7-exemple-complet-fonctionnel)

---

## 1. Manipulation de fichiers en PHP

PHP fournit plusieurs fonctions natives pour lire, écrire et gérer des fichiers.

### 1.1 Ouvrir un fichier : `fopen()`

```php
$handle = fopen('fichier.txt', 'r'); // lecture
```

Modes courants :

| Mode | Description |
|------|--------------|
| `r`  | Lecture seule, le pointeur est au début |
| `w`  | Écriture seule, écrase le fichier existant |
| `a`  | Écriture seule, ajoute à la fin (append) |
| `r+` | Lecture et écriture |
| `x`  | Création, échoue si le fichier existe déjà |

### 1.2 Lire un fichier

```php
// Lire tout le contenu d'un coup
$contenu = file_get_contents('fichier.txt');

// Lire ligne par ligne
$handle = fopen('fichier.txt', 'r');
while (($ligne = fgets($handle)) !== false) {
    echo $ligne;
}
fclose($handle);

// Lire tout le fichier dans un tableau (une ligne = un élément)
$lignes = file('fichier.txt');
```

### 1.3 Écrire dans un fichier

```php
// Écrire (écrase le contenu existant)
file_put_contents('fichier.txt', 'Bonjour le monde');

// Ajouter à la fin du fichier
file_put_contents('fichier.txt', "Nouvelle ligne\n", FILE_APPEND);

// Avec fopen/fwrite
$handle = fopen('fichier.txt', 'a');
fwrite($handle, "Autre ligne\n");
fclose($handle);
```

### 1.4 Vérifier l'existence et les propriétés d'un fichier

```php
if (file_exists('fichier.txt')) {
    echo "Le fichier existe";
}

echo filesize('fichier.txt');   // taille en octets
echo filemtime('fichier.txt');  // date de dernière modification (timestamp)
```

### 1.5 Copier, déplacer, supprimer

```php
copy('source.txt', 'destination.txt');
rename('ancien_nom.txt', 'nouveau_nom.txt'); // déplace ou renomme
unlink('fichier.txt'); // supprime le fichier
```

### 1.6 Manipuler des dossiers

```php
mkdir('dossier');                 // créer un dossier
rmdir('dossier');                 // supprimer un dossier vide
scandir('dossier');               // lister le contenu d'un dossier
is_dir('dossier');                // vérifie si c'est un dossier
```

---

## 2. Upload de fichiers via un formulaire

### 2.1 Formulaire HTML

```html
<form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="file" name="mon_fichier">
    <button type="submit">Envoyer</button>
</form>
```

⚠️ L'attribut `enctype="multipart/form-data"` est **obligatoire** pour l'upload de fichiers.

### 2.2 Script PHP de réception (`upload.php`)

```php
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['mon_fichier'])) {

    $fichier = $_FILES['mon_fichier'];

    // Vérifier qu'il n'y a pas d'erreur d'upload
    if ($fichier['error'] !== UPLOAD_ERR_OK) {
        die('Erreur lors de l\'upload : ' . $fichier['error']);
    }

    // Vérifier l'extension autorisée
    $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
    if ($extension !== 'csv') {
        die('Seuls les fichiers CSV sont autorisés.');
    }

    // Déplacer le fichier temporaire vers un dossier permanent
    $destination = __DIR__ . '/uploads/' . basename($fichier['name']);
    if (move_uploaded_file($fichier['tmp_name'], $destination)) {
        echo "Fichier uploadé avec succès : $destination";
    } else {
        echo "Échec du déplacement du fichier.";
    }
}
```

`$_FILES['mon_fichier']` contient :

| Clé | Description |
|-----|--------------|
| `name` | Nom original du fichier |
| `type` | Type MIME |
| `tmp_name` | Chemin temporaire sur le serveur |
| `error` | Code d'erreur (0 = pas d'erreur) |
| `size` | Taille en octets |

---

## 3. Lecture d'un fichier CSV

### 3.1 Avec `fgetcsv()` (recommandé)

```php
$handle = fopen('donnees.csv', 'r');

// Ignorer la première ligne si elle contient les en-têtes
$entetes = fgetcsv($handle, 0, ';');

while (($ligne = fgetcsv($handle, 0, ';')) !== false) {
    print_r($ligne); // $ligne est un tableau indexé de valeurs
}

fclose($handle);
```

Paramètres importants de `fgetcsv($handle, $longueur, $separateur, $enclosure)` :
- `$separateur` : `,` ou `;` selon le format de votre CSV (Excel FR utilise souvent `;`)
- `$enclosure` : caractère d'encadrement des valeurs, par défaut `"`

### 3.2 Associer les en-têtes aux valeurs

```php
$handle = fopen('donnees.csv', 'r');
$entetes = fgetcsv($handle, 0, ';');

$donnees = [];
while (($ligne = fgetcsv($handle, 0, ';')) !== false) {
    $donnees[] = array_combine($entetes, $ligne);
}
fclose($handle);

// $donnees[0] = ['nom' => 'Dupont', 'email' => 'dupont@mail.com', ...]
```

### 3.3 Détecter automatiquement le séparateur (optionnel)

```php
function detecterSeparateur(string $ligne): string
{
    $separateurs = [',', ';', "\t"];
    $comptes = [];
    foreach ($separateurs as $sep) {
        $comptes[$sep] = substr_count($ligne, $sep);
    }
    return array_search(max($comptes), $comptes);
}
```

---

## 4. Connexion à la base de données (PDO)

PDO est recommandé car il supporte plusieurs SGBD et les requêtes préparées (sécurité contre les injections SQL).

```php
<?php
function connexionBDD(): PDO
{
    $host = 'localhost';
    $dbname = 'ma_base';
    $user = 'root';
    $password = '';

    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die('Erreur de connexion : ' . $e->getMessage());
    }
}
```

Exemple de table cible :

```sql
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    email VARCHAR(150),
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

---

## 5. Import complet CSV → Base de données

### 5.1 Principe général

1. Ouvrir le fichier CSV
2. Lire les en-têtes (première ligne)
3. Parcourir chaque ligne
4. Insérer chaque ligne dans la base via une **requête préparée**
5. Utiliser une **transaction** pour accélérer et sécuriser l'import

### 5.2 Script d'import

```php
<?php
require 'connexionBDD.php'; // contient la fonction connexionBDD()

function importerCSV(string $cheminFichier, PDO $pdo): array
{
    if (!file_exists($cheminFichier)) {
        throw new RuntimeException("Fichier introuvable : $cheminFichier");
    }

    $handle = fopen($cheminFichier, 'r');
    if ($handle === false) {
        throw new RuntimeException("Impossible d'ouvrir le fichier.");
    }

    // Lire les en-têtes
    $entetes = fgetcsv($handle, 0, ';');

    $requete = $pdo->prepare(
        "INSERT INTO utilisateurs (nom, email) VALUES (:nom, :email)"
    );

    $nbInseres = 0;
    $nbErreurs = 0;

    $pdo->beginTransaction();

    try {
        while (($ligne = fgetcsv($handle, 0, ';')) !== false) {
            // Associer les en-têtes aux colonnes (facultatif mais lisible)
            $donnees = array_combine($entetes, $ligne);

            // Validation basique
            if (empty($donnees['nom']) || empty($donnees['email'])) {
                $nbErreurs++;
                continue;
            }

            if (!filter_var($donnees['email'], FILTER_VALIDATE_EMAIL)) {
                $nbErreurs++;
                continue;
            }

            $requete->execute([
                ':nom'   => trim($donnees['nom']),
                ':email' => trim($donnees['email']),
            ]);

            $nbInseres++;
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        fclose($handle);
        throw new RuntimeException("Erreur durant l'import : " . $e->getMessage());
    }

    fclose($handle);

    return [
        'inseres' => $nbInseres,
        'erreurs' => $nbErreurs,
    ];
}

// Utilisation
$pdo = connexionBDD();
$resultat = importerCSV(__DIR__ . '/uploads/donnees.csv', $pdo);

echo "{$resultat['inseres']} lignes importées, {$resultat['erreurs']} erreurs ignorées.";
```

### 5.3 Pourquoi utiliser une transaction ?

Sans transaction, chaque `INSERT` est validé immédiatement (auto-commit), ce qui est **lent** pour de gros fichiers et **risqué** en cas d'erreur en cours de route (import partiel). Avec `beginTransaction()` / `commit()` / `rollBack()`, soit tout est importé, soit rien ne l'est.

### 5.4 Import de très gros fichiers (optimisation)

Pour des fichiers volumineux (des dizaines de milliers de lignes), valider par lots :

```php
$compteur = 0;
$pdo->beginTransaction();

while (($ligne = fgetcsv($handle, 0, ';')) !== false) {
    $requete->execute([...]);
    $compteur++;

    if ($compteur % 500 === 0) {
        $pdo->commit();
        $pdo->beginTransaction();
    }
}
$pdo->commit();
```

Augmenter aussi, si nécessaire, les limites PHP dans `php.ini` :

```ini
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
memory_limit = 256M
```

---

## 6. Gestion des erreurs et bonnes pratiques

- **Toujours utiliser des requêtes préparées** (`prepare()` + `execute()`) pour éviter les injections SQL.
- **Valider les données** avant insertion (email, format, champs vides, doublons).
- **Vérifier l'extension et le type MIME** du fichier uploadé, ne jamais faire confiance au nom de fichier envoyé par l'utilisateur.
- **Stocker les fichiers uploadés hors de la racine web** si possible, ou dans un dossier sans exécution de scripts (`.htaccess` avec `php_flag engine off`).
- **Utiliser `try/catch`** autour des opérations fichiers et base de données.
- **Fermer les ressources** (`fclose()`) même en cas d'erreur.
- **Gérer l'encodage** : si le CSV vient d'Excel, il est souvent en `ISO-8859-1` / `Windows-1252`. Convertir si besoin :

```php
$ligneUtf8 = array_map(function ($valeur) {
    return mb_convert_encoding($valeur, 'UTF-8', 'ISO-8859-1');
}, $ligne);
```

- **Éviter les doublons** avec une contrainte `UNIQUE` en base et `INSERT ... ON DUPLICATE KEY UPDATE` ou `INSERT IGNORE` si pertinent.

---

## 7. Exemple complet fonctionnel

### Structure des fichiers

```
projet/
├── index.php          (formulaire d'upload)
├── upload.php          (réception + import)
├── connexionBDD.php    (fonction de connexion PDO)
└── uploads/             (dossier de stockage)
```

### `index.php`

```html
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Import CSV</title></head>
<body>
    <h1>Importer un fichier CSV</h1>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="mon_fichier" accept=".csv" required>
        <button type="submit">Importer</button>
    </form>
</body>
</html>
```

### `connexionBDD.php`

```php
<?php
function connexionBDD(): PDO
{
    return new PDO(
        "mysql:host=localhost;dbname=ma_base;charset=utf8mb4",
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
}
```

### `upload.php`

```php
<?php
require 'connexionBDD.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['mon_fichier'])) {
    die('Aucun fichier reçu.');
}

$fichier = $_FILES['mon_fichier'];

if ($fichier['error'] !== UPLOAD_ERR_OK) {
    die('Erreur upload : ' . $fichier['error']);
}

$extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
if ($extension !== 'csv') {
    die('Seuls les fichiers .csv sont acceptés.');
}

$destination = __DIR__ . '/uploads/' . uniqid() . '.csv';

if (!move_uploaded_file($fichier['tmp_name'], $destination)) {
    die('Échec du déplacement du fichier.');
}

try {
    $pdo = connexionBDD();
    $handle = fopen($destination, 'r');
    $entetes = fgetcsv($handle, 0, ';');

    $requete = $pdo->prepare("INSERT INTO utilisateurs (nom, email) VALUES (:nom, :email)");

    $pdo->beginTransaction();
    $inseres = 0;

    while (($ligne = fgetcsv($handle, 0, ';')) !== false) {
        $donnees = array_combine($entetes, $ligne);
        if (empty($donnees['nom']) || !filter_var($donnees['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            continue;
        }
        $requete->execute([':nom' => $donnees['nom'], ':email' => $donnees['email']]);
        $inseres++;
    }

    $pdo->commit();
    fclose($handle);

    echo "$inseres lignes importées avec succès.";
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo 'Erreur : ' . $e->getMessage();
}
```

---

## Résumé des fonctions PHP utiles

| Fonction | Usage |
|----------|-------|
| `fopen()` / `fclose()` | Ouvrir/fermer un fichier |
| `fgets()` | Lire une ligne texte |
| `fgetcsv()` | Lire une ligne CSV sous forme de tableau |
| `file_get_contents()` / `file_put_contents()` | Lire/écrire tout un fichier |
| `move_uploaded_file()` | Déplacer un fichier uploadé |
| `array_combine()` | Associer en-têtes et valeurs |
| `PDO::prepare()` / `execute()` | Requêtes préparées sécurisées |
| `beginTransaction()` / `commit()` / `rollBack()` | Transactions SQL |
