<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("../comum/apagaArquivos.php");
error_reporting(0);

$bd = new Database();

$id_sessao_c 	    = $_GET['idSessao'];
$id_sessao_s 	    = $_SESSION['idSessao'];

$idrede			= $_SESSION['idRede'];
$seg->verificaSession($id_sessao_s);

require_once("comum/layout.php"); 
	$tpl->addFile("CONTEUDO","vicashback.html");
	$tpl->ID_SESSAO = $_SESSION['idSessao'];
	$tpl->ID_REDE 	= $_SESSION['idRede'];

$tpl->show(); 
$bd->close();
?>