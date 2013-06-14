<?php
require_once 'svglib.php';


$qrcode = array (
    '111111100101101111111',
    '100000100111001000001',
    '101110101101101011101',
    '101110100101001011101',
    '101110100010101011101',
    '100000100000101000001',
    '111111101010101111111',
    '000000001101100000000',
    '111011111111011000100',
    '010010011000000011111',
    '100110100110110001111',
    '010010001100011001001',
    '011111110010110011111',
    '000000001101010101010',
    '111111101011000011011',
    '100000101001110111010',
    '101110101001010010011',
    '101110100001010101110',
    '101110101010100011001',
    '100000101011011010011',
    '111111101100101011001'
);

//$svg = SVGDocument::getInstance('qrcode/qrcode.svg'); //open to edit

$svg = SVGDocument::getInstance( ); //default read to use

$h = count($qrcode);
$w = strlen($qrcode[0]);


function filter(&$frame) {
    $h = count($frame);
    $w = strlen($frame[0]);

    $topMask = str_pad(str_repeat('1', $w - 14), $w, '0', STR_PAD_BOTH);
    $bottomMask = str_pad(str_repeat('1', $w - 7), $w, '0', STR_PAD_LEFT);

    for ($i = 0; $i < 7; $i++) {
        $frame[$i] &= $topMask;
    }

    for ($i = $h - 7; $i < $h; $i++) {
        $frame[$i] &= $bottomMask;
    }
}


function getLinearGradient($id) {
    $style1 = new SVGStyle();
    $style1->stopColor = '#F60';
    $stop1 = SVGStop::getInstance(null, $style1, 0.35);

    $style2 = new SVGStyle();
    $style2->stopColor = '#FF6';
    $stop2 = SVGStop::getInstance(null, $style2, 0.95);
    return SVGLinearGradient::getInstance($id, array ($stop1, $stop2));
}

filter($qrcode);

$pixelPerPoint = 20;
$outerFrame = 2;

$svg->setWidth(($w + 2 * $outerFrame) * $pixelPerPoint);
$svg->setHeight(($h + 2 * $outerFrame) * $pixelPerPoint);

$gradient = getLinearGradient('myGradient');
$svg->addDefs($gradient);

$style = new SVGStyle( );
$style->setFill($gradient);
$rect = SVGRect::getInstance(0, 0, null, $svg->getWidth(), $svg->getHeight(), $style);
$svg->addShape($rect);


for ($i = 0; $i < $h; $i++) {
    for ($j = 0; $j < $w; $j++) {
        $mark = $qrcode[$i][$j];
        if ($mark == '1') {
            $x = ($j + $outerFrame) * $pixelPerPoint;
            $y = ($i + $outerFrame) * $pixelPerPoint;
            $rect = SVGRect::getInstance($x, $y, null, $pixelPerPoint, $pixelPerPoint, 'fill:#000');
            // customize
            $rect->setHeight(18);
            $rect->setWidth(18);
            $rect->setRound(2);
            //$rect->rotate(-45, $x + 10, $y + 8);
            $svg->addShape($rect);
        }
    }
}

$eyeBorderData = 'M0 0 v140 h140 v-140 h-140Z M15 15 a300 300 0 0 0 110 0 a300 300 0 0 0 0 110 a300 300 0 0 0 -110 0 a300 300 0 0 0 0 -110Z';
$eyeCenterData = 'M40 40 a80 80 0 0 0 60 0 a80 80 0 0 0 0 60 a80 80 0 0 0 -60 0 a80 80 0 0 0 0 -60Z';

$eyes = array (
    'star' => array (
        'border' => 'M0 0 v140 h140 v-140 h-140Z M15 15 a300 300 0 0 0 110 0 a300 300 0 0 0 0 110 a300 300 0 0 0 -110 0 a300 300 0 0 0 0 -110Z',
        'center' => 'M40 40 a80 80 0 0 0 60 0 a80 80 0 0 0 0 60 a80 80 0 0 0 -60 0 a80 80 0 0 0 0 -60Z'
    ),
    'diamond' => array (
        'border' => 'M0 0 h110 l30 30 v110 h-110 l-30 -30 v-110Z M20 20 h82 l18 18 v82 h-82 l-18 -18 v-82Z',
        'center' => 'M40 40 h30 l30 30 v30 h-30 l-30 -30 v-30Z'
    ),
    'square' => array (
        'border' => 'M0 0 v140 h140 v-140 h-140Z M20 20 h100 v100 h-100 v-100Z',
        'center' => 'M40 40 h60 v60 h-60 v-60Z'
    ),
    'shield' => array (
        'border' => 'M0,10 a250,250 0 0 0 70,-10 a250,250 0 0 0 70,10 a180,180 0 0 0 0,120 a250,250 0 0 1 -140,0 a180,180 0 0 0 0,-120Z M20,25 a130,130 0 0 0 50,-10 a130,130 0 0 0 50,10 a180,180 0 0 0 0,95 a180,180 0 0 1 -100,0 a180,180 0 0 0 0,-95Z',
        'center' => 'M40,45 a95,95 0 0 0 30,-5 a95,95 0 0 0 30,5 a95,95 0 0 0 0,55 a95,95 0 0 1 -60,0 a95,95 0 0 0 0,-55Z'
    ),
);


function getEye($name) {
    global $eyes;
    $eyeBorder = SVGPath::getInstance($eyes[$name]['border'], null, 'fill:#000;fill-rule:evenodd');
    $eyeCenter = SVGPath::getInstance($eyes[$name]['center'], null, 'fill:#000');

    $eye = new SVGShape('<g></g>');
    $eye->append($eyeBorder);
    $eye->append($eyeCenter);
    return $eye;
}

$eye = getEye('diamond');

//left top
$ltEye = clone $eye;
$x = $outerFrame * $pixelPerPoint;
$y = $outerFrame * $pixelPerPoint;
$ltEye->setTransform("translate($x, $y)");
$svg->addShape($ltEye);

//right top
$rtEye = clone $eye;
$x = ($w - 7 + $outerFrame) * $pixelPerPoint;
$y = $outerFrame * $pixelPerPoint;
$rtEye->setTransform("translate($x, $y)");
$svg->addShape($rtEye);

//left bottom
$lbEye = clone $eye;
$x = $outerFrame * $pixelPerPoint;
$y = ($h - 7 + $outerFrame) * $pixelPerPoint;
$lbEye->setTransform("translate($x, $y)");
$svg->addShape($lbEye);

$img = SVGImage::getInstance(300, 300, null, 'qrcode/twitter.png');
$img->setStyle('opacity:.8;fill:transparent');
$img->setWidth($w * 0.2 * $pixelPerPoint);
$img->setHeight($h * 0.2 * $pixelPerPoint);
$svg->addShape($img);

$svg->asXML('output/output.svg'); //output to svg file
$svg->export('output/output.png');

echo 'Finished!';
?>