<?php

namespace App\Support;

class BarcodeGenerator
{
    /**
     * Generate a Code 39 barcode as PNG binary data.
     *
     * @param string $text
     * @param int $height
     * @param int $scale
     * @return string (raw PNG image data)
     */
    public static function generateCode39PNG($text, $height = 80, $scale = 2)
    {
        // Code 39 characters map (0 = narrow, 1 = wide)
        $encoding = [
            '0' => '000110100', '1' => '100100001', '2' => '001100001', '3' => '101100000',
            '4' => '000110001', '5' => '100110000', '6' => '001110000', '7' => '000100101',
            '8' => '100100100', '9' => '001100100', 'A' => '100001001', 'B' => '001001001',
            'C' => '101001000', 'D' => '000011001', 'E' => '100011000', 'F' => '001011000',
            'G' => '000001101', 'H' => '100001100', 'I' => '001001100', 'J' => '000011100',
            'K' => '100000011', 'L' => '001000011', 'M' => '101000010', 'N' => '000010011',
            'O' => '100010010', 'P' => '001010010', 'Q' => '000000111', 'R' => '100000110',
            'S' => '001000110', 'T' => '000010110', 'U' => '110000001', 'V' => '011000001',
            'W' => '111000000', 'X' => '010010001', 'Y' => '110010000', 'Z' => '011010000',
            '-' => '010000101', '.' => '110000100', ' ' => '011000100', '*' => '010010100',
            '$' => '010101000', '/' => '010100010', '+' => '010001010', '%' => '000101010'
        ];

        // Format input: upper case, starts and ends with *
        $text = strtoupper(trim($text));
        if ($text[0] !== '*') $text = '*' . $text;
        if ($text[strlen($text) - 1] !== '*') $text = $text . '*';

        $bars = '';
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            if (!isset($encoding[$char])) {
                continue; // Skip invalid characters
            }
            $pattern = $encoding[$char];
            
            // Draw 9 bars for character (5 black, 4 white)
            // Wide bar = 3 narrow, Narrow bar = 1 narrow
            for ($j = 0; $j < 9; $j++) {
                $barType = ($j % 2 === 0) ? '1' : '0'; // 1 = black, 0 = white/space
                $widthMultiplier = ($pattern[$j] === '1') ? 3 : 1;
                $bars .= str_repeat($barType, $widthMultiplier);
            }
            // Add a narrow inter-character gap (white)
            $bars .= '0';
        }

        $totalWidth = strlen($bars) * $scale;
        $imgWidth = $totalWidth + (20 * $scale); // Include margins
        $imgHeight = $height + (30 * $scale);

        $im = imagecreate($imgWidth, $imgHeight);
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);

        // Fill background
        imagefilledrectangle($im, 0, 0, $imgWidth, $imgHeight, $white);

        // Draw bars
        $x = 10 * $scale;
        for ($i = 0; $i < strlen($bars); $i++) {
            $color = ($bars[$i] === '1') ? $black : $white;
            imagefilledrectangle($im, $x, 10 * $scale, $x + $scale - 1, (10 * $scale) + $height, $color);
            $x += $scale;
        }

        // Draw text label below
        $cleanText = str_replace('*', '', $text);
        // Use standard font 4
        $font = 4;
        $fontWidth = imagefontwidth($font);
        $textX = ($imgWidth - (strlen($cleanText) * $fontWidth)) / 2;
        $textY = (10 * $scale) + $height + 5;
        imagestring($im, $font, $textX, $textY, $cleanText, $black);

        ob_start();
        imagepng($im);
        $imageData = ob_get_clean();
        imagedestroy($im);

        return $imageData;
    }
}
