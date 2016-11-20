<?php
require_once('ini.php');
require_once('include/image_db.php');

$where = '';
if($_REQUEST['ft-filename']) {
    $where .= " AND filename LIKE '%{$_REQUEST['ft-filename']}%'";
}
if($_REQUEST['ft-album']) {
    $where .= " AND image_id IN (SELECT image_id FROM image_album WHERE album_id = '{$_REQUEST['ft-album']}')";
}

$rows = registros('image', $where, 'image_id DESC');
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
                        Imágenes <small>Gestión de imágenes de PromoTV</small>
                    </h1>
                    <ol class="breadcrumb col-lg-6">
                        <li>
                            <i class="fa fa-dashboard"></i>  <a href="<?php echo $url_absolute; ?>menu.php">Principal</a>
                        </li>
                        <li class="active">
                            <i class="fa fa-picture-o"></i> Imágenes
                        </li>
                    </ol>
                    <div class="col-lg-6 text-right">
                        <a href="<?php echo $url_absolute; ?>imagenes_write.php" class="btn btn-primary"><i class="fa fa-plus"></i> Crear</a>
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
            <div class="row hidden-xs">
                <form name="filter" id="filter" method="post" enctype="multipart/form-data" action="#">
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="filename" class="col-lg-2">Fichero</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" name="ft-filename" id="filename" placeholder="Fichero" value="<?php echo $_REQUEST['ft-filename']; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <label for="album" class="col-lg-2">Album</label>
                            <div class="col-lg-10">
                                <select class="form-control select2" name="ft-album" id="album">
                                    <option value="" <?php if (!$_REQUEST['ft-album']) echo "selected"; ?>>-</option>
                                    <?php
                                    $albumes = registros('album');
                                    foreach($albumes as $album) {
                                    ?>
                                        <option value="<?php echo $album['album_id']; ?>" <?php if($_REQUEST['ft-album'] == $album['album_id']) echo "selected"; ?>>
                                            <?php echo $album['name']; ?>
                                        </option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <button type="submit" class="btn btn-default btn-sm">Filtrar</button>
                    </div>
                </form>
            </div>
            <br/>
            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-left"></th>
                                    <th class="text-left"></th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">Albumes</th>
                                    <th class="text-right">Ver</th>
                                    <th class="text-right">Borrar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cont = 0;
                                foreach($rows as $row) {
                                    $cont++;
                                    $albumes = registros('album', " AND album_id IN (SELECT album_id FROM image_album WHERE image_id = '$row[image_id]')");
                                    ?>
                                    <tr>
                                        <td class="text-left"><?php echo $cont; ?></td>
                                        <td class="text-left">
                                            <a href="<?php echo getImageUrl($row['filename']); ?>" target="_blank">
                                                <img src="<?php echo getThumbnailUrl($row['filename']); ?>">
                                            </a>
                                        </td>
                                        <td class="text-center"><a href="<?php echo $url_absolute; ?>imagenes_write.php?id=<?php echo $row['image_id']; ?>"><?php echo $row['filename']; ?></a></td>
                                        <td class="text-center">
                                            <?php
                                            foreach($albumes as $album) {
                                            ?>
                                                <a href="<?php echo $url_absolute; ?>/albumes_write.php?id=<?php echo $album['album_id']; ?>">
                                                    <?php echo $album['name']; ?>
                                                </a>
                                                <br/>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                        <td class="text-right"><a href="<?php echo getImageUrl($row['filename']); ?>" target="_blank"><i class="fa fa-eye"></i></a></td>
                                        <td class="text-right"><a href="#" class="confirm" data-url="?delete-image=<?php echo $row['image_id']; ?>"><i class="fa fa-times"></i></a></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
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
