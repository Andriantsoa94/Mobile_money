<?= $this->extend('layouts/layoutAdmin') ?>
<?= $this->section('contenu') ?>

<h2 class="mb-4">Tableau de bord</h2>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Gains totaux</div>
                <div class="fs-3 fw-bold text-success"><?= number_format((float) $totalGains, 0, ',', ' ') ?> Ar</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Transactions (total)</div>
                <div class="fs-3 fw-bold text-primary"><?= (int) $totalTransactions ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Transactions aujourd'hui</div>
                <div class="fs-3 fw-bold text-warning"><?= (int) $transactionsAujourdhui ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Clients enregistrés</div>
                <div class="fs-3 fw-bold text-info"><?= (int) $totalClients ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-3">Dernières transactions</h5>
        <?php if (empty($dernieresTransactions)): ?>
            <p class="text-muted mb-0">Aucune transaction pour le moment.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th class="text-end">Gain</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dernieresTransactions as $t): ?>
                            <tr>
                                <td><?= esc($t['clientNom'] ?? '—') ?></td>
                                <td><?= esc($t['typeNom'] ?? '—') ?></td>
                                <td><?= esc($t['valeur']) ?></td>
                                <td class="text-end"><?= number_format((float) $t['gain'], 0, ',', ' ') ?> Ar</td>
                                <td><?= esc($t['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
