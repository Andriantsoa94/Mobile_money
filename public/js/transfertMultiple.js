// Transfert multiple : ajout dynamique de lignes destinataire + verification operateur.
// Les prefixes valides et leur operateur sont injectes par la vue dans window.prefixesOperateurs
// sous la forme { "033": 1, "037": 1, "034": 2, ... }.

(function () {
    'use strict';

    var conteneur = document.getElementById('lignesDestinataires');
    var boutonAjouter = document.getElementById('ajouterLigne');
    var messageOperateur = document.getElementById('messageOperateur');

    if (!conteneur || !boutonAjouter) {
        return;
    }

    // Cree une nouvelle ligne (numero + montant) et l'ajoute au conteneur.
    function ajouterLigne() {
        var index = conteneur.querySelectorAll('.ligneDestinataire').length + 1;

        var ligne = document.createElement('div');
        ligne.className = 'ligneDestinataire';

        ligne.innerHTML =
            '<span class="numeroLigne">#' + index + '</span>' +
            '<div class="form-group">' +
                '<label>Numéro du destinataire</label>' +
                '<input type="text" name="numero[]" class="champNumero" placeholder="0331234567">' +
            '</div>' +
            '<div class="form-group">' +
                '<label>Montant (Ar)</label>' +
                '<input type="number" name="montant[]" min="1" step="1">' +
            '</div>' +
            '<button type="button" class="btn btn-secondary btnSupprimer">X</button>';

        conteneur.appendChild(ligne);

        ligne.querySelector('.btnSupprimer').addEventListener('click', function () {
            ligne.remove();
            renumeroter();
            verifierOperateurs();
        });

        ligne.querySelector('.champNumero').addEventListener('input', verifierOperateurs);
    }

    // Remet a jour les numeros de ligne (#1, #2, ...) apres une suppression.
    function renumeroter() {
        var lignes = conteneur.querySelectorAll('.ligneDestinataire');
        for (var i = 0; i < lignes.length; i++) {
            var badge = lignes[i].querySelector('.numeroLigne');
            if (badge) {
                badge.textContent = '#' + (i + 1);
            }
        }
    }

    // Retourne l'operateur associe a un numero via son prefixe (3 premiers chiffres),
    // ou null si le prefixe n'est pas reconnu.
    function operateurDe(numero) {
        var prefixe = (numero || '').substring(0, 3);
        var table = window.prefixesOperateurs || {};
        return Object.prototype.hasOwnProperty.call(table, prefixe) ? table[prefixe] : null;
    }

    // Verifie que tous les numeros saisis appartiennent au meme operateur.
    function verifierOperateurs() {
        var champs = conteneur.querySelectorAll('.champNumero');
        var operateurRef = null;
        var conflit = false;

        for (var i = 0; i < champs.length; i++) {
            var valeur = champs[i].value.trim();
            if (valeur.length < 3) {
                continue;
            }

            var op = operateurDe(valeur);
            if (op === null) {
                continue; // prefixe inconnu, laisse le backend gerer
            }

            if (operateurRef === null) {
                operateurRef = op;
            } else if (op !== operateurRef) {
                conflit = true;
                break;
            }
        }

        if (messageOperateur) {
            messageOperateur.style.display = conflit ? 'block' : 'none';
        }
    }

    boutonAjouter.addEventListener('click', ajouterLigne);

    // Brancher la verification sur les champs deja presents au chargement.
    var champsInitiaux = conteneur.querySelectorAll('.champNumero');
    for (var i = 0; i < champsInitiaux.length; i++) {
        champsInitiaux[i].addEventListener('input', verifierOperateurs);
    }
})();
