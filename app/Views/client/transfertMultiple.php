<?= $this->extend('layouts/layout_client') ?>
<?= $this->section('content') ?>

    <style>
        .ligneDestinataire { display: flex; gap: 10px; margin-bottom: 12px; align-items: flex-end; }
        .ligneDestinataire .form-group { flex: 1; margin-bottom: 0; }
        .numeroLigne { color: #6b7280; font-size: 13px; width: 24px; padding-bottom: 8px; }
    </style>

    <h2>Transfert vers plusieurs numéros</h2>

    <p>Solde actuel : <strong><?= number_format((float) $solde, 0, ',', ' ') ?> Ar</strong></p>
    <p class="text-muted" style="font-size: 14px;">
        Renseignez au moins un destinataire (les lignes vides sont ignorées).
        Tous les numéros doivent appartenir au même opérateur.
    </p>

    <form id="formTransfertMultiple" method="post" action="/client/transfert/multiple">
        <?= csrf_field() ?>

        <?php for ($i = 1; $i <= 5; $i++): ?>
            <div class="ligneDestinataire">
                <span class="numeroLigne">#<?= $i ?></span>
                <div class="form-group">
                    <label for="numero<?= $i ?>">Numéro du destinataire</label>
                    <input type="text" name="numero[]" id="numero<?= $i ?>" placeholder="0331234567"
                           value="<?= esc(old('numero.' . ($i - 1))) ?>">
                </div>
                <div class="form-group">
                    <label for="montant<?= $i ?>">Montant (Ar)</label>
                    <input type="number" name="montant[]" id="montant<?= $i ?>" min="1" step="1"
                           value="<?= esc(old('montant.' . ($i - 1))) ?>">
                </div>
            </div>
        <?php endfor; ?>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input type="hidden" name="frais" value="0">
                <input class="form-check-input"
                       type="checkbox"
                       role="switch"
                       name="frais"
                       id="fraisSwitch"
                       value="1"
                        <?= old('frais', '1') === '1' ? 'checked' : '' ?>>
                <label class="form-check-label fw-bold ms-2" for="fraisSwitch">
                    Inclure frais
                </label>
            </div>
        </div>

        <button type="button" class="btn" onclick="ouvrirModal('modalTransfertMultiple')">Envoyer</button>
        <a href="/client/transfert" class="btn btn-secondary">Transfert simple</a>
        <a href="/client" class="btn btn-secondary">Annuler</a>
    </form>

    <?= view('components/modal_confirmation', [
        'modalId' => 'modalTransfertMultiple',
        'formId'  => 'formTransfertMultiple',
        'titre'   => 'Confirmer les transferts',
        'message' => 'Voulez-vous confirmer l\'envoi vers tous les destinataires renseignés ?',
    ]) ?>

<?= $this->endSection() ?>
