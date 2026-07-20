CREATE DATABASE IF NOT EXISTS bibliotheque;

USE bibliotheque;

CREATE TABLE IF NOT EXISTS livres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    auteur VARCHAR(255) NOT NULL,
    isbn VARCHAR(32) NOT NULL UNIQUE,
    annee_publication SMALLINT UNSIGNED,
    categorie VARCHAR(120),
    resume TEXT,
    nom_fichier_couverture VARCHAR(255),
    statut ENUM('disponible', 'prete') NOT NULL DEFAULT 'disponible',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

create table user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    role VARCHAR(100),
    email VARCHAR(200) UNIQUE,
    password VARCHAR(200)
);

CREATE TABLE IF NOT EXISTS emprunts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    livre_id INT NOT NULL,
    user_id INT NOT NULL,
    nom_emprunteur VARCHAR(255) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    date_emprunt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_retour_prevue DATETIME NOT NULL,
    date_retour DATETIME,
    FOREIGN KEY (livre_id) REFERENCES livres(id),
    FOREIGN KEY (user_id) REFERENCES user(id)
);

CREATE INDEX idx_emprunts_status_retour_prevue ON emprunts(status, date_retour_prevue);

create Table empruntes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_livres INT,
    id_user INT
);

ALTER TABLE emprunts ADD COLUMN IF NOT EXISTS user_id INT NOT NULL AFTER livre_id;
ALTER TABLE emprunts ADD COLUMN IF NOT EXISTS status TINYINT(1) NOT NULL DEFAULT 1 AFTER nom_emprunteur;
ALTER TABLE emprunts ADD COLUMN IF NOT EXISTS date_retour_prevue DATETIME NULL AFTER date_emprunt;

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

CREATE INDEX idx_reservations_livre_status ON reservations(livre_id, status, position_file);

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