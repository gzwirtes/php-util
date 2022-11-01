<?php

namespace GZInfo\Util;


class EAN13Validator
{

    /**
     * @param $ean
     * @return bool
     */
    public function isAValidEAN13($ean)
    {
        echo $ean;
        $sumEvenIndexes = 0;
        $sumOddIndexes  = 0;

        $eanAsArray = array_map('intval', str_split($ean));

        if (!$this->has13Numbers($eanAsArray)) {
            return false;
        };

        for ($i = 0; $i < count($eanAsArray)-1; $i++) {
            if ($i % 2 === 0) {
                $sumOddIndexes  += $eanAsArray[$i];
            } else {
                $sumEvenIndexes += $eanAsArray[$i];
            }
        }

        $rest = ($sumOddIndexes + (3 * $sumEvenIndexes)) % 10;

        if ($rest !== 0) {
            $rest = 10 - $rest;
        }

        return $rest === $eanAsArray[12];
    }

    /**
     * @param array $ean
     * @return bool
     */
    private function has13Numbers(array $ean)
    {
        return count($ean) === 13;
    }

    function isValidBarcode($barcode) {
        //checks validity of: GTIN-8, GTIN-12, GTIN-13, GTIN-14, GSIN, SSCC
        //see: http://www.gs1.org/how-calculate-check-digit-manually
        $barcode = (string) $barcode;
        //we accept only digits
        if (!preg_match("/^[0-9]+$/", $barcode)) {
            return false;
        }
        //check valid lengths:
        $l = strlen($barcode);
        if(!in_array($l, [8,12,13,14,17,18]))
            return false;
        //get check digit
        $check = substr($barcode, -1);
        $barcode = substr($barcode, 0, -1);
        $sum_even = $sum_odd = 0;
        $even = true;
        while(strlen($barcode)>0) {
            $digit = substr($barcode, -1);
            if($even)
                $sum_even += 3 * $digit;
            else
                $sum_odd += $digit;
            $even = !$even;
            $barcode = substr($barcode, 0, -1);
        }
        $sum = $sum_even + $sum_odd;
        $sum_rounded_up = ceil($sum/10) * 10;
        return ($check == ($sum_rounded_up - $sum));
    }
}