<?php
class ColorConverter {
    
    /**
     * HEX'i RGB'ye çevirir
     */
    public function hexToRgb($hex) {
        $hex = str_replace('#', '', $hex);
        
        // 3 haneli hex'i 6 haneli yap
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        if (strlen($hex) != 6) {
            return false;
        }
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return [
            'r' => $r,
            'g' => $g,
            'b' => $b,
            'string' => "rgb($r, $g, $b)"
        ];
    }
    
    /**
     * RGB'yi HEX'e çevirir
     */
    public function rgbToHex($r, $g, $b) {
        $r = max(0, min(255, intval($r)));
        $g = max(0, min(255, intval($g)));
        $b = max(0, min(255, intval($b)));
        
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
    
    /**
     * HEX'i HSL'ye çevirir
     */
    public function hexToHsl($hex) {
        $rgb = $this->hexToRgb($hex);
        if (!$rgb) return false;
        
        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;
        
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $diff = $max - $min;
        
        // Lightness
        $l = ($max + $min) / 2;
        
        if ($diff == 0) {
            $h = $s = 0;
        } else {
            // Saturation
            $s = $l > 0.5 ? $diff / (2 - $max - $min) : $diff / ($max + $min);
            
            // Hue
            switch ($max) {
                case $r:
                    $h = (($g - $b) / $diff) + ($g < $b ? 6 : 0);
                    break;
                case $g:
                    $h = ($b - $r) / $diff + 2;
                    break;
                case $b:
                    $h = ($r - $g) / $diff + 4;
                    break;
            }
            $h /= 6;
        }
        
        return [
            'h' => round($h * 360),
            's' => round($s * 100),
            'l' => round($l * 100),
            'string' => sprintf("hsl(%d, %d%%, %d%%)", round($h * 360), round($s * 100), round($l * 100))
        ];
    }
    
    /**
     * Renk kontrastını hesaplar
     */
    public function getContrast($hex1, $hex2) {
        $lum1 = $this->getLuminance($hex1);
        $lum2 = $this->getLuminance($hex2);
        
        $brightest = max($lum1, $lum2);
        $darkest = min($lum1, $lum2);
        
        return ($brightest + 0.05) / ($darkest + 0.05);
    }
    
    /**
     * Rengin parlaklığını hesaplar
     */
    private function getLuminance($hex) {
        $rgb = $this->hexToRgb($hex);
        if (!$rgb) return 0;
        
        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;
        
        $r = $r <= 0.03928 ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g = $g <= 0.03928 ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b = $b <= 0.03928 ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);
        
        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }
    
    /**
     * HEX kodunu doğrular
     */
    public function validateHex($hex) {
        $hex = str_replace('#', '', $hex);
        return preg_match('/^[a-fA-F0-9]{3}$|^[a-fA-F0-9]{6}$/', $hex);
    }
    
    /**
     * Komplementer rengi bulur
     */
    public function getComplementary($hex) {
        $rgb = $this->hexToRgb($hex);
        if (!$rgb) return false;
        
        $comp_r = 255 - $rgb['r'];
        $comp_g = 255 - $rgb['g'];
        $comp_b = 255 - $rgb['b'];
        
        return $this->rgbToHex($comp_r, $comp_g, $comp_b);
    }

}

?>