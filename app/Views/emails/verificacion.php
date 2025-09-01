<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verificación de Cuenta</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; }
        .code { 
            font-size: 24px; 
            font-weight: bold; 
            color: #2c3e50;
            padding: 10px 20px;
            background: #f8f9fa;
            border-radius: 5px;
            display: inline-block;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Verificación de Cuenta</h2>
        <p>Gracias por registrarte. Usa este código para activar tu cuenta:</p>
        <div class="code"><?= $codigo ?></div>
        <p>El código expirará en 15 minutos.</p>
        <p>Si no solicitaste este registro, puedes ignorar este mensaje.</p>
    </div>
</body>
</html>