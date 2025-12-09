-- Tabla principal de profesores
CREATE TABLE profesores (
    id_profesor    INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(255) NOT NULL,
    email          VARCHAR(255) NOT NULL UNIQUE,
    es_tutor       BOOLEAN DEFAULT FALSE
);

-- Tabla de cursos (si no la tienes ya)
CREATE TABLE cursos (
    id_curso       INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(100) NOT NULL,
    numero         INT NOT NULL,
    letra          CHAR(1),
    planta         ENUM('Primera','Segunda','Tercera')
);

-- Tabla intermedia profesor-cursos para tutorías
CREATE TABLE tutorias (
    id_tutoria     INT AUTO_INCREMENT PRIMARY KEY,
    id_profesor    INT NOT NULL,
    id_curso       INT NOT NULL,
    FOREIGN KEY (id_profesor) REFERENCES profesores(id_profesor) ON DELETE CASCADE,
    FOREIGN KEY (id_curso)   REFERENCES cursos(id_curso) ON DELETE CASCADE,
    UNIQUE(id_profesor, id_curso)
);

-- Opcional: tabla alumnos si quieres el listado por curso
CREATE TABLE alumnos (
    id_alumno      INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(255) NOT NULL,
    apellidos      VARCHAR(255),
    id_curso       INT,
    FOREIGN KEY (id_curso) REFERENCES cursos(id_curso) ON DELETE SET NULL
);
