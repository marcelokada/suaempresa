<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	//$tpl = new Template("modal.html");

	$bd = new Database();
	
	//$idprod	= 	$_GET['idprod'];
	$idcart	= 	$_GET['idcart'];
	$idloja	= 	$_GET['idloja'];
	
	//$nome	= 	$seg->antiInjection($_POST['nome']);
	///$valor	= 	$seg->antiInjection($_POST['valor']);
		
	$sql = new Query ($bd);
	$txt = "DELETE FROM TREDE_CARRINHO 
			 WHERE SEQUENCIACRE = :idloja 
				AND VIDCARCARR = :idcart";
	$sql->addParam(':idloja',$idloja);
	$sql->addParam(':idcart',$idcart);
	$sql->executeSQL($txt);
    
	echo "Carrinho apagado com sucesso.";
	
$bd->close();
?>