<?php
$qrcode = 'qrcode/qrcode.svg';
$oup = 'output/output.svg';
$im = new Imagick();
$svg = file_get_contents($oup);

$im->readImageBlob($svg);

/*png settings*/
$im->setImageFormat("png24");
$im->resizeImage(200, 200, Imagick::FILTER_LANCZOS, 1);  /*Optional, if you need to resize*/
//$im->adaptiveresizeimage(600, 600);

/*jpeg*/
//$im->setImageFormat("jpeg");
//$im->adaptiveResizeImage(720, 445); /*Optional, if you need to resize*/

$im->writeImage('qrcode/code.png');
$im->clear();
$im->destroy();

echo 'Finished!';
?>