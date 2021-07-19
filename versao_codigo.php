<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	$bd = new Database();

	$idusua	= 	$seg->antiInjection($_POST['idusua']);
	$senha	= 	$seg->antiInjection($_POST['senha']);

	
	$senha	= 	md5($senha);
	
	$sql = new Query ($bd);
	$txt = "SELECT REDE_SENHAUS
			    FROM TREDE_USUADMIN
				WHERE REDE_SEQUSUA = :idusua";
	$sql->AddParam(':idusua',$idusua);
	$sql->executeQuery($txt);
    
	$res_senha  = $sql->result("REDE_SENHAUS");
	
	if($res_senha == $senha){
		$resultado['usua'] = '1';
	}else{
		$resultado['usua'] = '2';
	}
	
	$sql1 = new Query ($bd);
	$txt1 = "SELECT VVALUSCASH
			    FROM TREDE_CASHBACK_USU
				WHERE NIDUSUCASH = :idusua";
	$sql1->AddParam(':idusua',$idusua);
	$sql1->executeQuery($txt1);
	
	$res_cash  = $sql1->result("VVALUSCASH");
	
	$resultado['valorcash'] = $res_cash;
	
echo json_encode($resultado);

$bd->close();
?>