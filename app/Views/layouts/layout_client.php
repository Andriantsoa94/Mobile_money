<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Client</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f5f7; color: #222; }
        header { display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; background: #1f2937; color: #fff; }
        header a { color: #fff; text-decoration: none; margin-left: 15px; }
        header a:hover { text-decoration: underline; }
        main { padding: 30px; max-width: 700px; margin: 0 auto; }

        .alert { padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .alert-success { background: #dcfce7; color: #166534; }

        .btn { display: inline-block; background: #2563eb; color: #fff; border: none; padding: 10px 18px; border-radius: 6px; font-size: 14px; cursor: pointer; text-decoration: none; }
        .btn:hover { background: #1d4ed8; }
        .btn-secondary { background: #e5e7eb; color: #111; }
        .btn-secondary:hover { background: #d1d5db; }

        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 6px; font-size: 14px; }
        .form-group input { width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; }

        /* Modal de confirmation réutilisable */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: none; align-items: center; justify-content: center; z-index: 1000; }
        .modal-box { background: #fff; border-radius: 8px; padding: 24px; max-width: 360px; width: 90%; }
        .modal-box h3 { margin-top: 0; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
    </style>
</head>
<body>
    <header>
        <strong>Mobile Money</strong>
        <div>
            <a href="/client">Accueil</a>
            <a href="/logout">Déconnexion</a>
        </div>
    </header>

    <main>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>

    <script>
        function ouvrirModal(id) {
            document.getElementById(id).style.display = 'flex';
        }
        function fermerModal(id) {
            document.getElementById(id).style.display = 'none';
        }
        function confirmerModal(formId) {
            document.getElementById(formId).submit();
        }
    </script>
</body>
</html>
