-- Insertion d'users
INSERT INTO users (email, roles, password, nom, prenom, telephone, adresse, date_naissance, pseudo, is_suspended, is_chauffeur, is_passager, note, credits)
VALUES ('admin@ecoride.fr', '["ROLE_ADMIN"]', '$2y$13$eKOPwJl4WEdawSG/izg.kuS.MEHoDYxAqFX44DdOskLSOYYWZcMK.', 'Admin', 'User', '0101010101', '1 rue du test', '1990-01-01', 'admino', FALSE, FALSE, TRUE, 5.0, 50),
('employe@ecoride.fr', '["ROLE_EMPLOYE"]', '$2y$13$4qSGoizSVYVuOtEKRxhN.uCQseDobvQ2y9zsgz3twkHxXsBmfL7ii', 'Jean', 'Dupont', '0202020202', '2 avenue dev', '1992-05-10', 'jeanjean', FALSE, TRUE, TRUE, 4.5, 40),
('user@ecoride.fr', '["ROLE_USER"]', '$2y$13$4Tnts3PobACa1CnC./P77eKtUwkKZokci1WbVgr/RHvVWz5aDehwu', 'Marie', 'Durand', '0303030303', '3 boulevard code', '1995-09-25', 'mariedu', FALSE, FALSE, TRUE, 4.2, 30);





-- covoiturage
INSERT INTO covoiturages (
    conducteur_id, date_depart, heure_depart, lieu_depart,
    date_arrivee, heure_arrivee, lieu_arrivee, statut,
    nb_place, prix_personne, voiture_id, created_at
)
VALUES (
    1, 2, '2025-05-15', '08:30:00', 'Paris',
    '2025-05-15', '11:30:00', 'Lyon', 'ouvert',
    3, 15.00, NULL, NOW()
);

-- Association d’un passager au covoiturage
INSERT INTO covoiturage_user (covoiturage_id, user_id)
VALUES (1, 3); -- le covoiturage 1 avec le passager ID 3

-- Insertion d'un avis
INSERT INTO avis (commentaire, note, statut, trajet_bien_passe, created_at, user_id, conducteur_id, trajet_id)
VALUES (
    'Très bon trajet, chauffeur ponctuel !',
    5,
    'validé',
    TRUE,
    NOW(),
    3,  -- ID utilisateur (ex : Marie)
    2,  -- ID conducteur (ex : Jean)
    1   -- ID du covoiturage correspondant
);


-- Préférences pour l'utilisateur 3 (Marie)
INSERT INTO preferences (fumeur, animal, musique, autres, user_id)
VALUES (FALSE, TRUE, TRUE, 'Aime discuter pendant le trajet.', 3);


-- Exemple de voiture pour l'utilisateur 2 (Jean)
INSERT INTO voiture (
    modele, marque, immatriculation, couleur,
    date_premiere_immatriculation, ecologique, nb_place, energie, user_id
) VALUES (
    'Model 3', 'Tesla', 'AA-123-BB', 'Noir',
    '2022-06-01', TRUE, 5, 'Électrique', 2
);
