<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page introuvable - Gestion bibliothèque</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f7f7f7;
            color: #1f2937;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 16px;
        }

        .nav {
            background: #0f172a;
            color: #fff;
        }

        .nav .container {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .nav a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }

        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            margin-top: 16px;
            text-align: center;
        }

        .error-code {
            font-size: 4rem;
            font-weight: bold;
            color: #ef4444;
            margin-bottom: 16px;
        }

        .error-message {
            font-size: 1.25rem;
            margin-bottom: 16px;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 24px;
        }

        .btn {
            padding: 8px 16px;
            background: #0f172a;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
        }

        .btn:hover {
            background: #1e293b;
        }
    </style>
</head>
<body>

    <main class="container">
        <div class="card">
            <div class="error-code">404</div>
            <h1>Page introuvable</h1>
            <div class="actions">
                <a href="<?= site_url('/livres') ?>" class="btn">Retour au catalogue</a>
            </div>
        </div>
    </main>
</body>
</html>
