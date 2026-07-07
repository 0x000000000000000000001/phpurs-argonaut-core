<?php

$_jsonParser = function($fail, $succ, $s = null) use (&$_jsonParser) {
    if (func_num_args() < 3) {
        $__args = func_get_args();
        return function(...$more) use ($__args, &$_jsonParser) {
            return $_jsonParser(...array_merge($__args, $more));
        };
    }
    
    $decoded = json_decode($s);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return $fail(json_last_error_msg());
    }
    return $succ($decoded);
};

$exports['_jsonParser'] = $_jsonParser;
return $exports;
