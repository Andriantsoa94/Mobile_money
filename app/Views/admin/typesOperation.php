<?= $this->extend('layouts/layoutAdmin') ?>
<?= $this->section('contenu') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Types d'opération</h2>
    <a href="/admin/types/nouveau" class="btn btn-primary">+ Nouveau type</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (empty($types)): ?>
            <p class="text-muted mb-0">Aucun type d'opération.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Génère un gain ?</th>
                            <th>État</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($types as $t): ?>
                            <tr>
                                <td><?= esc($t['nom']) ?></td>
                                <td>
                                    <?php if (! empty($t['isGain'])): ?>
                                        <span class="badge bg-success">Oui</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Non</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (! empty($t['isActif'])): ?>
                                        <span class="badge bg-primary">Actif</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border">Inactif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <form action="/admin/types/<?= (int) $t['id'] ?>/basculer" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <?= ! empty($t['isActif']) ? 'Désactiver' : 'Activer' ?>
                                        </button>
                                    </form>
                                    <a href="/admin/types/<?= (int) $t['id'] ?>/modifier" class="btn btn-sm btn-outline-secondary">Modifier</a>
                                    <form action="/admin/types/<?= (int) $t['id'] ?>/supprimer" method="post" class="d-inline">
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
