<?= $this->extend('layouts/layoutAdmin') ?>
<?= $this->section('contenu') ?>

<h2 class="mb-4">Situation des gains</h2>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" action="/admin/gains" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="dateDebut" class="form-label">Du</label>
                <input type="date" class="form-control" name="dateDebut" id="dateDebut"
                       value="<?= esc($filtres['dateDebut'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="dateFin" class="form-label">Au</label>
                <input type="date" class="form-control" name="dateFin" id="dateFin"
                       value="<?= esc($filtres['dateFin'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label for="idTypeOperation" class="form-label">Type d'opération</label>
                <select class="form-select" name="idTypeOperation" id="idTypeOperation">
                    <option value="">Tous</option>
                    <?php foreach ($types as $t): ?>
                        <option value="<?= (int) $t['id'] ?>"
                            <?= ((int) ($filtres['idTypeOperation'] ?? 0) === (int) $t['id']) ? 'selected' : '' ?>>
                            <?= esc($t['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filtrer</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="text-muted small">Total des gains sur la période</div>
        <div class="fs-2 fw-bold text-success"><?= number_format((float) $totalGains, 0, ',', ' ') ?> Ar</div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        
        <h5 class="card-title mb-3">Mes gains</h5>
        <?php if (empty($gainsParType)): ?>
            <p class="text-muted mb-0">Aucune donnée pour la période.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th class="text-end">Nombre de transactions</th>
                            <th class="text-end">Total des gains</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gainsParType as $g): ?>
                            <tr>
                                <td><?= esc($g['typeNom'] ?? 'Non classé') ?></td>
                                <td class="text-end"><?= (int) $g['nombre'] ?></td>
                                <td class="text-end"><?= number_format((float) $g['total'], 0, ',', ' ') ?> Ar</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-body">
        
        <h5 class="card-title mb-3">Les gains des autres operateurs</h5>
        <?php if (empty($autreOperateur)): ?>
            <p class="text-muted mb-0">Aucune donnée pour la période.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Opérateur</th>
                            <th class="text-end">Nombre de transactions</th>
                            <th class="text-end">Total des gains</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($autreOperateur as $g): ?>
                            <tr>
                                <td><?= esc($g['autreOperateurNom'] ?? 'Non classé') ?></td>
                                <td class="text-end"><?= (int) $g['nombre'] ?></td>
                                <td class="text-end"><?= number_format((float) $g['total'], 0, ',', ' ') ?> Ar</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
