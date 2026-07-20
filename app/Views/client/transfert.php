<?= $this->extend('layouts/layout_client') ?>
<?= $this->section('content') ?>

    <h2>Transfert</h2>

    <p>Solde actuel : <strong><?= number_format((float) $solde, 0, ',', ' ') ?> Ar</strong></p>

    <form id="formTransfert" method="post" action="/client/transfert">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="numero">Numéro du destinataire</label>
            <input type="text" name="numero" id="numero" placeholder="0331234567" value="<?= old('numero') ?>" required>
        </div>

        <div class="form-group">
            <label for="montant">Montant à transférer (Ar)</label>
            <input type="number" name="montant" id="montant" min="1" step="1" required>
        </div>

        <button type="button" class="btn" onclick="ouvrirModal('modalTransfert')">Transférer</button>
        <a href="/client" class="btn btn-secondary">Annuler</a>
    </form>

    <?= view('components/modal_confirmation', [
        'modalId' => 'modalTransfert',
        'formId'  => 'formTransfert',
        'titre'   => 'Confirmer le transfert',
        'message' => 'Voulez-vous confirmer ce transfert vers ce numéro ?',
    ]) ?>

<?= $this->endSection() ?>
