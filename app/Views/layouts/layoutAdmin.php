<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mobile Money — Administration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f7fa; }
        .sidebar {
            min-height: 100vh;
            background: #1f2937;
        }
        .sidebar a {
            color: #d1d5db;
            display: block;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 4px;
        }
        .sidebar a:hover { background: #374151; color: #fff; }
        .sidebar a.actif { background: #2563eb; color: #fff; }
        .sidebar .titre { color: #9ca3af; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; padding: 12px 16px 6px; }
        .sidebar .marque { color: #fff; padding: 20px 16px 16px; font-weight: 700; font-size: 18px; }
        .contenu { padding: 30px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php $uri = trim((string) service('uri')->getPath(), '/'); ?>
        <nav class="col-md-3 col-lg-2 sidebar p-3">
            <div class="marque">Mobile Money</div>
            <div class="titre">Général</div>
            <a href="/admin" class="<?= $uri === 'admin' ? 'actif' : '' ?>">Tableau de bord</a>
            <a href="/admin/gains" class="<?= str_starts_with($uri, 'admin/gains') ? 'actif' : '' ?>">Situation des gains</a>
            <a href="/admin/clients" class="<?= str_starts_with($uri, 'admin/clients') ? 'actif' : '' ?>">Clients</a>

            <div class="titre">Paramétrage</div>
            <a href="/admin/prefixes" class="<?= str_starts_with($uri, 'admin/prefixes') ? 'actif' : '' ?>">Préfixes</a>
            <a href="/admin/config" class="<?= str_starts_with($uri, 'admin/config') ? 'actif' : '' ?>">Barèmes</a>

            <div class="titre">Compte</div>
            <a href="/logout">Déconnexion</a>
        </nav>

        <main class="col-md-9 col-lg-10 contenu">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <?= $this->renderSection('contenu') ?>
        </main>
    </div>
</div>
</body>
</html>
