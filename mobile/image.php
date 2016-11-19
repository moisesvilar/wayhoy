<?php
require_once("includes/ini.php");

global $method;
global $headers;
global $contentType;

$user = $headers['User'];
$albumGaleriaRid = 1;

/**
 * GET: listado de imágenes de la galería de R
 */
if ($method == 'GET' && isset($_GET['galleryr'])) {
    /**
     * 500: error de base de datos.
     */
    if (($imagenes = images(false, 'galeriar@wayhoy.com')) === false) {
        http_response_code(500);
        exit;
    }
    echo toJson($imagenes);
}
/**
 * GET: listado de imágenes
 */
elseif ($method == 'GET' && !isset($_GET['id']) && !isset($_GET['galleryr'])) {
    /**
     * 500: error de base de datos.
     */
    if (($imagenes = images(false, $user)) === false) {
        http_response_code(500);
        exit;
    }
    echo toJson($imagenes);
}
/**
 * GET: obtención de datos de una imagen
 */
elseif ($method == 'GET' && isset($_GET['id']) && !isset($_GET['album']) && !isset($_GET['galleryr'])) {
	$id = $_GET['id'];

    if($_GET['albumimage']) {
        /**
         * 404: imagen no encontrada.
         */
        $imageAlbumId = $id;
        if (($imagen = image(false, false, false, false, $imageAlbumId)) === false) {
            http_response_code(404);
            exit;
        }
        $id = $imagen['id'];
    }
    else {
        /**
         * 404: imagen no encontrada.
         */
        if (($imagen = image($id)) === false) {
            http_response_code(404);
            exit;
        }
    }

    /**
     * 403: la imagen no pertenece al usuario
     */
    if (!isYours('image', $id, $user)) {
        http_response_code(403);
        exit;
    }

    echo toJson($imagen);
}
/**
 * GET: obtención de datos de una imagen asociada a un album
 */
elseif ($method == 'GET' && isset($_GET['id']) && isset($_GET['album'])  && !isset($_GET['galleryr'])) {

    $id = $_GET['id'];

    /**
     * 404: no se ha encontrado la imagen.
     */
    if (($imagen = image_album($id)) === false) {
        http_response_code(405);
        exit;
    }

    /**
     * 403: la imagen no pertenece al usuario
     */
    if (!isYours('image', $id, $user)) {
        http_response_code(403);
        exit;
    }

    /**
     * 404: no se ha encontrado el album.
     */
    if (!album($_GET['album'])) {
        http_response_code(404);
        exit;
    }

    /**
     * 403: el album no pertenece al usuario
     */
    if (!isYours('album', $album, $user)) {
        http_response_code(403);
        exit;
    }
    echo toJson($imagen);
}
/**
 * POST (multipart/form-data): subida de imágenes a la galería.
 */
elseif ($method == 'POST' && !isset($_GET['album']) && startsWith($contentType, 'multipart/form-data')) {

    /**
     * 400: faltan parámetros obligatorios.
     */
    if (!$_FILES || !$_FILES['file'] || !$_FILES['file']['name']) {
        http_response_code(400);
        exit;
    }

    /**
     * 500: error interno
     */
    if ($_FILES['file']['error']) {
        http_response_code(500);
        exit;
    }

    $config = parse_ini_file('includes/config.ini');
    $images_directory = $config['images_directory'];
    $thumbnails_directory = $config['thumbnails_directory'];
    $filename = hash_file('md5', $_FILES['file']['tmp_name']).'.jpg';
	$filename = md5(time().$filename).".jpg";
    $url = siteURL().$config['project']."/".$images_directory.$filename;
    $url_thumbnail = siteURL().$config['project']."/".$thumbnails_directory.$filename;

    $resultOriginal = prepareOriginalImage($_FILES['file']['tmp_name'], $images_directory.$filename);
    $resultThumbnail = prepareThumbnailImage($_FILES['file']['tmp_name'], $thumbnails_directory.$filename);

    /**
     * 500: error al copiar el archivo
     */
    if (!$resultOriginal || !$resultThumbnail) {
        http_response_code(500);
        exit;
    }

    /**
     * Si la imagen ya existe, la devolvemos sin insertar en la base de datos.
     */
    if ($image = image(false, false, $filename, $user)) {
        echo toJson($image);
        exit;
    }

    /**
     * 500: error al insertar la imagen en la base de datos.
     */
    if (($image = image_in($filename, $url, $url_thumbnail, $user)) === false) {
        http_response_code(500);
        exit;
    }

    echo toJson($image);
}
/**
 * POST (multipart/form-data): subida de imágenes a un album.
 */
elseif ($method == 'POST' && isset($_GET['album']) && startsWith($contentType, 'multipart/form-data')) {
    /**
     * 400: faltan parámetros obligatorios.
     */
    if (!$_FILES || !$_FILES['file'] || !$_FILES['file']['name'] || !$_GET['album']) {
        http_response_code(400);
        exit;
    }

    /**
     * 500: error interno
     */
    if ($_FILES['file']['error']) {
        http_response_code(500);
        exit;
    }

    $album = $_GET['album'];

    /**
     * 404: el album no existe
     */
    if (!album($album)) {
        http_response_code(404);
        exit;
    }

    /**
     * 403: el album no pertenece al usuario
     */
    if (!isYours('album', $album, $user)) {
        http_response_code(403);
        exit;
    }

    $config = parse_ini_file('includes/config.ini');
    $images_directory = $config['images_directory'];
    $thumbnails_directory = $config['thumbnails_directory'];
    $filename = hash_file('md5', $_FILES['file']['tmp_name']).'.jpg';
	$filename = md5(time().$filename).'.jpg';
    $url = siteURL().$config['project']."/".$images_directory.$filename;
    $url_thumbnail = siteURL().$config['project']."/".$thumbnails_directory.$filename;

    $resultOriginal = prepareOriginalImage($_FILES['file']['tmp_name'], $images_directory.$filename);
    $resultThumbnail = prepareThumbnailImage($_FILES['file']['tmp_name'], $thumbnails_directory.$filename);

    /**
     * 500: error al copiar el archivo
     */
    if (!$resultOriginal || !$resultThumbnail) {
        http_response_code(500);
        exit;
    }

    /**
     * Si la imagen no existe, la insertamos en la base de datos
     */
    if (!($image = image(false, false, $filename, $user))) {
        /**
         * 500: error al insertar la imagen en la base de datos.
         */
        if (($image = image_in($filename, $url, $url_thumbnail, $user)) === false) {
            http_response_code(500);
            exit;
        }
    }

    /**
     * Si la imagen no está asociada al album, la asociamos
     */
    if (!image($image['id'], $album)) {
        /**
         * 500: error al insertar la imagen en la base de datos.
         */
        if (!$image == image_album_in($image['id'], $album)) {
            http_response_code(500);
            exit;
        }
    }
	
    echo toJson($image);
}
/**
 * POST (application/json): enlazar imágenes ya subidas a un álbum.
 */
elseif ($method == 'POST' && isset($_GET['user']) && isset($_GET['album']) && startsWith($contentType, 'application/json')) {
    /**
     * 400: faltan parámetros obligatorios
     */
    if (!isset($body['id'])) {
        http_response_code(400);
        exit;
    }

    $album = $_GET['album'];

    /**
     * 404: el album no existe
     */
    if (!album($album)) {
        http_response_code(404);
        exit;
    }

    /**
     * 403: el album no pertenece al usuario
     */
    if (!isYours('album', $album, $user)) {
        http_response_code(403);
        exit;
    }

    $id = $body['id'];

    /**
     * 404: la imagen no existe
     */
    if (!image($id)) {
        http_response_code(404);
        exit;
    }
    /**
     * 403: la imagen no pertenece al usuario
     */
    if (!isYours('image', $id, $user)) {
        http_response_code(403);
        exit;
    }

    /**
     * Si la imagen no está asociada al album, la asociamos
     */
    if (!image_album($id, $album)) {
        /**
         * 500: error al insertar la imagen en la base de datos.
         */
        if (!image_album_in($id, $album)) {
            http_response_code(500);
            exit;
        }
    }
	$imageAlbumId = $body['id'];
    $imageAlbum = image_album(false, false, $imageAlbumId);
    //$id = $imageAlbum['image_id'];
	 $idd = $imageAlbum['image_album_id'];
    $image = image_album(false, false, $idd);
    echo toJson($image);
}
/**
 * PUT: Edición de datos de una imagen de la galería
 */
elseif ($method == 'PUT' && !isset($_GET['album']) && isset($_GET['id'])) {
    /**
     * 400: faltan parámetros obligatorios
     */
    if (!isset($body['id'])) {
        http_response_code(400);
        exit;
    }

    $id = $body['id'];


	if (!isset($body['orden'])){
		/**
		 * 404: la imagen no existe
		 */
    	if (!image($id)) {
        	http_response_code(404);
        	exit;
    	}
		$image = image($id);
	
		/**
		 * 403: la imagen no pertenece al usuario
		 */
		if (!isYours('image', $id, $user)) {
			http_response_code(403);
			exit;
		}
	
		$scale = $body['scale'];
		$angle = $body['angle'];
		$x = $body['x'];
		$y = $body['y'];
		$w = $body['w'];
		$h = $body['h'];
	
		$config = parse_ini_file('includes/config.ini');
		$images_directory = $config['images_directory'];
		$thumbnails_directory = $config['thumbnails_directory'];
		$filename = $image['filename'];
		$pathImage = $images_directory.$filename;
		$pathThumbnail = $thumbnails_directory.$filename;
		if (!updImage($pathImage, $angle, $w, $h, $x, $y, $scale)) {
			http_response_code(500);
			exit;
		}

    	if (!prepareThumbnailImage($pathImage, $pathThumbnail)) {
       	 http_response_code(403);
       	 exit;
    	}

    	echo toJson($image);
	}else{
		$orden = $body['orden'];
		$imageAlbumId= $body['id'];
		$albumId=$body['album'];
		if (!$orden || !$imageAlbumId || !$albumId){
			http_response_code(400);
			 exit;
			}
		// cambiamos el nº de orden de la imagen elegida y actualizamos el resto
		   if (!ordenamelo($orden, $albumId, $imageAlbumId)){
			   http_response_code(500);
			 exit;
		   }else{
			//Salimos porque en alngular ya recargará el álbum con el orden adecuado
			 exit;    
			}	
		}
	}
/**
 * PUT: Edición de datos de una imagen pertenciente a un album
 */
elseif ($method == 'PUT' && isset($_GET['album'])) {
    /**
     * 400: faltan parámetros obligatorios
     */
    if (!isset($body['id'])) {
        http_response_code(400);
        exit;
    }

    $album = $_GET['album'];

    /**
     * 404: el album no existe
     */
    if (!album($album)) {
        http_response_code(404);
        exit;
    }

    /**
     * 403: el album no pertenece al usuario
     */
    if (!isYours('album', $album, $user)) {
        http_response_code(403);
        exit;
    }

    $imageAlbumId = $body['id'];
    $imageAlbum = image_album(false, false, $imageAlbumId);
    $id = $imageAlbum['image_id'];
	$idd = $imageAlbum['image_album_id'];
    /**
     * 404: la imagen no existe
     */
    if (!image($id)) {
        if (!image_album($idd)) {
        http_response_code(404);
        exit;
    }
    }
	
    /**
     * 403: la imagen no pertenece al usuario
     */
    if (!isYours('image', $id, $user)) {
        http_response_code(403);
        exit;
    }

    /**
     * Si la imagen no está enlazada, la enlazamos
     */
 
    /**
     * 500: error al actualizar la imagen
     */
    if (!image_album_up($imageAlbumId, $body['title'], $body['description'], $body['subdescription'])) {
        http_response_code(500);
        exit;
    }

    $image = image(false, false, $imageAlbumId);
    echo toJson($image);
}
/**
 * DELETE: desasociar una imagen de un álbum.
 */
elseif ($method == 'DELETE' && isset($_GET['album']) && isset($_GET['id'])) {
    $album = $_GET['album'];

    /**
     * 404: el album no existe
     */
    if (!album($album)) {
        http_response_code(404);
        exit;
    }

    /**
     * 403: el album no pertenece al usuario
     */
    if (!isYours('album', $album, $user)) {
        http_response_code(403);
        exit;
    }
		
    $imageAlbumId = $_GET['id'];
    $imageAlbum = image_album(false, false, $imageAlbumId);
    $id = $imageAlbum['image_id'];
	$orden = $imageAlbum['posicion'];
    /**
     * 404: la imagen no existe
     */
    if (!image($id)) {
        http_response_code(404);
        exit;
    }

    /**
     * 403: la imagen no pertenece al usuario
     */
    if (!isYours('image', $id, $user)) {
        http_response_code(403);
        exit;
    }

    /**
     * 500: error al actualizar la imagen
     */
    if (!image_album_de(false, false, $imageAlbumId)) {
        http_response_code(500);
        exit;
    }
	if (!updateorderFromelim($orden, $album)) {
        http_response_code(505);
        exit;
    }
	exit;
	
}
/**
 * DELETE: eliminar una imagen
 */
elseif ($method == 'DELETE' && !isset($_GET['album']) && isset($_GET['id'])) {
	
    $id = $_GET['id'];

    /**
     * 404: la imagen no existe
     */
    if (!image($id)) {
        http_response_code(404);
        exit;
    }

    /**
     * 403: la imagen no pertenece al usuario
     */
    if (!isYours('image', $id, $user)) {
        http_response_code(403);
        exit;
    }

    $image = image($id);

    /**
     * Si la imagen a eliminar sólo la posee este usuario, se borra también de disco.
     */
    if ($count = count(images(false, false, $image['filename'], true)) == 1) {
        $config = parse_ini_file('includes/config.ini');
        $images_directory = $config['images_directory'];
        $thumbnails_directory = $config['thumbnails_directory'];
        $filename = $image['filename'];
        unlink($images_directory.$filename);
        unlink($thumbnails_directory.$filename);
    }
	/**
     * eliminamos de image_album en lugar de image para poder reestablecer el orden
     */
	getAlbumyOrdenFromelimGen($id);
    /**
     * 500: error al eliminar la imagen de la base de datos
     */
    if (!image_de($id)) {
        http_response_code(500);
        exit;
    }
}
else {
    http_response_code(405);
    exit;
}