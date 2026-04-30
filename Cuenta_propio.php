<?php

class Cuenta {
    private $conexion;

    /**
     * Constructor que recibe la instancia de conexión PDO.
     */
    public function __construct($db) {
        $this->conexion = $db;
    }

    /**
     * HU6: Crear cuenta bancaria.
     * Genera un número de cuenta único y lo persiste en la base de datos.
     * * @param int $idUsuario Identificador del cliente dueño de la cuenta.
     * @param string $tipoDeCuenta Tipo de cuenta (ahorro o corriente).
     * @return array Respuesta con el estado de la operación.
     */
    public function crearNuevaCuenta($idUsuario, $tipoDeCuenta) {
        // Generación de número de cuenta único (Requerimiento técnico HU6)
        // Formato: CTA + Año + 6 dígitos aleatorios
        $numeroDeCuentaUnico = "CTA-" . date("Y") . "-" . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        try {
            // Preparación de la consulta (Cumple DoD 1.3 - No concatenación)
            $query = "INSERT INTO CUENTAS_BANCARIAS (id_usuario, num_cuenta, tipo, saldo, estado) 
                      VALUES (:id_usuario, :num_cuenta, :tipo, 0.00, 'activa')";
            
            $sentencia = $this->conexion->prepare($query);
            
            // Vinculación de parámetros segura
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
            // Manejo de errores
            return [
                "status" => "error", 
                "mensaje" => "Error de base de datos: " . $excepcion->getMessage()
            ];
        }
    }
}