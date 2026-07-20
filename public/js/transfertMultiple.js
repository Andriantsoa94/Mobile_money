// Transfert multiple : ajout dynamique de numeros + verification meme operateur.
// Le montant total est unique (divise cote backend), seuls les numeros se multiplient.
// window.prefixesOperateurs est injecte par la vue : { "033": 1, "037": 1, "032": 2, ... }

(function () {
    'use strict';

    var conteneur = document.getElementById('lignesNumeros');
    var boutonAjouter = document.getElementById('ajouterLigne');
    var messageOperateur = document.getElementById('messageOperateur');

    if (!conteneur || !boutonAjouter) {
        return;
    }

    function ajouterLigne() {
        var index = conteneur.querySelectorAll('.ligneNumero').length + 1;

        var ligne = document.createElement('div');
        ligne.className = 'ligneNumero';

        ligne.innerHTML =
            '<span class="numeroLigne">#' + index + '</span>' +
            '<div class="form-group">' +
                '<label>Numéro du destinataire</label>' +
                '<input type="text" name="numero[]" class="champNumero" placeholder="0331234567">' +
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

    function renumeroter() {
        var lignes = conteneur.querySelectorAll('.ligneNumero');
        for (var i = 0; i < lignes.length; i++) {
            var badge = lignes[i].querySelector('.numeroLigne');
            if (badge) {
                badge.textContent = '#' + (i + 1);
            }
        }
    }

    // Retourne l'operateur pour un prefixe connu, ou le prefixe brut lui-meme
    // si le prefixe n'est pas dans la table (permet quand meme de comparer
    // "meme operateur" entre numeros non repertories).
    function cleOperateurDe(numero) {
        var prefixe = (numero || '').substring(0, 3);
        var table = window.prefixesOperateurs || {};
        if (Object.prototype.hasOwnProperty.call(table, prefixe)) {
            return 'op:' + table[prefixe];
        }
        return 'prefixe:' + prefixe;
    }

    function verifierOperateurs() {
        var champs = conteneur.querySelectorAll('.champNumero');
        var reference = null;
        var conflit = false;

        for (var i = 0; i < champs.length; i++) {
            var valeur = champs[i].value.trim();
            if (valeur.length < 3) {
                continue;
            }

            var cle = cleOperateurDe(valeur);

            if (reference === null) {
                reference = cle;
            } else if (cle !== reference) {
                conflit = true;
                break;
            }
        }

        if (messageOperateur) {
            messageOperateur.style.display = conflit ? 'block' : 'none';
        }
    }

    boutonAjouter.addEventListener('click', ajouterLigne);

    var champsInitiaux = conteneur.querySelectorAll('.champNumero');
    for (var i = 0; i < champsInitiaux.length; i++) {
        champsInitiaux[i].addEventListener('input', verifierOperateurs);
    }
})();
