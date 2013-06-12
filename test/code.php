<?php
require_once 'qrlib.php';

QRCode::png('PHP QRCode', 'c.png', 'H', 10, 5, false, 0xFFFFFF, 0x000000, array ('eye_pattern' => 8));


echo 'Hello';
