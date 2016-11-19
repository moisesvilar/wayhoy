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
 * @param $email string El email del usuario.
 * @return array|bool El resultado de la operación.
 */
function user($email) {
    $email = db_escape($email);
    $q = "SELECT * FROM user WHERE email = '$email'";
    $info = db_select($q);
    return count($info) > 0 ? $info[0] : false;
}

/**
 * Introduce un nuevo usuario en la base de datos.
 * @param $email string El email del nuevo usuario.
 * @param $password string El hash MD5 del nuevo usuario.
 * @return bool|mysqli_result El resultado de la operación.
 */
function user_in($id, $email, $password) {
    $id = db_escape($id);
    $email = db_escape($email);
    $password = db_escape($password);
    $q = "
    UPDATE user SET email = '$email', password = '$password', registration_ts = CURRENT_TIMESTAMP WHERE user_id_r = '$id'
    ";
    return db_query($q);
}

/**
 * Actualiza los datos de un usuario.
 * @param string $email El email del usuario.
 * @param string $password El hash MD5 de la nueva contraseña del usuario.
 * @return bool|mysqli_result El resultado de la operación.
 */
function user_up($email, $password) {
    $email = db_escape($email);
    $password = db_escape($password);
    $q = "
    UPDATE user SET
    password = '$password',
    update_ts = CURRENT_TIMESTAMP
    WHERE email = '$email'";
    return db_query($q);
}
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
        $q = "SELECT screen.*, user_screen.name as name, user_screen.ts_up as updated FROM screen INNER JOIN user_screen ON user_screen.screen = screen.code WHERE user_screen.user = '$user' AND screen.code = '$code'";
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
    user_screen.name as name, 
	user_screen.user as user
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
 * @param bool|int $album El identificador del album. Opcional.
 * @param bool|string $user El email del usuario. Opcional
 * * @param bool|string $filename El nombre del fichero que contiene. Opcional
 * @return array|bool Los datos requeridos o false si ha ocurrido algún error.
 */
function images($album=false, $user=false, $filename=false, $alwaysArray=false) {
    $album = db_escape($album);
    $user = db_escape($user);
    $q = "";
    if ($album) {
        $q = "
        SELECT
        *
        FROM image_album
        INNER JOIN image ON image_album.image_id = image.image_id
        WHERE image_album.album_id = '$album'
        ";
        if ($user) $q .= " AND image.user = '$user'";
        $q .= " ORDER BY posicion, image_album.image_album_id DESC";
        $images = db_result($q, true);
        if ($images === false) return false;
        $result = array();
        $i=0;
        foreach($images as $image) {
            $result[$i]['id'] = $image['image_album_id'];
			$result[$i]['idim'] = $image['image_id'];
            $result[$i]['url_thumbnail'] = $image['url_thumbnail'];
            $result[$i]['url'] = '/mobile/images/'.$image['filename'];
			$result[$i]['position'] = $image['posicion'];
			$result[$i]['duration'] = $image['duration'];
			$result[$i]['title'] = $image['title'];
			$result[$i]['description'] = preg_replace('#<br\s*/?>#i', "", $image['description']);
			$result[$i]['subdescription'] = $image['subdescription'];
            $i++;
        }
        return $result;
    }
    elseif($user) {
        $q = "SELECT * FROM image WHERE user = '$user' ORDER BY ts DESC";
    }
    elseif($filename) {
        $q = "SELECT * FROM image WHERE filename = '$filename' ORDER BY ts DESC";
        $image = db_result($q, $alwaysArray);
        return $image;
    }
    $images = db_result($q, true);
    if ($images === false) return false;
    $result = array();
    $i=0;
    foreach($images as $image) {
        $result[$i]['id'] = $image['image_id'];
        $result[$i]['url_thumbnail'] = $image['url_thumbnail'];
        $result[$i]['url'] = '/mobile/images/'.$image['filename'];
		$result[$i]['tipo'] = $image['tipo'];
		$result[$i]['ts'] = $image['ts'];
        $i++;
    }
    return $result;
}

/**
 * Obtiene la imagen especificada.
 * @param int $id El identificador de la imagen.
 * @param int $album El identificador del album. Opcional.
 * @param string @filename El nombre de la imagen. Opcional.
 * @param string @user El email del usuario. Opcional.
 * @return array|bool Los datos de la imagen o false si no se ha encontrado.
 */
function image($id, $album=false, $filename=false, $user=false, $imageAlbumId=false) {
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
		FROM image
		LEFT JOIN image_album ON image_album.image_id = image.image_id
		WHERE image_album.image_album_id = '$id'
		ORDER BY image_album.update_ts DESC
		LIMIT 1
		";
        $image = db_result($q);
        if (!$image) return false;
        $result['id'] = $image['image_id'];
        $result['url'] = '/mobile/images/'.$image['filename'];
        $result['url_thumbnail'] = $image['url_thumbnail'];
        $result['filename'] = $image['filename'];
        $result['title'] = $image['title'];
        $result['description'] = preg_replace('#<br\s*/?>#i', "", $image['description']);
        $result['subdescription'] = $image['subdescription'];
		$result['duration'] = $image['duration'];
        return $result;
    }
    elseif ($filename && $user) {
        $q = "SELECT * FROM image WHERE filename = '$filename' AND user = '$user'";
        $image = db_result($q);
        if (!$image) return false;
        $result['id'] = $image['image_id'];
        $result['url'] = '/mobile/images/'.$image['filename'];
        $result['url_thumbnail'] = $image['url_thumbnail'];
        $result['filename'] = $image['filename'];
		$result[$i]['tipo'] = $image['tipo'];
		$result[$i]['ts'] = $image['ts'];
        return $result;
    }
    elseif ($id) {
        $q = "SELECT * FROM image WHERE image_id = '$id'";
        $image = db_result($q);
        if (!$image) return false;
        $result['id'] = $image['image_id'];
        $result['url'] = '/mobile/images/'.$image['filename'];
        $result['url_thumbnail'] = $image['url_thumbnail'];
        $result['filename'] = $image['filename'];
		$result[$i]['tipo'] = $image['tipo'];
		$result[$i]['ts'] = $image['ts'];
        return $result;
    }
    elseif($imageAlbumId) {
        $q = "
        SELECT
        image.url_thumbnail,
        image.url,
        image.filename,
        image_album.*
        FROM image_album
        INNER JOIN image ON image.image_id = image_album.image_id
        WHERE image_album.image_album_id = '$imageAlbumId'
        ";
        $image = db_result($q);
        if (!$image) return false;
        $result['id'] = $image['image_id'];
        $result['url'] = '/mobile/images/'.$image['filename'];
        $result['url_thumbnail'] = $image['url_thumbnail'];
        $result['filename'] = $image['filename'];
        $result['title'] = $image['title'];
        $result['description'] = preg_replace('#<br\s*/?>#i', "", $image['description']);
        $result['subdescription'] = $image['subdescription'];
		$result['duration'] = $image['duration'];
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
function image_in($filename, $url, $url_thumbnail, $user, $tipo) {
    $url = db_escape($url);
    $url_thumbnail = db_escape($url_thumbnail);
    $user = db_escape($user);
	$tipo = db_escape($tipo);
    $q = "INSERT INTO image (filename, url, url_thumbnail, user, tipo) VALUES ('$filename', '$url', '$url_thumbnail', '$user', '$tipo')";
    $id = db_in($q);
    if (!$id) return false;
    return image($id);
}
function fromtpv_in($url, $user) {
	$q = "SELECT url FROM image WHERE url = '$url'";
	
    if (!$image = db_result($q)){
		
    $url = db_escape($url);
	$q = "INSERT INTO image (filename, url, url_thumbnail, user) VALUES ('$url', '$url', '$url', '$user')";
    if (!db_in($q)){ return 'error';}else{ return 'insertada';}	
	}else{
	return 'no insertada';	
		}
}
/**
 * Realiza una consulta en la tabla image_album
 * @param int $imageId Identificador de la imagen.
 * @param int $albumId Identificador del album.
 * @return bool|array Los datos requeridos o false si ha habido algún error.
 */
function image_album($imageId, $albumId=false, $imageAlbumId=false) {
    if (!$imageAlbumId) {
        $imageId = db_escape($imageId);
        $albumId = db_escape($albumId);
        $q = "SELECT * FROM image_album WHERE image_album_id = '$imageId' AND album_id = '$albumId'";
        $images = db_result($q, true);
        if ($images === false) return false;
        $result = array();
        $i=0;
        foreach($images as $image) {
            $result[$i]['album_id'] = $image['album_album_id'];
            $result[$i]['image_id'] = $image['image_id'];
            $result[$i]['image_album_id'] = $image['image_album_id'];
            $result[$i]['title'] = $image['title'];
            $result[$i]['description'] = preg_replace('#<br\s*/?>#i', "", $image['description']);
            $result[$i]['subdescription'] = $image['subdescription'];
			$result[$i]['position'] = $image['posicion'];
			$result[$i]['duration'] = $image['duration'];
            $i++;
        }
        return $result;
    }
    else {
        $imageAlbumId = db_escape($imageAlbumId);
        $q = "SELECT * FROM image_album WHERE image_album_id = '$imageAlbumId'";
        $image = db_result($q);
        if ($image === false) return false;
        return $image;
    }
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
	$q = "update image_album SET posicion = posicion + 1 
WHERE album_id = '$albumId'";
	db_query($q);
    $q = "INSERT INTO image_album (album_id, image_id, posicion) VALUES ('$albumId', '$imageId', '1')";
   return  db_query($q);
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
function image_album_up($imageAlbumId, $title, $descripcion, $subdescription, $duration) {
    $imageAlbumId = db_escape($imageAlbumId);
    $title = db_escape($title);
    $descripcion = db_escape($descripcion);
    $subdescription = db_escape($subdescription);
	$duration = db_escape($duration);
    $q = "
    UPDATE image_album SET
    title = '$title',
    description = '$descripcion',
    subdescription = '$subdescription',
    update_ts = CURRENT_TIMESTAMP,
	duration = '$duration'
    WHERE image_album_id = '$imageAlbumId'
    ";
    return db_query($q);
}

/**
 * Desasocia una imagen de un album.
 * @param int $imageId El identificador de la imagen.
 * @param int $albumId El identificador del álbum.
 * @return bool|mysqli_result
 */
function image_album_de($imageId, $albumId, $imageAlbumId=false) {
    if (!$imageAlbumId) {
        $imageId = db_escape($imageId);
        $albumId = db_escape($albumId);
        $q = "DELETE FROM image_album WHERE image_id = '$imageId' AND album_id = '$albumId'";
        return db_query($q);
    }
    else {
        $imageAlbumId = db_escape($imageAlbumId);
        $q = "DELETE FROM image_album WHERE image_album_id = '$imageAlbumId'";
        return db_query($q);
    }
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
    $q = "SELECT * FROM $table WHERE $field = '$id' AND (user = '$user' || user='demo@wayhoy.com')";
    $row = db_result($q);
    return $row !== false;
}

/**
 * Envía un álbum al módulo WayHoy! de la pantalla especificada.
 * @param int $albumId El identificador del album.
 * @param string $screenId El código de la pantalla.
 * @return bool El resultado de la operación.
 */
function sendAlbum($albumId, $screenId, $cambio, $valor) {
    $albumId = db_escape($albumId);
    $screenId = db_escape($screenId);
	$cambio = db_escape($cambio);
	$valor = db_escape($valor);
	if ($albumId == '1000'){
		$campo = 'video';
		$video = $cambio.':'.$valor;
	}else if ($albumId == '1001'){
		$campo = 'font';
		$video = $valor;
	}else if ($albumId == '1002'){
		$campo = 'tpl';
		$video = $valor;
	}else if ($albumId == '1003'){
		$campo = 'fondo';
		$video = $valor;
	}else if ($albumId == '1004'){
		if ($cambio==str_replace('twitter', '', $cambio)){
			$campo = 'radio';
		}else{
			$campo = 'twitter';	
		}
		$video = $valor;
	}else if ($albumId == '1005'){
		$campo = 'cambio';
		$video = $valor;
	}else{ 
		$campo = 'album_id';
		$video = $albumId;
	}
    $q = "
    INSERT INTO screen_album (screen, album_id, cambio, valor, video, font, tpl, fondo, ts) VALUES
    ('$screenId', '$albumId', '$cambio', '$valor', '$video', '', '', '', CURRENT_TIMESTAMP)
    ON DUPLICATE KEY UPDATE
    `".$campo."` = '$video',
	`cambio` = '$cambio',
	`valor` = '$valor',
    `ts` = CURRENT_TIMESTAMP
    ";
    if (!db_query($q)) return false;
	$q = "SELECT programming_id FROM programming WHERE DAYOFWEEK(NOW()) = day AND CURTIME() BETWEEN start AND end AND pausado = '0' AND screen = '$screenId' ORDER BY start DESC LIMIT 1";
	$programming = db_result($q);
	$programmingId = $programming['programming_id'];
	$q = "UPDATE programming SET pausado = '1' WHERE programming_id = '$programmingId'";
	return db_query($q);
}
/*
comprobamos si el nº de orden ya lo tiene alguna imagen en el album
*/
function ordenExists($orden, $albumId){
	$orden = db_escape($orden);
	$albumId = db_escape($albumId);
	$q = "SELECT image_album.image_album_id FROM image_album
WHERE posicion = '$orden' AND album_id = '$albumId'";
	$result = db_result($q, true);	
	$imageAlbumId = $result[0]['image_album_id'];
	return $imageAlbumId;
}
// el nº de orden existe. Por lo tanto cambiamos el nº de orden de la imagen que lo tiene y le sumamos 1 a todas las que tengan un nº de orden superior
function ordenamelo($orden, $albumId, $imageAlbumId){
	$orden = db_escape($orden);
	$albumId = db_escape($albumId);
	$imageAlbumId = db_escape($imageAlbumId);
	$q = "SELECT posicion FROM image_album
WHERE image_album_id = '$imageAlbumId'";
	$result = db_result($q, true);	
	$ordenAnt = $result[0]['posicion'];
	$q = "UPDATE image_album 
SET posicion = null 
WHERE image_album_id = '$imageAlbumId'";
	db_query($q);
if ($ordenAnt<$orden){
	$q = "UPDATE image_album 
SET posicion = posicion - 1 
WHERE posicion > '$ordenAnt' && posicion <= '$orden' 
AND album_id = '$albumId'";
	}else{
	$q = "UPDATE image_album 
SET posicion = posicion + 1 
WHERE posicion >= '$orden' && posicion < '$ordenAnt'
AND album_id = '$albumId'";
	}
	db_query($q);
	$q = "UPDATE image_album SET posicion = '$orden' WHERE image_album_id = '$imageAlbumId'";
	return db_query($q);
}
function updateorderFromelim($orden, $albumId){
	$q = "UPDATE image_album 
SET posicion = posicion - 1 
WHERE posicion > '$orden'
AND album_id = '$albumId'";
return db_query($q);
}
function getAlbumyOrdenFromelimGen($imageId){
	$q = "select album_id, image_album_id, posicion from image_album where image_id = '$imageId'";
	$images = db_result($q, true);
        if ($images === false) return false;
        foreach($images as $image) {
            $albumId = $image['album_id'];
            $orden = $image['posicion'];
			$imageAlbumId = $image['image_album_id'];
			image_album_de(false, false, $imageAlbumId);
			updateorderFromelim($orden, $albumId);
        }
}
function setToken($email, $token) {
    $email = db_escape($email);
    $token = db_escape($token);
    $q = "
    INSERT INTO user_recovery (email, token) VALUES ('$email', '$token')
    ";
    if (db_query($q)) return $token;
    return false;
}
