<?php
// funciones.php

/**
 * Registra una acción en el historial de la aplicación.
 *
 * Esta función inserta un nuevo registro en la tabla 'historial' para llevar
 * un control de las acciones importantes que realizan los usuarios.
 *
 * @param PDO $pdo La conexión a la base de datos.
 * @param int $usuario_id El ID del usuario que realiza la acción.
 * @param string $accion El tipo de acción (ej. 'CREACIÓN DE REGISTRO').
 * @param string $descripcion Un texto detallado sobre la acción realizada.
 */
function registrar_historial($pdo, $usuario_id, $accion, $descripcion) {
    try {
        $sql = "INSERT INTO historial (usuario_id, accion, descripcion) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario_id, $accion, $descripcion]);
    } catch (PDOException $e) {
        // En una aplicación real, se registraría este error en un archivo de log.
        // error_log('Error al registrar en historial: ' . $e->getMessage());
    }
}

/**
 * Crea una nueva notificación para un usuario específico.
 *
 * Esta función inserta un nuevo registro en la tabla 'notificaciones' para
 * alertar a un usuario sobre un evento relevante.
 *
 * @param PDO $pdo La conexión a la base de datos.
 * @param int $usuario_id El ID del usuario que recibirá la notificación.
 * @param string $mensaje El texto de la notificación.
 * @param string|null $link Un enlace opcional al que la notificación puede dirigir.
 * @return bool Devuelve true si la notificación se creó con éxito, false en caso de error.
 */
function crear_notificacion($pdo, $usuario_id, $mensaje, $link = null) {
    try {
        $sql = "INSERT INTO notificaciones (usuario_id, mensaje, link) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario_id, $mensaje, $link]);
        return true;
    } catch (PDOException $e) {
        // En una aplicación real, se registraría este error.
        // error_log('Error al crear notificación: ' . $e->getMessage());
        return false;
    }
}

// Puedes añadir más funciones útiles aquí en el futuro.
// Por ejemplo, una función para obtener el rol de un usuario,
// o una para formatear fechas de una manera específica.

?>
