<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Avatar extends CI_Controller
{


    /**
     * Generates and outputs a square avatar image.
     *
     * The original code's font-sizing loop was complex and resulted in small text.
     * This revised version uses a simpler and more reliable method:
     * 1. Start with a large font size (e.g., 60% of the image size).
     * 2. Define a maximum boundary for the text (e.g., 80% of the image).
     * 3. Loop and decrease the font size until the text fits within that boundary.
     * This ensures the initials are consistently large and well-centered.
     *
     * @param string $name The full name to generate initials from.
     * @param int $size The width and height of the avatar in pixels.
     * @return void This function outputs a PNG image directly to the browser.
     */
    public function generate($name, $color = 'orange', $size = 128)
    {

        // --- 1. GET INITIALS ---
        $name = urldecode($name);
        $words = preg_split('/\s+/', trim($name));
        $initials = '';
        if (count($words) >= 2) {
            $initials = strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        } elseif (!empty($words[0])) {
            $initials = strtoupper(mb_substr($words[0], 0, 2));
        } else {
            $initials = '??';
        }

        // --- 2. CREATE IMAGE AND COLORS ---
        $img = imagecreatetruecolor($size, $size);

        // --- FIXED COLOR PALETTE ---
        $colorPalette = [
            'orange' => [245, 120, 41], // Updated to match the provided orange
            'blue'   => [52, 152, 219],
            'red'    => [231, 76, 60],
            'green'  => [46, 204, 113],
            'black'  => [44, 62, 80],
            'white'  => [236, 240, 241]
        ];
        // Allow user to define color
        $selectedColor = null;
        if ($color && isset($colorPalette[strtolower($color)])) {
            $selectedColor = $colorPalette[strtolower($color)];
        } else {
            // Pick color based on first letter
            $colorKeys = array_keys($colorPalette);
            $firstLetter = strtoupper($initials[0]);
            $colorIndex = (ord($firstLetter) - ord('A')) % count($colorKeys);
            $selectedColor = $colorPalette[$colorKeys[$colorIndex]];
        }
        $bg = imagecolorallocate($img, $selectedColor[0], $selectedColor[1], $selectedColor[2]);
        imagefill($img, 0, 0, $bg);

        // Text color (fixed to white as in your example)
        $textColor = imagecolorallocate($img, 255, 255, 255);

        // --- 3. ADD TEXT ---
        // IMPORTANT: Make sure this font file path is correct for your server.
        // For this example, we assume 'Arial.ttf' is in the same directory.
        $fontFile = __DIR__ . '/assets/fonts/Arial.ttf';

        // Try both absolute and relative paths for font file
        $fontFile = __DIR__ . '/../../assets/fonts/Arial.ttf';
        if (!file_exists($fontFile)) {
            $fontFile = realpath(__DIR__ . '/../../assets/fonts/Arial.ttf');
        }

        if (!file_exists($fontFile)) {
            // Fallback to a built-in font if Arial.ttf is not found.
            // This will be smaller and not as nice, but prevents an error.
            $fontSize = 3; // Built-in fonts are sized 1-5
            $textWidth = imagefontwidth($fontSize) * strlen($initials);
            $textHeight = imagefontheight($fontSize);
            $x = ($size - $textWidth) / 2;
            $y = ($size - $textHeight) / 2;
            imagestring($img, $fontSize, $x, $y, $initials, $textColor);
        } else {
            // --- REVISED FONT SIZING LOGIC ---

            // Start with a font size that is 60% of the image size
            $fontSize = $size * 0.4;
            
            // Define the maximum area the text can occupy (e.g., 80% of the image)
            $maxWidth = $size * 0.6;
            $maxHeight = $size * 0.6;

            // Loop and decrease the font size until the text fits
            do {
                $bbox = imagettfbbox($fontSize, 0, $fontFile, $initials);
                // If font file is invalid, bbox is false
                if ($bbox === false) break;

                $textWidth = abs($bbox[2] - $bbox[0]);
                $textHeight = abs($bbox[7] - $bbox[1]);

                // If the text is too big, decrease the font size and try again
                if ($textWidth > $maxWidth || $textHeight > $maxHeight) {
                    $fontSize--;
                } else {
                    // The text fits, so we can stop looping
                    break;
                }
            } while ($fontSize > 1);

            // Calculate the final X and Y coordinates to center the text
            $bbox = imagettfbbox($fontSize, 0, $fontFile, $initials);
            $textWidth = abs($bbox[2] - $bbox[0]);
            $textHeight = abs($bbox[7] - $bbox[1]);
            $x = ($size - $textWidth) / 2;
            $y = ($size + $textHeight) / 2;

            // Draw the text on the image
            imagettftext($img, $fontSize, 0, $x, $y, $textColor, $fontFile, $initials);
        }

        // --- 4. OUTPUT IMAGE ---
        header('Content-Type: image/png');
        imagepng($img);

        // Clean up the image resource
        imagedestroy($img);
    }



    /**
     * Helper function for hslToRgb.
     */
    private function hue2rgb($p, $q, $t)
    {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1 / 6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1 / 2) return $q;
        if ($t < 2 / 3) return $p + ($q - $p) * (2 / 3 - $t) * 6;
        return $p;
    }
}
