<?= $this->extend('layouts/layoutAdmin') ?>
<?= $this->section('contenu') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Commissions autres opérateurs</h2>
    <a href="/admin/commissions/nouveau" class="btn btn-primary">+ Nouvelle commission</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (empty($commissions)): ?>
            <p class="text-muted mb-0">Aucune commission configurée.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Opérateur</th>
                            <th class="text-end">Pourcentage</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commissions as $c): ?>
                            <tr>
                                <td><?= esc($c['operateurNom'] ?? '—') ?></td>
                                <td class="text-end"><?= number_format((float) $c['pourcentage'], 2, ',', ' ') ?> %</td>
                                <td class="text-end">
                                    <a href="/admin/commissions/<?= (int) $c['id'] ?>/modifier" class="btn btn-sm btn-outline-secondary">Modifier</a>
                                    <form action="/admin/commissions/<?= (int) $c['id'] ?>/supprimer" method="post" class="d-inline">
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
