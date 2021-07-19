<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	$bd = new Database();
	
	$idcart		= 	$seg->antiInjection($_POST['idcart']);
	$idloja		= 	$seg->antiInjection($_POST['idloja']);
	$idusua		= 	$seg->antiInjection($_POST['idusua']);
		
	$sql = new Query ($bd);
	$txt = "UPDATE TREDE_CARRINHO SET VFECHACARR = 's'
			 WHERE SEQUENCIACRE = :idloja 
				AND VIDCARCARR = :idcart
				AND REDE_SEQUSUA = :usua";
	$sql->addParam(':idloja',$idloja);
	$sql->addParam(':idcart',$idcart);
	$sql->addParam(':usua',$idusua);
	$sql->executeSQL($txt);

	$sql1 = new Query ($bd);
	$txt1 = "SELECT VLOGEMAILCRE FROM TREDE_CREDENCIADOS 
				 WHERE SEQUENCIACRE = :idloja";
	$sql1->addParam(':idloja',$idloja);
	//$sql1->executeSQL($txt1);

	//$eventos['emailcred'] = $sql1->result("VLOGEMAILCRE");

echo json_decode($eventos);

$bd->close();
?>