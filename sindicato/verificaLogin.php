<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	//$tpl = new Template("modal.html");

	$bd = new Database();

	$login	= 	$seg->antiInjection($_POST['login']);
	
	$sql = new Query ($bd);
	$txt = "SELECT REDE_SEQUSUA					
			    FROM TREDE_USUADMIN
				WHERE REDE_LOGUSUA = :login";
	$sql->AddParam(':login',$login);
	$sql->executeQuery($txt);
    
	$resultado = $sql->result("REDE_SEQUSUA");
	
	if($resultado == ''){
		$eventos['log']		= '0';//liberado
	}else{
		$eventos['log']		= $sql->result("REDE_SEQUSUA");
	}
		
echo json_encode($eventos);

$bd->close();
?>