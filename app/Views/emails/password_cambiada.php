<!DOCTYPE html>
<html>
<body>
    <h2 style="color: #57b846;">¡Contraseña Actualizada!</h2>
    <p>Hola <?= $nombre ?>,</p> <!-- Cambiar $user['nombre'] por $nombre -->
    
    <p>Tu contraseña fue modificada exitosamente el:<br>
    <strong><?= $fecha ?></strong></p> <!-- Usar variable $fecha -->
    
    <p>Detalles del cambio:</p>
    <ul>
        <li>Dirección IP: <?= $ip ?></li>
        <li>Hora: <?= $fecha ?></li>
    </ul>
    
    <hr style="border: 1px solid #eee;">
    
    <p><small>Este es un mensaje automático, por favor no responder.</small></p>
</body>
</html>