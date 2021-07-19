<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	//$tpl = new Template("modal.html");

	$bd = new Database();

	$idusua	= 	$_POST['idusua'];
		
	$sql = new Query ($bd);
	$txt = "DELETE FROM TREDE_MEMBROS WHERE SEQ = '".$idusua."' ";
	//$sql->addParam(':idusua',idusua);
	$sql->executeSQL($txt);

	//echo $sql;
$bd->close();
?>