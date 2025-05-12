# EcoRide – Plateforme de covoiturage d'entreprise

## 📌 Présentation

EcoRide est une application web de covoiturage destinée aux entreprises. Elle permet aux utilisateurs de créer, rechercher et réserver des trajets, et offre des interfaces spécifiques selon les rôles (utilisateur, employé, administrateur).

---

## 🛠️ Technologies utilisées

- PHP 8.2
- Symfony 7.2
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


🗄️ Base de données SQL manuelle
Afin de démontrer la maîtrise de la base de données indépendamment des outils Symfony ou Doctrine, deux fichiers SQL ont été réalisés manuellement :

sql/schema.sql : contient les instructions SQL pures pour créer l’ensemble des tables, les clés primaires et étrangères, ainsi que les contraintes nécessaires à l’application (relations OneToMany, ManyToMany, OneToOne, etc.).

sql/data.sql : permet d’insérer des données de démonstration manuelles cohérentes dans toutes les tables (utilisateurs, covoiturages, véhicules, avis, préférences, etc.).

Ces fichiers permettent d’installer et de tester la base sans aucune dépendance à Doctrine, et peuvent être exécutés dans n’importe quelle instance PostgreSQL avec les commandes suivantes :

dropdb -U postgres ecoride
createdb -U postgres ecoride
psql -U postgres -d ecoride -f sql/schema.sql
psql -U postgres -d ecoride -f sql/data.sql
