<?= $this->extend('layouts/layoutAdmin') ?>
<?= $this->section('contenu') ?>

<div class="mb-3">
    <a href="/admin/clients" class="text-decoration-none">← Retour à la liste</a>
</div>

<h2 class="mb-4"><?= esc($client['nom']) ?></h2>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Solde actuel</div>
                <div class="fs-3 fw-bold text-success"><?= number_format((float) $solde, 0, ',', ' ') ?> Ar</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">CIN</div>
                <div class="fs-5"><?= esc($client['CIN']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Numéros associés</div>
                <?php if (empty($numeros)): ?>
                    <div>—</div>
                <?php else: ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($numeros as $n): ?>
                            <li><?= esc($n['numero']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-3">Historique des transactions</h5>
        <?php if (empty($transactions)): ?>
            <p class="text-muted mb-0">Aucune transaction.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th class="text-end">Gain / Frais</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $t): ?>
                            <tr>
                                <td><?= esc($t['created_at']) ?></td>
                                <td><?= esc($t['typeNom'] ?? '—') ?></td>
                                <td class="text-end"><?= number_format((float) $t['gain'], 0, ',', ' ') ?> Ar</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
