create DATABASE proyecto_victor;
use proyecto_victor;

-- ============================================
-- TABLA: USUARIOS
-- ============================================
CREATE TABLE `USUARIOS` (
  `id_usuario`        INT           PRIMARY KEY AUTO_INCREMENT COMMENT 'Llave primaria',
  `nombre`            VARCHAR(80)   NOT NULL,
  `apellido`          VARCHAR(80)   NOT NULL,
  `email`             VARCHAR(120)  NOT NULL UNIQUE,
  `password_hash`     VARCHAR(255)  NOT NULL,
  `fecha_registro`    DATE          DEFAULT (CURDATE()),
  `activo`            TINYINT(1)    DEFAULT 1  COMMENT '1=activo, 0=inactivo',
  `intentos_fallidos` TINYINT       DEFAULT 0  COMMENT 'Contador de intentos fallidos de login',
  `bloqueado`         TINYINT(1)    DEFAULT 0  COMMENT '1=bloqueado, 0=activo - HU24'
) COMMENT = 'Clientes registrados en el sistema';

-- ============================================
-- TABLA: ADMINISTRADORES
-- ============================================
CREATE TABLE `ADMINISTRADORES` (
  `id_admin`       INT          PRIMARY KEY AUTO_INCREMENT,
  `nombre`         VARCHAR(80)  NOT NULL,
  `apellido`       VARCHAR(80)  NOT NULL,
  `email`          VARCHAR(120) NOT NULL UNIQUE,
  `password_hash`  VARCHAR(255) NOT NULL,
  `fecha_registro` DATE         DEFAULT (CURDATE()),
  `activo`         TINYINT(1)   DEFAULT 1 COMMENT '1=activo, 0=inactivo'
) COMMENT = 'Administradores del sistema - HU25, HU26, HU27';

-- ============================================
-- TABLA: CUENTAS_BANCARIAS
-- ============================================
CREATE TABLE `CUENTAS_BANCARIAS` (
  `id_cuenta`      INT                          PRIMARY KEY AUTO_INCREMENT,
  `id_usuario`     INT                          NOT NULL,
  `num_cuenta`     VARCHAR(20)                  NOT NULL UNIQUE,
  `tipo`           ENUM('ahorro','corriente')   NOT NULL,
  `saldo`          DECIMAL(15,2)                NOT NULL DEFAULT 0,
  `fecha_apertura` DATE                         DEFAULT (CURDATE()),
  `estado`         ENUM('activa','cerrada')     DEFAULT 'activa',
  CONSTRAINT `fk_cuenta_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `USUARIOS` (`id_usuario`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) COMMENT = 'No borrar si tiene movimientos asociados';

-- ============================================
-- TABLA: MOVIMIENTOS
-- ============================================
CREATE TABLE `MOVIMIENTOS` (
  `id_movimiento`     INT                                       PRIMARY KEY AUTO_INCREMENT,
  `id_cuenta_origen`  INT                                       NOT NULL,
  `id_cuenta_destino` INT                                       NULL COMMENT 'NULL en depósitos y retiros',
  `tipo`              ENUM('deposito','retiro','transferencia') NOT NULL,
  `concepto`          VARCHAR(100)                              NOT NULL COMMENT 'Ej: Transferencia a cuenta 001',
  `descripcion`       TEXT                                      NULL COMMENT 'Detalle opcional de la operación',
  `monto`             DECIMAL(15,2)                             NOT NULL,
  `fecha`             DATETIME                                  DEFAULT (NOW()) COMMENT 'Incluye fecha y hora',
  `estado`            ENUM('completada','fallida','pendiente')  NOT NULL DEFAULT 'pendiente',
  `saldo_post_op`     DECIMAL(15,2)                            NOT NULL COMMENT 'Saldo de cuenta origen tras la operación',
  `referencia`        VARCHAR(20)                               NOT NULL UNIQUE COMMENT 'Número único por movimiento',
  CONSTRAINT `fk_movimiento_origen`
    FOREIGN KEY (`id_cuenta_origen`) REFERENCES `CUENTAS_BANCARIAS` (`id_cuenta`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT `fk_movimiento_destino`
    FOREIGN KEY (`id_cuenta_destino`) REFERENCES `CUENTAS_BANCARIAS` (`id_cuenta`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) COMMENT = 'Registra depósitos, retiros y transferencias';

-- ============================================
-- TABLA: SESIONES
-- ============================================
CREATE TABLE `SESIONES` (
  `id_sesion`    INT          PRIMARY KEY AUTO_INCREMENT,
  `id_usuario`   INT          NOT NULL,
  `token`        VARCHAR(255) NOT NULL UNIQUE,
  `fecha_inicio` DATETIME     DEFAULT (NOW()),
  `fecha_expira` DATETIME     NOT NULL,
  `ip`           VARCHAR(45)  NULL,
  `user_agent`   VARCHAR(255) NULL COMMENT 'Navegador/dispositivo - HU21',
  `activa`       TINYINT(1)   DEFAULT 1 COMMENT '1=activa, 0=invalidada',
  CONSTRAINT `fk_sesion_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `USUARIOS` (`id_usuario`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) COMMENT = 'Si se borra usuario, se borran sus sesiones';

-- ============================================
-- TABLA: LOG_ADMIN
-- ============================================
CREATE TABLE `LOG_ADMIN` (
  `id_log`     INT          PRIMARY KEY AUTO_INCREMENT,
  `id_admin`   INT          NOT NULL,
  `id_cuenta`  INT          NULL,
  `id_usuario` INT          NULL,
  `accion`     VARCHAR(100) NOT NULL COMMENT 'Ej: cuenta_desactivada, usuario_bloqueado',
  `fecha`      DATETIME     DEFAULT (NOW()),
  `detalle`    TEXT         NULL COMMENT 'Descripción adicional de la acción',
  CONSTRAINT `fk_log_admin`
    FOREIGN KEY (`id_admin`) REFERENCES `ADMINISTRADORES` (`id_admin`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT `fk_log_cuenta`
    FOREIGN KEY (`id_cuenta`) REFERENCES `CUENTAS_BANCARIAS` (`id_cuenta`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,
  CONSTRAINT `fk_log_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `USUARIOS` (`id_usuario`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) COMMENT = 'Auditoría de acciones del administrador - HU25, HU26, HU27';

-- ============================================
-- TABLA: RECUPERACION_CONTRASENA
-- ============================================
CREATE TABLE `RECUPERACION_CONTRASENA` (
  `id_recuperacion` INT          PRIMARY KEY AUTO_INCREMENT,
  `id_usuario`      INT          NOT NULL,
  `token`           VARCHAR(255) NOT NULL UNIQUE COMMENT 'Token único enviado al correo',
  `fecha_expira`    DATETIME     NOT NULL,
  `usado`           TINYINT(1)   DEFAULT 0 COMMENT '1=ya usado, 0=vigente',
  CONSTRAINT `fk_recuperacion_usuario`
    FOREIGN KEY (`id_usuario`) REFERENCES `USUARIOS` (`id_usuario`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) COMMENT = 'Soporte para HU5 - Recuperación de contraseña';

-- ============================================
-- ÍNDICES ADICIONALES
-- ============================================
CREATE INDEX `idx_cuentas_usuario`    ON `CUENTAS_BANCARIAS` (`id_usuario`);
CREATE INDEX `idx_movimientos_origen` ON `MOVIMIENTOS` (`id_cuenta_origen`);
CREATE INDEX `idx_movimientos_destino`ON `MOVIMIENTOS` (`id_cuenta_destino`);
CREATE INDEX `idx_movimientos_fecha`  ON `MOVIMIENTOS` (`fecha`);
CREATE INDEX `idx_sesiones_usuario`   ON `SESIONES` (`id_usuario`);
CREATE INDEX `idx_log_admin`          ON `LOG_ADMIN` (`id_admin`);
CREATE INDEX `idx_log_fecha`          ON `LOG_ADMIN` (`fecha`);
CREATE INDEX `idx_recuperacion_user`  ON `RECUPERACION_CONTRASENA` (`id_usuario`);