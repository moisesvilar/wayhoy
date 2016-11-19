<?php
require_once("../includes/ini.php");
if(!isset($_GET['album'])) return false;
$albumId = $_GET['album'];
if(!isset($_GET['cod'])){
	http_response_code(404);
    	exit;
	}else{
	$code=db_escape($code);	
}
$config = parse_ini_file('../includes/config.ini');
$q='select code from screen where code="' . $code . '"';
	if(!$result = db_query($q)){
		http_response_code(404);
    	exit;
	}
$images = images($albumId);
echo toJson($images);
exit;
?>
<?PHP if (isset($rtrtr)){ ?>
<rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
    <channel>
        <title>WayHoy Álbum</title>
        <description>Imágenes de álbum del usuario</description>
        <link>http://wayhoy.mobi</link>
        <?php
        foreach($images as $image) {
            $size = getimagesize($image['url']);
            $width = $size[0];
            $height = $size[1];
			$imagen=str_replace('http://wayhoy.tv','http://promotv.mundo-r.com/promotv',$image['url']);
			//$imagen=$image['url'];
        ?>
            <item>
                <title><?php echo $image['title']; ?></title>
                <content type="xhtml"><?php echo $image['description']; ?></content>
                <description><?php echo $image['subdescription']; ?></description>
                <url>
                    <?php echo $imagen; ?>
                </url>
                <image>
                    <url>
                        <?php echo $imagen; ?>
                    </url>
                    <title><?php echo $image['title']; ?></title>
                    <link>http://wayhoy.mobi</link>
                    <width><?php echo $width; ?></width>
                    <height><?php echo $height; ?></height>
                    <description><?php echo $image['subdescription']; ?></description>
                </image>
            </item>
        <?php
        }
        ?>
    </channel>
</rss>
<?PHP } ?>