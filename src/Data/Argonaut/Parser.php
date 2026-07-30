<?php

$_jsonParser = function($fail, $succ, $s) use (&$_jsonParser) {
    
    $decoded = json_decode($s);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return $fail(\json_last_error_msg());
    }
    return $succ($decoded);
};

$exports['_jsonParser'] = $_jsonParser;
return $exports;
