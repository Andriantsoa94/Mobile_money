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

    <!-- Messages d'erreur / succès -->
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
                type="tel"
                name="numero"
                id="numero"
                inputmode="numeric"
                maxlength="10"
                placeholder="0331234567"
                value="<?= old('numero') ?>"
                required
                autofocus
            >
            <small id="numero-error" class="field-error"></small>
        </div>

        <button type="submit" class="btn-primary" id="submitBtn">
            Continuer
        </button>
    </form>
</div>

<script>
    // Liste des préfixes valides injectée depuis le contrôleur (dynamique, synchro avec /admin/prefixes)
    const prefixesAutorises = <?= json_encode($prefixes ?? []) ?>;

    const input      = document.getElementById('numero');
    const errorEl     = document.getElementById('numero-error');
    const submitBtn   = document.getElementById('submitBtn');
    const form        = document.getElementById('loginForm');

    input.addEventListener('input', function () {
        // Ne garder que les chiffres
        this.value = this.value.replace(/[^0-9]/g, '');

        const prefixe = this.value.substring(0, 3);

        if (this.value.length >= 3 && !prefixesAutorises.includes(prefixe)) {
            errorEl.textContent = 'Préfixe non autorisé (' + prefixesAutorises.join(', ') + ' uniquement)';
            this.setCustomValidity('Préfixe invalide');
        } else {
            errorEl.textContent = '';
            this.setCustomValidity('');
        }
    });

    form.addEventListener('submit', function (e) {
        const prefixe = input.value.substring(0, 3);

        if (input.value.length !== 10 || !prefixesAutorises.includes(prefixe)) {
            e.preventDefault();
            errorEl.textContent = 'Numéro invalide. Vérifiez le préfixe et la longueur (10