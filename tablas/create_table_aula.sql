CREATE TABLE aulas (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(100) NOT NULL,        -- Ej: 1DAW, Ateka, Medusa
    letra           CHAR(1) NOT NULL,             -- D (Lomo Derecho) o I (Lomo Izquierdo)
    numero          INT NOT NULL UNIQUE,          -- Número del aula, obligatorio y único
    planta          ENUM('Primera', 'Segunda', 'Tercera') NOT NULL
);

INSERT INTO aulas (nombre, letra, numero, planta)
VALUES ('1DAW', 'D', 101, 'Primera');

INSERT INTO aulas (nombre, letra, numero, planta)
VALUES ('Ateka', 'I', 202, 'Segunda');

INSERT INTO aulas (nombre, letra, numero, planta)
VALUES ('Medusa', 'D', 303, 'Tercera');
