<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();

$bd = new Database();

$sql = new Query ($bd);
$txt = "";
//$sql->executeQuery($txt);

define('VERSAO',"localhost");