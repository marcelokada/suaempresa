<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	$bd = new Database();
	
	$idusu		= 	$seg->antiInjection($_POST['idusu']);
	$idplano	= 	$seg->antiInjection($_POST['idplano']);	
	$idtrans	= 	$seg->antiInjection($_POST['idtrans']);	
		
	$sql = new Query ($bd);
	$txt = "UPDATE TREDE_PAGAPLANO SET NIDCODIPLAN = :idtrans
			 WHERE SEQUPAGPLAN = :idplano
			   AND NIDUPAGPLAN = :idusu";
	$sql->addParam(':idtrans',$idtrans);
	$sql->addParam(':idplano',$idplano);
	$sql->addParam(':idusu',$idusu);
	$sql->executeSQL($txt);

	echo $idtrans;

$bd->close();
?>