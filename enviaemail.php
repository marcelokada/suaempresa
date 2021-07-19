<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();
$func = new Funcao();

require_once("comum/layout.php");
$tpl->addFile("CONTEUDO", "enviaemail.html");

if(isset($_POST['email'])){
	$sql245 = new Query ($bd);
	$txt245 = "SELECT TITULO,TEXTO FROM TREDE_EMAIL";
	$sql245->executeQuery($txt245);

	$titulo = utf8_encode($sql245->result("TITULO"));
	$texto  = utf8_encode($sql245->result("TEXTO"));

	$func->EnviarEmail('chinespx@gmail.com',$titulo,$texto);
}

$tpl->show();
$bd->close();