<?= $this->extend('layouts/layout_client') ?>
<?= $this->section('content') ?>

    <style>
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; margin-top: 20px; }
        th, td { text-align: left; padding: 10px 14px; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        th { background: #f9fafb; }
        tr:last-child td { border-bottom: none; }
    </style>

    <h2>Historique des transactions</h2>

    <?php if (empty($transactions)): ?>
        <p>Aucune transaction trouvée.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Valeur</th>
                    <th>Frais</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $t): ?>
                    <tr>
                        <td><?= esc($t['created_at']) ?></td>
                        <td><?= esc($t['typeNom'] ?? '—') ?></td>
                        <td><?= number_format((float) $t['valeur'], 0, ',', ' ') ?> Ar</td>
                        <td><?= number_format((float) $t['frais'], 0, ',', ' ') ?> Ar</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?= $this->endSection() ?>
