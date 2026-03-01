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