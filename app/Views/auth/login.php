<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="/assets/css/login.css">
</head>
<body>

<div class="login-container">
    <h1>Connexion</h1>
    <p class="subtitle">Entrez votre numéro de téléphone</p>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/login" method="post" id="loginForm">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="numero">Numéro de téléphone</label>
            <input
                    type="text"
                    name="numero"
                    id="numero"
                    placeholder="0331234567"
                    value="<?= old('numero') ?>"
                    autofocus
            >
        </div>

        <button type="submit" class="btn-primary" id="submitBtn">
            Continuer
        </button>
    </form>

    <div class="test-accounts">
        Admin (0330000000)
        Client (0331111111)
        Client (0372222222)
    </div>
</div>

</body>
</html>