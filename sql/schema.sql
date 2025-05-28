-- Table users
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(180) NOT NULL UNIQUE,
    roles JSON NOT NULL,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(255),
    prenom VARCHAR(255),
    telephone VARCHAR(255),
    adresse VARCHAR(255),
    date_naissance DATE,
    photo BYTEA,
    pseudo VARCHAR(255),
    is_suspended BOOLEAN NOT NULL DEFAULT FALSE,
    is_chauffeur BOOLEAN NOT NULL DEFAULT FALSE,
    is_passager BOOLEAN NOT NULL DEFAULT TRUE,
    note FLOAT,
    credits INTEGER DEFAULT 20
);

-- Table des voitures
CREATE TABLE voiture (
    id SERIAL PRIMARY KEY,
    modele VARCHAR(255) NOT NULL,
    marque VARCHAR(255) NOT NULL,
    immatriculation VARCHAR(255) NOT NULL,
    couleur VARCHAR(255) NOT NULL,
    date_premiere_immatriculation DATE NOT NULL,
    ecologique BOOLEAN DEFAULT FALSE,
    nb_place INTEGER,
    energie VARCHAR(255),
    user_id INTEGER,
    CONSTRAINT fk_voiture_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des covoiturages
CREATE TABLE covoiturages (
    id SERIAL PRIMARY KEY,
    conducteur_id INTEGER,
    date_depart DATE NOT NULL,
    heure_depart TIME NOT NULL,
    lieu_depart VARCHAR(255) NOT NULL,
    date_arrivee DATE NOT NULL,
    heure_arrivee TIME NOT NULL,
    lieu_arrivee VARCHAR(255) NOT NULL,
    statut VARCHAR(255),
    nb_place INTEGER NOT NULL,
    prix_personne FLOAT NOT NULL,
    voiture_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_covoiturage_conducteur FOREIGN KEY (conducteur_id) REFERENCES users(id),
    CONSTRAINT fk_covoiturage_voiture FOREIGN KEY (voiture_id) REFERENCES voiture(id)
);

-- Table des avis
CREATE TABLE avis (
    id SERIAL PRIMARY KEY,
    commentaire VARCHAR(255),
    note INTEGER NOT NULL,
    statut VARCHAR(255),
    trajet_bien_passe BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INTEGER,
    conducteur_id INTEGER,
    trajet_id INTEGER,
    CONSTRAINT fk_avis_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_avis_conducteur FOREIGN KEY (conducteur_id) REFERENCES users(id),
    CONSTRAINT fk_avis_trajet FOREIGN KEY (trajet_id) REFERENCES covoiturages(id)
);



-- Table pivot pour les passagers
CREATE TABLE covoiturage_user (
    covoiturage_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    PRIMARY KEY (covoiturage_id, user_id),
    CONSTRAINT fk_passager_covoiturage FOREIGN KEY (covoiturage_id) REFERENCES covoiturages(id) ON DELETE CASCADE,
    CONSTRAINT fk_passager_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des préférences utilisateur
CREATE TABLE preferences (
    id SERIAL PRIMARY KEY,
    fumeur BOOLEAN,
    animal BOOLEAN,
    musique BOOLEAN,
    autres TEXT,
    user_id INTEGER NOT NULL UNIQUE,
    CONSTRAINT fk_preferences_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


