<?php
/*
 * PHP QR Code encoder
 *
 * Image output of code using GD2
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

    define('QR_IMAGE', true);

    class QRimage {

        //----------------------------------------------------------------------
        public static function png($frame, $filename = false, $pixelPerPoint = 4, $outerFrame = 4,$saveandprint=FALSE, $back_color, $fore_color, $styles)
        {
            $image = self::image($frame, $pixelPerPoint, $outerFrame, $back_color, $fore_color, $styles);

            if ($filename === false) {
                Header("Content-type: image/png");
                ImagePng($image);
            } else {
                if($saveandprint===TRUE){
                    ImagePng($image, $filename);
                    header("Content-type: image/png");
                    ImagePng($image);
                }else{
                    ImagePng($image, $filename);
                }
            }

            ImageDestroy($image);
        }

        //----------------------------------------------------------------------
        public static function jpg($frame, $filename = false, $pixelPerPoint = 8, $outerFrame = 4, $q = 85)
        {
            $image = self::image($frame, $pixelPerPoint, $outerFrame, $back_color, $fore_color);

            if ($filename === false) {
                Header("Content-type: image/jpeg");
                ImageJpeg($image, null, $q);
            } else {
                ImageJpeg($image, $filename, $q);
            }

            ImageDestroy($image);
        }

        //----------------------------------------------------------------------
        private static function image($frame, $pixelPerPoint = 4, $outerFrame = 4, $back_color = 0xFFFFFF, $fore_color = 0x000000, $styles = array ())
        {
            $h = count($frame);
            $w = strlen($frame[0]);

            $imgW = $w + 2*$outerFrame;
            $imgH = $h + 2*$outerFrame;

            $base_image =ImageCreate($imgW, $imgH);

            // convert a hexadecimal color code into decimal eps format (green = 0 1 0, blue = 0 0 1, ...)
            $r1 = round((($fore_color & 0xFF0000) >> 16), 5);
            $g1 = round((($fore_color & 0x00FF00) >> 8), 5);
            $b1 = round(($fore_color & 0x0000FF), 5);

            // convert a hexadecimal color code into decimal eps format (green = 0 1 0, blue = 0 0 1, ...)
            $r2 = round((($back_color & 0xFF0000) >> 16), 5);
            $g2 = round((($back_color & 0x00FF00) >> 8), 5);
            $b2 = round(($back_color & 0x0000FF), 5);

            $col[0] = ImageColorAllocate($base_image, $r2, $g2, $b2); // background color
            $col[1] = ImageColorAllocate($base_image, $r1, $g1, $b1); // foreground color

            imagefill($base_image, 0, 0, $col[0]);

            for($y=0; $y<$h; $y++) {
                for($x=0; $x<$w; $x++) {
                    if ($frame[$y][$x] == '1') {
                        ImageSetPixel($base_image,$x+$outerFrame,$y+$outerFrame,$col[1]);
                    }
                }
            }

            $target_image =imagecreatetruecolor($imgW * $pixelPerPoint, $imgH * $pixelPerPoint);
            imagealphablending($target_image, true);
            ImageCopyResized($target_image, $base_image, 0, 0, 0, 0, $imgW * $pixelPerPoint, $imgH * $pixelPerPoint, $imgW, $imgH);

            // apply styles
            if (is_array($styles) && !empty($styles)) {
                $eyePattern = $styles['eye_pattern'];
                $patternFile = "../patterns/pattern-{$eyePattern}.png";

                if (file_exists($patternFile)) {
                    $posPattern = imagecreatefrompng($patternFile);
                    $patternW = imagesx($posPattern);
                    $patternH = imagesy($posPattern);
                    $posW = $posH = 7 * $pixelPerPoint;

                    $pattern = imagecreatetruecolor($posW, $posH);
                    $col[0] = ImageColorAllocate($pattern, $r2, $g2, $b2); // background color
                    $col[1] = ImageColorAllocate($pattern, $r1, $g1, $b1); // foreground color
                    imagefill($pattern, 0, 0, $col[0]);
                    imagecopyresized($pattern, $posPattern, 0, 0, 0, 0, $posW, $posH, $patternW, $patternH);

                    imagecopyresized($target_image, $pattern, $outerFrame * $pixelPerPoint, $outerFrame * $pixelPerPoint, 0, 0, $posW, $posH, $posW, $posH);
                    imagecopyresized($target_image, $pattern, ($w + $outerFrame) * $pixelPerPoint - $posW, $outerFrame * $pixelPerPoint, 0, 0, $posW, $posH, $posW, $posH);
                    imagecopyresized($target_image, $pattern, $outerFrame * $pixelPerPoint, ($h + $outerFrame) * $pixelPerPoint - $posH, 0, 0, $posW, $posH, $posW, $posH);

                    imagedestroy($pattern);
                    imagedestroy($posPattern);
                }
            }

            ImageDestroy($base_image);

            return $target_image;
        }
    }