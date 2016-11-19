<?php
require_once('ini.php');
require_once('include/administrador_db.php');

$row = get_administrator($_REQUEST['id']);
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

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
                        Administradores <small>Gestión de administradores</small>
                    </h1>
                    <ol class="breadcrumb col-lg-6">
                        <li>
                            <i class="fa fa-dashboard"></i>  <a href="<?php echo $url_absolute; ?>menu.php">Principal</a>
                        </li>
                        <li>
                            <i class="fa fa-users"></i> <a href="<?php echo $url_absolute; ?>administradores.php">Administradores</a>
                        </li>
                        <li class="active">
                            <i class="fa fa-user"></i> <?php echo $row['Nombre']; ?>
                        </li>
                    </ol>
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
                        <h3>Álbumes asociados al administrador</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="text-center">Nombre del álbum</th>
                                    <th class="text-right" style="width: 60px;">Borrar</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $album_users = registros('album_user', " AND user = '$_REQUEST[id]' AND role_id = '2'");
                                foreach($album_users as $album_user) {
                                    $album = registro('album', 'album_id', $album_user['album_id']);
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $album['name']; ?></td>
                                        <td class="text-right" style="width: 60px;">
                                            <a href="#" class="confirm" data-url="?id=<?php echo $_REQUEST['id']; ?>&delete-album_user=<?php echo $album_user['album_user_id']; ?>"><i class="fa fa-times"></i></a>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group row">
                            <label for="album" class="col-lg-2">Asociar a <?php echo $row['Nombre']; ?> a un album</label>
                            <div class="col-lg-8">
                                <select class="form-control select2" name="album" id="album" style="width: 100%;">
                                    <option value="">-</option>
                                    <?php
                                    $albumes = registros('album', " AND album_id NOT IN (SELECT album_id FROM album_user WHERE user = '$_REQUEST[id]' AND role_id = '2')", 'name');
                                    foreach($albumes as $album) {
                                        ?>
                                        <option value="<?php echo $album['album_id']; ?>"><?php echo $album['name']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-default col-lg-2">Añadir</button>
                        </div>
                        <h3>Permisos de <?php echo $row['Nombre']; ?></h3>
                        <?php
                        $permisos = registro('permissions', 'user', $row['id'], " AND role_id = '2'");
                        ?>
                        <div class="checkbox">
                            <label>
                                <input type="hidden" name="albums" value="<?php echo $permisos['albums']; ?>">
                                <input type="checkbox" id="albums" <?php if($permisos['albums']) echo "checked"; ?> > Gestionar álbumes
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="hidden" name="link_images" value="<?php echo $permisos['link_images']; ?>">
                                <input type="checkbox" id="link_images" <?php if($permisos['link_images']) echo "checked"; ?>> Asociar o desasociar imágenes de un álbum a otro álbum
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="hidden" name="upload_images" value="<?php echo $permisos['upload_images']; ?>">
                                <input type="checkbox" id="upload_images" <?php if($permisos['upload_images']) echo "checked"; ?>> Subida de nuevas imágenes a un álbum
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="hidden" name="programming_albums" value="<?php echo $permisos['programming_albums']; ?>">
                                <input type="checkbox" id="programming_albums" <?php if($permisos['programming_albums']) echo "checked"; ?>> Programar álbumes
                            </label>
                        </div>
                        <input type="hidden" name="save" value="1">
                        <input type="hidden" name="permissions_id" value="<?php echo $permisos['permissions_id']; ?>">
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script src="js/custom.js"></script>
</body>

</html>
