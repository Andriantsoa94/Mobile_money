<?= $this->extend('layouts/layout_client') ?>
<?= $this->section('content') ?>

    <h2>Retrait</h2>

    <p>Solde actuel : <strong><?= number_format((float) $solde, 0, ',', ' ') ?> Ar</strong></p>

    <form id="formRetrait" method="post" action="/client/retrait">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="montant">Montant à retirer (Ar)</label>
            <input type="number" name="montant" id="montant" min="1" step="1" required>
        </div>

        <button type="button" class="btn" onclick="ouvrirModal('modalRetrait')">Retirer</button>
        <a href="/client" class="btn btn-secondary">Annuler</a>
    </form>

    <?= view('components/modal_confirmation', [
        'modalId' => 'modalRetrait',
        'formId'  => 'formRetrait',
        'titre'   => 'Confirmer le retrait',
        'message' => 'Voulez-vous confirmer ce retrait ? Les frais éventuels seront déduits de votre solde.',
    ]) ?>

<?= $this->endSection() ?>
