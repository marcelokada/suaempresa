<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	$bd = new Database();
	
	$id		= 	$seg->antiInjection($_POST['id']);
	$nome	= 	utf8_decode($seg->antiInjection($_POST['nome']));

	
		$sql = new Query ($bd);
		$txt = "UPDATE TREDE_SUBCATEGORIA SET VNOMECATESUB = :nome
				 WHERE NNUMECATESUB = :id";
		$sql->AddParam(':id',$id);
		$sql->AddParam(':nome',$nome);
		$sql->executeSQL($txt);
		
		echo "Subcategoria alterada com sucesso!";
    

$bd->close();
?>