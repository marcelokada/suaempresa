<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	$bd = new Database();
	
	$id		= 	$seg->antiInjection($_POST['id']);
	
		$sql = new Query ($bd);
		$txt = "SELECT VNOMECATESUB,NNUMECATESUB
				  FROM TREDE_SUBCATEGORIA
				 WHERE NNUMECATESUB = :id";
		$sql->AddParam(':id',$id);
		$sql->executeQuery($txt);
		
		$json['id'] 	= $sql->result("NNUMECATESUB");
		$json['nome'] 	= ucwords(utf8_encode($sql->result("VNOMECATESUB")));
		
		echo json_encode($json);
    

$bd->close();
?>