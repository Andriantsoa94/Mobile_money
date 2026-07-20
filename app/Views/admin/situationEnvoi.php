<?= $this->extend('layouts/layoutAdmin') ?>
<?= $this->section('contenu') ?>

<h2 class="mb-4">Situation des montants à envoyer</h2>
<p class="text-muted" style="font-size: 14px;">
    Montants transférés par nos clients vers d'autres opérateurs, à leur reverser.
</p>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" action="/admin/situation-envoi" class="row g-3 align-items-end">
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
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filtrer</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="text-muted small">Total à envoyer, tous opérateurs confondus</div>
        <div class="fs-2 fw-bold text-warning"><?= number_format((float) $totalAEnvoyer, 0, ',', ' ') ?> Ar</div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (empty($montants)): ?>
            <p class="text-muted mb-0">Aucun transfert vers un autre opérateur pour la période.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Opérateur</th>
                            <th class="text-end">Nombre de transferts</th>
                            <th class="text-end">Montant à envoyer</th>
                            <th class="text-end">Commission perçue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($montants as $m): ?>
                            <tr>
                                <td><?= esc($m['operateurNom'] ?? 'Non classé') ?></td>
                                <td class="text-end"><?= (int) $m['nombre'] ?></td>
                                <td class="text-end fw-bold"><?= number_format((float) $m['totalMontant'], 0, ',', ' ') ?> Ar</td>
                                <td class="text-end"><?= number_format((float) $m['totalCommission'], 0, ',', ' ') ?> Ar</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
