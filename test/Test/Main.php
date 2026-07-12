<?php

$thisIsNull = null;
$thisIsBoolean = true;
$thisIsNumber = 12;
$thisIsString = "foobar";
$thisIsArray = ["foo", "bar"];
$thisIsObject = (object)["foo" => "bar"];
$thisIsInvalidString = "\\\xffff";

$exports['thisIsNull'] = $thisIsNull;
$exports['thisIsBoolean'] = $thisIsBoolean;
$exports['thisIsNumber'] = $thisIsNumber;
$exports['thisIsString'] = $thisIsString;
$exports['thisIsArray'] = $thisIsArray;
$exports['thisIsObject'] = $thisIsObject;
$exports['thisIsInvalidString'] = $thisIsInvalidString;
return $exports;
