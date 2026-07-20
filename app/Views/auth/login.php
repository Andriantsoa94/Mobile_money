<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #1f2937, #2563eb);
        }
        .carteLogin {
            max-width: 420px;
            width: 100%;
            margin: 0 auto;
            border: none;
            border-radius: 14px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
        }
        .logoLogin {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #2563eb;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 22px;
            margin: 0 auto 16px;
        }
        .comptesTest {
            font-size: 13px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card carteLogin p-4 p-md-5">
        <div class="logoLogin">MM</div>
        <h1 class="h4 text-center mb-1">Mobile Money</h1>
        <p class="text-muted text-center mb-4">Entrez votre numéro de téléphone</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/login" method="post" id="loginForm">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="numero" class="form-label">Numéro de téléphone</label>
                <input
                        type="text"
                        name="numero"
                        id="numero"
                        class="form-control form-control-lg"
                        placeholder="0331234567"
                        value="<?= old('numero') ?>"
                        autofocus
                >
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100" id="submitBtn">
                Continuer
            </button>
        </form>

        <hr class="my-4">

        <div class="comptesTest">
            <div>Admin : 0330000000</div>
            <div>Client : 0331111111</div>
            <div>Client : 0372222222</div>
        </div>
    </div>
</div>

</body>
</html>
