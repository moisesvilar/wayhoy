<?php
if(isset($_REQUEST['error'])) {
    $has_error = 'has-error';
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>PromosTV - Panel administrador</title>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="css/custom.css" rel="stylesheet">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body id="login-page">
        <div id="wrapper">
            <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php">PromoTV</a>
                </div>
            </nav>

            <div id="page-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-4 col-lg-offset-4">
                            <form name="login" id="login" action="menu.php" method="post">
                                <div class="form-group row <?php echo $has_error; ?>">
                                    <label for="username" class="col-lg-4">Nombre de usuario</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="username" id="username" placeholder="Nombre de usuario">
                                    </div>
                                </div>
                                <div class="form-group row <?php echo $has_error; ?>">
                                    <label for="password" class="col-lg-4">Contraseña</label>
                                    <div class="col-lg-8">
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Contraseña de acceso">
                                    </div>
                                </div>
                                <input type="hidden" name="login" value="1">
                                <button type="submit" class="btn btn-success btn-block">Acceder</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="js/jquery.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/custom.js"></script>
    </body>
</html>
