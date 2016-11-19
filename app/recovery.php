<?php
require_once("../mobile/includes/ini.php");

$error = false;
$success = false;

if ($_POST['set-new-password'] == '1') {
    if ($_POST['password'] != $_POST['repeat-password']) {
        $error = "Las contraseñas no coinciden";
    }
    elseif (!isset($_POST['email'])) {
        $error = "Falta el parámetro email.";
    }
    else {
        $password = md5($_POST['password']);
        $email = $_POST['email'];
        $q = "UPDATE user SET password = '$password' WHERE email = '$email'";
        if (!db_query($q)) {
            $error = "Ha ocurrido un error que no esperábamos";
        }
        else {
            $success = "La contraseña se ha establecido correctamente";
        }
    }
}
else {
    $token = $_GET['t'];

    $q = "SELECT email, creation_ts FROM user_recovery WHERE token = '$token'";
    $row = db_result($q);
    if (!$row['email']) {
        header('HTTP/1.0 404 Not Found');
        exit;
    }

    $email = $row['email'];

    $expire = strtotime($row['creation_ts']." +1 days");
    $now = strtotime(date("d-m-Y H:i:00",time()));
    if($now > $expire){
        $error = "La petición ha caducado. Vuelva a solicitar la recuperación de su contraseña a través de la aplicación móvil.";
    }
}
?>

<!DOCTYPE html>
<html data-ng-app="angularApp">
<head>
    <meta charset="utf-8" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="msapplication-tap-highlight" content="no" />
    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />

    <link rel="stylesheet" type="text/css" href="css/vendor/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/bootstrap/bootstrap-theme.min.css">
    <link rel="stylesheet" type="text/css" href="css/vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/index.css" />

    <title>Promos TV | Recuperación de contraseña</title>

</head>
<body>


<div class="jumbotron text-center">
    <div class="container">
        <h1>Introduzca su nueva contraseña</h1>
        <?php
        if ($error) {
        ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php
        }
        elseif ($success) {
        ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php
        }
        else {
            ?>
            <form action="#" name="set-new-password" method="post">
                <input type="hidden" name="set-new-password" value="1">
                <input type="hidden" name="email" value="<?php echo $email; ?>">

                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Nueva contraseña" name="password" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Confirme su nueva contraseña"
                           name="repeat-password" required>
                </div>
                <button type="submit" class="btn btn-default">Aceptar</button>
            </form>
        <?php
        }
        ?>
    </div>
</div>

<script src="js/vendor/jquery/jquery-1.11.3.min.js"></script>
</body>
</html>
