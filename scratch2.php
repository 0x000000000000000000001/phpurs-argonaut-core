<?php
// eq for Eq
require_once "output/Data.Eq/index.php";
$eq = $GLOBALS['Data_Eq_eq'];

// eq for Maybe
require_once "output/Data.Maybe/index.php";
$eqMaybe = $GLOBALS['Data_Maybe_eqMaybe'];
$eqNumber = $GLOBALS['Data_Eq_eqNumber'];

$justInt = new \Data\Maybe\Data_Maybe_Just(12);
$justFloat = new \Data\Maybe\Data_Maybe_Just(12.0);

$res1 = $eq($eqMaybe($eqNumber))($justInt)($justInt);
$res2 = $eq($eqMaybe($eqNumber))($justInt)($justFloat);
var_dump($res1, $res2);

// Let's test eq for Array
require_once "output/Data.Array/index.php";
$eqArray = $GLOBALS['Data_Eq_eqArray'];
$res3 = $eq($eqArray($eqMaybe($eqNumber)))([$justInt])([$justInt]);
var_dump($res3);
