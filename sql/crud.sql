
-- CREATE

INSERT INTO users (email, roles, password, nom, prenom, telephone, adresse, date_naissance, pseudo, is_suspended, is_chauffeur, is_passager, note, credits)
VALUES ('nouveau@ecoride.fr', '["ROLE_USER"]', 'passwordhash', 'Test', 'User', '0600000000', '4 rue SQL', '1998-04-12', 'testuser', FALSE, FALSE, TRUE, 4.0, 20);

INSERT INTO covoiturages (conducteur_id, date_depart, heure_depart, lieu_depart, date_arrivee, heure_arrivee, lieu_arrivee, statut, nb_place, prix_personne, voiture_id, created_at)
VALUES (2, '2025-07-01', '09:00:00', 'Toulouse', '2025-07-01', '11:30:00', 'Albi', 'ouvert', 2, 10.00, NULL, NOW());

-- READ 

SELECT * FROM users WHERE is_suspended = FALSE;

SELECT * FROM covoiturages WHERE lieu_arrivee = 'Lyon';

-- UPDATE 

UPDATE users SET note = 4.8 WHERE email = 'user@ecoride.fr';

UPDATE covoiturages SET nb_place = 1 WHERE id = 1;

-- DELETE

DELETE FROM covoiturage_user WHERE covoiturage_id = 1 AND user_id = 3;

DELETE FROM avis WHERE id = 5;
