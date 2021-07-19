<?php
require_once("comum/autoload.php"); 
require_once("comum/layout.php"); 
error_reporting(0);
$tpl->addFile("CONTEUDO","limiteatingido.html");

$_SESSION = array();
session_destroy();
$tpl->show(); 
?>