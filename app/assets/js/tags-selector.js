document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner l'élément select pour les tags
    const tagsSelect = document.querySelector('.select-tags');
    
    if (tagsSelect) {
        // Ajouter les attributs pour permettre une meilleure interaction
        tagsSelect.setAttribute('multiple', 'multiple');
        
        // Ajout de la possibilité d'ajouter un nouveau tag (à implémenter côté serveur plus tard)
        const tagsForm = tagsSelect.closest('form');
        if (tagsForm) {
            tagsForm.addEventListener('submit', function(e) {
                // Vous pouvez ajouter ici une logique pour traiter les nouveaux tags si nécessaire
                console.log('Formulaire soumis avec les tags sélectionnés');
            });
        }
    }
});