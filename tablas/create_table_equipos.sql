CREATE TABLE equipos (
     id            INT NOT NULL AUTO_INCREMENT PRIMARY KEY
    ,nombre        VARCHAR(255) NOT NULL
    ,tipo          CHAR(1) NOT NULL
    ,numero_serie  INT NOT NULL
    ,estado        ENUM('Operativo','En reparación','Baja') NOT NULL
    ,fecha_alta    DATE DEFAULT (CURRENT_DATE)
    ,fecha_baja    DATE DEFAULT ('99991231')

    #DATOS AUDITORÍA
    ,usuario_alta   VARCHAR(255)
    ,ip_alta        CHAR(15)
    ,fecha_sis_alta TIMESTAMP

    ,usuario_modi   VARCHAR(255)
    ,ip_modi        CHAR(15)
    ,fecha_modi     TIMESTAMP

    ,UNIQUE KEY (numero_serie)
);