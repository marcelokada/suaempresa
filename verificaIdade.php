<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	//$tpl = new Template("modal.html");

	$bd = new Database();

	$cpf	= 	$seg->antiInjection($_POST['cpf']);
	
	$sql = new Query ($bd);
	$txt = "SELECT REDE_SEQUSUA					
			    FROM TREDE_USUADMIN
				WHERE REDE_CPFUSUA = :cpf";
	$sql->AddParam(':cpf',$cpf);
	$sql->executeQuery($txt);
    
	$resultado = $sql->result("REDE_SEQUSUA");
	
	if($resultado == ''){
		$eventos['id']		= '0';//liberado
	}else{
		$eventos['id']		= $sql->result("REDE_SEQUSUA");
	}
		
echo json_encode($eventos);

$bd->close();
?>