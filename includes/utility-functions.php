<?php
if (!function_exists('ns_format_unit')) {
    function ns_format_unit($type, $number, $desig) {
        if (!$desig) { $desig = ""; }
        else { $desig = " " . substr($desig,0,1); }
        return $type . " " . $number . $desig;
    }
}

if (!function_exists('ns_get_unit_token')) {
    function ns_get_unit_token($chapter, $unittype, $unitnum, $unitdesig) {
        return str_replace(" ", "_", $chapter) . '-' . $unittype . '-' . sprintf("%04d",$unitnum) . '-' . $unitdesig;
    }
}
