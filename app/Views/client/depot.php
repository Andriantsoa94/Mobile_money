<?= $this->extend('layouts/layout_client') ?>
<?= $this->section('content') ?>

    <h2>Dépôt</h2>

    <p>Solde actuel : <strong><?= number_format((float) $solde, 0, ',', ' ') ?> Ar</strong></p>

    <form id="formDepot" method="post" action="/client/depot">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="montant">Montant à déposer (Ar)</label>
            <input type="number" name="montant" id="montant" min="1" step="1" required>
        </div>

        <button type="button" class="btn" onclick="ouvrirModal('modalDepot')">Déposer</button>
        <a href="/client" class="btn btn-secondary">Annuler</a>
    </form>

    <?= view('components/modal_confirmation', [
        'modalId' => 'modalDepot',
        'formId'  => 'formDepot',
        'titre'   => 'Confirmer le dépôt',
        'message' => 'Voulez-vous confirmer ce dépôt ?',
    ]) ?>

<?= $this->endSection() ?>
