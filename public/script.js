
    // Fonction pour afficher une alerte Bootstrap
    function showAlert(message, type) {
        const alertContainer = document.getElementById('alert-container');
        
        // Créer l'élément div de l'alerte
        const alertDiv = document.createElement('div');
        alertDiv.classList.add('alert', `alert-${type}`, 'alert-dismissible', 'fade', 'show');
        alertDiv.setAttribute('role', 'alert');
        
        // Ajouter le message de l'alerte
        alertDiv.textContent = message;
        
        // Ajouter un bouton de fermeture pour l'alerte
        const closeButton = document.createElement('button');
        closeButton.classList.add('close');
        closeButton.setAttribute('type', 'button');
        closeButton.setAttribute('data-dismiss', 'alert');
        closeButton.setAttribute('aria-label', 'Close');
        closeButton.innerHTML = '&times;';
        alertDiv.appendChild(closeButton);
        
        // Ajouter l'alerte au conteneur
        alertContainer.appendChild(alertDiv);
        
        // L'alerte disparaît après 5 secondes
        setTimeout(() => {
            alertDiv.classList.remove('show');
            alertDiv.addEventListener('transitionend', () => {
                alertContainer.removeChild(alertDiv);
            });
        }, 5000);
    }

    // Ajout d'un écouteur d'événement au bouton de recherche
    document.getElementById('searchBtn').addEventListener('click', function() {
        // Récupérer les valeurs des champs
        const depart = document.getElementById('depart').value;
        const arrivee = document.getElementById('arrivee').value;
        const date = document.getElementById('date').value;

        // Validation : vérifier que tous les champs sont remplis
        if (!depart || !arrivee || !date) {
            showAlert("Veuillez remplir tous les champs avant de soumettre la recherche.", 'danger');
        } else {
            // Si tout est bon, afficher un message de succès
            showAlert("Recherche en cours...", 'success');

            // Exemple d'action après validation (par exemple, afficher les résultats dans la console)
            console.log("Recherche avec :", depart, arrivee, date);
        }
    });
