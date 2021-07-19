<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart();
//error_reporting(0);

	$bd = new Database();
	
	$id		= 	$seg->antiInjection($_POST['id']);
	$tp	  = 	$seg->antiInjection($_POST['tp']);

	
		$sql = new Query ($bd);
		$txt = "UPDATE TREDE_PROFISSAO SET CSITUPROF = :tipo
				 WHERE NNUMEPROF = :id";
		$sql->AddParam(':id',$id);
		$sql->AddParam(':tipo',$tp);
		$sql->executeSQL($txt);


$bd->close();
?>