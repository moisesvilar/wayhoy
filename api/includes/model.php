<?php
/**
 * Obtiene un token temporal
 * @return string El token temporal
 */
function token($sub=false) {
    $seconds = time();
    if ($sub) {
        $seconds = $seconds - $sub * 60;
    }
    $minutes = intval($seconds/60);
    return strval($minutes);
}

/**
 * Obtiene un usuario
 * @param $idCustomer int El identificador del usuario en R.
 * @return array|bool El resultado de la operación.
 */
function user($idCustomer) {
    $idCustomer = db_escape($idCustomer);
    $q = "SELECT * FROM user WHERE user_id_r = '$idCustomer'";
    $info = db_select($q);
    return count($info) > 0 ? $info[0] : false;
}

/**
 * Introduce un nuevo usuario en la base de datos.
 * @param $email string El email del nuevo usuario.
 * @param $password string El hash MD5 del nuevo usuario.
 * @return bool|mysqli_result El resultado de la operación.
 */
function user_in($idCustomer) {
    $idCustomer = db_escape($idCustomer);
    $q = "
    INSERT INTO user (user_id_r) VALUES ('$idCustomer')
    ";
    return db_query($q);
}

/**
 * Actualiza los datos de un usuario.
 * @param int $idCustomer El identificador de R del usuario.
 * @param int $newIdCustomer El nuevo identificador de R del usuario.
 * @return bool|mysqli_result El resultado de la operación.
 */
function user_up($idCustomer, $newIdCustomer) {
    $idCustomer = db_escape($idCustomer);
    $newIdCustomer = db_escape($newIdCustomer);
    $q = "
    UPDATE user SET
    user_id_r = '$newIdCustomer',
    update_ts = CURRENT_TIMESTAMP
    WHERE user_id_r = '$idCustomer'";
    return db_query($q);
}

/**
 * Elimina un usuario del sistema.
 * @param int $idCustomer El identificador de R del usuario.
 * @return bool El resultado de la operación.
 */
function user_de($idCustomer) {
    $idCustomer = db_escape($idCustomer);
    $q = " DELETE FROM user WHERE user_id_r = '$idCustomer'";
    return db_query($q);
}

/**
 * Obtiene las pantallas de un usuario.
 * @param string $user El email del usuario.
 * @return array|bool Los datos requeridos o false si ha ocurrido algún error.
 */
function screens($user) {
    $user = db_escape($user);
    $q = "
    SELECT
    screen.code,
    user_screen.name
    FROM screen
    INNER JOIN user_screen ON screen.code = user_screen.screen
    WHERE user_screen.user = '$user'
    ";
    return db_result($q, true);
}

/**
 * Realiza una consulta sobre la tabla screen.
 * @param bool|string $code El código del screen.
 * @param bool|string $icc El ICC del screen.
 * @param bool|string $user El email del usuario.
 * @return array|bool Los datos requeridos o false si ha ocurrido algún error.
 */
function screen($code=false, $icc=false, $user=false) {
    $q = "SELECT * FROM screen WHERE 1=1";
    if ($code && !$user) {
        $code = db_escape($code);
        $q .= " AND code = '$code'";
    }
    elseif ($icc) {
        $icc = db_escape($icc);
        $q .= " AND icc = '$icc'";
    }
    elseif ($code && $user) {
        $user = db_escape($user);
        $q = "SELECT screen.* FROM screen INNER JOIN user_screen ON user_screen.screen = screen.code WHERE user_screen.user = '$user' AND screen.code = '$code'";
    }
    return db_result($q);
}

/**
 * Realiza una consulta sobre la tabla user_screen
 * @param bool|string $user El email del usuario.
 * @param bool|string $screen El código del screen.
 * @return array|bool Los datos requeridos o false si ha ocurrido algún error.
 */
function user_screen($user=false, $screen=false) {
    $user = db_escape($user);
    $screen = db_escape($screen);
    $q = "
    SELECT
    user_screen.screen as code,
    user_screen.name as name
    FROM user_screen
    WHERE 1=1
    ";
    if ($user) $q .= " AND user = '$user'";
    if ($screen) $q .= " AND screen = '$screen'";
    return db_result($q);
}

/**
 * Añade una pantalla a un usuario.
 * @param string $code El código de la pantalla.
 * @param string $user El email del usuario.
 * @param string $name El nombre de la pantalla.
 * @return bool|mysqli_result El resultado de la operación.
 */
function user_screen_in($code, $user, $name='') {
    $code = db_escape($code);
    $user = db_escape($user);
    $name = db_escape($name);
    $q = "INSERT INTO user_screen (name, screen, user) VALUES ('$name', '$code', '$user')";
    return db_query($q);
}

/**
 * Actualiza los datos de un STB asociado a un usuario.
 * @param string $code El código del STB.
 * @param string $name El nuevo nombre del STB.
 * @return bool|mysqli_result El resultado de la operación.
 */
function user_screen_up($code, $name) {
    $code = db_escape($code);
    $name = db_escape($name);
    $q = "
    UPDATE user_screen SET
    name = '$name',
    update_ts = CURRENT_TIMESTAMP
    WHERE screen = '$code'
    ";
    return db_query($q);
}

/**
 * Obtiene todos los albumes del usuario.
 * @param string $user El email del usuario.
 * @return array|bool Los datos requeridos o false si ha ocurrido algún error.
 */
function albums($user) {
    $user = db_escape($user);
    $q = "SELECT * FROM album WHERE album.user = '$user' ORDER BY creation_ts DESC";
    $albums = db_result($q, true);
    if ($albums === false) return false;
    $result = array();
    $i=0;
    foreach($albums as $album) {
        $result[$i]['id'] = $album['album_id'];
        $result[$i]['name'] = $album['name'];
        $result[$i]['images'] = images($album['album_id']);
        $i++;
    }
    return $result;
}

/**
 * Obtiene el album especificado.
 * @param int $id El identificador del album.
 * @return array|bool El album especificado o false si ha ocurrido algún error.
 */
function album($id) {
    $id = db_escape($id);
    $q = "SELECT * FROM album WHERE album_id = '$id'";
    $album = db_result($q);
    $result['id'] = $album['album_id'];
    $result['name'] = $album['name'];
    $result['images'] = images($album['album_id']);
    return $result;
}

/**
 * Obtiene un conjunto de imágenes especificado.
 * @param int $album El identificador del album.
 * @return array|bool Los datos requeridos o false si ha ocurrido algún error.
 */
function images($album) {
    $album = db_escape($album);
    $q = "
    SELECT
    image.url,
    image.filename,
    image_album.*
    FROM image
    INNER JOIN image_album ON image_album.image_id = image.image_id
    WHERE image_album.album_id = '$album'
    ";
    $q .= " ORDER BY posicion, image_album.image_album_id DESC";
    $images = db_result($q, true);
    if ($images === false) return false;
    $result = array();
    $i=0;
    foreach($images as $image) {
        $result[$i]['title'] = $image['title'];
        $result[$i]['description'] = $image['description'];
        $result[$i]['subdescription'] = $image['subdescription'];
        $result[$i]['filename'] = $image['filename'];
        $result[$i]['url'] = $image['url'];
        $i++;
    }
    return $result;
}
function albumprogramm($stb){
	$q="SELECT album as album_id FROM programming WHERE DAYOFWEEK(NOW()) = day AND CURTIME() BETWEEN start AND end AND pausado = '0' AND screen = '$stb' ORDER BY start DESC LIMIT 1";
	return db_result($q, true);
	}
function pausarInactivos($stb){
	$q="UPDATE programming SET pausado = '0' WHERE screen = '$stb'";
	return db_query($q);
}
/**
 * Obtiene la imagen especificada.
 * @param int $id El identificador de la imagen.
 * @param int $album El identificador del album. Opcional.
 * @param string @filename El nombre de la imagen. Opcional.
 * @param string @user El email del usuario. Opcional.
 * @return array|bool Los datos de la imagen o false si no se ha encontrado.
 */
function image($id, $album=false, $filename=false, $user=false) {
    $id = db_escape($id);
    $album = db_escape($album);
    $filename = db_escape($filename);
    $user = db_escape($user);

    if ($album) {
        $q = "
        SELECT
        image.url_thumbnail,
        image.url,
        image.filename,
        image_album.*
        FROM image_album
        INNER JOIN image ON image.image_id = image_album.image_id
        WHERE image_album.image_id = '$id' AND image_album.album_id = '$album'";
        $image = db_result($q);
        if (!$image) return false;
        $result['id'] = $image['image_id'];
        $result['url'] = $image['url'];
        $result['url_thumbnail'] = $image['url_thumbnail'];
        $result['filename'] = $image['filename'];
        $result['title'] = $image['title'];
        $result['description'] = $image['description'];
        $result['subdescription'] = $image['subdescription'];
        return $result;
    }
    elseif ($filename && $user) {
        $q = "SELECT * FROM image WHERE filename = '$filename' AND user = '$user'";
        $image = db_result($q);
        if (!$image) return false;
        $result['id'] = $image['image_id'];
        $result['url'] = $image['url'];
        $result['url_thumbnail'] = $image['url_thumbnail'];
        $result['filename'] = $image['filename'];
        return $result;
    }
    elseif ($id) {
        $q = "SELECT * FROM image WHERE image_id = '$id'";
        $image = db_result($q);
        if (!$image) return false;
        $result['id'] = $image['image_id'];
        $result['url'] = $image['url'];
        $result['url_thumbnail'] = $image['url_thumbnail'];
        $result['filename'] = $image['filename'];
        return $result;
    }
    return false;
}

/**
 * Elimina una imagen de la base de datos.
 * @param int $imageId El identificador de la imagen.
 * @return bool|mysqli_result El resultado de la operación.
 */
function image_de($imageId) {
    $imageId = db_escape($imageId);
    $q = "DELETE FROM image WHERE image_id = '$imageId'";
    return db_query($q);
}

/**
 * Realiza una escritura en la tabla album
 * @param string $name El nombre del album.
 * @param string $email El email del usuario.
 * @return bool|array El album creado o false si ha ocurrido algún error.
 */
function album_in($name, $user) {
    $name = db_escape($name);
    $user = db_escape($user);
    $q = " INSERT INTO album (name, user) VALUES ('$name', '$user')";
    $id = db_in($q);
    if (!$id) return false;
    return album($id);
}

/**
 * Actualiza los datos de un album
 * @param int $id El identificador del album
 * @param string $name El nuevo nombre del album.
 * @return bool|array El album actualizado o false si ha ocurrido algún error.
 */
function album_up($id, $name) {
    $id = db_escape($id);
    $name = db_escape($name);
    $q = "
    UPDATE album SET
    name = '$name',
    update_ts = CURRENT_TIMESTAMP
    WHERE album_id = '$id'";
    if (!db_query($q)) return false;
    return album($id);
}

/**
 * Elimina un album de la base de datos
 * @param $id
 * @return bool|mysqli_result
 */
function album_de($id) {
    $id = db_escape($id);
    $q = "DELETE FROM album WHERE album_id = '$id'";
    return db_query($q);
}

/**
 * Inserta una nueva imagen en la base de datos.
 * @param string $filename El nombre del archivo que contiene la imagen.
 * @param string $url La URL que apunta a la imagen.
 * @param string $url_thumbnail La URL que apunta al thumbnail.
 * @param string $user El email del usuario.
 * @return array|bool Los datos de la imagen o false si ha ocurrido algún error.
 */
function image_in($filename, $url, $url_thumbnail, $user) {
    $url = db_escape($url);
    $url_thumbnail = db_escape($url_thumbnail);
    $user = db_escape($user);
    $q = "INSERT INTO image (filename, url, url_thumbnail, user) VALUES ('$filename', '$url', '$url_thumbnail', '$user')";
    $id = db_in($q);
    if (!$id) return false;
    return image($id);
}

/**
 * Realiza una consulta en la tabla image_album
 * @param int $imageId Identificador de la imagen.
 * @param int $albumId Identificador del album.
 * @return bool|array Los datos requeridos o false si ha habido algún error.
 */
function image_album($imageId, $albumId) {
    $imageId = db_escape($imageId);
    $albumId = db_escape($albumId);
    $q = "SELECT * FROM image_album WHERE image_id = '$imageId' AND album_id = '$albumId'";
    $images = db_result($q, true);
    if ($images === false) return false;
    $result = array();
    $i=0;
    foreach($images as $image) {
        $result[$i]['album_id'] = $image['album_id'];
        $result[$i]['image_id'] = $image['image_id'];
        $result[$i]['title'] = $image['title'];
        $result[$i]['descripcion'] = $image['descripcion'];
        $result[$i]['subdescripcion'] = $image['subdescripcion'];
        $i++;
    }
    return $result;
}

/**
 * Asocia una imagen a un álbum.
 * @param int $imageId Identificador de la imagen.
 * @param int $albumId Identificador del album.
 * @return bool|mysqli_result El resultado de la operación.
 */
function image_album_in($imageId, $albumId) {
    $imageId = db_escape($imageId);
    $albumId = db_escape($albumId);
    $q = "INSERT INTO image_album (album_id, image_id) VALUES ('$albumId', '$imageId')";
    return db_query($q);
}

/**
 * Actualiza los datos de una imagen asociada a un album.
 * @param int $imageId El identificador de la imagen.
 * @param int $albumId El identificador del album.
 * @param string $title El título de la imagen.
 * @param string $descripcion La descripción de la imagen.
 * @param string $subdescription La subdescripción de la imagen.
 * @return bool|mysqli_result El resultado de la operación.
 */
function image_album_up($imageId, $albumId, $title, $descripcion, $subdescription) {
    $imageId = db_escape($imageId);
    $albumId = db_escape($albumId);
    $title = db_escape($title);
    $descripcion = db_escape($descripcion);
    $subdescription = db_escape($subdescription);
    $q = "
    UPDATE image_album SET
    title = '$title',
    description = '$descripcion',
    subdescription = '$subdescription',
    update_ts = CURRENT_TIMESTAMP
    WHERE image_id = '$imageId' AND album_id = '$albumId'
    ";
    return db_query($q);
}

/**
 * Desasocia una imagen de un album.
 * @param int $imageId El identificador de la imagen.
 * @param int $albumId El identificador del álbum.
 * @return bool|mysqli_result
 */
function image_album_de($imageId, $albumId) {
    $imageId = db_escape($imageId);
    $albumId = db_escape($albumId);
    $q = "DELETE FROM image_album WHERE image_id = '$imageId' AND album_id = '$albumId'";
    return db_query($q);
}

/**
 * Comprueba si un objeto pertenece al usuario.
 * @param string $table Nombre de la tabla del objeto.
 * @param int $id Identificador del objeto.
 * @param string $user Email del usuario.
 * @return bool El resultado de la comprobación.
 */
function isYours($table, $id, $user, $field=false) {
    $table = db_escape($table);
    $id = db_escape($id);
    $user = db_escape($user);
    $field = db_escape($field);
    if (!$field) $field = $table."_id";
    $q = "SELECT * FROM $table WHERE $field = '$id' AND user = '$user'";
    $row = db_result($q);
    return $row !== false;
}

/**
 * Envía un álbum al módulo WayHoy! de la pantalla especificada.
 * @param int $albumId El identificador del album.
 * @param string $screenId El código de la pantalla.
 * @return bool El resultado de la operación.
 */
function sendAlbum($albumId, $screenId) {
    //TODO: no implementado.
    return true;
}

/**
 * Introduce una pantalla en el sistema.
 * @param string $code El código del STB.
 * @param string $icc El ICC del STB.
 * @param int $idCustomer El identificador del cliente en R.
 * @return bool|mysqli_result El resultado de la operación.
 */
function screen_in($code, $icc, $idCustomer) {
    $code = db_escape($code);
    $icc = db_escape($icc);
    $idCustomer = db_escape($idCustomer);
    $q = "
    INSERT INTO screen (code, icc, user_id_r) VALUES
    ('$code', '$icc', '$idCustomer')
    ";
    return db_query($q);
}

/**
 * Introduce un STB en el sistema.
 * @param int $idCustomer El identificador del cliente en R.
 * @param string $icc El ICC del STB.
 * @return bool|mysqli_result El resultado de la operación.
 */
function stb_in($idCustomer, $icc) {
    $code = generar_clave(6);
    while (screen($code)) {
        $code = generar_clave(6);
    }
    return screen_in($code, $icc, $idCustomer);
}

/**
 * Realiza una consulta en la tabla de STBs.
 * @param int $idCustomer El identificador del cliente en R.
 * @param string $icc El ICC del STB.
 * @return array|bool Los datos requeridos o false en caso de error.
 */
function stb($idCustomer=false, $icc) {
    $idCustomer = db_escape($idCustomer);
    $icc = db_escape($icc);
    $q = "SELECT * FROM screen WHERE icc = '$icc'";
    if ($idCustomer) $q .= " AND user_id_r = '$idCustomer'";
    return db_result($q, true);
}

/**
 * Actualiza el ICC de un STB.
 * @param string $oldIcc El ICC del STB a modificar.
 * @param string $newIcc El nuevo ICC del STB.
 * @return bool|mysqli_result El resultado de la operación.
 */
function stb_up($oldIcc, $newIcc) {
    $oldIcc = db_escape($oldIcc);
    $newIcc = db_escape($newIcc);
    $q = "UPDATE screen SET icc = '$newIcc' WHERE icc = '$oldIcc'";
    return db_query($q);
}

/**
 * Elimina el STB indicado.
 * @param string $icc El ICC del STB a eliminar.
 * @return bool|mysqli_result El resultado de la operación.
 */
function stb_de($icc) {
    $icc = db_escape($icc);
    $q = "DELETE FROM screen WHERE icc = '$icc'";
    return db_query($q);
}

/**
 * Elimina todos los usuarios sin STBs asociados.
 */
function removeUsersWithouthStbs() {
    $q = "DELETE FROM user WHERE user_id_r NOT IN (SELECT user_id_r FROM screen)";
    db_query($q);
}

/**
 * Obtiene los álbumes asociados a una pantalla.
 * @param string $screen Código de la pantalla.
 * @return array|bool Los datos requeridos o false si ha ocurrido algún error.
 */
function screen_albums($screen) {
    $screen = db_escape($screen);
    $q = "SELECT * FROM screen_album WHERE screen = '$screen'";
    return db_result($q, true);
}
?>