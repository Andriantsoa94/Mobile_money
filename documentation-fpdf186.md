# Documentation FPDF 1.86 : Générer des PDF en PHP

> FPDF est une librairie PHP pure (sans dépendances) permettant de générer des documents PDF à la volée. Version couverte : **1.86**.

## Table des matières

1. [Installation](#1-installation)
2. [Créer un PDF minimal](#2-créer-un-pdf-minimal)
3. [Configuration de la page](#3-configuration-de-la-page)
4. [Les polices (fonts)](#4-les-polices-fonts)
5. [Écrire du texte : Cell, MultiCell, Write](#5-écrire-du-texte--cell-multicell-write)
6. [Couleurs](#6-couleurs)
7. [Dessiner : lignes, rectangles, formes](#7-dessiner--lignes-rectangles-formes)
8. [Images](#8-images)
9. [Faire un tableau (table)](#9-faire-un-tableau-table)
10. [Liens et hyperliens](#10-liens-et-hyperliens)
11. [Gestion des pages, sauts de page et en-têtes/pieds de page](#11-gestion-des-pages-sauts-de-page-et-en-têtespieds-de-page)
12. [Positionnement (X, Y)](#12-positionnement-x-y)
13. [Sortie du PDF (afficher, télécharger, sauvegarder)](#13-sortie-du-pdf-afficher-télécharger-sauvegarder)
14. [Exemple complet : facture avec tableau](#14-exemple-complet--facture-avec-tableau)
15. [Récapitulatif de toutes les méthodes utiles](#15-récapitulatif-de-toutes-les-méthodes-utiles)
16. [Bonnes pratiques et pièges courants](#16-bonnes-pratiques-et-pièges-courants)

---

## 1. Installation

### 1.1 Téléchargement manuel

Téléchargez FPDF depuis [fpdf.org](http://www.fpdf.org) et placez le dossier dans votre projet, puis incluez le fichier principal :

```php
require('fpdf/fpdf.php');
```

### 1.2 Installation via Composer (recommandé)

```bash
composer require setasign/fpdf
```

```php
require 'vendor/autoload.php';
```

---

## 2. Créer un PDF minimal

```php
<?php
require('fpdf/fpdf.php');

$pdf = new FPDF();   // création de l'objet PDF
$pdf->AddPage();     // ajoute une première page
$pdf->SetFont('Arial', 'B', 16); // police, style, taille
$pdf->Cell(0, 10, 'Bonjour le monde !');
$pdf->Output(); // affiche le PDF dans le navigateur
```

C'est le squelette de base présent dans **tout** script FPDF :
1. Créer l'objet `FPDF`
2. Ajouter une page avec `AddPage()`
3. Définir une police avec `SetFont()`
4. Écrire du contenu
5. Générer la sortie avec `Output()`

---

## 3. Configuration de la page

### 3.1 Constructeur : orientation, unité, format

```php
$pdf = new FPDF($orientation, $unite, $format);

// Exemples
$pdf = new FPDF('P', 'mm', 'A4'); // Portrait, millimètres, A4 (par défaut)
$pdf = new FPDF('L', 'mm', 'A4'); // Paysage (Landscape)
$pdf = new FPDF('P', 'mm', 'Letter'); // format lettre US
$pdf = new FPDF('P', 'mm', [210, 297]); // format personnalisé en mm
```

| Paramètre | Valeurs possibles |
|-----------|---------------------|
| Orientation | `P` (portrait), `L` (paysage) |
| Unité | `mm`, `cm`, `in`, `pt` |
| Format | `A3`, `A4`, `A5`, `Letter`, `Legal`, ou `[largeur, hauteur]` |

### 3.2 Marges

```php
$pdf->SetMargins(20, 15, 20); // gauche, haut, droite (en unité définie, ex: mm)
$pdf->SetLeftMargin(20);
$pdf->SetTopMargin(15);
$pdf->SetRightMargin(20);
$pdf->SetAutoPageBreak(true, 25); // saut de page auto + marge basse de 25mm
```

### 3.3 Ajouter une page

```php
$pdf->AddPage();              // page avec l'orientation du constructeur
$pdf->AddPage('L');           // forcer une page en paysage
$pdf->AddPage('P', 'A5');     // changer orientation ET format pour cette page
```

### 3.4 Métadonnées du document

```php
$pdf->SetTitle('Facture N°2026-001');
$pdf->SetAuthor('Ma Société');
$pdf->SetSubject('Facturation client');
$pdf->SetKeywords('facture, client, 2026');
$pdf->SetCreator('Mon Application PHP');
```

---

## 4. Les polices (fonts)

### 4.1 Polices standard intégrées (pas besoin de fichier)

```php
$pdf->SetFont('Arial', '', 12);       // normal
$pdf->SetFont('Arial', 'B', 12);      // gras (Bold)
$pdf->SetFont('Arial', 'I', 12);      // italique
$pdf->SetFont('Arial', 'BI', 12);     // gras + italique
$pdf->SetFont('Arial', 'U', 12);      // souligné (Underline)
$pdf->SetFont('Times', '', 12);
$pdf->SetFont('Courier', '', 12);     // police à chasse fixe
$pdf->SetFont('Symbol', '', 12);
$pdf->SetFont('ZapfDingbats', '', 12);
```

Les polices standards (Arial, Times, Courier, Symbol, ZapfDingbats) sont **toujours disponibles** sans configuration supplémentaire, mais elles ne gèrent que les caractères latins de base (attention aux accents avec un encodage non compatible : voir 4.3).

### 4.2 Ajouter une police personnalisée (TrueType/OTF)

```php
// Une seule fois, avant utilisation
$pdf->AddFont('Roboto', '', 'Roboto-Regular.php');
$pdf->AddFont('Roboto', 'B', 'Roboto-Bold.php');

$pdf->SetFont('Roboto', '', 12);
```

Les fichiers `.php` de police doivent être générés au préalable avec l'utilitaire `MakeFont` fourni par FPDF (à partir d'un fichier `.ttf`).

### 4.3 Gestion des accents et de l'encodage

FPDF utilise par défaut l'encodage **ISO-8859-1** (Latin-1), pas l'UTF-8. Si votre texte contient des accents en UTF-8 (le cas la plupart du temps en PHP moderne), vous devez le convertir :

```php
$texte = utf8_decode('Café à emporter'); // dépréciée en PHP 8.2+, alternative ci-dessous
$pdf->Cell(0, 10, $texte);

// Alternative recommandée (PHP 8.2+, utf8_decode étant dépréciée)
$texte = mb_convert_encoding('Café à emporter', 'ISO-8859-1', 'UTF-8');
$pdf->Cell(0, 10, $texte);
```

> Pour un support UTF-8 complet et natif, il existe la variante **tFPDF** (basée sur FPDF, gère l'UTF-8 avec des polices TrueType) ou la librairie plus moderne **FPDI/mPDF**.

### 4.4 Taille de police par défaut et espacement

```php
$pdf->SetFontSize(14);      // change juste la taille, garde la police actuelle
$pdf->SetLineWidth(0.3);    // épaisseur des traits (bordures, lignes)
```

---

## 5. Écrire du texte : Cell, MultiCell, Write

### 5.1 `Cell()` — bloc de texte sur une seule ligne

```php
$pdf->Cell(largeur, hauteur, texte, bordure, saut, alignement, remplissage, lien);
```

```php
$pdf->Cell(40, 10, 'Nom :');           // largeur 40, hauteur 10, sans bordure
$pdf->Cell(40, 10, 'Nom :', 1);        // avec bordure (1 = tout autour)
$pdf->Cell(0, 10, 'Titre', 0, 1, 'C'); // largeur 0 = jusqu'au bord droit, retour ligne (1), centré
```

| Paramètre | Description |
|-----------|--------------|
| `largeur` | en unité de la page ; `0` = jusqu'à la marge droite |
| `hauteur` | hauteur de la cellule |
| `texte` | contenu (chaîne simple, pas de retour à la ligne automatique) |
| `bordure` | `0` (aucune), `1` (tour complet), ou combinaison `'LTRB'` (Left/Top/Right/Bottom) |
| `saut` | `0` = reste sur la même ligne après ; `1` = retour à la ligne ; `2` = va à la ligne du dessous sans changer X |
| `alignement` | `'L'` (gauche, défaut), `'C'` (centré), `'R'` (droite) |
| `remplissage` | `true`/`false`, utilise `SetFillColor()` |
| `lien` | URL ou identifiant de lien interne |

### 5.2 Aligner plusieurs `Cell()` côte à côte

```php
$pdf->Cell(40, 10, 'Nom', 1);
$pdf->Cell(40, 10, 'Prénom', 1);
$pdf->Cell(40, 10, 'Email', 1, 1); // dernier = retour à la ligne
```

### 5.3 `MultiCell()` — texte multi-lignes avec retour automatique

Indispensable pour les paragraphes, descriptions, contenus longs.

```php
$pdf->MultiCell(largeur, hauteurLigne, texte, bordure, alignement, remplissage);
```

```php
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 7, "Ceci est un long paragraphe qui va automatiquement revenir à la ligne quand il atteint la largeur maximale définie, sans avoir besoin de le découper manuellement.");
```

### 5.4 `Write()` — texte fluide (façon HTML), avec liens intégrés

```php
$pdf->SetFont('Arial', '', 12);
$pdf->Write(5, 'Ceci est un texte normal, puis ');
$pdf->SetFont('Arial', 'U', 12);
$pdf->Write(5, 'un lien cliquable', 'https://exemple.com');
```

`Write()` continue sur la même position verticale, contrairement à `MultiCell()` qui saute toujours après.

### 5.5 Espacement entre lignes / retour à la ligne manuel

```php
$pdf->Ln();      // retour à la ligne (hauteur = dernière hauteur de cellule)
$pdf->Ln(10);    // retour à la ligne avec un espacement précis de 10 unités
```

---

## 6. Couleurs

```php
// Couleur du texte
$pdf->SetTextColor(0, 0, 0);       // noir (RGB)
$pdf->SetTextColor(200, 0, 0);     // rouge

// Couleur de fond (utilisée avec le paramètre "remplissage" de Cell)
$pdf->SetFillColor(230, 230, 230); // gris clair

// Couleur des traits/bordures
$pdf->SetDrawColor(0, 0, 0);

// Utilisation avec remplissage activé
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(0, 10, 'Ligne grisée', 1, 1, 'L', true); // le dernier "true" active le fond
```

---

## 7. Dessiner : lignes, rectangles, formes

```php
// Ligne : Line(x1, y1, x2, y2)
$pdf->Line(10, 20, 200, 20);

// Rectangle : Rect(x, y, largeur, hauteur, style)
$pdf->Rect(10, 10, 100, 50);        // contour seul (par défaut)
$pdf->Rect(10, 10, 100, 50, 'F');   // rempli (Fill), utilise SetFillColor
$pdf->Rect(10, 10, 100, 50, 'DF');  // contour + rempli

// Épaisseur des traits
$pdf->SetLineWidth(0.5);
```

> FPDF de base ne propose pas les cercles/ellipses nativement (contrairement à certaines librairies dérivées) ; on les simule via des courbes de Bézier ou on utilise une extension comme `fpdf_rotation`/`fpdf_geometric`.

---

## 8. Images

```php
$pdf->Image($fichier, x, y, largeur, hauteur, type, lien);
```

```php
$pdf->Image('logo.png', 10, 10, 30);          // largeur 30, hauteur calculée automatiquement
$pdf->Image('logo.jpg', 10, 10, 30, 20);        // largeur et hauteur imposées
$pdf->Image('photo.png', 10, 10, 0, 0, '', 'https://monsite.com'); // image cliquable
```

Formats supportés nativement : **JPEG, PNG, GIF** (GIF via conversion interne).

### 8.1 Positionner une image et continuer le texte à côté

```php
$pdf->Image('logo.png', 10, 8, 25);
$pdf->SetXY(40, 10); // repositionne le curseur texte à droite de l'image
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Nom de la société');
```

---

## 9. Faire un tableau (table)

FPDF ne propose **pas** de fonction native `Table()` : on construit un tableau en combinant plusieurs `Cell()` avec des largeurs fixes.

### 9.1 Tableau simple avec bordures

```php
$pdf->SetFont('Arial', 'B', 11);

// En-têtes
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(60, 8, 'Produit', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Quantité', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Prix unitaire', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Total', 1, 1, 'C', true); // dernier "1" = retour à la ligne

// Lignes de données
$pdf->SetFont('Arial', '', 10);
$produits = [
    ['nom' => 'Chaise', 'quantite' => 2, 'prix' => 49.99],
    ['nom' => 'Table',  'quantite' => 1, 'prix' => 149.99],
];

foreach ($produits as $produit) {
    $total = $produit['quantite'] * $produit['prix'];
    $pdf->Cell(60, 8, $produit['nom'], 1);
    $pdf->Cell(30, 8, $produit['quantite'], 1, 0, 'C');
    $pdf->Cell(30, 8, number_format($produit['prix'], 2) . ' EUR', 1, 0, 'R');
    $pdf->Cell(30, 8, number_format($total, 2) . ' EUR', 1, 1, 'R');
}
```

### 9.2 Tableau avec lignes de couleur alternée (zébré)

```php
$pdf->SetFont('Arial', '', 10);
$alterner = false;

foreach ($produits as $produit) {
    $pdf->SetFillColor($alterner ? 245 : 255, $alterner ? 245 : 255, $alterner ? 245 : 255);
    $pdf->Cell(60, 8, $produit['nom'], 1, 0, 'L', true);
    $pdf->Cell(30, 8, $produit['quantite'], 1, 0, 'C', true);
    $pdf->Cell(30, 8, number_format($produit['prix'], 2), 1, 1, 'R', true);
    $alterner = !$alterner;
}
```

### 9.3 Tableau avec largeurs de colonnes dynamiques (calculées)

```php
$largeurPage = $pdf->GetPageWidth() - 20; // moins les marges
$colonnes = ['Produit' => 0.4, 'Quantité' => 0.2, 'Prix' => 0.2, 'Total' => 0.2];

foreach ($colonnes as $titre => $ratio) {
    $pdf->Cell($largeurPage * $ratio, 8, $titre, 1, 0, 'C', true);
}
$pdf->Ln();
```

### 9.4 Tableau avec cellules multi-lignes (texte long dans une colonne)

Comme `MultiCell()` retourne toujours à la ligne, on utilise une astuce : calculer la hauteur nécessaire, puis repositionner les cellules voisines à la même hauteur.

```php
function ligneTableau($pdf, $description, $prix)
{
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Colonne description (peut être multi-lignes)
    $pdf->MultiCell(120, 6, $description, 1);
    $hauteurUtilisee = $pdf->GetY() - $y;

    // Colonne prix, repositionnée à la hauteur de départ
    $pdf->SetXY($x + 120, $y);
    $pdf->Cell(40, $hauteurUtilisee, $prix, 1, 0, 'R');

    $pdf->SetY($y + $hauteurUtilisee); // aligne le curseur pour la ligne suivante
}

ligneTableau($pdf, "Chaise en bois massif avec accoudoirs rembourrés, disponible en plusieurs coloris", "49,99 EUR");
```

### 9.5 Gérer le saut de page automatique à l'intérieur d'un tableau

```php
foreach ($produits as $produit) {
    // Vérifier s'il reste assez de place avant la fin de page
    if ($pdf->GetY() > $pdf->GetPageHeight() - 30) {
        $pdf->AddPage();
        // Ré-afficher les en-têtes du tableau sur la nouvelle page
        afficherEntetesTableau($pdf);
    }

    $pdf->Cell(60, 8, $produit['nom'], 1);
    $pdf->Cell(30, 8, $produit['quantite'], 1, 1);
}
```

---

## 10. Liens et hyperliens

```php
// Lien externe sur une cellule
$pdf->Cell(0, 10, 'Visitez notre site', 0, 1, 'L', false, 'https://monsite.com');

// Lien avec Write()
$pdf->Write(5, 'Cliquez ici', 'https://monsite.com');

// Lien interne (vers une autre page du même PDF)
$lien = $pdf->AddLink();          // crée un identifiant de lien interne
$pdf->SetLink($lien, 0, 3);       // pointe vers la page 3
$pdf->Cell(0, 10, 'Aller au chapitre 3', 0, 1, 'L', false, $lien);
```

---

## 11. Gestion des pages, sauts de page et en-têtes/pieds de page

### 11.1 En-tête et pied de page automatiques

En créant une **classe qui étend FPDF**, on peut définir `Header()` et `Footer()`, appelées automatiquement sur chaque page.

```php
<?php
require('fpdf/fpdf.php');

class PDF extends FPDF
{
    // Appelé automatiquement au début de chaque AddPage()
    function Header()
    {
        $this->Image('logo.png', 10, 8, 25);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Ma Société', 0, 1, 'C');
        $this->Ln(5);
    }

    // Appelé automatiquement en fin de page
    function Footer()
    {
        $this->SetY(-15); // 15 unités depuis le bas
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages(); // active le remplacement de {nb} par le nombre total de pages
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Contenu de la page');
$pdf->Output();
```

### 11.2 Saut de page manuel

```php
$pdf->AddPage(); // force une nouvelle page à tout moment
```

### 11.3 Saut de page automatique

```php
$pdf->SetAutoPageBreak(true, 20); // active (par défaut) avec 20 unités de marge basse
$pdf->SetAutoPageBreak(false);    // désactive complètement (à vos risques)
```

### 11.4 Détecter/gérer un saut de page manuellement (méthode `AcceptPageBreak`)

```php
class PDF extends FPDF
{
    function AcceptPageBreak()
    {
        // Retourner false empêche le saut de page automatique standard
        // Utile pour gérer soi-même un tableau multi-colonnes par exemple
        return true;
    }
}
```

---

## 12. Positionnement (X, Y)

```php
$pdf->SetX(50);          // position horizontale absolue
$pdf->SetY(100);         // position verticale absolue (valeur négative = depuis le bas)
$pdf->SetXY(50, 100);    // les deux en même temps

$x = $pdf->GetX();       // récupérer la position actuelle
$y = $pdf->GetY();

$largeurPage = $pdf->GetPageWidth();
$hauteurPage = $pdf->GetPageHeight();
```

---

## 13. Sortie du PDF (afficher, télécharger, sauvegarder)

```php
$pdf->Output();                          // affiche dans le navigateur (par défaut)
$pdf->Output('I', 'document.pdf');       // 'I' = Inline (affichage navigateur)
$pdf->Output('D', 'document.pdf');       // 'D' = Download (force le téléchargement)
$pdf->Output('F', '/chemin/document.pdf'); // 'F' = File (enregistre sur le serveur)
$contenu = $pdf->Output('S');            // 'S' = String (retourne le contenu en chaîne, utile pour l'envoyer par email)
```

| Mode | Effet |
|------|-------|
| `I` | Affiche le PDF directement dans le navigateur |
| `D` | Force le téléchargement du fichier |
| `F` | Enregistre le PDF sur le disque serveur |
| `S` | Retourne le PDF sous forme de chaîne binaire (ex: pièce jointe email) |

---

## 14. Exemple complet : facture avec tableau

```php
<?php
require('fpdf/fpdf.php');

class FacturePDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(0, 10, 'FACTURE N°2026-001', 0, 1, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, 'Date : ' . date('d/m/Y'), 0, 1);
        $this->Ln(8);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function EnteteTableau()
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(230, 230, 230);
        $this->Cell(90, 8, 'Produit', 1, 0, 'L', true);
        $this->Cell(25, 8, 'Qté', 1, 0, 'C', true);
        $this->Cell(35, 8, 'Prix unit.', 1, 0, 'R', true);
        $this->Cell(35, 8, 'Total', 1, 1, 'R', true);
    }
}

$produits = [
    ['nom' => 'Chaise en bois', 'quantite' => 4, 'prix' => 49.99],
    ['nom' => 'Table rectangulaire', 'quantite' => 1, 'prix' => 199.00],
    ['nom' => 'Lampe de bureau', 'quantite' => 2, 'prix' => 34.50],
];

$pdf = new FacturePDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->EnteteTableau();

$pdf->SetFont('Arial', '', 10);
$totalGeneral = 0;

foreach ($produits as $produit) {
    $total = $produit['quantite'] * $produit['prix'];
    $totalGeneral += $total;

    $pdf->Cell(90, 8, $produit['nom'], 1);
    $pdf->Cell(25, 8, $produit['quantite'], 1, 0, 'C');
    $pdf->Cell(35, 8, number_format($produit['prix'], 2) . ' EUR', 1, 0, 'R');
    $pdf->Cell(35, 8, number_format($total, 2) . ' EUR', 1, 1, 'R');
}

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(150, 10, 'TOTAL', 1, 0, 'R');
$pdf->Cell(35, 10, number_format($totalGeneral, 2) . ' EUR', 1, 1, 'R');

$pdf->Output('D', 'facture-2026-001.pdf');
```

---

## 15. Récapitulatif de toutes les méthodes utiles

| Méthode | Rôle |
|---------|------|
| `new FPDF($orientation, $unite, $format)` | Créer le document |
| `AddPage($orientation, $format)` | Ajouter une page |
| `SetFont($famille, $style, $taille)` | Définir la police |
| `AddFont($famille, $style, $fichier)` | Ajouter une police personnalisée |
| `SetFontSize($taille)` | Changer uniquement la taille |
| `SetTextColor($r,$g,$b)` | Couleur du texte |
| `SetFillColor($r,$g,$b)` | Couleur de fond |
| `SetDrawColor($r,$g,$b)` | Couleur des traits |
| `SetLineWidth($largeur)` | Épaisseur des traits |
| `Cell($w,$h,$txt,$border,$ln,$align,$fill,$link)` | Bloc de texte sur une ligne |
| `MultiCell($w,$h,$txt,$border,$align,$fill)` | Texte multi-lignes |
| `Write($h,$txt,$link)` | Texte fluide, avec lien |
| `Ln($h)` | Retour à la ligne |
| `Line($x1,$y1,$x2,$y2)` | Tracer une ligne |
| `Rect($x,$y,$w,$h,$style)` | Tracer un rectangle |
| `Image($fichier,$x,$y,$w,$h,$type,$lien)` | Insérer une image |
| `AddLink()` / `SetLink()` | Créer des liens internes |
| `SetX()` / `SetY()` / `SetXY()` | Positionner le curseur |
| `GetX()` / `GetY()` | Récupérer la position |
| `GetPageWidth()` / `GetPageHeight()` | Dimensions de la page |
| `SetMargins()` / `SetAutoPageBreak()` | Marges et sauts de page |
| `SetTitle()` / `SetAuthor()` / etc. | Métadonnées du document |
| `AliasNbPages()` | Active `{nb}` = nombre total de pages |
| `PageNo()` | Numéro de la page actuelle |
| `Output($dest, $nom)` | Générer/envoyer le PDF final |

---

## 16. Bonnes pratiques et pièges courants

- **Encodage** : convertissez toujours vos chaînes UTF-8 vers ISO-8859-1 avant de les passer à `Cell()`/`MultiCell()`/`Write()`, sinon les accents s'affichent mal (`é`, `à`, `ç`...). Utilisez `mb_convert_encoding($texte, 'ISO-8859-1', 'UTF-8')`.
- **Largeur 0** dans `Cell()`/`MultiCell()` signifie "jusqu'à la marge droite" — pratique pour les titres pleine largeur.
- Après un `MultiCell()`, la position **X repart à la marge gauche** automatiquement ; pensez à repositionner avec `SetX()` si vous enchaînez avec une autre cellule sur la même ligne.
- Pour un vrai tableau propre avec cellules de hauteurs variables, il faut calculer manuellement la hauteur du texte le plus long (voir section 9.4) — FPDF ne le fait pas pour vous.
- N'oubliez pas `AliasNbPages()` si vous affichez le nombre total de pages dans le pied de page.
- `SetAutoPageBreak()` doit être configuré **avant** `AddPage()` si vous voulez changer son comportement dès la première page.
- Pour des besoins plus avancés (UTF-8 natif, HTML vers PDF, formulaires PDF, signatures), envisagez des variantes/héritiers de FPDF : **tFPDF** (UTF-8), **FPDI** (importer des PDF existants comme modèles), ou des librairies plus complètes comme **mPDF** ou **Dompdf**.
- FPDF ne valide pas vos entrées : vérifiez toujours vous-même que les données (prix, quantités...) sont bien formatées avant de les insérer dans le PDF.
