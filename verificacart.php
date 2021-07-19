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
	$txt = "SELECT VFECHACARR 
			  FROM TREDE_CARRINHO
			 WHERE SEQUENCIACRE = :idloja 
			   AND VIDCARCARR = :idcart
			   AND REDE_SEQUSUA = :usua
		  GROUP BY VFECHACARR";
	$sql->addParam(':idloja',$idloja);
	$sql->addParam(':idcart',$idcart);
	$sql->addParam(':usua',$idusua);
	$sql->executeQuery($txt);
    
	$res_cart = $sql->result("VFECHACARR");
	
	$eventos['res'] = $res_cart;
	
	if($res_cart == 's'){
		$eventos['ca'] = '1';
	}else if($res_cart == 'n'){
		$eventos['ca'] = '2';
	}
	
	echo json_encode($eventos);

$bd->close();
?>