<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$id_sessao 	    = $_SESSION['idSessao'];
$id_confer 	    = $_GET['idSessao'];

$id_admin    	= $_SESSION['admin'];

$seg->verificaSession($id_sessao);

require_once("comum/layout.php");
$tpl->addFile("CONTEUDO","altera_plausua.html");
$tpl->ID_SESSAO = $_GET['idSessao'];
$tpl->ID_ADMIN 	= $_SESSION['admin'];



$tpl->show();
$bd->close();
?>