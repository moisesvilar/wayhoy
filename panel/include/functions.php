<?php

/**
 * Comprueba las credenciales de usuario de un administrador
 * TODO: enlazar con la API de R.
 * @param $username
 * @param $password
 * @return bool
 */
function check_admin_credentials($username, $password) {
    if ($username == 'demo' && $password == 'demo') {
        $_SESSION['access'] = true;
        $_SESSION['user'] = array(
            'username' => $username
        );
        return true;
    }
    $_SESSION['access'] = false;
    return false;
}

/**
 * Comprueba si el administrador ha accedido correctamente a la aplicación.
 * @return bool
 */
function check_access() {
    return $_SESSION['access'] === true;
}

/**
 * Devuelve los datos del usuario administrador que ha tenido acceso.
 * @return mixed
 */
function get_user() {
    return $_SESSION['user'];
}

/**
 * Elimina la sesión del usuario.
 */
function logout() {
    unset($_SESSION['access']);
    unset($_SESSION['user']);
}

/**
 * Realiza una consulta sobre una tabla.
 * @param $table
 * @param $where
 * @return array|bool
 */
function registros($table, $where=false, $order=false) {
    $q = "SELECT * FROM $table WHERE 1=1";
    if($where) $q .= " $where";
    if($order) $q .= " ORDER BY $order";
    return db_select($q);
}

/**
 * Obtiene un único registro de la base de datos.
 * @param $table
 * @param $id_field
 * @param $id
 * @return array|bool
 */
function registro($table, $id_field, $id, $where='') {
    $q = "SELECT * FROM $table WHERE $id_field = '$id' $where LIMIT 1";
    return db_result($q);
}

/**
 * Elimina uno o varios registros de una tabla.
 * @param $table
 * @param $where
 * @return bool|mysqli_result
 */
function remove_row($table, $where) {
    if(!$where) return false;
    $q = "DELETE FROM $table WHERE 1=1 $where";
    return db_query($q);
}

/**
 * Obtiene la URL actual del sitio
 * @return string
 */
function siteURL() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'].'/';
    return $protocol.$domainName;
}


/**
 * Prepara una imagen para su almacenamiento como imagen original.
 * @param string $originalImage Ruta a la imagen original.
 * @param string $outputImage Ruta a la imagen resultado.
 * @return bool El resultado de la operación.
 */
function prepareOriginalImage($originalImage, $outputImage) {
    if (!convertToJpeg($originalImage, $outputImage)) return false;
    $config = parse_ini_file(dirname(__FILE__).'/config.ini');
    $finalHeight = $config['original_height'];
    list($width, $height) = getimagesize($outputImage);
    $originalAspect = $width / $height;
    $finalWidth = $finalHeight * $originalAspect;
    resizeImage($outputImage, $finalWidth, $finalHeight);
    return true;
}

/**
 * Prepara una imagen para su almacenamiento como thumbnail.
 * @param string $originalImage Ruta a la imagen original.
 * @param string $outputImage Ruta a la imagen resultado.
 * @return bool El resultado de la operación.
 */
function prepareThumbnailImage($originalImage, $outputImage) {
    if (!convertToJpeg($originalImage, $outputImage)) return false;
    $config = parse_ini_file(dirname(__FILE__).'/config.ini');
    $finalHeight = $config['thumbnail_height'];
    list($width, $height) = getimagesize($outputImage);
    $originalAspect = $width / $height;
    $finalWidth = $finalHeight * $originalAspect;
    resizeImage($outputImage, $finalWidth, $finalHeight);
    return true;
}

/**
 * Convierte cualquier imagen a JPEG
 * @param string $originalImage Ruta a la imagen original
 * @param string $outputImage Ruta a la imagen resultado.
 * @param int $quality Nivel de calidad de la imagen. 100 (defecto) la mejor, 0 la peor.
 * @return bool False si el formato de la imagen no está soportado. True si la operación ha concluído con éxito.
 */
function convertToJpeg($originalImage, $outputImage, $quality=100) {
    $finfo = finfo_open(FILEINFO_MIME);
    $mimetype = finfo_file($finfo, $originalImage);
    finfo_close($finfo);
    $exploded = explode(';', $mimetype);
    $mimetype = $exploded[0];
    $exploded = explode('/', $mimetype);
    $ext = $exploded[1];
    if (preg_match('/jpg|jpeg/i',$ext)) {
        $imageTmp = imagecreatefromjpeg($originalImage);
    }
    elseif (preg_match('/png/i',$ext)) {
        $imageTmp = imagecreatefrompng($originalImage);
    }
    elseif (preg_match('/gif/i',$ext)) {
        $imageTmp = imagecreatefromgif($originalImage);
    }
    elseif (preg_match('/bmp/i',$ext)) {
        $imageTmp = imagecreatefromwbmp($originalImage);
    }
    else {
        return false;
    }
    imagejpeg($imageTmp, $outputImage, $quality);
    return $imageTmp;
}

/**
 * Cambia el tamaño de una imagen.
 * @param string $file Ruta completa a la imagen.
 * @param int $w Ancho de la imagen en pixeles.
 * @param int $h Alto de la imagen en pixeles.
 * @param int $quality Nivel de calidad de la imagen. 100 (defecto) la mejor, 0 la peor.
 * @param bool $crop Si se recorta la imagen quedándose con la parte central o no.
 */
function resizeImage($file, $w, $h, $quality=100, $crop=false) {
    list($width, $height) = getimagesize($file);
    $src = imagecreatefromjpeg($file);
    $dst = imagecreatetruecolor($w, $h);
    if ($crop) {
        $originalAspect = $width / $height;
        $finalAspect = $w / $h;
        if ( $originalAspect >= $finalAspect )  {
            $new_height = $h;
            $new_width = $width / ($height / $h);
        }
        else  {
            $new_width = $w;
            $new_height = $height / ($width / $w);
        }
        $x = 0 - ($new_width - $w) / 2;
        $y = 0 - ($new_height - $h) / 2;
        imagecopyresampled($dst, $src, $x, $y, 0, 0, $new_width, $new_height, $width, $height);
    }
    else {
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);
    }
    imagejpeg($dst, $file, $quality);
}

/**
 * Actualiza una imagen.
 * @param $pathImage
 * @param $angle
 * @param $w
 * @param $h
 * @param $x
 * @param $y
 * @param $scale
 * @return bool
 */
function updImage($pathImage, $angle, $w, $h, $x, $y, $scale) {
    $nimg = imagecreatetruecolor($w,$h);
    $im_src = convertToJpeg($pathImage,$pathImage);
    if (!$im_src) return false;
    imagealphablending($im_src, false);
    imagesavealpha($im_src, true);
    $im_src = imagerotate($im_src, $angle * -1, 0);
    imagecopyresampled($nimg, $im_src, 0, 0, ceil($x/$scale),     ceil($y/$scale),     $w, $h, $w/$scale, $h/$scale);
    return imagejpeg($nimg, $pathImage, 90);
}

/**
 * Obtiene los administradores
 * @return array
 */
function get_administrators() {
    $user = get_user();
    //TODO: implementar
    return array(
        array(
            'id' => 'alice',
            'name' => 'alice',
            'Nombre' => 'Alice'
        ),
        array(
            'id' => 'bob',
            'name' => 'bob',
            'Nombre' => 'Bob'
        ),
        array(
            'id' => 'charlie',
            'name' => 'charlie',
            'Nombre' => 'Charlie'
        ),
        array(
            'id' => 'trudis',
            'name' => 'trudis',
            'Nombre' => 'Trudis'
        ),
    );
}

/**
 * Obtiene un administrador.
 * @param $id
 * @return mixed
 */
function get_administrator($id) {
    $administradores = get_administrators();
    foreach($administradores as $administrador) {
        if($administrador['id'] == $id) return $administrador;
    }
}

function getImageUrl($filename) {
    $config = parse_ini_file(dirname(__FILE__).'/config.ini');
    return "/".$config['project']."/".$config['images_directory'].$filename;
}

function getThumbnailUrl($filename) {
    $config = parse_ini_file(dirname(__FILE__).'/config.ini');
    return "/".$config['project']."/".$config['thumbnails_directory'].$filename;
}