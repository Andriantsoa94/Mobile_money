<?= $this->extend('layouts/layoutAdmin') ?>
<?= $this->section('contenu') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Barèmes (frais et gain par tranche)</h2>
    <a href="/admin/config/nouveau" class="btn btn-primary">+ Nouvelle tranche</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (empty($baremes)): ?>
            <p class="text-muted mb-0">Aucune tranche configurée.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-end">Montant min</th>
                            <th class="text-end">Montant max</th>
                            <th class="text-end">Frais (client)</th>
                            <th class="text-end">Gain (plateforme)</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($baremes as $b): ?>
                            <tr>
                                <td class="text-end"><?= number_format((float) $b['min'], 0, ',', ' ') ?> Ar</td>
                                <td class="text-end"><?= number_format((float) $b['max'], 0, ',', ' ') ?> Ar</td>
                                <td class="text-end"><?= number_format((float) $b['frais'], 0, ',', ' ') ?> Ar</td>
                                <td class="text-end"><?= number_format((float) $b['gain'], 0, ',', ' ') ?> Ar</td>
                                <td class="text-end">
                                    <a href="/admin/config/<?= (int) $b['id'] ?>/modifier" class="btn btn-sm btn-outline-secondary">Modifier</a>
                                    <form action="/admin/config/<?= (int) $b['id'] ?>/supprimer" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
