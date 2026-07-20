<?= $this->extend('layouts/layoutAdmin') ?>
<?= $this->section('contenu') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Préfixes</h2>
    <a href="/admin/prefixes/nouveau" class="btn btn-primary">+ Nouveau préfixe</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (empty($prefixes)): ?>
            <p class="text-muted mb-0">Aucun préfixe enregistré.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Préfixe</th>
                            <th>Opérateur</th>
                            <th>Appartenance</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prefixes as $p): ?>
                            <tr>
                                <td><code><?= esc($p['numero']) ?></code></td>
                                <td><?= esc($p['operateurNom'] ?? '—') ?></td>
                                <td>
                                    <?php if ((int) ($p['appartenance'] ?? 0) === 1): ?>
                                        <span class="badge bg-primary">Nous appartient</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Autre opérateur</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="/admin/prefixes/<?= (int) $p['id'] ?>/modifier" class="btn btn-sm btn-outline-secondary">Modifier</a>
                                    <form action="/admin/prefixes/<?= (int) $p['id'] ?>/supprimer" method="post" class="d-inline">
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
