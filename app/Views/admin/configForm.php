<?= $this->extend('layouts/layoutAdmin') ?>
<?= $this->section('contenu') ?>

<?php
$estEdition = $bareme !== null;
$action     = $estEdition ? '/admin/config/' . (int) $bareme['id'] . '/modifier' : '/admin/config';
$titre      = $estEdition ? 'Modifier la tranche' : 'Nouvelle tranche';
?>

<h2 class="mb-4"><?= esc($titre) ?></h2>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= esc($action) ?>">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-3">
                    <label for="min" class="form-label">Montant min (Ar)</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="min" id="min"
                           value="<?= esc(old('min', $bareme['min'] ?? '')) ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="max" class="form-label">Montant max (Ar)</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="max" id="max"
                           value="<?= esc(old('max', $bareme['max'] ?? '')) ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="frais" class="form-label">Frais (payé par le client)</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="frais" id="frais"
                           value="<?= esc(old('frais', $bareme['frais'] ?? '')) ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="gain" class="form-label">Gain (gardé par la plateforme)</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="gain" id="gain"
                           value="<?= esc(old('gain', $bareme['gain'] ?? '')) ?>" required>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="/admin/config" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
