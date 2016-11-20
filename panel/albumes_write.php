<?php
require_once('ini.php');
require_once('include/album_db.php');

$table = 'album';
$id_field = 'album_id';

$row = false;
if($_REQUEST['id']) {
    $row = registro($table, $id_field, $_REQUEST['id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>PromoTV - Panel de administración</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/sb-admin.css" rel="stylesheet">
    <link href="css/plugins/morris.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

<div id="wrapper">
    <?php include(dirname(__FILE__).'/partials/nav.php'); ?>

    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Albumes <small>Gestión de álbumes de PromoTV</small>
                    </h1>
                    <ol class="breadcrumb col-lg-6">
                        <li>
                            <i class="fa fa-dashboard"></i>  <a href="<?php echo $url_absolute; ?>menu.php">Principal</a>
                        </li>
                        <li>
                            <i class="fa fa-book"></i> <a href="<?php echo $url_absolute; ?>albumes.php">Albumes</a>
                        </li>
                        <li class="active">
                            <i class="fa fa-plus"></i> Crear
                        </li>
                    </ol>
                    <div class="col-lg-6 text-right">
                        <a href="<?php echo $url_absolute; ?>albumes_write.php" class="btn btn-primary"><i class="fa fa-plus"></i> Crear más</a>
                        <a href="<?php echo $url_absolute; ?>imagenes_write.php?album_id=<?php echo $_REQUEST['id']; ?>" class="btn btn-success"><i class="fa fa-plus"></i> Subir imagen</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <?php
                    if ($error) echo $msg_error;
                    elseif($success) echo $msg_success;
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <form name="edit" id="edit" method="post" enctype="multipart/form-data" action="#">
                        <div class="form-group row">
                            <label for="name" class="col-lg-2">Nombre</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Nombre" value="<?php echo $row['name']; ?>">
                            </div>
                        </div>
                        <input type="hidden" name="save" value="1">
                        <input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>">
                        <button type="submit" class="btn btn-success btn-block">Guardar</button>
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
