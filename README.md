# EcoRide – Plateforme de covoiturage d'entreprise

## 📌 Présentation

EcoRide est une application web de covoiturage destinée aux entreprises. Elle permet aux utilisateurs de créer, rechercher et réserver des trajets, et offre des interfaces spécifiques selon les rôles (utilisateur, employé, administrateur).

---

## 🛠️ Technologies utilisées

- PHP 8.2
- Symfony 6.x
- PostgreSQL
- Composer
- Bootstrap 5
- JavaScript / Chart.js
- Heroku (pour le déploiement distant)

---

## 💻 Déploiement en local

Suivez les étapes ci-dessous pour installer et exécuter le projet en local :

### 1. Cloner le dépôt

```bash
git clone https://github.com/jo1208/EcoRide.git
cd EcoRide

### 2. Installer les dépendances PHP

composer install

### 3. Configurer les variables d’environnement
Crée un fichier .env.local à la racine du projet :

APP_ENV=dev
APP_SECRET=changeme
DATABASE_URL="postgresql://postgres:password@127.0.0.1:5432/ecoride"

### 4. Créer la base de données

php bin/console doctrine:database:create

### 5. Lancer les migrations et insérer les données

php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load

###6. Lancer le serveur Symfony

Si tu utilises l’outil Symfony CLI :
symfony serve ou php -S localhost:8000 -t public

### Identifiants de test

Rôle	Email	Mot de passe
Admin	admin@ecoride.fr	adminpass
Employé	employe@ecoride.fr	employepass
Utilisateur	user@ecoride.fr	userpass
