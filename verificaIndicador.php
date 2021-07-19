<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	//$tpl = new Template("modal.html");

	$bd = new Database();

	$indicador	= 	$seg->antiInjection($_POST['indicador']);
	
	$sql = new Query ($bd);
	$txt = "SELECT REDE_SEQUSUA, REDE_LOGUSUA				
			    FROM TREDE_USUADMIN
				WHERE REDE_LOGUSUA = :indicador";
	$sql->AddParam(':indicador',$indicador);
	$sql->executeQuery($txt);
    
	$resultado = $sql->result("REDE_SEQUSUA");
	
	if($resultado == ''){
		$eventos['ind']	= '0';//vai para o admin geral
	}else{
		$eventos['ind']	= $sql->result("REDE_SEQUSUA");
		$eventos['log_ind']	= $sql->result("REDE_LOGUSUA");
	}
		
echo json_encode($eventos);

$bd->close();
?>