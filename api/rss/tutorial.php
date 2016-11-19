<?php
require_once("../includes/ini.php");

$language = $_GET['language'] ? $_GET['language'] : 'es';
$config = parse_ini_file('../includes/config.ini');
// $siteUrl=siteURL().$config['project'];
$siteurl='http://promotv.mundo-r.com/promotv/api';
$imageUrl = $siteurl."/".$config['images_directory']."/".$language."/".$config['tutorial_image'];
$imageUrls = siteURL().$config['project']."/".$config['images_directory']."/".$language."/".$config['advertising_image'];
$size = getimagesize($imageUrls);
$width = $size[0];
$height = $size[1];
?>

<rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
    <channel>
        <title></title>
        <description></description>
        <link>http://wayhoy.mobi</link>
        <item>
            <title></title>
            <content type="xhtml"></content>
            <description></description>
            <url>
                <?php echo $imageUrl; ?>
            </url>
            <image>
                <url>
                    <?php echo $imageUrl; ?>
                </url>
                <title></title>
                <link>http://wayhoy.mobi</link>
                <width><?php echo $width; ?></width>
                <height><?php echo $height; ?></height>
                <description></description>
            </image>
        </item>
    </channel>
</rss>