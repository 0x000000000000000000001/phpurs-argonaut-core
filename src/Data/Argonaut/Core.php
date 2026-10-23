<?php

$id = function ($x) {
    return $x;
};

class JsonNull implements \JsonSerializable
{
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return null;
    }
}

$jsonNull = new JsonNull();

$stringify = function ($j) {
    return \json_encode($j);
};

$stringifyWithIndent = function ($i, $j) {
    // We can't easily set arbitrary indentation in PHP json_encode without rewriting it,
    // JSON_PRETTY_PRINT uses 4 spaces.
    $encoded = \json_encode($j, JSON_PRETTY_PRINT);
    if ($encoded === false) {
        $err = 'json_encode failed: ' . \json_last_error_msg() . "\n";
        \file_put_contents('php://stderr', $err);
        return '';
    }
    return $encoded;
};

$_caseJson = function ($isNull, $isBool, $isNum, $isStr, $isArr, $isObj, $j) {
    if ($j === null || $j instanceof JsonNull) {
        return $isNull(1);
    } elseif (\is_bool($j)) {
        return $isBool($j);
    } elseif (\is_int($j) || \is_float($j)) {
        return $isNum($j);
    } elseif (\is_string($j)) {
        return $isStr($j);
    } elseif (\is_array($j) && (empty($j) || \array_keys($j) === \range(0, \count($j) - 1))) {
        return $isArr($j);
    }
    // In PHP, objects from JSON decode are stdClass, or arrays depending on options.
    // If it's an array but not sequential, it's an object in JS.
    elseif (\is_array($j) || \is_object($j)) {
        // Purescript-foreign-object expects stdClass
        $obj = \is_object($j) ? $j : (object) $j;
        return $isObj($obj);
    } else {
        return $isObj((object) $j);
    }
};

$_compare = function ($EQ, $GT, $LT, $a, $b) use (&$_compare) {
    $isArray = function ($v) {
        if (!\is_array($v)) {
            return false;
        }
        if (empty($v)) {
            return true;
        }
        return \array_keys($v) === \range(0, \count($v) - 1);
    };

    if ($a === null || $a instanceof JsonNull) {
        if ($b === null || $b instanceof JsonNull) {
            return $EQ;
        } else {
            return $LT;
        }
    } elseif (\is_bool($a)) {
        if (\is_bool($b)) {
            if ($a === $b) {
                return $EQ;
            } elseif ($a === false) {
                return $LT;
            } else {
                return $GT;
            }
        } elseif ($b === null || $b instanceof JsonNull) {
            return $GT;
        } else {
            return $LT;
        }
    } elseif (\is_int($a) || \is_float($a)) {
        if (\is_int($b) || \is_float($b)) {
            if ($a === $b) {
                return $EQ;
            } elseif ($a < $b) {
                return $LT;
            } else {
                return $GT;
            }
        } elseif ($b === null || $b instanceof JsonNull) {
            return $GT;
        } elseif (\is_bool($b)) {
            return $GT;
        } else {
            return $LT;
        }
    } elseif (\is_string($a)) {
        if (\is_string($b)) {
            if ($a === $b) {
                return $EQ;
            } elseif ($a < $b) {
                return $LT;
            } else {
                return $GT;
            }
        } elseif ($b === null || $b instanceof JsonNull) {
            return $GT;
        } elseif (\is_bool($b)) {
            return $GT;
        } elseif (\is_int($b) || \is_float($b)) {
            return $GT;
        } else {
            return $LT;
        }
    } elseif ($isArray($a)) {
        if ($isArray($b)) {
            $lenA = \count($a);
            $lenB = \count($b);
            $min = \min($lenA, $lenB);
            for ($i = 0; $i < $min; $i++) {
                $ca = $_compare($EQ, $GT, $LT, $a[$i], $b[$i]);
                if ($ca !== $EQ) {
                    return $ca;
                }
            }
            if ($lenA === $lenB) {
                return $EQ;
            } elseif ($lenA < $lenB) {
                return $LT;
            } else {
                return $GT;
            }
        } elseif ($b === null || $b instanceof JsonNull) {
            return $GT;
        } elseif (\is_bool($b)) {
            return $GT;
        } elseif (\is_int($b) || \is_float($b)) {
            return $GT;
        } elseif (\is_string($b)) {
            return $GT;
        } else {
            return $LT;
        }
    } else {
        if ($b === null || $b instanceof JsonNull) {
            return $GT;
        } elseif (\is_bool($b)) {
            return $GT;
        } elseif (\is_int($b) || \is_float($b)) {
            return $GT;
        } elseif (\is_string($b)) {
            return $GT;
        } elseif ($isArray($b)) {
            return $GT;
        } else {
            $aa = (array) $a;
            $bb = (array) $b;
            $akeys = \array_keys($aa);
            $bkeys = \array_keys($bb);
            if (\count($akeys) < \count($bkeys)) {
                return $LT;
            } elseif (\count($akeys) > \count($bkeys)) {
                return $GT;
            }
            $keys = \array_unique(\array_merge($akeys, $bkeys));
            \sort($keys);
            foreach ($keys as $k) {
                if (!\array_key_exists($k, $aa)) {
                    return $LT;
                } elseif (!\array_key_exists($k, $bb)) {
                    return $GT;
                }
                $ck = $_compare($EQ, $GT, $LT, $aa[$k], $bb[$k]);
                if ($ck !== $EQ) {
                    return $ck;
                }
            }
            return $EQ;
        }
    }
};

$exports['fromBoolean'] = $id;
$exports['fromNumber'] = $id;
$exports['fromString'] = $id;
$exports['fromArray'] = $id;
$exports['fromObject'] = $id;
$exports['jsonNull'] = $jsonNull;
$exports['stringify'] = $stringify;
$exports['stringifyWithIndent'] = $stringifyWithIndent;
$exports['_caseJson'] = $_caseJson;
$exports['_compare'] = $_compare;
return $exports;
