<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	$bd = new Database();

	$idcart	= 	$seg->antiInjection($_POST['idcart']);
	$idloja	= 	$seg->antiInjection($_POST['idloja']);
	
	//$idcart	= 	'K5AUlumcgGrpnN321xVM';
	//$idloja	= 	'53';
	
	$sql = new Query ($bd);
	$txt = "SELECT SUM(NVVALOCARR) SOMA
			    FROM TREDE_CARRINHO
				WHERE VIDCARCARR = :car
				  AND SEQUENCIACRE = :id";
	$sql->AddParam(':id',$idloja);
	$sql->AddParam(':car',$idcart);
	$sql->executeQuery($txt);
    
	$eventos['valor'] 		= $formata->formataNumero( $sql->result("SOMA"));
	$eventos['valor_real']  = $sql->result("SOMA");

	$sql1 = new Query ($bd);
	$txt1 = "SELECT SUM(VVACASCARR) SOMA_CASH
			    FROM TREDE_CARRINHO
				WHERE VIDCARCARR = :car
				  AND SEQUENCIACRE = :id";
	$sql1->AddParam(':id',$idloja);
	$sql1->AddParam(':car',$idcart);
	$sql1->executeQuery($txt1);
	
	//$eventos['cash'] 		= $formata->formataNumero($sql1->result("VVACASCARR"));
	
echo json_encode($eventos);
//echo $idcart;

$bd->close();
?>