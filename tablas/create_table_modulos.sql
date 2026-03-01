CREATE TABLE modulos (
     id            INT NOT NULL AUTO_INCREMENT PRIMARY KEY
    ,nombre        VARCHAR(255) NOT NULL
    ,codigo        VARCHAR(50)  NOT NULL
    ,es_optativo   BOOLEAN DEFAULT FALSE
    ,id_grupo      INT
    ,fecha_alta    DATE DEFAULT (CURRENT_DATE)
    ,fecha_baja    DATE DEFAULT ('99991231')

    #DATOS AUDITORÍA
    ,usuario_alta   VARCHAR(255)
    ,ip_alta        CHAR(15)
    ,fecha_sis_alta TIMESTAMP

    ,usuario_modi   VARCHAR(255)
    ,ip_modi        CHAR(15)
    ,fecha_modi     TIMESTAMP

    ,FOREIGN KEY (id_grupo) REFERENCES cursos(id) ON DELETE SET NULL
);

CREATE TABLE modulo_profesores (
     id            INT NOT NULL AUTO_INCREMENT PRIMARY KEY
    ,id_modulo     INT NOT NULL
    ,id_profesor   INT NOT NULL
    ,FOREIGN KEY (id_modulo)   REFERENCES modulos(id)    ON DELETE CASCADE
    ,FOREIGN KEY (id_profesor) REFERENCES profesores(id) ON DELETE CASCADE
    ,UNIQUE (id_modulo, id_profesor)
);

CREATE TABLE matriculas (
     id            INT NOT NULL AUTO_INCREMENT PRIMARY KEY
    ,id_modulo     INT NOT NULL
    ,id_alumno     INT NOT NULL
    ,FOREIGN KEY (id_modulo) REFERENCES modulos(id)  ON DELETE CASCADE
    ,FOREIGN KEY (id_alumno) REFERENCES alumnos(id)  ON DELETE CASCADE
    ,UNIQUE (id_modulo, id_alumno)
);