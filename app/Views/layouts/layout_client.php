<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Client</title>
</head>
<body>
    <header>
        <a href="/client">Accueil</a>
        <a href="/logout">Déconnexion</a>
    </header>

    <main>
        <?= $this->renderSection('content') ?>
    </main>
</body>
</html>
