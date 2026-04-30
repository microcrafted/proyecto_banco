<?php
// 1. Incluir el modelo que creaste
require_once 'backend/models/Cuenta.php';

// 2. Simulación de la conexión (Ajusta los datos si tu equipo usa otros)
try {
    $host = "localhost";
    $db_name = "proyecto_victor"; // Tu base de datos
    $username = "root";
    $password = "";

    $db = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. Instanciar tu clase Cuenta
    $cuentaModel = new Cuenta($db);

    // 4. Ejecutar la función para el usuario con ID 1 (el que insertaste)
    // HU6: Crear cuenta bancaria
    $resultado = $cuentaModel->crearNuevaCuenta(1, 'ahorro');

    // 5. Mostrar el resultado en el navegador
    echo "<h1>Resultado de la Prueba HU6</h1>";
    echo "<pre>";
    print_r($resultado);
    echo "</pre>";

} catch (PDOException $e) {
    echo "Error de conexión o SQL: " . $e->getMessage();
}
?>
