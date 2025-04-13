// assets/js/select2-init.js

// Attendre que jQuery soit chargé
function waitForJQuery(callback) {
    if (typeof jQuery !== 'undefined') {
        // jQuery est chargé
        callback();
    } else {
        // Réessayer dans 50ms
        setTimeout(function() {
            waitForJQuery(callback);
        }, 50);
    }
}

// Fonction pour initialiser Select2
function initSelect2() {
    waitForJQuery(function() {
        // Vérifier que Select2 est disponible
        if (typeof $.fn.select2 !== 'undefined') {
            // Détruire les instances existantes pour éviter les doublons
            $('.select-tags').select2('destroy');
            
            // Initialiser Select2
            $('.select-tags').select2({
                placeholder: 'Sélectionnez un ou plusieurs tags',
                allowClear: true,
                closeOnSelect: false,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Aucun tag ne correspond à votre recherche";
                    }
                }
            });
            
            console.log('Select2 a été initialisé avec succès');
        } else {
            console.error('Select2 n\'est pas chargé correctement');
            // Charger Select2 dynamiquement si nécessaire
            loadSelect2();
        }
    });
}

// Charger Select2 dynamiquement
function loadSelect2() {
    // Créer et charger le CSS de Select2
    var linkElement = document.createElement('link');
    linkElement.rel = 'stylesheet';
    linkElement.href = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css';
    document.head.appendChild(linkElement);
    
    // Créer et charger le JS de Select2
    var scriptElement = document.createElement('script');
    scriptElement.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
    scriptElement.onload = function() {
        // Une fois chargé, initialiser Select2
        initSelect2();
    };
    document.body.appendChild(scriptElement);
}

// Initialiser au chargement du DOM
document.addEventListener('DOMContentLoaded', function() {
    initSelect2();
});

// Réinitialiser après le chargement complet de la page
window.addEventListener('load', function() {
    initSelect2();
});

// Fallback pour s'assurer que Select2 est initialisé après un certain délai
setTimeout(function() {
    initSelect2();
}, 300);

// Exposer la fonction pour permettre une réinitialisation manuelle
window.initSelect2 = initSelect2;