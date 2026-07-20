<?= $this->extend('layouts/layoutAdmin') ?>
<?= $this->section('contenu') ?>

<h2 class="mb-4">Clients</h2>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" action="/admin/clients" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label for="q" class="form-label">Rechercher (nom, CIN ou numéro)</label>
                <input type="text" class="form-control" name="q" id="q" value="<?= esc($recherche) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Rechercher</button>
            </div>
            <div class="col-md-2">
                <a href="/admin/clients" class="btn btn-outline-secondary w-100">Réinitialiser</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (empty($clients)): ?>
            <p class="text-muted mb-0">Aucun client trouvé.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Numéro</th>
                            <th>CIN</th>
                            <th class="text-end">Solde</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $c): ?>
                            <tr>
                                <td><?= esc($c['nom']) ?></td>
                                <td><?= esc($c['numeros'] ?? '—') ?></td>
                                <td><?= esc($c['CIN']) ?></td>
                                <td class="text-end"><?= number_format((float) ($c['soldeValue'] ?? 0), 0, ',', ' ') ?> Ar</td>
                                <td class="text-end">
                                    <a href="/admin/clients/<?= (int) $c['id'] ?>" class="btn btn-sm btn-outline-primary">Détail</a>
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
