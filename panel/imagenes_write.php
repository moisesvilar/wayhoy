<?php
require_once('ini.php');
require_once('include/image_db.php');

$table = 'image';
$id_field = 'image_id';

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
    <link rel="stylesheet" type="text/css" href="css/vendor/jquery-guillotine/jquery-guillotine.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="js/jquery.js"></script>
    <script>
        function co() {
            var picture = $('#sample_picture');
            // Make sure the image is completely loaded before calling the plugin
            // Initialize plugin (with custom event)
            picture.guillotine({eventOnChange: 'guillotinechange'});
            // Display inital data
            var data = picture.guillotine('getData');
            for(var key in data) {$('#'+key).val(data[key]);}
            // Bind button actions
            $('#rotate_left').click(function(){ picture.guillotine('rotateLeft'); });
            $('#rotate_right').click(function(){ picture.guillotine('rotateRight'); });
            $('#fit').click(function(){ picture.guillotine('fit'); });
            $('#zoom_in').click(function(){ picture.guillotine('zoomIn'); });
            $('#zoom_out').click(function(){ picture.guillotine('zoomOut'); });
            // Update data on change
            picture.on('guillotinechange', function(ev, data, action) {
                data.scale = parseFloat(data.scale.toFixed(4));
                for(var k in data) {$('#i'+k).html(data[k]); $('#'+k).val(data[k])}
            });
            // Make sure the 'load' event is triggered at least once (for cached images)
            // if (picture.prop('complete')) picture.trigger('load')
        }
        function updateImageData() {
            var picture = $('#sample_picture');
            var data = picture.guillotine('getData');
            for(var key in data) {$('#'+key).val(data[key]);}
            return true;
        }
    </script>
    <script src="js/vendor/jquery-guillotine/jquery-guillotine.min.js"></script>
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
                        <li>
                            <i class="fa fa-book"></i> <a href="<?php echo $url_absolute; ?>imagenes.php">Imágenes</a>
                        </li>
                        <li class="active">
                            <i class="fa fa-plus"></i> Crear
                        </li>
                    </ol>
                    <div class="col-lg-6 text-right">
                        <a href="<?php echo $url_absolute; ?>imagenes_write.php" class="btn btn-primary"><i class="fa fa-plus"></i> Crear más</a>
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
                        <?php
                        if(!$_REQUEST['id']) {
                        ?>
                            <div class="form-group row">
                                <label for="name" class="col-lg-2">Fichero</label>

                                <div class="col-lg-10">
                                    <input accept="image/*" type="file" class="form-control" name="file" id="file">
                                </div>
                            </div>
                            <?php
                            if($_REQUEST['album_id']) {
                            ?>
                                <div class="form-group row">
                                    <label for="album" class="col-lg-2">Asociar a un album</label>
                                    <div class="col-lg-8">
                                        <select class="form-control select2" name="album" id="album" style="width: 100%;">
                                            <option value="">-</option>
                                            <?php
                                            $albumes = registros('album', " AND album_id NOT IN (SELECT album_id FROM image_album WHERE image_id = '$_REQUEST[id]')", 'name');
                                            foreach($albumes as $album) {
                                            ?>
                                                <option value="<?php echo $album['album_id']; ?>" <?php if($_REQUEST['album_id'] == $album['album_id']) echo "selected"; ?>>
                                                    <?php echo $album['name']; ?>
                                                </option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-default col-lg-2">Añadir</button>
                                </div>
                            <?php
                            }
                            ?>
                        <?php
                        }
                        else {
                            $image = registro('image', 'image_id', $_REQUEST['id']);
                        ?>
                            <div class="form-group row">
                                <label for="title" class="col-lg-2">Título</label>
                                <div class="col-lg-10">
                                    <input type="text" name="title" id="title" class="form-control" placeholder="Título para la imagen" value="<?php echo $image['title']; ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="description" class="col-lg-2">Descripción</label>
                                <div class="col-lg-10">
                                    <input type="text" name="description" id="description" class="form-control" placeholder="Descripción para la imagen" value="<?php echo $image['description']; ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="subdescription" class="col-lg-2">Subdescripción</label>
                                <div class="col-lg-10">
                                    <textarea class="form-control" rows="5" placeholder="Subdescripción para la imagen" name="subdescription" id="subdescription"><?php echo $image['subdescription']; ?></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-block" onclick="updateImageData()">Guardar</button><br/>

                            <h3>Álbumes asociados a la imagen</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Nombre del álbum</th>
                                            <th class="text-right" style="width: 60px;">Duplicar</th>
                                            <th class="text-right" style="width: 60px;">Editar</th>
                                            <th class="text-right" style="width: 60px;">Borrar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $image_albums = registros('image_album', " AND image_id = '$_REQUEST[id]'");
                                        foreach($image_albums as $image_album) {
                                            $album = registro('album', 'album_id', $image_album['album_id']);
                                        ?>
                                            <tr>
                                                <td class="text-center"><?php echo $album['name']; ?></td>
                                                <td class="text-right" style="width: 60px;">
                                                    <a href="#" data-toggle="modal" data-target="#modal-duplicate-<?php echo $album['album_id']; ?>"><i class="fa fa-plus"></i></a>
                                                </td>
                                                <td class="text-right" style="width: 60px;">
                                                    <a href="#" data-toggle="modal" data-target="#modal-<?php echo $album['album_id']; ?>"><i class="fa fa-edit"></i></a>
                                                </td>
                                                <td class="text-right" style="width: 60px;">
                                                    <a href="#" class="confirm" data-url="?id=<?php echo $_REQUEST['id']; ?>&delete-image_album=<?php echo $image_album['image_album_id']; ?>"><i class="fa fa-times"></i></a>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group row">
                                <label for="album" class="col-lg-2">Asociar a un album</label>
                                <div class="col-lg-8">
                                    <select class="form-control select2" name="album" id="album" style="width: 100%;">
                                        <option value="">-</option>
                                        <?php
                                        $albumes = registros('album', false, 'name');
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

                            <h3>Reescala la imagen</h3>
                            <div class='frame' style="border:3px solid black;border-radius:4px">
                                <img id='sample_picture' src="<?php echo getImageUrl($image['filename']); ?>" onload="co()" style="max-width: 100%;">
                            </div>
                            <h3 class="fa fa-arrows"> Puedes mover la imagen dentro del cuadro</h3>
                            <div class="container-fluid">
                                <div id='controls' class="row" style="margin-top:15px;margin-bottom:15px;">
                                    <button id='rotate_left'  type='button' title='Rotate left' class="btn btn-warning fa fa-undo col-xs-2 col-sm-2 col-md-2 col-lg-2"> </button>
                                    <button id='zoom_out'     type='button' title='Zoom out' class="btn btn-warning fa fa-search-minus  col-xs-3 col-sm-3 col-md-3 col-lg-3"> </button>
                                    <button id='fit'          type='button' title='Fit image' class="btn btn-warning fa fa-compress  col-xs-2 col-sm-2 col-md-2 col-lg-2"> </button>
                                    <button id='zoom_in'      type='button' title='Zoom in' class="btn btn-warning fa fa-search-plus  col-xs-3 col-sm-3 col-md-3 col-lg-3"> </button>
                                    <button id='rotate_right' type='button' title='Rotate right' class="btn btn-warning  fa fa-repeat  col-xs-2 col-sm-2 col-md-2 col-lg-2"> </button>
                                </div>
                            </div>
                            <input type="hidden" class="form-control" name="url" id="url" value="<?php echo getImageUrl($image['filename']); ?>">
                            <input type="hidden" class="form-control" name="imid" id="imid" value="<?php echo getImageUrl($image['filename']); ?>">
                            <input type="hidden" class="form-control" name="scale" id="scale">
                            <input type="hidden" class="form-control" name="angle" id="angle">
                            <input type="hidden" class="form-control" name="x" id="x">
                            <input type="hidden" class="form-control" name="y" id="y">
                            <input type="hidden" class="form-control" name="w" id="w">
                            <input type="hidden" class="form-control" name="h" id="h">
                        <?php
                        }
                        ?>
                        <input type="hidden" name="save" value="1">
                        <input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>">
                        <button type="submit" class="btn btn-success btn-block" onclick="updateImageData()">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$image_albums = registros('image_album', " AND image_id = '$_REQUEST[id]'");
foreach($image_albums as $image_album) {
    $album = registro('album', 'album_id', $image_album['album_id']);
?>
    <div class="modal fade" id="modal-<?php echo $album['album_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modal_label-<?php echo $album['album_id']; ?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" enctype="multipart/form-data" action="#">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modal_label-<?php echo $album['album_id']; ?>">
                            Datos de la imagen en el album <?php echo $album['name']; ?>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="title-<?php echo $album['album_id']; ?>" class="col-lg-2">Título</label>
                            <div class="col-lg-10">
                                <input type="text" name="title" id="title-<?php echo $album['album_id']; ?>" class="form-control" placeholder="Título para la imagen" value="<?php echo $image_album['title']; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description-<?php echo $album['album_id']; ?>" class="col-lg-2">Descripción</label>
                            <div class="col-lg-10">
                                <input type="text" name="description" id="description-<?php echo $album['album_id']; ?>" class="form-control" placeholder="Descripción para la imagen" value="<?php echo $image_album['description']; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="subdescription-<?php echo $album['album_id']; ?>" class="col-lg-2">Subdescripción</label>
                            <div class="col-lg-10">
                                <textarea class="form-control" rows="5" placeholder="Subdescripción para la imagen" name="subdescription" id="subdescription-<?php echo $album['album_id']; ?>"><?php echo $image_album['subdescription']; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="save" value="1">
                        <input type="hidden" name="image_album_id" value="<?php echo $image_album['image_album_id']; ?>">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-duplicate-<?php echo $album['album_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modal_label-duplicate-<?php echo $album['album_id']; ?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" enctype="multipart/form-data" action="#">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modal_label-duplicate-<?php echo $album['album_id']; ?>">
                            Duplicar imagen
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="new_album" class="col-lg-2">Album</label>
                            <div class="col-lg-10">
                                <select class="form-control select2" name="new_album" id="new_album" style="width: 100%;" required>
                                    <?php
                                    $albumes = registros('album', " AND album_id <> '$album[album_id]'", 'name');
                                    foreach($albumes as $album) {
                                        ?>
                                        <option value="<?php echo $album['album_id']; ?>">
                                            <?php echo $album['name']; ?>
                                        </option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="title-<?php echo $album['album_id']; ?>" class="col-lg-2">Título</label>
                            <div class="col-lg-10">
                                <input type="text" name="title" id="title-<?php echo $album['album_id']; ?>" class="form-control" placeholder="Título para la imagen" value="<?php echo $image_album['title']; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description-<?php echo $album['album_id']; ?>" class="col-lg-2">Descripción</label>
                            <div class="col-lg-10">
                                <input type="text" name="description" id="description-<?php echo $album['album_id']; ?>" class="form-control" placeholder="Descripción para la imagen" value="<?php echo $image_album['description']; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="subdescription-<?php echo $album['album_id']; ?>" class="col-lg-2">Subdescripción</label>
                            <div class="col-lg-10">
                                <textarea class="form-control" rows="5" placeholder="Subdescripción para la imagen" name="subdescription" id="subdescription-<?php echo $album['album_id']; ?>"><?php echo $image_album['subdescription']; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="save" value="1">
                        <input type="hidden" name="duplicate" value="<?php echo $_REQUEST['id'] ;?>">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Duplicar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
}
?>

<script src="js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script src="js/custom.js"></script>

</body>
</html>
