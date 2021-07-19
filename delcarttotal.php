<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	//$tpl = new Template("modal.html");

	$bd = new Database();
		
	$idcart	= 	$seg->antiInjection($_POST['id']);
	$idloja	= 	$seg->antiInjection($_POST['idloja']);
		
	$sql = new Query ($bd);
	$txt = "DELETE FROM TREDE_CARRINHO 
			 WHERE SEQUENCIACRE = :idloja 
				AND VIDCARCARR = :idcart";
	$sql->addParam(':idloja',$idloja);
	$sql->addParam(':idcart',$idcart);
	$sql->executeSQL($txt);
    
	echo "Carrinho Apagado com Sucesso!";
	
$bd->close();
?>