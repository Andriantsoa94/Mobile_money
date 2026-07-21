<?= $this->extend('layouts/layoutAdmin') ?>
<?= $this->section('contenu') ?>

<?php
$estEdition = $commission !== null;
$action     = $estEdition ? '/admin/commissions/' . (int) $commission['id'] . '/modifier' : '/admin/commissions';
$titre      = $estEdition ? 'Modifier la commission' : 'Nouvelle commission';
?>

<h2 class="mb-4"><?= esc($titre) ?></h2>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= esc($action) ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="idOperateur" class="form-label">Opérateur</label>
                <select class="form-select" name="idOperateur" id="idOperateur" required>
                    <option value="">— Choisir —</option>
                    <?php foreach ($operateurs as $op): ?>
                        <option value="<?= (int) $op['id'] ?>"
                            <?= ((int) old('idOperateur', $commission['idOperateur'] ?? 0) === (int) $op['id']) ? 'selected' : '' ?>>
                            <?= esc($op['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="pourcentage" class="form-label">Pourcentage en plus (%)</label>
                    <input type="number" step="0.01" min="0" max="100" class="form-control" name="pourcentage" id="pourcentage"
                           value="<?= esc(old('pourcentage', $commission['pourcentage'] ?? '0')) ?>" required>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="/admin/commissions" class="btn btn-outline-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
