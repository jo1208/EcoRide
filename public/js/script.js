document.addEventListener('DOMContentLoaded', function () {
    // Fonction pour afficher une alerte Bootstrap
    function showAlert(message, type) {
        const alertContainer = document.getElementById('alert-container');
        
        const alertDiv = document.createElement('div');
        alertDiv.classList.add('alert', `alert-${type}`, 'alert-dismissible', 'fade', 'show');
        alertDiv.setAttribute('role', 'alert');
        alertDiv.textContent = message;

        const closeButton = document.createElement('button');
        closeButton.classList.add('close');
        closeButton.setAttribute('type', 'button');
        closeButton.setAttribute('data-dismiss', 'alert');
        closeButton.setAttribute('aria-label', 'Close');
        closeButton.innerHTML = '&times;';
        alertDiv.appendChild(closeButton);
        
        alertContainer.appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.classList.remove('show');
            alertDiv.addEventListener('transitionend', () => {
                alertContainer.removeChild(alertDiv);
            });
        }, 5000);
    }

    // Code à exécuter une fois le DOM chargé
    const searchBtn = document.getElementById('searchBtn');
    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
            const depart = document.getElementById('depart').value;
            const arrivee = document.getElementById('arrivee').value;
            const date = document.getElementById('date').value;

            if (!depart || !arrivee || !date) {
                showAlert("Veuillez remplir tous les champs avant de soumettre la recherche.", 'danger');
            } else {
                showAlert("Recherche en cours...", 'success');
                console.log("Recherche avec :", depart, arrivee, date);
            }
        });
    }
});
