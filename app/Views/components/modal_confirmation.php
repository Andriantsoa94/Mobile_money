<?php
/**
 * Composant réutilisable : modal de confirmation.
 *
 * Variables attendues à l'inclusion :
 *  - $modalId (string) identifiant unique du modal
 *  - $formId  (string) id du <form> à soumettre si l'utilisateur confirme
 *  - $titre   (string) titre affiché
 *  - $message (string) texte de confirmation
 *
 * Exemple d'utilisation dans une vue :
 *   <?= $this->include('components/modal_confirmation', [
 *       'modalId' => 'modalDepot',
 *       'formId'  => 'formDepot',
 *       'titre'   => 'Confirmer le dépôt',
 *       'message' => 'Voulez-vous confirmer ce dépôt ?',
 *   ]) ?>
 */
?>
<div id="<?= esc($modalId) ?>" class="modal-overlay">
    <div class="modal-box">
        <h3><?= esc($titre) ?></h3>
        <p><?= esc($message) ?></p>
        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" onclick="fermerModal('<?= esc($modalId, 'js') ?>')">Annuler</button>
            <button type="button" class="btn" onclick="confirmerModal('<?= esc($formId, 'js') ?>')">Confirmer</button>
        </div>
    </div>
</div>
