<?php
if (!function_exists('generateUniqueNumericCode')) {
    function generateUniqueNumericCode($length) {
        $numericCode = '';
        for ($i = 0; $i < $length; $i++) {
            $numericCode .= random_int(0, 9);
        }
        return $numericCode;
    }
}
?>