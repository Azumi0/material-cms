<?php

namespace materialhelpers;

use Yii;
use app\models\GPhotosTable;

class Convert
{

    public static function removePL($str, $char = '-')
    {
        $str = strip_tags(str_replace('<br />', $char, $str));

        $foreign_characters = array(
            '/ä|æ|ǽ/' => 'ae',
            '/ö|œ/' => 'oe',
            '/ü/' => 'ue',
            '/Ä/' => 'Ae',
            '/Ü/' => 'Ue',
            '/Ö/' => 'Oe',
            '/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|Α|Ά|Ả|Ạ|Ầ|Ẫ|Ẩ|Ậ|Ằ|Ắ|Ẵ|Ẳ|Ặ|А/' => 'A',
            '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª|α|ά|ả|ạ|ầ|ấ|ẫ|ẩ|ậ|ằ|ắ|ẵ|ẳ|ặ|а/' => 'a',
            '/Б/' => 'B',
            '/б/' => 'b',
            '/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
            '/ç|ć|ĉ|ċ|č/' => 'c',
            '/Д/' => 'D',
            '/д/' => 'd',
            '/Ð|Ď|Đ|Δ/' => 'Dj',
            '/ð|ď|đ|δ/' => 'dj',
            '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Ε|Έ|Ẽ|Ẻ|Ẹ|Ề|Ế|Ễ|Ể|Ệ|Е|Э/' => 'E',
            '/è|é|ê|ë|ē|ĕ|ė|ę|ě|έ|ε|ẽ|ẻ|ẹ|ề|ế|ễ|ể|ệ|е|э/' => 'e',
            '/Ф/' => 'F',
            '/ф/' => 'f',
            '/Ĝ|Ğ|Ġ|Ģ|Γ|Г|Ґ/' => 'G',
            '/ĝ|ğ|ġ|ģ|γ|г|ґ/' => 'g',
            '/Ĥ|Ħ/' => 'H',
            '/ĥ|ħ/' => 'h',
            '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|Η|Ή|Ί|Ι|Ϊ|Ỉ|Ị|И|Ы/' => 'I',
            '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|η|ή|ί|ι|ϊ|ỉ|ị|и|ы|ї/' => 'i',
            '/Ĵ/' => 'J',
            '/ĵ/' => 'j',
            '/Ķ|Κ|К/' => 'K',
            '/ķ|κ|к/' => 'k',
            '/Ĺ|Ļ|Ľ|Ŀ|Ł|Λ|Л/' => 'L',
            '/ĺ|ļ|ľ|ŀ|ł|λ|л/' => 'l',
            '/М/' => 'M',
            '/м/' => 'm',
            '/Ñ|Ń|Ņ|Ň|Ν|Н/' => 'N',
            '/ñ|ń|ņ|ň|ŉ|ν|н/' => 'n',
            '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|Ο|Ό|Ω|Ώ|Ỏ|Ọ|Ồ|Ố|Ỗ|Ổ|Ộ|Ờ|Ớ|Ỡ|Ở|Ợ|О/' => 'O',
            '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|ο|ό|ω|ώ|ỏ|ọ|ồ|ố|ỗ|ổ|ộ|ờ|ớ|ỡ|ở|ợ|о/' => 'o',
            '/П/' => 'P',
            '/п/' => 'p',
            '/Ŕ|Ŗ|Ř|Ρ|Р/' => 'R',
            '/ŕ|ŗ|ř|ρ|р/' => 'r',
            '/Ś|Ŝ|Ş|Ș|Š|Σ|С/' => 'S',
            '/ś|ŝ|ş|ș|š|ſ|σ|ς|с/' => 's',
            '/Ț|Ţ|Ť|Ŧ|τ|Т/' => 'T',
            '/ț|ţ|ť|ŧ|т/' => 't',
            '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|Ũ|Ủ|Ụ|Ừ|Ứ|Ữ|Ử|Ự|У/' => 'U',
            '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|υ|ύ|ϋ|ủ|ụ|ừ|ứ|ữ|ử|ự|у/' => 'u',
            '/Ý|Ÿ|Ŷ|Υ|Ύ|Ϋ|Ỳ|Ỹ|Ỷ|Ỵ|Й/' => 'Y',
            '/ý|ÿ|ŷ|ỳ|ỹ|ỷ|ỵ|й/' => 'y',
            '/В/' => 'V',
            '/в/' => 'v',
            '/Ŵ/' => 'W',
            '/ŵ/' => 'w',
            '/Ź|Ż|Ž|Ζ|З/' => 'Z',
            '/ź|ż|ž|ζ|з/' => 'z',
            '/Æ|Ǽ/' => 'AE',
            '/ß/' => 'ss',
            '/Ĳ/' => 'IJ',
            '/ĳ/' => 'ij',
            '/Œ/' => 'OE',
            '/ƒ/' => 'f',
            '/ξ/' => 'ks',
            '/π/' => 'p',
            '/β/' => 'v',
            '/μ/' => 'm',
            '/ψ/' => 'ps',
            '/Ё/' => 'Yo',
            '/ё/' => 'yo',
            '/Є/' => 'Ye',
            '/є/' => 'ye',
            '/Ї/' => 'Yi',
            '/Ж/' => 'Zh',
            '/ж/' => 'zh',
            '/Х/' => 'Kh',
            '/х/' => 'kh',
            '/Ц/' => 'Ts',
            '/ц/' => 'ts',
            '/Ч/' => 'Ch',
            '/ч/' => 'ch',
            '/Ш/' => 'Sh',
            '/ш/' => 'sh',
            '/Щ/' => 'Shch',
            '/щ/' => 'shch',
            '/Ъ|ъ|Ь|ь/' => '',
            '/Ю/' => 'Yu',
            '/ю/' => 'yu',
            '/Я/' => 'Ya',
            '/я/' => 'ya'
        );

        $array_from = array_keys($foreign_characters);
        $array_to = array_values($foreign_characters);
        $str = preg_replace($array_from, $array_to, $str);

        $str = mb_convert_case($str, MB_CASE_LOWER, 'utf-8');
        //$search = array("ę", "ó", "ą", "ś", "ł", "ż", "ź", "ć", "ń");
        //$change = array("e", "o", "a", "s", "l", "z", "z", "c", "n");
        //$str = str_ireplace($search, $change, $str);

        $str = self::replaceSpecial($str, $char);
        return $str;
    }

    public static function replaceSpecial($str, $char = '-')
    {
        $str = preg_replace("/[^a-zA-Z0-9_]/", " ", $str);
        $str = trim($str);
        $str = str_replace(" ", $char, $str);
        while (preg_match("/$char$char/", $str)) {
            $str = str_replace("$char$char", "$char", $str);
        }
        return $str;
    }

    public static function number_to_words($number) {

        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'fourty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . self::number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $tmp = floor($hundreds);
                $string = $dictionary[$tmp] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . self::number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = self::number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::number_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }

    public static function shuffle_assoc($list)
    {
        if (!is_array($list)) return $list;

        $keys = array_keys($list);
        shuffle($keys);
        $random = array();
        foreach ($keys as $key) {
            $random[$key] = $list[$key];
        }

        return $random;
    }

    public static function toLower($str)
    {
        return mb_convert_case($str, MB_CASE_LOWER, 'utf-8');
    }

    public static function toUpper($str)
    {
        return mb_convert_case($str, MB_CASE_UPPER, 'utf-8');
    }

    public static function firstToUpper($str)
    {
        $first = mb_substr($str, 0, 1, 'utf-8');
        $rest = mb_substr($str, 1, null, 'utf-8');
        return mb_convert_case($first, MB_CASE_UPPER, 'utf-8').$rest;
    }

    public static function substr($str, $start, $length = null)
    {
        return mb_substr($str, $start, $length, 'utf-8');
    }

    public static function cutString($str, $x)
    {
        $str = strip_tags($str);

        if (strlen($str) > $x + 5) {
            $opis = str_split($str, $x);
            $opis = $opis[0];
            while (isset($opis[$x - 1]) && $opis[$x - 1] != " ") {
                $x--;
            }
            $opis = substr($opis, 0, $x - 1);
            $opis .= "...";
        } else {
            $opis = $str;
        }
        return $opis;
    }

    public static function cutNames($str, $x)
    {
        $str = strip_tags($str);

        if (strlen($str) > $x + 5) {
            $opis = str_split($str, $x);
            $opis = $opis[0];
            while (isset($opis[$x - 1]) && $opis[$x - 1] != " ") {
                $x--;
            }
            $opis = substr($opis, 0, $x - 1);
        } else {
            $opis = $str;
        }
        return $opis;
    }

    public static function explode($delimiter, $string)
    {
        $values = array();

        $results = explode($delimiter, $string);
        foreach ($results as $r) {
            $values[$r] = $r;
        }

        return $values;
    }

    public static function super_unique($array, $key)

    {

        $temp_array = array();

        foreach ($array as &$v) {

            if (!isset($temp_array[$v[$key]])) {
                $temp_array[$v[$key]] =& $v;
            }

        }

        $array = array_values($temp_array);

        return $array;


    }

    public static function parseDate($date)
    {
        $days = array(1 => 'poniedziałek', 2 => 'wtorek', 3 => 'środa', 4 => 'czwartek', 5 => 'piątek', 6 => 'sobota', 7 => 'niedziela');
        $months = array(1 => 'stycznia', 2 => 'lutego', 3 => 'marca', 4 => 'kwietnia', 5 => 'maja', 6 => 'czerwca', 7 => 'lipca', 8 => 'sierpnia', 9 => 'września', 10 => 'października', 11 => 'listopada', 12 => 'grudnia');

        $date = date_parse($date);

        return $days[$date['day']] . ', ' . $date['day'] . ' ' . $months[$date['month']] . ' ' . $date['year'];
    }

    public static function dropImageCache($filename)
    {
        $dir = substr($filename, 0, 2);
        $pattern = substr($filename, 0, -4);

        if (is_dir(MAIN_PATH . '/public/i/' . $dir . '/')) {
            $dh = opendir(MAIN_PATH . '/public/i/' . $dir . '/');
            while ($d = readdir($dh)) {
                if (preg_match('/^' . $pattern . '/', $d)) {
                    @unlink(MAIN_PATH . '/public/i/' . $dir . '/' . $d);
                }
            }
            closedir($dh);
        }
    }

    public static function imageCache($dir, $image, $type, $width, $height = false, $ftype = 'jpg')
    {
        if ($type == 'default') {
            $filename = substr($image, 0, -4) . (($ftype == 'jpg') ? '.jpg' : '.' . $ftype);
        } else {
            $filename = substr($image, 0, -4) . '_' . $width . (($height) ? 'x' . $height : '') . '_' . $type . (($ftype == 'jpg') ? '.jpg' : '.' . $ftype);
        }

        $dir2 = substr($filename, 0, 2) . '/';

        if (file_exists(MAIN_PATH . '/public/i/' . $dir2 . $filename)) {
            return '/i/' . $dir2 . $filename;
        } else {
            return self::generate($dir, $dir2, $image, $width, $height, $type, $filename, $ftype);
        }
    }

    public static function imageCache2($dir, $image, $code, $ftype = 'png', $adaptive = false, $over = 'no-image.png')
    {
        $ext = pathinfo($image, PATHINFO_EXTENSION);

        $filename = str_replace('.' . $ext, '', $image) . '_' . $code . (($ftype == 'jpg') ? '.jpg' : '.' . $ftype);

        $dir2 = substr($filename, 0, 2) . '/';

        if (file_exists(MAIN_PATH . '/public/i/' . $dir2 . $filename)) {
            return '/i/' . $dir2 . $filename;
        } else {
            $ex = explode('x', $code);
            return self::generate($dir, $dir2, $image, $ex[0], $ex[1], 'divisor', $filename, $ftype, true, $adaptive, $over);
        }
    }

    protected static function generate($dir, $dir2, $image, $width, $height, $type, $newname, $ftype, $ext = false, $adaptive = false, $over = 'no-image.png')
    {
        $color = array(255, 255, 255);

        if ($ftype == 'png'){
            $color = 'alpha';
        }

        if (!is_dir(MAIN_PATH . '/public/i/')) { mkdir(MAIN_PATH . '/public/i/', 0777); }
        if (!is_dir(MAIN_PATH . '/public/i/' . $dir2)) { mkdir(MAIN_PATH . '/public/i/' . $dir2, 0777); }

        $options = array('preserveAlpha' => true, 'preserveTransparency' => true);

        if (!$ext) {
            if (!is_file(MAIN_PATH . $dir . $image)) {
                $type = 'special';
                $thumb = new GDSpecial(MAIN_PATH . '/public/imgs/' . $over, $options);
                $newname = str_replace(array('.jpg', '.png'), '', $over) . '_' . $width . (($height) ? 'x' . $height : '') . '_' . $type . '.png';
                $color = 'alpha';

                if (file_exists(MAIN_PATH . '/public/i/' . $dir2 . $newname)) {
                    return '/i/' . $dir2 . $newname;
                }
            } else {
                $thumb = new GDSpecial(MAIN_PATH . $dir . $image, $options);
            }
        } else {
            if (!is_file(MAIN_PATH . $dir . $image)) {
                $newname = str_replace(array('.jpg', '.png'), '', $over) . '_' . $width . (($height) ? 'x' . $height : '') . '_' . $type . '.png';

                if (file_exists(MAIN_PATH . '/public/i/' . $dir2 . $newname)) {
                    return '/i/' . $dir2 . $newname;
                } else {
                    $type = 'special';
                    $thumb = new GDSpecial(MAIN_PATH . '/public/imgs/' . $over, $options);
                    $color = 'alpha';
                }
            } else {
                $thumb = new GDSpecial(MAIN_PATH . $dir . $image, $options);
            }
        }

        if ($type == 'resize') {
            if ($height) {
                $thumb->resize($width, $height);
            } else {
                $thumb->resize($width, 0);
            }
        }
        if ($type == 'adaptive') {
            if ($height) {
                $thumb->adaptiveResize($width, $height);
            }
        }
        if ($type == 'special') {
            if ($height) {
                $thumb->specialResize($width, $height, $color);
            } else {
                $thumb->resize($width);
            }
        }
        if ($type == 'speciald') {
            if ($height) {
                $thumb->specialResize($width, $height, $color, 'bottom');
            } else {
                $thumb->resize($width);
            }
        }
        if ($type == 'divisor') {
            $pDB = new GPhotosTable();

            $photo = $pDB->getByName($image);
            if (isset($photo) && !empty($photo)) {
                $sides = array('90', '270', '-270', '-90');

                if (in_array($photo['rotate'], $sides)) {
                    $targ = -1 * $photo['rotate'];
                    $thumb->rotateImageNDegrees($targ);
                }
                if (in_array($photo['rotate'], array('180', '-180'))) {
                    $thumb->rotateImageNDegrees(180);
                }
                $thumb->crop($photo['x'], $photo['y'], $photo['width'], $photo['height']);
                if ($adaptive) {
                    $thumb->adaptiveResize($width, $height);
                } else {
                    $thumb->resize($width, $height);
                }
            } else {
                $newname = str_replace(array('.jpg', '.png'), '', $over) . '_' . $width . (($height) ? 'x' . $height : '') . '_' . $type . '.png';

                if (file_exists(MAIN_PATH . '/public/i/' . $dir2 . $newname)) {
                    return '/i/' . $dir2 . $newname;
                } else {
                    $type = 'special';
                    $thumb = new GDSpecial(MAIN_PATH . '/public/imgs/' . $over, $options);
                    $color = 'alpha';
                }

                if ($height) {
                    $thumb->specialResize($width, $height, $color);
                } else {
                    $thumb->resize($width);
                }
            }
        }

        $thumb->save(MAIN_PATH . '/public/i/' . $dir2 . $newname, $ftype);

        $fname = MAIN_PATH . '/public/i/' . $dir2 . $newname;
        if (file_exists($fname)) {
            list($genWidth, $genHeight) = getimagesize($fname);

            if (!$height){
                $height = $genHeight;
            }

            if ($width != $genWidth || $height != $genHeight) {
                $sext = pathinfo($fname, PATHINFO_EXTENSION);
                $thumb = imagecreatetruecolor($width, $height);
                if ($sext == 'jpg') {
                    $source = imagecreatefromjpeg($fname);
                } else {
                    $source = imagecreatefrompng($fname);
                }

                if ($ftype == 'png') {
                    imagesavealpha($thumb, true);

                    $trans_colour = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
                    imagefill($thumb, 0, 0, $trans_colour);
                }

                imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $height, $genWidth, $genHeight);

                if ($ftype == 'jpg') {
                    imagejpeg($thumb, $fname);
                } elseif ($ftype == 'png') {
                    imagepng($thumb, $fname);
                }
            }

            return '/i/' . $dir2 . $newname;
        } else {
            return false;
        }
    }

    public static function guestVariety($num, $type){
        $types = array(
            'Goś' => array('ć', 'ci', 'ci')
        );

        $text = null;

        if ($num == 0){
            $text = $type . $types[$type][1];
        } elseif ($num == 1){
            $text = $type . $types[$type][0];
        } else {
            $text = $type . $types[$type][1];
        }

        return $text;
    }

    public static function monthVariety($num, $type){
        $types = array(
            'miesi' => array('ąc', 'ące', 'ęcy'),
            'm' => array('-c', '-ce', '-cy')
        );

        $text = null;

        if ($num == 1){
            $text = $type . $types[$type][0];
        } elseif ($num > 1 && $num < 5){
            $text = $type . $types[$type][1];
        } else {
            $text = $type . $types[$type][2];
        }

        return $text;
    }

    public static function variety($num, $type)
    {
        $types = array(
            'wyświetl' => array('eń', 'enie', 'enia'),
            'ofert' => array('', 'a', 'y'),
            'os' => array('ób licytuje', 'oba licytuje', 'oby licytują'),
            'produkt' => array('ów', '', 'y'),
            'sztuk' => array('', 'a', 'i'),
            'pozostał' => array('o', 'a', 'y'),
            'opini' => array('i', 'a', 'e'),
            'Goś' => array('ć', 'ci', 'ci')
        );

        $text = null;

        switch (strlen($num)) {
            case 1: {
                if ($num == 0 || $num > 4) {
                    $text = $type . $types[$type][0];
                }
                if ($num == 1) {
                    $text = $type . $types[$type][1];
                }
                if ($num > 1 && $num <= 4) {
                    $text = $type . $types[$type][2];
                }
            }
                break;
            default: {
            if ($num >= 10 && $num < 20) {
                $text = $type . $types[$type][0];
            } else {
                $nums = substr($num, -1, 1);
                if ($nums == 0 || $nums == 1 || $nums > 4) {
                    $text = $type . $types[$type][0];
                }
                if ($nums > 1 && $nums <= 4) {
                    $text = $type . $types[$type][2];
                }
            }
            }
            break;
        }

        return $text;
    }

    public static function varietyTime($num, $type)
    {
        $types = array(
            1 => array('dzień', 'dni'),
            2 => array('godzin', 'godzina', 'godziny'),
            3 => array('minut', 'minuta', 'minuty'),
            4 => array('sekund', 'sekunda', 'sekundy')
        );

        switch ($type) {
            case 1: {
                switch (strlen($num)) {
                    case 1: {
                        if ($num == 0 || $num > 1) {
                            $text = $types[$type][1];
                        }
                        if ($num == 1) {
                            $text = $types[$type][0];
                        }
                    }
                        break;
                    default: {
                    $text = $types[$type][1];
                    }
                    break;
                }
            }
                break;
            case 2:
            case 3:
            case 4: {
                switch (strlen($num)) {
                    case 1: {
                        if ($num == 0 || $num > 4) {
                            $text = $types[$type][0];
                        }
                        if ($num == 1) {
                            $text = $types[$type][1];
                        }
                        if ($num > 1 && $num <= 4) {
                            $text = $types[$type][2];
                        }
                    }
                        break;
                    default: {
                    if ($num >= 10 && $num < 20) {
                        $text = $types[$type][0];
                    } else {
                        $nums = substr($num, -1, 1);
                        if ($nums == 0 || $nums == 1 || $nums > 4) {
                            $text = $types[$type][0];
                        }
                        if ($nums > 1 && $nums <= 4) {
                            $text = $types[$type][2];
                        }
                    }
                    }
                    break;
                }
            }
                break;
        }

        return $text;
    }

    public static function getTime($date)
    {
        $time = Stat::implodeDate($date);

        $ret = null;
        if ($time[0]) {
            $ret .= $time[0] . ' ' . self::varietyTime($time[0], 1) . ' ';
        }
        if ($time[1]) {
            $ret .= $time[1] . ' ' . self::varietyTime($time[1], 2) . ' ';
        }
        if (!$time[0] || !$time[1]) {
            $ret .= $time[2] . ' ' . self::varietyTime($time[2], 3) . ' ';
        }
        //if (!$time[0] && !$time[1]) { $ret .= $time[3] . ' ' . self::varietyTime($time[3], 4); }

        if (!$time[0] && !$time[1] && !$time[2]) {
            $ret = 'mniej niż minuta';
        }

        return $ret;
    }

    public static function prepareInStatement($name, $prefix, $values)
    {
        if (sizeof($values) > 1) {
            $keys = $params = array();

            $x = 1;
            foreach ($values as $v) {
                $keys[] = ':' . $prefix . $x;
                $params[':' . $prefix . $x] = $v;
                $x++;
            }

            $statement = array($name, 'IN', '(', implode(', ', $keys), ')');
        } else {
            $statement = array($name, '=', ':' . $prefix);
            $params = array(':' . $prefix => $values[0]);
        }

        return array('statement' => implode(' ', $statement), 'values' => $params);
    }

    public static function getUrl($route, $toRoute, $key = false, $value = false)
    {
        if ($key && $value) {
            $toRoute[$key] = $value;
        }
        if ($key && !$value) {
            if (!isset($toRoute[$key + 1])) {
                unset($toRoute[$key]);
            } else {
                $toRoute[$key] = '';
            }
        }

        $route_params = array(
            $route,
            'dt' => implode('/', $toRoute)
        );
        $url = Yii::$app->urlManager->createUrl($route_params);
        return str_replace(array('%2F', '%3D', '%3B', '%2C'), array('/', '=', ';', ','), $url);
    }

    public static function getPagiUrl($route, $toRoute, $key = false, $value = false)
    {
        if ($key && $value) {
            $toRoute[$key] = $value;
        }
        if ($key && !$value) {
            if (!isset($toRoute[$key + 1])) {
                unset($toRoute[$key]);
            } else {
                $toRoute[$key] = '';
            }
        }
        $route_params = array(
            $route,
            $toRoute
        );
        $url = Yii::$app->urlManager->createUrl($route_params);
        return str_replace(array('%2F', '%3D', '%3B', '%2C'), array('/', '=', ';', ','), $url);
    }
    public static function odmiana($odmiany, $int)
    {
        $txt = $odmiany[2];
        if ($int == 1) $txt = $odmiany[0];
        $jednosci = (int)substr($int, -1);
        $reszta = $int % 100;
        if (($jednosci > 1 && $jednosci < 5) & !($reszta > 10 && $reszta < 20)) {
            $txt = $odmiany[1];
        }
        return $txt;
    }

    public static function liczba($int)
    {
        $slowa = Array(
            'minus',

            Array(
                'zero',
                'jeden',
                'dwa',
                'trzy',
                'cztery',
                'pięć',
                'sześć',
                'siedem',
                'osiem',
                'dziewięć'),

            Array(
                'dziesięć',
                'jedenaście',
                'dwanaście',
                'trzynaście',
                'czternaście',
                'piętnaście',
                'szesnaście',
                'siedemnaście',
                'osiemnaście',
                'dziewiętnaście'),

            Array(
                'dziesięć',
                'dwadzieścia',
                'trzydzieści',
                'czterdzieści',
                'pięćdziesiąt',
                'sześćdziesiąt',
                'siedemdziesiąt',
                'osiemdziesiąt',
                'dziewięćdziesiąt'),

            Array(
                'sto',
                'dwieście',
                'trzysta',
                'czterysta',
                'pięćset',
                'sześćset',
                'siedemset',
                'osiemset',
                'dziewięćset'),

            Array(
                'tysiąc',
                'tysiące',
                'tysięcy'),

            Array(
                'milion',
                'miliony',
                'milionów'),

            Array(
                'miliard',
                'miliardy',
                'miliardów'),

            Array(
                'bilion',
                'biliony',
                'bilionów'),

            Array(
                'biliard',
                'biliardy',
                'biliardów'),

            Array(
                'trylion',
                'tryliony',
                'trylionów'),

            Array(
                'tryliard',
                'tryliardy',
                'tryliardów'),

            Array(
                'kwadrylion',
                'kwadryliony',
                'kwadrylionów'),

            Array(
                'kwintylion',
                'kwintyliony',
                'kwintylionów'),

            Array(
                'sekstylion',
                'sekstyliony',
                'sekstylionów'),

            Array(
                'septylion',
                'septyliony',
                'septylionów'),

            Array(
                'oktylion',
                'oktyliony',
                'oktylionów'),

            Array(
                'nonylion',
                'nonyliony',
                'nonylionów'),

            Array(
                'decylion',
                'decyliony',
                'decylionów')
        );

        $wynik = '';
        $j = abs((int)$int);

        if ($j == 0) return $slowa[1][0];
        $jednosci = $j % 10;
        $dziesiatki = ($j % 100 - $jednosci) / 10;
        $setki = ($j - $dziesiatki * 10 - $jednosci) / 100;

        if ($setki > 0) $wynik .= $slowa[4][$setki - 1] . ' ';

        if ($dziesiatki > 0) {
            if ($dziesiatki == 1) {
                $wynik .= $slowa[2][$jednosci] . ' ';
            } else {
                $wynik .= $slowa[3][$dziesiatki - 1] . ' ';
            }
        }

        if ($jednosci > 0 && $dziesiatki != 1) $wynik .= $slowa[1][$jednosci] . ' ';
        return $wynik;
    }

    public static function slownie($int)
    {
        $slowa = Array(
            'minus',

            Array(
                'zero',
                'jeden',
                'dwa',
                'trzy',
                'cztery',
                'pięć',
                'sześć',
                'siedem',
                'osiem',
                'dziewięć'),

            Array(
                'dziesięć',
                'jedenaście',
                'dwanaście',
                'trzynaście',
                'czternaście',
                'piętnaście',
                'szesnaście',
                'siedemnaście',
                'osiemnaście',
                'dziewiętnaście'),

            Array(
                'dziesięć',
                'dwadzieścia',
                'trzydzieści',
                'czterdzieści',
                'pięćdziesiąt',
                'sześćdziesiąt',
                'siedemdziesiąt',
                'osiemdziesiąt',
                'dziewięćdziesiąt'),

            Array(
                'sto',
                'dwieście',
                'trzysta',
                'czterysta',
                'pięćset',
                'sześćset',
                'siedemset',
                'osiemset',
                'dziewięćset'),

            Array(
                'tysiąc',
                'tysiące',
                'tysięcy'),

            Array(
                'milion',
                'miliony',
                'milionów'),

            Array(
                'miliard',
                'miliardy',
                'miliardów'),

            Array(
                'bilion',
                'biliony',
                'bilionów'),

            Array(
                'biliard',
                'biliardy',
                'biliardów'),

            Array(
                'trylion',
                'tryliony',
                'trylionów'),

            Array(
                'tryliard',
                'tryliardy',
                'tryliardów'),

            Array(
                'kwadrylion',
                'kwadryliony',
                'kwadrylionów'),

            Array(
                'kwintylion',
                'kwintyliony',
                'kwintylionów'),

            Array(
                'sekstylion',
                'sekstyliony',
                'sekstylionów'),

            Array(
                'septylion',
                'septyliony',
                'septylionów'),

            Array(
                'oktylion',
                'oktyliony',
                'oktylionów'),

            Array(
                'nonylion',
                'nonyliony',
                'nonylionów'),

            Array(
                'decylion',
                'decyliony',
                'decylionów')
        );

        $in = preg_replace('/[^-\d]+/', '', $int);
        $out = '';

        if ($in{0} == '-') {
            $in = substr($in, 1);
            $out = $slowa[0] . ' ';
        }

        $txt = str_split(strrev($in), 3);

        if ($in == 0) $out = $slowa[1][0] . ' ';

        for ($i = count($txt) - 1; $i >= 0; $i--) {
            $liczba = (int)strrev($txt[$i]);
            if ($liczba > 0) {
                if ($i == 0) {
                    $out .= self::liczba($liczba) . ' ';
                } else {
                    $out .= ($liczba > 1 ? self::liczba($liczba) . ' ' : '') . self::odmiana($slowa[4 + $i], $liczba) . ' ';
                }
            }
        }
        return trim($out);
    }

    public static function makeDate($ds, $add, $format = "Y-m-d")
    {
        $pd = date_parse($ds);

        return date($format, mktime($pd['hour'], $pd['minute'], $pd['second'], $pd['month'], $pd['day'] + $add, $pd['year']));
    }

    public static function numberLimiter($number, $limiter, $char = "+")
    {
        if ($number > $limiter){
            return $limiter . $char;
        } else {
            return $number;
        }
    }

    public static function halfTable($table)
    {
        if (!isset($table[0])){
            return [];
        }
        $total = count($table);
        if ($total == 0){
            return [];
        }

        $half = ceil($total / 2);
        if ($half < 1){
            $half = $total;
        }

        $first = [];
        $second = [];
        for ($i = 0; $i < $half; $i++){
            $first[] = $table[$i];
        }

        if ($half == $total){
            return [$first, []];
        } else {
            for ($z = $half; $z < $total; $z++){
                $second[] = $table[$z];
            }
            return [$first, $second];
        }
    }

    public static function threeTable($table)
    {
        $total = count($table);
        if ($total == 0){
            return [];
        }

        $one = ceil($total / 3);
        if ($one < 1){
            $one = $total;
        }

        $first = [];
        $second = [];
        $third = [];
        for ($i = 0; $i < $one; $i++){
            $first[] = $table[$i];
        }

        if ($one == $total){
            return [$first, [], []];
        } else {
            $two = $one * 2;
            for ($z = $one; $z < $two; $z++){
                if (isset($table[$z])) {
                    $second[] = $table[$z];
                }
            }

            if ($two >= $total){
                return [$first, $second, []];
            } else {
                for ($x = $two; $x < $total; $x++){
                    if (isset($table[$x])) {
                        $third[] = $table[$x];
                    }
                }

                return [$first, $second, $third];
            }
        }
    }

    public static function humanFilesize($bytes, $decimals = 2) {
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = (int) floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}

