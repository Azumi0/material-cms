<?php

namespace materialhelpers;

use Yii;

class Validators
{
    static public function checkNIP($str) {
        $str = preg_replace('/[^0-9]+/', '', $str);

        if (strlen($str) !== 10) {
            return false;
        }

        $arrSteps = array(6, 5, 7, 2, 3, 4, 5, 6, 7);
        $intSum = 0;

        for ($i = 0; $i < 9; $i++) {
            $intSum += $arrSteps[$i] * $str[$i];
        }

        $int = $intSum % 11;
        $intControlNr = $int === 10 ? 0 : $int;

        if ($intControlNr == $str[9]) {
            return true;
        }

        return false;
    }

    static public function anyKeyNotEmpty($needles, $haystack) {
        foreach ($needles as $needle) {
            if (!empty($haystack[$needle])) {
                return true;
            }
        }

        return false;
    }
}