USE bibliotheque;

-- Allow historical years (e.g. 1831) that are out of range for YEAR.
ALTER TABLE livres MODIFY annee_publication SMALLINT UNSIGNED NULL;

INSERT INTO livres (
	titre,
	auteur,
	isbn,
	annee_publication,
	categorie,
	resume,
	nom_fichier_couverture,
	statut
) VALUES
('Le Petit Prince', 'Antoine de Saint-Exupery', '9782070408504', 1943, 'Roman', 'Un pilote rencontre un jeune prince venu d une autre planete.', 'le_petit_prince.jpg', 'disponible'),
('L''Etranger', 'Albert Camus', '9782070360024', 1942, 'Roman', 'Meursault vit un proces apres un acte irreparable sur une plage d Alger.', 'l_etranger.jpg', 'disponible'),
('Notre-Dame de Paris', 'Victor Hugo', '9782253001683', 1831, 'Classique', 'Le destin tragique d Esmeralda, Quasimodo et Claude Frollo.', 'notre_dame_de_paris.jpg', 'prete'),
('Le Rouge et le Noir', 'Stendhal', '9782253004226', 1830, 'Classique', 'L ascension sociale de Julien Sorel entre ambition et passion.', 'le_rouge_et_le_noir.jpg', 'disponible'),
('Germinal', 'Emile Zola', '9782253004677', 1885, 'Classique', 'La vie difficile des mineurs et la naissance d une revolte ouvriere.', 'germinal.jpg', 'disponible'),
('Les Miserables', 'Victor Hugo', '9782253096344', 1862, 'Classique', 'Le parcours de Jean Valjean entre justice, redemption et compassion.', 'les_miserables.jpg', 'prete'),
('Madame Bovary', 'Gustave Flaubert', '9782070409228', 1857, 'Classique', 'Le portrait d Emma Bovary, en quete d un ideal inaccessible.', 'madame_bovary.jpg', 'disponible'),
('Bel-Ami', 'Guy de Maupassant', '9782253004837', 1885, 'Roman', 'Georges Duroy gravit les echelons du journalisme parisien.', 'bel_ami.jpg', 'disponible'),
('Candide', 'Voltaire', '9782070407804', 1759, 'Philosophie', 'Une satire du monde a travers le voyage initiatique de Candide.', 'candide.jpg', 'disponible'),
('La Peste', 'Albert Camus', '9782070360420', 1947, 'Roman', 'Une epidemie bouleverse la ville d Oran et revele les choix humains.', 'la_peste.jpg', 'disponible'),
('Le Comte de Monte-Cristo', 'Alexandre Dumas', '9782253003915', 1844, 'Aventure', 'Edmond Dantes prepare une vengeance apres une longue captivite.', 'monte_cristo.jpg', 'disponible'),
('Vingt Mille Lieues sous les mers', 'Jules Verne', '9782253006329', 1870, 'Science-fiction', 'Le professeur Aronnax explore les oceans a bord du Nautilus.', '20000_lieues.jpg', 'disponible');

create table user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    role VARCHAR(100),
    email VARCHAR(200) UNIQUE,
    password VARCHAR(200)
);

insert into user (nom, role, email, password) values ('admin', 'admin', 'admin@gmail.com', 'admin');
update user set role = 'admin' where id ='1';
INSERT INTO user VALUES (2, 'Jean', 'biblio', 'jean@gmail.com', '...');
INSERT INTO user VALUES (3, 'Marie', 'lecteur', 'marie@gmail.com', '...');

ALTER TABLE emprunts ADD COLUMN IF NOT EXISTS user_id INT NULL AFTER livre_id;
ALTER TABLE emprunts ADD COLUMN IF NOT EXISTS status TINYINT(1) NOT NULL DEFAULT 1 AFTER nom_emprunteur;
ALTER TABLE emprunts ADD COLUMN IF NOT EXISTS date_retour_prevue DATETIME NULL AFTER date_emprunt;

UPDATE emprunts SET user_id = 1 WHERE user_id IS NULL;
UPDATE emprunts SET status = CASE WHEN date_retour IS NULL THEN 1 ELSE 0 END;
UPDATE emprunts SET date_retour_prevue = DATE_ADD(date_emprunt, INTERVAL 14 DAY) WHERE date_retour_prevue IS NULL;

ALTER TABLE emprunts
	MODIFY user_id INT NOT NULL,
	MODIFY date_retour_prevue DATETIME NOT NULL;

CREATE TABLE IF NOT EXISTS reservations (
	id INT AUTO_INCREMENT PRIMARY KEY,
	livre_id INT NOT NULL,
	user_id INT NOT NULL,
	position_file INT NOT NULL,
	status ENUM('en_attente', 'notifiee', 'annulee', 'terminee') NOT NULL DEFAULT 'en_attente',
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	FOREIGN KEY (livre_id) REFERENCES livres(id),
	FOREIGN KEY (user_id) REFERENCES user(id)
);

CREATE TABLE IF NOT EXISTS auteurs (
	id INT AUTO_INCREMENT PRIMARY KEY,
	nom VARCHAR(255) NOT NULL UNIQUE,
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS livre_auteur (
	id INT AUTO_INCREMENT PRIMARY KEY,
	livre_id INT NOT NULL,
	auteur_id INT NOT NULL,
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (livre_id) REFERENCES livres(id) ON DELETE CASCADE,
	FOREIGN KEY (auteur_id) REFERENCES auteurs(id) ON DELETE CASCADE,
	UNIQUE KEY uq_livre_auteur (livre_id, auteur_id)
);

CREATE TABLE IF NOT EXISTS avis_livres (
	id INT AUTO_INCREMENT PRIMARY KEY,
	livre_id INT NOT NULL,
	user_id INT NOT NULL,
	note TINYINT NOT NULL,
	commentaire TEXT NOT NULL,
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (livre_id) REFERENCES livres(id) ON DELETE CASCADE,
	FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
	CHECK (note >= 1 AND note <= 5)
);

INSERT IGNORE INTO auteurs (nom)
SELECT DISTINCT TRIM(auteur)
FROM livres
WHERE auteur IS NOT NULL AND TRIM(auteur) <> '';

INSERT IGNORE INTO livre_auteur (livre_id, auteur_id)
SELECT l.id, a.id
FROM livres l
JOIN auteurs a ON a.nom = l.auteur;