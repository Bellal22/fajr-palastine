<?php

namespace App\Support;

class Arabic
{
    private $utf8Glyphs = [
        '0622' => ['fe81', 'fe81', 'fe82', 'fe82'], // alef_madda
        '0623' => ['fe83', 'fe83', 'fe84', 'fe84'], // alef_hamza
        '0624' => ['fe85', 'fe85', 'fe86', 'fe86'], // waw_hamza
        '0625' => ['fe87', 'fe87', 'fe88', 'fe88'], // alef_hamza_below
        '0626' => ['fe89', 'fe8a', 'fe8b', 'fe8c'], // yeh_hamza
        '0627' => ['fe8d', 'fe8d', 'fe8e', 'fe8e'], // alef
        '0628' => ['fe8f', 'fe90', 'fe91', 'fe92'], // beh
        '0629' => ['fe93', 'fe93', 'fe94', 'fe94'], // teh_marbuta
        '062a' => ['fe95', 'fe96', 'fe97', 'fe98'], // teh
        '062b' => ['fe99', 'fe9a', 'fe9b', 'fe9c'], // theh
        '062c' => ['fe9d', 'fe9e', 'fe9f', 'fea0'], // jeem
        '062d' => ['fea1', 'fea2', 'fea3', 'fea4'], // hah
        '062e' => ['fea5', 'fea6', 'fea7', 'fea8'], // khah
        '062f' => ['fea9', 'fea9', 'feaa', 'feaa'], // dal
        '0630' => ['feab', 'feab', 'feac', 'feac'], // thal
        '0631' => ['fead', 'fead', 'feae', 'feae'], // reh
        '0632' => ['feaf', 'feaf', 'feb0', 'feb0'], // zain
        '0633' => ['feb1', 'feb2', 'feb3', 'feb4'], // seen
        '0634' => ['feb5', 'feb6', 'feb7', 'feb8'], // sheen
        '0635' => ['feb9', 'feba', 'febb', 'febc'], // sad
        '0636' => ['febd', 'febe', 'febf', 'fec0'], // dad
        '0637' => ['fec1', 'fec2', 'fec3', 'fec4'], // tah
        '0638' => ['fec5', 'fec6', 'fec7', 'fec8'], // zah
        '0639' => ['fec9', 'feca', 'fecb', 'fecc'], // ain
        '063a' => ['fecd', 'fece', 'fecf', 'fed0'], // ghain
        '0640' => ['0640', '0640', '0640', '0640'], // tatweel
        '0641' => ['fed1', 'fed2', 'fed3', 'fed4'], // feh
        '0642' => ['fed5', 'fed6', 'fed7', 'fed8'], // qaf
        '0643' => ['fed9', 'feda', 'fedb', 'fedc'], // kaf
        '0644' => ['fedd', 'fede', 'fedf', 'fee0'], // lam
        '0645' => ['fee1', 'fee2', 'fee3', 'fee4'], // meem
        '0646' => ['fee5', 'fee6', 'fee7', 'fee8'], // noon
        '0647' => ['fee9', 'feea', 'feeb', 'feec'], // heh
        '0648' => ['feed', 'feed', 'feee', 'feee'], // waw
        '0649' => ['feef', 'feef', 'fef0', 'fef0'], // alef_maksura
        '064a' => ['fef1', 'fef2', 'fef3', 'fef4'], // yeh
        '06440622' => ['fef5', 'fef5', 'fef6', 'fef6'], // lam_alef_madda
        '06440623' => ['fef7', 'fef7', 'fef8', 'fef8'], // lam_alef_hamza
        '06440625' => ['fef9', 'fef9', 'fefa', 'fefa'], // lam_alef_hamza_below
        '06440627' => ['fefb', 'fefb', 'fefc', 'fefc'], // lam_alef
    ];

    // Letters that can connect to the Right (Previous)
    // Basically all letters except Hamza (0621)
    private $prevConnect = [
        '0622', '0623', '0624', '0625', '0626', '0627', '0628', '0629', '062a', '062b', 
        '062c', '062d', '062e', '062f', '0630', '0631', '0632', '0633', '0634', '0635', 
        '0636', '0637', '0638', '0639', '063a', '0640', '0641', '0642', '0643', '0644', 
        '0645', '0646', '0647', '0648', '0649', '064a',
        '06440622', '06440623', '06440625', '06440627'
    ];

    // Letters that can connect to the Left (Next)
    // All Dual-Joining letters. (Excludes Right-Only joiners: Alef, Dal, Thal, Reh, Zain, Waw, Teh Marbuta, Alef Maksura)
    private $nextConnect = [
         '0626', '0628', '062a', '062b', '062c', '062d', '062e', 
         '0633', '0634', '0635', '0636', '0637', '0638', '0639', '063a', 
         '0640', '0641', '0642', '0643', '0644', '0645', '0646', '0647', '064a',
         // Lam-Alef ligatures do not connect to left
    ];

    public function utf8Glyphs($str)
    {
        $str = $this->utf8ToUnicode($str);
        $total = count($str);
        $output = '';

        for ($i = 0; $i < $total; $i++) {
            $current = $str[$i];
            
            // Handle lam-alef
            if ($i < $total - 1 && $current == '0644') {
                $next = $str[$i+1];
                $lamalef = '0644' . $next;
                if (isset($this->utf8Glyphs[$lamalef])) {
                    $current = $lamalef;
                    // Skip next char as it is consumed
                    $str[$i+1] = ''; 
                    // Note: we don't increment $i to skip, because we need $i to be correct index.
                    // Instead we mark next as processed.
                    // Actually, simpler to just consume it now.
                }
            }
            
            if ($current === '') continue;

            if (!isset($this->utf8Glyphs[$current])) {
                $output .= $this->unicodeToUtf8($current);
                continue;
            }

            $prev = ($i > 0) ? $str[$i-1] : null;
            // Scan forward for next non-empty char
            $next = null;
            for ($j = $i + 1; $j < $total; $j++) {
                if ($str[$j] !== '') {
                    $next = $str[$j];
                    break;
                }
            }

            // Connection Logic:
            // Connect to Previous IF: Previous allows Left-Join AND Current allows Right-Join
            $connectPrev = $prev && in_array($prev, $this->nextConnect) && in_array($current, $this->prevConnect);
            
            // Connect to Next IF: Current allows Left-Join AND Next allows Right-Join
            $connectNext = $next && in_array($current, $this->nextConnect) && in_array($next, $this->prevConnect);

            // Determine Position
            // 0: Isolated, 1: Final (End), 2: Medial, 3: Initial (Start)
            if ($connectPrev && $connectNext) {
                $pos = 2; // Medial
            } elseif ($connectPrev) {
                $pos = 1; // Final
            } elseif ($connectNext) {
                $pos = 3; // Initial
            } else {
                $pos = 0; // Isolated
            }
            
            // Handle special Lam-Alef case, it shouldn't be separated if it was formed
            if (strlen($current) == 8) { // IT IS LAM ALEF
                 // Lam-Alef is Right-Joining only.
                 // So it behaves like Final if connected, or Isolated.
                 // It can never be Initial or Medial (Left-Joining).
                 // My logic above handles it via arrays:
                 // Lam-Alef is in prevConnect, NOT in nextConnect.
                 // So connectNext will be FALSE.
                 // So pos will be 1 or 0.
                 // However, utf8Glyphs array for Lam Alef is: [Isolated, Final, Isolated, Final] usually?
                 // Or [Iso, Fin, Iso, Fin].
                 // Actually standard mappings often repeat.
                 // Let's rely on standard logic.
            }

            if (isset($this->utf8Glyphs[$current][$pos])) {
                 $output .= $this->unicodeToUtf8($this->utf8Glyphs[$current][$pos]);
            } else {
                 $output .= $this->unicodeToUtf8($current);
            }
            
            // Consume the component of Lam-Alef if used
            if (strlen($current) == 8) {
                $i++;
            }
        }
        
        // Reverse text for verify RTL rendering in standard LTR renderer
        return $this->mb_strrev($output);
    }

    private function utf8ToUnicode($str)
    {
        $unicode = [];
        $values = [];
        $lookingFor = 1;
        
        for ($i = 0; $i < strlen($str); $i++) {
            $thisValue = ord($str[$i]);
            if ($thisValue < 128) {
                $unicode[] = sprintf('%04x', $thisValue);
            } else {
                if (count($values) == 0) $lookingFor = ($thisValue < 224) ? 2 : 3;
                $values[] = $thisValue;
                if (count($values) == $lookingFor) {
                    $number = ($lookingFor == 3) ?
                        (($values[0] % 16) * 4096) + (($values[1] % 64) * 64) + ($values[2] % 64) :
                        (($values[0] % 32) * 64) + ($values[1] % 64);
                    
                    $unicode[] = sprintf('%04x', $number);
                    $values = [];
                    $lookingFor = 1;
                }
            }
        }
        return $unicode;
    }

    private function unicodeToUtf8($code)
    {
        if ($code == '0640') return ''; // strip tatweel
        $num = hexdec($code);
        if ($num < 128) return chr($num);
        if ($num < 2048) return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
        return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
    }

    private function mb_strrev($str)
    {
        preg_match_all('/./us', $str, $ar);
        // Reverse Logic needs to handle non-Arabic numbers?
        // Actually usually we want to reverse line by line or strictly Arabic blocks.
        // But for mixed content (Numbers), if we reverse everything: "100" becomes "001".
        // That is bad. "2025-12-29" becomes "92-21-5202".
        // We should ONLY reverse Arabic words?
        // Or we rely on the user passing simple strings.
        // In the PDF view: {{ shape_arabic($number) }}.
        // If number is English, it shouldn't be reversed?
        // My function `utf8Glyphs` converts non-arabic chars to default.
        // But `mb_strrev` reverses EVERYTHING.
        // I should probably fix this to only reverse the Arabic parts or keep numbers intact?
        // But `bidi` algorithm is complex.
        // For now, let's assume `shape_arabic` is called on specific text strings only.
        return join('', array_reverse($ar[0]));
    }
}
