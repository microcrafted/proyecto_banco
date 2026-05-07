<?php
class Cuenta {
    private $conexion;

    public function __construct($db) {
        $this->conexion = $db;
    }

    //HU6 
    public function crearNuevaCuenta($idUsuario, $tipoDeCuenta) {
        //CTA + Año + 6 dígitos
        $numeroDeCuentaUnico = "CTA-" . date("Y") . "-" . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        try {
            $query = "INSERT INTO CUENTAS_BANCARIAS (id_usuario, num_cuenta, tipo, saldo, estado) 
                VALUES (:id_usuario, :num_cuenta, :tipo, 0.00, 'activa')";
            
            $sentencia = $this->conexion->prepare($query);
            
            $sentencia->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
            $sentencia->bindParam(':num_cuenta', $numeroDeCuentaUnico, PDO::PARAM_STR);
            $sentencia->bindParam(':tipo', $tipoDeCuenta, PDO::PARAM_STR);

            if ($sentencia->execute()) {
                return [
                    "status" => "success",
                    "mensaje" => "Cuenta creada exitosamente.",
                    "data" => [
                        "numero_cuenta" => $numeroDeCuentaUnico,
                        "tipo" => $tipoDeCuenta
                    ]
                ];
            } else {
                return ["status" => "error", "mensaje" => "No se pudo crear la cuenta."];
            }

        } catch (PDOException $excepcion) {
            return ["status" => "error", "mensaje" => "Error de BD: " . $excepcion->getMessage()];
        }
    }

    //HU7 - Dashboard
    public function obtenerCuentas($id_usuario) {
        $query = "SELECT id_cuenta, num_cuenta, tipo, saldo FROM CUENTAS_BANCARIAS WHERE id_usuario = :id_usuario AND estado = 'activa'";
        $stmt = $this->conexion->prepare($query); // Usamos $conexion para respetar el código de Mich
        $stmt->bindParam(":id_usuario", $id_usuario);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // HU9: Crear múltiples cuentas para un mismo usuario
    public function crearCuentaAdicional($idUsuario, $tipoDeCuenta) {
    return $this->crearNuevaCuenta($idUsuario, $tipoDeCuenta);
    }

    // HU10: Cerrar cuenta (borrado lógico)
    public function cerrarCuenta($idCuenta, $idUsuario) {
    try {
        // 1. Verificar que la cuenta pertenezca al usuario
        $query = "SELECT saldo, estado FROM CUENTAS_BANCARIAS 
                WHERE id_cuenta = :id_cuenta AND id_usuario = :id_usuario";

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_cuenta', $idCuenta, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cuenta) {
            return ["status" => "error", "mensaje" => "Cuenta no encontrada o no pertenece al usuario"];
        }

        // 2. Validar que no esté ya cerrada
        if ($cuenta['estado'] === 'cerrada') {
            return ["status" => "error", "mensaje" => "La cuenta ya está cerrada"];
        }

        // 3. Validar saldo en 0
        if ($cuenta['saldo'] > 0) {
            return ["status" => "error", "mensaje" => "La cuenta debe tener saldo 0 para cerrarse"];
        }

        // 4. Borrado lógico
        $update = "UPDATE CUENTAS_BANCARIAS 
                SET estado = 'cerrada' 
                WHERE id_cuenta = :id_cuenta";

        $stmt = $this->conexion->prepare($update);
        $stmt->bindParam(':id_cuenta', $idCuenta, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return ["status" => "success", "mensaje" => "Cuenta cerrada correctamente"];
        }

        return ["status" => "error", "mensaje" => "Error al cerrar la cuenta"];

    } catch (PDOException $e) {
        return ["status" => "error", "mensaje" => "Error BD: " . $e->getMessage()];
    }
    }

    //HU8 y HU16 Consultar detalle y saldo
    public function obtenerDetalleCuenta($id_cuenta, $id_usuario) {
        $query = "SELECT num_cuenta, tipo, saldo, estado FROM CUENTAS_BANCARIAS WHERE id_cuenta = :id_cuenta AND id_usuario = :id_usuario";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':id_cuenta', $id_cuenta, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //HU11 y HU23 Realizar Depósito con Registro de Log
    public function depositar($id_cuenta, $monto) {
        if ($monto <= 0) {
            return ["status" => "error", "mensaje" => "El monto a depositar debe ser mayor a 0."];
        }

        try {
            $this->conexion->beginTransaction();

            //actualizamos el saldo (HU11)
            $querySuma = "UPDATE CUENTAS_BANCARIAS SET saldo = saldo + :monto WHERE id_cuenta = :id_cuenta AND estado = 'activa'";
            $stmtSuma = $this->conexion->prepare($querySuma);
            $stmtSuma->bindParam(':monto', $monto);
            $stmtSuma->bindParam(':id_cuenta', $id_cuenta, PDO::PARAM_INT);
            $stmtSuma->execute();

            if ($stmtSuma->rowCount() == 0) {
                $this->conexion->rollBack();
                return ["status" => "error", "mensaje" => "La cuenta no existe o está cerrada."];
            }

            //registramos el movimiento en los Logs (HU23)
            $tipo = 'deposito';
            $queryLog = "INSERT INTO TRANSACCIONES (id_cuenta_destino, tipo_operacion, monto) VALUES (:id_cuenta, :tipo, :monto)";
            $stmtLog = $this->conexion->prepare($queryLog);
            $stmtLog->bindParam(':id_cuenta', $id_cuenta, PDO::PARAM_INT);
            $stmtLog->bindParam(':tipo', $tipo);
            $stmtLog->bindParam(':monto', $monto);
            $stmtLog->execute();

            $this->conexion->commit();
            
            return ["status" => "success", "mensaje" => "Depósito realizado con éxito."];

        } catch (PDOException $e) {
            $this->conexion->rollBack();
            return ["status" => "error", "mensaje" => "Error al procesar el depósito: " . $e->getMessage()];
        }
    }

    //HU13 Transferencia entre cuentas (Corregido para evitar colisiones PDO)
    public function transferir($id_cuenta_origen, $num_cuenta_destino, $monto){
        if ($monto <= 0) {
            return ["status" => "error", "mensaje" => "El monto a transferir debe ser mayor a 0."];
        }

        try {
            $this->conexion->beginTransaction();

            //validar que la cuenta destino exista y esté activa
            $queryDestino = "SELECT id_cuenta FROM CUENTAS_BANCARIAS WHERE num_cuenta = :num_cuenta AND estado = 'activa'";
            $stmtDestino = $this->conexion->prepare($queryDestino);
            $stmtDestino->bindParam(':num_cuenta', $num_cuenta_destino, PDO::PARAM_STR);
            $stmtDestino->execute();
            $cuentaDestino = $stmtDestino->fetch(PDO::FETCH_ASSOC);

            if (!$cuentaDestino) {
                $this->conexion->rollBack();
                return ["status" => "error", "mensaje" => "La cuenta destino no existe o no está activa."];
            }

            $id_cuenta_destino_real = $cuentaDestino['id_cuenta'];

            if ($id_cuenta_origen == $id_cuenta_destino_real) {
                $this->conexion->rollBack();
                return ["status" => "error", "mensaje" => "No puedes transferir a tu misma cuenta de origen."];
            }

            //validar fondos de la cuenta origen bloqueando la fila (HU22)
            $querySaldo = "SELECT saldo FROM CUENTAS_BANCARIAS WHERE id_cuenta = :id_cuenta AND estado = 'activa' FOR UPDATE";
            $stmtSaldo = $this->conexion->prepare($querySaldo);
            $stmtSaldo->bindParam(':id_cuenta', $id_cuenta_origen, PDO::PARAM_INT);
            $stmtSaldo->execute();
            $cuentaOrigen = $stmtSaldo->fetch(PDO::FETCH_ASSOC);

            if (!$cuentaOrigen || $cuentaOrigen['saldo'] < $monto) {
                $this->conexion->rollBack();
                return ["status" => "error", "mensaje" => "Fondos insuficientes para realizar la transferencia."];
            }

            //restar el dinero a la cuenta origen
            $queryResta = "UPDATE CUENTAS_BANCARIAS SET saldo = saldo - :monto WHERE id_cuenta = :id_cuenta";
            $stmtResta = $this->conexion->prepare($queryResta);
            $stmtResta->bindParam(':monto', $monto);
            $stmtResta->bindParam(':id_cuenta', $id_cuenta_origen, PDO::PARAM_INT);
            $stmtResta->execute();

            //sumar el dinero a la cuenta destino
            $querySuma = "UPDATE CUENTAS_BANCARIAS SET saldo = saldo + :monto WHERE id_cuenta = :id_cuenta";
            $stmtSuma = $this->conexion->prepare($querySuma);
            $stmtSuma->bindParam(':monto', $monto);
            $stmtSuma->bindParam(':id_cuenta', $id_cuenta_destino_real, PDO::PARAM_INT);
            $stmtSuma->execute();

            //registrar la transferencia en Logs (HU23)
            $tipo = 'transferencia';
            $queryLogTrans = "INSERT INTO TRANSACCIONES (id_cuenta_origen, id_cuenta_destino, tipo_operacion, monto) VALUES (:origen, :destino, :tipo, :monto)";
            $stmtLogTrans = $this->conexion->prepare($queryLogTrans);
            $stmtLogTrans->bindParam(':origen', $id_cuenta_origen, PDO::PARAM_INT);
            $stmtLogTrans->bindParam(':destino', $id_cuenta_destino_real, PDO::PARAM_INT);
            $stmtLogTrans->bindParam(':tipo', $tipo);
            $stmtLogTrans->bindParam(':monto', $monto);
            $stmtLogTrans->execute();

            $this->conexion->commit();
            return ["status" => "success", "mensaje" => "Transferencia realizada exitosamente."];

        } catch (PDOException $e) {
            $this->conexion->rollBack();
            return ["status" => "error", "mensaje" => "Error al procesar transferencia: " . $e->getMessage()];
        }
    }




}
?>