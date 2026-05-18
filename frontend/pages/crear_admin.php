<?php
require_once '../../backend/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // 1. Define los datos de tu superusuario
    $nombre = 'IAN ALEXANDER';
    $apellido = 'TRANQUILINO MADARIAGA';
    $email = 'ianmada@buhobank.com'; // Este será tu correo de acceso
    $password_plana = 'admin1234567';  // Esta será tu contraseña

    // 2. Encriptamos la contraseña con el estándar de PHP
    $password_hash = password_hash($password_plana, PASSWORD_BCRYPT);

    // 3. Insertamos en la tabla ADMINISTRADORES
    $query = "INSERT INTO ADMINISTRADORES (nombre, apellido, email, password_hash) 
            VALUES (:nombre, :apellido, :email, :password_hash)";
            
    $stmt = $db->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password_hash', $password_hash);

    if ($stmt->execute()) {
        echo "<h2 style='color: green;'>¡Éxito!</h2>";
        echo "<p>El usuario administrador ha sido creado en la base de datos.</p>";
        echo "<p><b>Correo:</b> $email <br><b>Contraseña:</b> $password_plana</p>";
        echo "<a href='admin_login.php'>Ir al Login Administrativo</a>";
    } else {
        echo "<h2 style='color: red;'>Error</h2><p>No se pudo crear el administrador.</p>";
    }

} catch (PDOException $e) {
    // Si la columna se llama 'password' en lugar de 'password_hash' en tu BD, te avisará aquí
    echo "Error de Base de Datos: " . $e->getMessage();
}
?>