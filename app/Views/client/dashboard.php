<?= $this->extend('layouts/layout_client') ?>
<?= $this->section('content') ?>

    <div class="dashboard">
        <h2>Bonjour, <?= esc($user['nom'] ?? 'Client') ?></h2>

        <div class="solde-card">
            <p>Solde actuel</p>
            <h1><?= number_format($solde, 0, ',', ' ') ?> Ar</h1>
        </div>

        <div class="actions">
            <a href="/client/depot" class="btn">Dépôt</a>
            <a href="/client/retrait" class="btn">Retrait</a>
            <a href="/client/transfert" class="btn">Transfert</a>
            <a href="/client/historique" class="btn">Historique</a>
            <a href="/client/epargne" class="btn">Epargne</a>
        </div>

        <h3>Dernières transactions</h3>
        <div class="card shadow-sm border-0 my-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history me-2"></i>Dernières transactions
                </h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Valeur</th>
                            <th scope="col">Frais</th>
                            <th scope="col">Gain</th>
                            <th scope="col" class="pe-4 text-end">Date & Heure</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($dernieresTransactions)): ?>
                            <?php foreach ($dernieresTransactions as $t): ?>
                                <tr>
                                    <td class="ps-4 fw-semibold text-dark">
                                        <?= number_format((float) $t['valeur'], 2, ',', ' ') ?> Ar
                                    </td>

                                    <td>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                            <?= number_format((float) $t['frais'], 2, ',', ' ') ?> Ar
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            +<?= number_format((float) $t['gain'], 2, ',', ' ') ?> Ar
                                        </span>
                                    </td>

                                    <td class="pe-4 text-end text-muted small">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <?= date('d/m/Y à H:i', strtotime(esc($t['created_at']))) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Aucune transaction récente trouvée.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>
