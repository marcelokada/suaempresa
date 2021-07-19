<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
//error_reporting(0);

	$bd = new Database();
	
	$id		= 	$seg->antiInjection($_POST['id']);
	
		$sql = new Query ($bd);
		$txt = "SELECT REDE_SEQUSUA,CNOMEADMIN,EMAILADMIN,CSITUADMIN,NIVELADMIN
				  FROM TREDE_ADMINS
				 WHERE REDE_SEQUSUA = :id";
		$sql->AddParam(':id',$id);
		$sql->executeQuery($txt);
		
		$json['id'] 	= $sql->result("REDE_SEQUSUA");
		$json['nome'] 	= ucwords(utf8_encode($sql->result("CNOMEADMIN")));
		$json['email'] 	= $sql->result("EMAILADMIN");
		$json['nivel'] 	= $sql->result("NIVELADMIN");
		$json['situ'] 	= $sql->result("CSITUADMIN");

		echo json_encode($json);
    

$bd->close();
?>