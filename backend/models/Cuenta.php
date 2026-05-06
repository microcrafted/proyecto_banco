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
}
?>