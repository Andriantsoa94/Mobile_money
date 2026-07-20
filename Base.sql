CREATE TABLE role (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    type       VARCHAR(100) NOT NULL,
    created_at DATETIME,
    updated_at DATETIME
);

CREATE TABLE user (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    nom        VARCHAR(100) NOT NULL,
    CIN        DECIMAL(10,2) NOT NULL,
    idrole     INTEGER,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (idrole) REFERENCES role(id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Table : operateur
CREATE TABLE operateur (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    nom        VARCHAR(100) NOT NULL,
    created_at DATETIME,
    updated_at DATETIME
);

-- Table : prefixe
CREATE TABLE prefixe (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    numero       DECIMAL(10,2) NOT NULL,
    idoperateur  INTEGER,
    created_at   DATETIME,
    updated_at   DATETIME,
    FOREIGN KEY (idoperateur) REFERENCES operateur(id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE typeOperation (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    nom        VARCHAR(100) NOT NULL,
    isGain     BOOLEAN,
    isActif    BOOLEAN NOT NULL DEFAULT 1,
    created_at DATETIME,
    updated_at DATETIME
);

CREATE TABLE config (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    min        DECIMAL(10,2) NOT NULL,
    max        DECIMAL(10,2) NOT NULL,
    gain       DECIMAL(10,2) NOT NULL,
    created_at DATETIME,
    updated_at DATETIME
);

CREATE TABLE "transaction" (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    idOperateur      INTEGER,
    idTypeOperation  INTEGER,
    gain             DECIMAL(10,2) NOT NULL,
    idUser           INTEGER,
    created_at       DATETIME NOT NULL,
    updated_at       DATETIME,
    FOREIGN KEY (idOperateur)     REFERENCES operateur(id)     ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (idTypeOperation) REFERENCES typeOperation(id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (idUser)          REFERENCES user(id)          ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE solde (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    value      DECIMAL(10,2) NOT NULL,
    idUser     INTEGER,
    created_at DATETIME NOT NULL,
    updated_at DATETIME,
    FOREIGN KEY (idUser) REFERENCES user(id) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE numero (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    numero     DECIMAL(10,2) NOT NULL,
    iduser     INTEGER,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (iduser) REFERENCES user(id) ON DELETE SET NULL ON UPDATE CASCADE
);



INSERT INTO role (id, type, created_at, updated_at) VALUES
(1, 'admin',  datetime('now'), datetime('now')),
(2, 'client', datetime('now'), datetime('now'));

INSERT INTO operateur (id, nom, created_at, updated_at) VALUES
(1, 'Telma', datetime('now'), datetime('now'));

INSERT INTO prefixe (id, numero, idoperateur, created_at, updated_at) VALUES
(1, '033', 1, datetime('now'), datetime('now')),
(2, '037', 1, datetime('now'), datetime('now'));

INSERT INTO user (id, nom, CIN, idrole, created_at, updated_at) VALUES
(1, 'Jean',  '1234567890', 1, datetime('now'), datetime('now')), -- admin
(2, 'Marie', '1111111111', 2, datetime('now'), datetime('now')), -- client
(3, 'Paul',  '2222222222', 2, datetime('now'), datetime('now')); -- client

INSERT INTO numero (id, numero, iduser, created_at, updated_at) VALUES
(1, '0330000000', 1, datetime('now'), datetime('now')), -- admin
(2, '0331111111', 2, datetime('now'), datetime('now')), -- Marie
(3, '0372222222', 3, datetime('now'), datetime('now')); -- Paul

INSERT INTO solde (id, value, idUser, created_at, updated_at) VALUES
(1, 50000, 2, datetime('now'), datetime('now')), -- solde de Marie
(2, 15000, 3, datetime('now'), datetime('now')); -- solde de Paul

INSERT INTO typeOperation (id, nom, isGain, isActif, created_at, updated_at) VALUES
(1, 'Depot',     0, 1, datetime('now'), datetime('now')),
(2, 'Retrait',   1, 1, datetime('now'), datetime('now')),
(3, 'Transfert', 1, 1, datetime('now'), datetime('now'));

INSERT INTO config (id, min, max, gain, created_at, updated_at) VALUES
(1,  100,      1000,    50,   datetime('now'), datetime('now')),
(2,  1001,     5000,    50,   datetime('now'), datetime('now')),
(3,  5001,     10000,   100,  datetime('now'), datetime('now')),
(4,  10001,    25000,   200,  datetime('now'), datetime('now')),
(5,  25001,    50000,   400,  datetime('now'), datetime('now')),
(6,  50001,    100000,  800,  datetime('now'), datetime('now')),
(7,  100001,   250000,  1500, datetime('now'), datetime('now')),
(8,  250001,   500000,  1500, datetime('now'), datetime('now')),
(9,  500001,   1000000, 2500, datetime('now'), datetime('now')),
(10, 1000001,  2000000, 3000, datetime('now'), datetime('now'));