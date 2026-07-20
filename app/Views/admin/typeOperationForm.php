<?= $this->extend('layouts/layoutAdmin') ?>
<?= $this->section('contenu') ?>

<?php
$estEdition = $type !== null;
$action     = $estEdition ? '/admin/types/' . (int) $type['id'] . '/modifier' : '/admin/types';
$titre      = $estEdition ? 'Modifier le type d\'opération' : 'Nouveau type d\'opération';
?>

<h2 class="mb-4"><?= esc($titre) ?></h2>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= esc($action) ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" name="nom" id="nom" maxlength="100"
                       value="<?= esc(old('nom', $type['nom'] ?? '')) ?>" required>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="isGain" id="isGain" value="1"
                    <?= ((int) old('isGain', $type['isGain'] ?? 0) === 1) ? 'checked' : '' ?>>
                <label class="form-check-label" for="isGain">Ce type génère un gain pour l'opérateur</label>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/admin/types" class="btn btn-outline-secondary">Annuler</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
