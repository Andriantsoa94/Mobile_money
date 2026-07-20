<?= $this->extend('layouts/layout_client') ?>
<?= $this->section('content') ?>

    <style>
        .ligneDestinataire { display: flex; gap: 10px; margin-bottom: 12px; align-items: flex-end; }
        .ligneDestinataire .form-group { flex: 1; margin-bottom: 0; }
        .numeroLigne { color: #6b7280; font-size: 13px; width: 24px; padding-bottom: 8px; }
        .btnSupprimer { padding: 6px 12px; }
        #messageOperateur { display: none; color: #991b1b; background: #fee2e2; padding: 8px 12px; border-radius: 6px; margin-bottom: 12px; font-size: 14px; }
    </style>

    <h2>Transfert vers plusieurs numéros</h2>

    <p>Solde actuel : <strong><?= number_format((float) $solde, 0, ',', ' ') ?> Ar</strong></p>
    <p class="text-muted" style="font-size: 14px;">
        Ajoutez autant de destinataires que nécessaire. Tous les numéros doivent
        appartenir au même opérateur.
    </p>

    <div id="messageOperateur">
        Attention : les numéros saisis ne sont pas tous du même opérateur.
    </div>

    <form id="formTransfertMultiple" method="post" action="/client/transfert/multiple">
        <?= csrf_field() ?>

        <div id="lignesDestinataires">
            <?php
            // Au rechargement (erreur backend), on réaffiche les lignes saisies ;
            // sinon on part avec une seule ligne vide.
            $numerosSaisis  = old('numero') ?? [''];
            $montantsSaisis = old('montant') ?? [''];
            foreach ($numerosSaisis as $i => $numeroSaisi):
            ?>
                <div class="ligneDestinataire">
                    <span class="numeroLigne">#<?= $i + 1 ?></span>
                    <div class="form-group">
                        <label>Numéro du destinataire</label>
                        <input type="text" name="numero[]" class="champNumero" placeholder="0331234567"
                               value="<?= esc($numeroSaisi) ?>">
                    </div>
                    <div class="form-group">
                        <label>Montant (Ar)</label>
                        <input type="number" name="montant[]" min="1" step="1"
                               value="<?= esc($montantsSaisis[$i] ?? '') ?>">
                    </div>
                    <?php if ($i > 0): ?>
                        <button type="button" class="btn btn-secondary btnSupprimer">X</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mb-3">
            <button type="button" id="ajouterLigne" class="btn btn-secondary">+ Ajouter un numéro</button>
        </div>

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

    <script>
        // Table des préfixes -> opérateur, pour la vérification côté client.
        window.prefixesOperateurs = <?= json_encode($prefixesOperateurs ?? []) ?>;
    </script>
    <script src="/js/transfertMultiple.js"></script>

<?= $this->endSection() ?>
