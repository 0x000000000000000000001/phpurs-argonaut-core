<?php
require_once "output/Data.Eq/index.php";
$eqNumber = $GLOBALS['Data_Eq_eqNumber'];
$eq = $eqNumber->{'eq'};
var_dump($eq(12)(12));
var_dump($eq(12)(12.0));
var_dump($eq(12.0)(12.0));
var_dump($eq(12)(13));
