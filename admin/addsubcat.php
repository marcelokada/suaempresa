<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	$bd = new Database();
	
	$id		= 	$seg->antiInjection($_POST['id']);
	$nome	= 	utf8_decode($seg->antiInjection($_POST['nome']));

	
		$sql = new Query ($bd);
		$txt = "INSERT INTO TREDE_SUBCATEGORIA 
		(VNOMECATESUB,NNUMECATECAT,VSITUCATESUB)
		VALUE
		(:nome,:id,'a')";
		$sql->AddParam(':id',$id);
		$sql->AddParam(':nome',$nome);
		$sql->executeSQL($txt);
		
		echo "Subcategoria adicionada com sucesso!";
    

$bd->close();
?>