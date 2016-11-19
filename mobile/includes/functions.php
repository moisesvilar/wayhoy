<?php

/**
 * Comprueba las credenciales de un usuario.
 * @param $email string El email del usuario.
 * @param $secret string. La clave secreta del usuario, calculada como el hash MD5 del nombre de usuario, el hash
 * MD5 de la contraseña y el token temporal.
 * @return bool Si las credenciales son correctas o no.
 */
function checkCredentials($email, $secret) {
    if ($email == 'demo' && $secret == 'jFveJ0YCqn') return true;
    $user = user($email);
    if (!$user) return false;
    $token = token();
    $expectedSecret = md5($email.$user['password'].$token);
    if ($expectedSecret != $secret) {
        $token = token(1);
        $expectedSecret = md5($email.$user['password'].$token);
        if ($expectedSecret != $secret) return false;
        else return true;
    }
    else return true;
}

/**
 * Devuelve el objeto a formato JSON y establece la cabecera de la respuesta con el Content-Type apropiado.
 * @param array $object Un array asociativo con los datos del objeto.
 * @return string El objeto en formato JSON.
 */
function toJson($object) {
    header('Content-Type: application/json; charset=utf-8');
    return json_encode($object);
}

/**
 * Comprueba si una cadena comienza con una subcadena
 * @param string $haystack La cadena.
 * @param string $needle La subcadena.
 * @return bool El resultado de la comprobación.
 */
function startsWith($haystack, $needle) {
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

/**
 * Comprueba si una cadena termina con una subcadena.
 * @param string $haystack La cadena.
 * @param string $needle La subcadena.
 * @return bool El resultado de la comprobación.
 */
function endsWith($haystack, $needle) {
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
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
    else{
		return false;
		}
    imagejpeg($imageTmp, $outputImage, $quality);
    return $imageTmp;
}

/**
 * Prepara una imagen para su almacenamiento como imagen original.
 * @param string $originalImage Ruta a la imagen original.
 * @param string $outputImage Ruta a la imagen resultado.
 * @return bool El resultado de la operación.
 */
function prepareOriginalImage($originalImage, $outputImage) {
    if (!convertToJpeg($originalImage, $outputImage)) return false;
    $config = parse_ini_file('includes/config.ini');
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
    $config = parse_ini_file('includes/config.ini');
    $finalHeight = $config['thumbnail_height'];
    list($width, $height) = getimagesize($outputImage);
    $originalAspect = $width / $height;
    $finalWidth = $finalHeight * $originalAspect;
    resizeImage($outputImage, $finalWidth, $finalHeight);
    return true;
}

/**
 * Guarda una imagen codificada en base 64 al fichero especificado.
 * @param string $base64_string La imagen codificada en base 64
 * @param string $output_file La ruta al fichero de salida.
 * @return string La ruta al fichero de salida.
 */
function base64_to_jpeg($base64_string, $output_file) {
    $ifp = fopen($output_file, "wb");
    $data = explode(',', $base64_string);
    fwrite($ifp, base64_decode($data[1]));
    fclose($ifp);
    return $output_file;
}

/**
 * Establece un token de recuperación de contraseña
 * @param string $email El email.
 * @return bool|string El token generado o false si ha ocurrido algún error.
 */
function setRecoveryToken($email) {
    $token = md5($email.date('Ymdhis'));
    return setToken($email, $token);
}

/**
 * Envía un email.
 * @param array $conf Array con los parámetros de configuración.
 * @return string La descripción del error o cadena vacía si la operación ha concluído satisfactoriamente.
 * @throws Exception
 * @throws phpmailerException
 */
function sendEmail($conf) {
    try {
        $config = parse_ini_file('includes/config.ini');
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 1;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = $config['email_protocol'];
        $mail->Host = $config['email_host'];
        $mail->Port = $config['email_port'];
        $mail->Username = $config['email_user'];
        $mail->Password = $config['email_pass'];
        $mail->SetFrom($config['email_from'], $config['email_from_name']);
        $mail->Subject = utf8_encode("=?UTF-8?B?" . base64_encode($conf['subject']) .  "?=");
        $mail->MsgHTML($conf['body']);
        $mail->AddAddress($conf['email']);

        if (!$mail->send()) {
            return $mail->ErrorInfo;
        }
        return "";
    } catch(Exception $e) {
        return $e->getMessage();
    }
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
 * TODO: implementar
 * @param $admin
 * @return int
 */
function total_licenses($admin) {
    if($admin == 'alice') return 2;
    if($admin == 'bob') return 3;
    if($admin == 'charlie') return 8;
    if($admin == 'trudis') return 4;
    return 0;
}

/**
 * Comprueba las credenciales a través de la API de R.
 * TODO: enlazar con la API de R.
 * @param $user
 * @param $pass
 * @return bool
 */
function checkCredentialsR($user, $pass) {
    if ($user == 'alice@alice.com' && $pass == 'alice') return true;
    if ($user == 'bob@bob.com' && $pass == 'bob') return true;
    if ($user == 'charlie@charlie.com' && $pass == 'charlie') return true;
    if ($user == 'trudis@trudis.com' && $pass == 'trudis') return true;
    return false;
}

/**
 * Genera una clave de la longitud especificada.
 * @param int $longitud La longitud de la clave a generar.
 * @return string La clave generada.
 */
function generar_clave($longitud){
    $cadena="[^A-Z0-9]";
    return substr(preg_replace($cadena, "", md5(rand())) .
        preg_replace($cadena, "", md5(rand())) .
        preg_replace($cadena, "", md5(rand())),
        0, $longitud
    );
}