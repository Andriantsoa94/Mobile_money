<?= $this->extend('layouts/layoutAdmin') ?>
<?= $this->section('contenu') ?>

<?php
$estEdition = $prefixe !== null;
$action     = $estEdition ? '/admin/prefixes/' . (int) $prefixe['id'] . '/modifier' : '/admin/prefixes';
$titre      = $estEdition ? 'Modifier le préfixe' : 'Nouveau préfixe';
?>

<h2 class="mb-4"><?= esc($titre) ?></h2>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= esc($action) ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="numero" class="form-label">Préfixe (3 chiffres)</label>
                <input type="text" class="form-control" name="numero" id="numero" maxlength="3"
                       value="<?= esc(old('numero', $prefixe['numero'] ?? '')) ?>" required>
            </div>

            <div class="mb-3">
                <label for="idoperateur" class="form-label">Opérateur</label>
                <select class="form-select" name="idoperateur" id="idoperateur">
                    <option value="">— Aucun —</option>
                    <?php foreach ($operateurs as $op): ?>
                        <option value="<?= (int) $op['id'] ?>"
                            <?= ((int) old('idoperateur', $prefixe['idoperateur'] ?? 0) === (int) $op['id']) ? 'selected' : '' ?>>
                            <?= esc($op['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/admin/prefixes" class="btn btn-outline-secondary">Annuler</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
