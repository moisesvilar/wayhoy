<?php
$success = false;
$msg_success = "
<div class='alert alert-success'>
    <strong>La solicitud se ha procesado correctamente</strong>
</div>
";
$error = false;
$msg_error = "
<div class='alert alert-danger'>
    <strong>Ha ocurrido un error al procesar la solicitud</strong>
    Estamos trabajando en solucionarlo. Si el error persiste transcurridos unos minutos, por favor contacte con el administrador del sistema.
</div>
";

if ($_REQUEST['delete-image']) {
    $image_id = $_REQUEST['delete-image'];
    if(!remove_row('image', " AND image_id = '$image_id'")) {
        $msg_error = "
        <div class='alert alert-danger'>
            <strong>Ha ocurrido un error al intentar eliminar la imagen</strong>
            Estamos trabajando en solucionarlo. Si el error persiste transcurridos unos minutos, por favor contacte con el administrador del sistema.
        </div>
        ";
        $error = true;
    }
    else {
        $msg_success = "
        <div class='alert alert-success'>
            <strong>La imagen se ha eliminado correctamente.</strong>
        </div>
        ";
        $success = true;
    }
}

if ($_REQUEST['delete-image_album']) {
    $image_album_id = $_REQUEST['delete-image_album'];
    if(!remove_row('image_album', " AND image_album_id = '$image_album_id'")) {
        $msg_error = "
        <div class='alert alert-danger'>
            <strong>Ha ocurrido un error al intentar eliminar el album de la imagen</strong>
            Estamos trabajando en solucionarlo. Si el error persiste transcurridos unos minutos, por favor contacte con el administrador del sistema.
        </div>
        ";
        $error = true;
    }
    else {
        $msg_success = "
        <div class='alert alert-success'>
            <strong>La imagen se ha eliminado del album correctamente.</strong>
        </div>
        ";
        $success = true;
    }
}


if($_REQUEST['save'] == '1') {
    if (!$_REQUEST['id']) {
        if($_FILES['file']['name']) {
            $config = parse_ini_file(dirname(__FILE__).'/config.ini');
            $images_directory = dirname(__FILE__).'/../../../'.$config['project'].'/'.$config['images_directory'];
            $thumbnails_directory = dirname(__FILE__).'/../../../'.$config['project'].'/'.$config['thumbnails_directory'];
            $filename = hash_file('md5', $_FILES['file']['tmp_name']).'.jpg';
            $filename = md5(time().$filename).".jpg";
            $url = siteURL().$config['project']."/".$config['images_directory'].$filename;
            $url_thumbnail = siteURL().$config['project']."/".$config['thumbnails_directory'].$filename;

            $resultOriginal = prepareOriginalImage($_FILES['file']['tmp_name'], $images_directory.$filename);
            $resultThumbnail = prepareThumbnailImage($_FILES['file']['tmp_name'], $thumbnails_directory.$filename);

            if (!$resultOriginal || !$resultThumbnail) {
                $error = true;
                $msg_error = "
                <div class='alert alert-danger'>
                    <strong>Ha ocurrido un error al subir la imagen</strong>
                    Estamos trabajando en solucionarlo. Si el error persiste transcurridos unos minutos, por favor contacte con el administrador del sistema.
                </div>
                ";
            }
            else {
                $q = "INSERT INTO image SET filename = '$filename', url = '$url', url_thumbnail = '$url_thumbnail'";
                $id = db_in($q);
                if(!$id) $error = true;
                else $success = true;
                $_REQUEST['id'] = $id;
            }
        }
    }
    else {
        $image = registro('image', 'image_id', $_REQUEST['id']);

        $scale = $_REQUEST['scale'];
        $angle = $_REQUEST['angle'];
        $x = $_REQUEST['x'];
        $y = $_REQUEST['y'];
        $w = $_REQUEST['w'];
        $h = $_REQUEST['h'];

        if($x || $y || $w || $h || $scale || $angle) {
            $config = parse_ini_file(dirname(__FILE__) . '/config.ini');
            $images_directory = dirname(__FILE__) . '/../../../' . $config['project'] . '/' . $config['images_directory'];
            $thumbnails_directory = dirname(__FILE__) . '/../../../' . $config['project'] . '/' . $config['thumbnails_directory'];
            $filename = $image['filename'];
            $pathImage = $images_directory . $filename;
            $pathThumbnail = $thumbnails_directory . $filename;
            if (!updImage($pathImage, $angle, $w, $h, $x, $y, $scale)) {
                $error = true;
                $msg_error = "
            <div class='alert alert-danger'>
                <strong>Ha ocurrido un error al actualizar la imagen</strong>
                Estamos trabajando en solucionarlo. Si el error persiste transcurridos unos minutos, por favor contacte con el administrador del sistema.
            </div>
            ";
            } elseif (!prepareThumbnailImage($pathImage, $pathThumbnail)) {
                $error = true;
                $msg_error = "
            <div class='alert alert-danger'>
                <strong>Ha ocurrido un error al actualizar la imagen de thumbnail</strong>
                Estamos trabajando en solucionarlo. Si el error persiste transcurridos unos minutos, por favor contacte con el administrador del sistema.
            </div>
            ";
            }
        }

        $q = "UPDATE image SET title = '$_REQUEST[title]', description = '$_REQUEST[description]', subdescription = '$_REQUEST[subdescription]' WHERE image_id = '$_REQUEST[id]'";
        if (!db_query($q)) {
            $error = true;
            $msg_error = "
                <div class='alert alert-danger'>
                    <strong>Ha ocurrido un error al actualizar los datos de la imagen</strong>
                    Estamos trabajando en solucionarlo. Si el error persiste transcurridos unos minutos, por favor contacte con el administrador del sistema.
                </div>
                ";
        } else {
            $success = true;
        }
    }

    if($_REQUEST['album']) {
        $image = registro('image', 'image_id', $_REQUEST['id']);
        $q = "
        INSERT INTO image_album SET
        image_id = '$_REQUEST[id]',
        album_id = '$_REQUEST[album]',
        title = '$image[title]',
        description = '$image[description]',
        subdescription = '$image[subdescription]'
        ";
        if(!db_in($q)) $error = true;
        else $success = true;
    }

    if($_REQUEST['image_album_id']) {
        $q = "
        UPDATE image_album SET
        title = '$_REQUEST[title]',
        description = '$_REQUEST[description]',
        subdescription = '$_REQUEST[subdescription]'
        WHERE image_album_id = '$_REQUEST[image_album_id]'
        ";
        db_query($q);
        $success = true;
    }

    if($_REQUEST['duplicate']) {
        $q = "
        INSERT INTO image_album SET
        image_id = '$_REQUEST[duplicate]',
        album_id = '$_REQUEST[new_album]',
        title = '$_REQUEST[title]',
        description = '$_REQUEST[description]',
        subdescription = '$_REQUEST[subdescription]'
        ";
        if(!db_in($q)) $error = true;
        else $success = true;
    }
}