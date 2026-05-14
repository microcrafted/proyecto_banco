use proyecto_victor;

CREATE TABLE TRANSACCIONES (
    id_transaccion INT AUTO_INCREMENT PRIMARY KEY,
    id_cuenta_origen INT NULL, -- Nulo si es depósito en efectivo
    id_cuenta_destino INT NULL, -- Nulo si es retiro en efectivo
    tipo_operacion VARCHAR(20) NOT NULL, -- 'deposito', 'retiro', 'transferencia'
    monto DECIMAL(10,2) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

show tables;

select * from USUARIOS;
select * from ADMINISTRADORES;
select * from CUENTAS_BANCARIAS;
select * from TRANSACCIONES;

-- transacciones 
SELECT t.id_transaccion, t.tipo_operacion, t.monto, t.fecha, c.num_cuenta, CONCAT(u.nombre, ' ', u.apellido) AS cliente
FROM TRANSACCIONES t
INNER JOIN CUENTAS_BANCARIAS c ON t.id_cuenta_origen = c.id_cuenta
INNER JOIN USUARIOS u ON c.id_usuario = u.id_usuario
ORDER BY t.fecha DESC;

-- detalle de transferencias
SELECT t.id_transaccion, t.fecha, t.monto, co.num_cuenta AS cuenta_origen, cd.num_cuenta AS cuenta_destino
FROM TRANSACCIONES t
INNER JOIN CUENTAS_BANCARIAS co ON t.id_cuenta_origen = co.id_cuenta
INNER JOIN CUENTAS_BANCARIAS cd ON t.id_cuenta_destino = cd.id_cuenta
WHERE t.tipo_operacion = 'transferencia';

-- total de operaciones y dinero que ha movido
SELECT u.id_usuario,CONCAT(u.nombre, ' ', u.apellido) AS cliente, COUNT(t.id_transaccion) AS total_operaciones,SUM(t.monto) AS volumen_dinero_movido
FROM USUARIOS u
INNER JOIN CUENTAS_BANCARIAS c ON u.id_usuario = c.id_usuario
INNER JOIN TRANSACCIONES t ON c.id_cuenta = t.id_cuenta_origen
GROUP BY u.id_usuario, u.nombre, u.apellido
ORDER BY volumen_dinero_movido DESC;

-- total de cuentas y saldo
SELECT u.id_usuario,CONCAT(u.nombre, ' ', u.apellido) AS cliente,u.email,COUNT(c.id_cuenta) AS cantidad_cuentas,IFNULL(SUM(c.saldo), 0) AS saldo_total_banco
FROM USUARIOS u
LEFT JOIN CUENTAS_BANCARIAS c ON u.id_usuario = c.id_usuario
GROUP BY u.id_usuario, cliente, u.email
ORDER BY saldo_total_banco DESC;

