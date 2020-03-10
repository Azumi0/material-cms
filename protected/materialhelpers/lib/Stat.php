<?php

namespace materialhelpers;

use Ramsey\Uuid\Uuid;
use Yii;

class Stat
{
    public static $role = null;

    static public function keyGen($l = 5, $patternType = 1) {
        $key = $rand = null;

        switch($patternType){
            default:
                $pattern = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
                break;
            case 2:
                $pattern = '123456789QWERTYUIPASDFGHJKLZXCVBNM';
                break;
            case 3:
                $pattern = 'qwertyuiopasdfghjklzxcvbnm';
                break;
            case 4:
                $pattern = 'QWERTYUIOPASDFGHJKLZXCVBNM';
                break;
        }

        for ($x = 0; $x < $l; $x++) {
            $rand = rand(0, strlen($pattern) - 1);
            $key .= $pattern{$rand};
        }

        return $key;
    }

    static public function keyGenSecure() {
        $key = Uuid::uuid4()->toString();

        return trim(str_replace('-', '', $key));
    }

    static public function getAge($date) {
        $y = date("Y", strtotime($date));
        $m = date("n", strtotime($date));
        $d = date("j", strtotime($date));

        $cy = date("Y");
        $cm = date("n");
        $cd = date("j");

        $x = -1;

        if ($m == $cm) {
            if ($d < $cd) {
                $x = 0;
            }
        } else if ($m < $cm) {
            $x = 0;
        }

        $age = ($cy - $y) + $x;

        return $age;
    }

    static public function implodeDate($date, $date2 = null) {
        $utime = strtotime($date);
        $ctime = ($date2) ? strtotime($date2) : time();

        $time = $utime - $ctime;

        if ($time > 0) {
            $days = floor($time / 86400);
            $time = $time - ($days * 86400);
            $hours = floor($time / 3600);
            $time = $time - ($hours * 3600);
            $minutes = floor($time / 60);
            $seconds = $time - $minutes * 60;
        } else {
            return array(0, 0, 0, 0);
        }

        return array($days, $hours, $minutes, $seconds);
    }

    static public function implodeDateTime($time) {
        if ($time > 0) {
            $days = floor($time / 86400);
            $time = $time - ($days * 86400);
            $hours = floor($time / 3600);
            $time = $time - ($hours * 3600);
            $minutes = floor($time / 60);
            $seconds = $time - $minutes * 60;
        } else {
            return array(0, 0, 0, 0);
        }

        return array($days, $hours, $minutes, $seconds);
    }

    static public function cutString($str, $x) {
        $str = strip_tags($str);

        if (strlen($str) > $x + 5) {
            $opis = str_split($str, $x);
            $opis = $opis[0];
            while (isset($opis[$x - 1]) && $opis[$x - 1] != " ")
                $x--;
            $opis = substr($opis, 0, $x - 1);
            $opis .= "...";
        } else {
            $opis = $str;
        }
        return $opis;
    }

    static public function encode($array) {
        return base64_encode(serialize($array));
    }

    static public function decode($array) {
        return unserialize(base64_decode($array));
    }

    static public function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    static public function getCookies() {
        $cookies = Yii::$app->response->cookies;
        $val = $cookies->getValue('sh_cookies_accept', false);
        if (isset($val) && !empty($val)){
            return false;
        } else {
            return true;
        }
    }

    static public function getPerms($perms) {
        if (self::$role == 'su'){
            return true;
        } elseif (isset($perms) && !empty($perms)) {
            if (Yii::$app->user->can($perms)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    static public function clear($path, $exception = false) {
        $dh = opendir($path);

        while ($f = readdir($dh)) {
            if ($f != '..' && $f != '.') {
                $p = $path . $f;
                if (is_dir($p)) {
                    self::clear($p . '/', $exception);
                    if ($exception){
                        if (is_array($exception)) {
                            if (array_search($f, $exception) === false){
                                rmdir($p);
                            }
                        } else {
                            if ($f != $exception){
                                rmdir($p);
                            }
                        }
                    } else {
                        rmdir($p);
                    }
                } else {
                    if ($exception) {
                        if (is_array($exception)) {
                            if (array_search($f, $exception) === false){
                                @unlink($p);
                            }
                        } else {
                            if ($f != $exception) {
                                @unlink($p);
                            }
                        }
                    } else {
                        @unlink($p);
                    }
                }
            }
        }

        closedir($dh);
    }

    public static function countVarInArr($arr, $var){
        $count = 0;
        foreach ($arr as $item) {
            if (is_array($item)){
                $count = $count + self::countVarInArr($item, $var);
            } else {
                if ($item == $var) {
                    $count++;
                }
            }
        }
        return $count;
    }

    public static function in_array_recursive($needle, $haystack)
    {

        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($haystack));

        foreach($it AS $element) {
            if($element == $needle) {
                return true;
            }
        }

        return false;
    }

    public static function notEmptyArray($arr)
    {
        foreach ($arr as $item) {
            if (!empty($item)) return true;
        }

        return false;
    }

    public static function questionsSort($a, $b)
    {
        if ($a['position'] == $b['position']) {
            return 0;
        }
        return ($a['position'] < $b['position']) ? -1 : 1;
    }
}