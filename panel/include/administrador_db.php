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

if ($_REQUEST['delete-album_user']) {
    $album_user_id = $_REQUEST['delete-album_user'];
    if(!remove_row('album_user', " AND album_user_id = '$album_user_id'")) {
        $msg_error = "
        <div class='alert alert-danger'>
            <strong>Ha ocurrido un error al intentar eliminar el album del administrador</strong>
            Estamos trabajando en solucionarlo. Si el error persiste transcurridos unos minutos, por favor contacte con el administrador del sistema.
        </div>
        ";
        $error = true;
    }
    else {
        $msg_success = "
        <div class='alert alert-success'>
            <strong>El administrador se ha eliminado del album correctamente.</strong>
        </div>
        ";
        $success = true;
    }
}

if($_REQUEST['save'] == '1') {
    if($_REQUEST['album']) {
        $q = "INSERT INTO album_user SET album_id = '$_REQUEST[album]', user = '$_REQUEST[id]', role_id = '2'";
        if(!db_in($q)) $error = true;
        else $success = true;
    }

    if(!$_REQUEST['permissions_id']) {
        $q = "
        INSERT INTO permissions SET
        user = '$_REQUEST[id]',
        role_id = '2'
        ";
        $permissions_id = db_in($q);
        if(!$permissions_id) $error = true;
        else $success = true;
        $_REQUEST['permissions_id'] = $permissions_id;
    }

    if($_REQUEST['permissions_id']) {
        print_r($_REQUEST);
        $q = "
        UPDATE permissions SET
        albums = '$_REQUEST[albums]',
        link_images = '$_REQUEST[link_images]',
        upload_images = '$_REQUEST[upload_images]',
        programming_albums = '$_REQUEST[programming_albums]'
        WHERE permissions_id = '$_REQUEST[permissions_id]'
        ";
        if(!db_query($q)) $error = true;
        else $success = true;
    }

}