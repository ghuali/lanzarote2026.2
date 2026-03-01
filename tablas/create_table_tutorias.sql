CREATE TABLE profesores (
     id            INT NOT NULL AUTO_INCREMENT PRIMARY KEY
    ,nombre        VARCHAR(255) NOT NULL
    ,email         VARCHAR(255) NOT NULL
    ,es_tutor      BOOLEAN DEFAULT FALSE
    ,fecha_alta    DATE DEFAULT (CURRENT_DATE)
    ,fecha_baja    DATE DEFAULT ('99991231')

    #DATOS AUDITORÍA
    ,usuario_alta   VARCHAR(255)
    ,ip_alta        CHAR(15)
    ,fecha_sis_alta TIMESTAMP

    ,usuario_modi   VARCHAR(255)
    ,ip_modi        CHAR(15)
    ,fecha_modi     TIMESTAMP

    ,KEY (email)
);

CREATE TABLE tutores (
     id            INT NOT NULL AUTO_INCREMENT PRIMARY KEY
    ,nombre        VARCHAR(255) NOT NULL
    ,email         VARCHAR(255) NOT NULL
    ,antiguedad    CHAR(4) NOT NULL
    ,fecha_alta    DATE DEFAULT (CURRENT_DATE)
    ,fecha_baja    DATE DEFAULT ('99991231')

    #DATOS AUDITORÍA
    ,usuario_alta   VARCHAR(255)
    ,ip_alta        CHAR(15)
    ,fecha_sis_alta TIMESTAMP

    ,usuario_modi   VARCHAR(255)
    ,ip_modi        CHAR(15)
    ,fecha_modi     TIMESTAMP

    ,KEY (email)
);

CREATE TABLE aulas (
     id            INT NOT NULL AUTO_INCREMENT PRIMARY KEY
    ,nombre        VARCHAR(255) NOT NULL
    ,letra         CHAR(1) NOT NULL
    ,numero        INT NOT NULL
    ,planta        ENUM('Primera','Segunda','Tercera') NOT NULL
    ,fecha_alta    DATE DEFAULT (CURRENT_DATE)
    ,fecha_baja    DATE DEFAULT ('99991231')

    #DATOS AUDITORÍA
    ,usuario_alta   VARCHAR(255)
    ,ip_alta        CHAR(15)
    ,fecha_sis_alta TIMESTAMP

    ,usuario_modi   VARCHAR(255)
    ,ip_modi        CHAR(15)
    ,fecha_modi     TIMESTAMP
);

CREATE TABLE cursos (
     id            INT NOT NULL AUTO_INCREMENT PRIMARY KEY
    ,nombre_grado  VARCHAR(255) NOT NULL
    ,curso_numero  INT NOT NULL
    ,letra         CHAR(1)
    ,fecha_alta    DATE DEFAULT (CURRENT_DATE)
    ,fecha_baja    DATE DEFAULT ('99991231')

    #DATOS AUDITORÍA
    ,usuario_alta   VARCHAR(255)
    ,ip_alta        CHAR(15)
    ,fecha_sis_alta TIMESTAMP

    ,usuario_modi   VARCHAR(255)
    ,ip_modi        CHAR(15)
    ,fecha_modi     TIMESTAMP
);

-- Tablas intermedias sin auditoría ya que no pasan por Base
CREATE TABLE tutorias (
     id          INT NOT NULL AUTO_INCREMENT PRIMARY KEY
    ,id_profesor INT NOT NULL
    ,id_curso    INT NOT NULL
    ,FOREIGN KEY (id_profesor) REFERENCES profesores(id) ON DELETE CASCADE
    ,FOREIGN KEY (id_curso)    REFERENCES cursos(id)     ON DELETE CASCADE
    ,UNIQUE (id_profesor, id_curso)
);

CREATE TABLE alumnos (
     id          INT NOT NULL AUTO_INCREMENT PRIMARY KEY
    ,nombre      VARCHAR(255) NOT NULL
    ,apellidos   VARCHAR(255)
    ,id_curso    INT
    ,fecha_alta  DATE DEFAULT (CURRENT_DATE)
    ,fecha_baja  DATE DEFAULT ('99991231')

    #DATOS AUDITORÍA
    ,usuario_alta   VARCHAR(255)
    ,ip_alta        CHAR(15)
    ,fecha_sis_alta TIMESTAMP

    ,usuario_modi   VARCHAR(255)
    ,ip_modi        CHAR(15)
    ,fecha_modi     TIMESTAMP

    ,FOREIGN KEY (id_curso) REFERENCES cursos(id) ON DELETE SET NULL
);