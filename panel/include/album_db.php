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
if ($_REQUEST['delete-album']) {
    $album_id = $_REQUEST['delete-album'];
    if(!remove_row('album', " AND album_id = '$album_id'")) {
        $msg_error = "
        <div class='alert alert-danger'>
            <strong>Ha ocurrido un error al intentar eliminar el album</strong>
            Estamos trabajando en solucionarlo. Si el error persiste transcurridos unos minutos, por favor contacte con el administrador del sistema.
        </div>
        ";
        $error = true;
    }
    else {
        $msg_success = "
        <div class='alert alert-success'>
            <strong>El album se ha eliminado correctamente.</strong>
        </div>
        ";
        $success = true;
    }
}

if ($_REQUEST['save'] == '1') {
    if ($_REQUEST['id']) {
        $q = "UPDATE album SET name = '$_REQUEST[name]' WHERE album_id = '$_REQUEST[id]'";
        if(!db_query($q)) $error = true;
        else $success = true;
    }
    else {
        $q = "INSERT INTO album SET name = '$_REQUEST[name]'";
        $id = db_in($q);
        if(!$id) $error = true;
        else $success = true;
        $_REQUEST['id'] = $id;
    }
}