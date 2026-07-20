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
        </div>

        <h3>Dernières transactions</h3>
        <ul>
            <?php foreach ($dernieresTransactions as $t): ?>
                <li><?= esc($t['operation']) ?> — <?= esc($t['datetime']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

<?= $this->section('content') ?>