<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
//error_reporting(0);

$bd = new Database();

$id		= 	$seg->antiInjection($_POST['id']);

$sql = new Query ($bd);
$txt = "SELECT REDE_LOGBLOK
			FROM TREDE_USUADMIN
				 WHERE REDE_SEQUSUA = :id";
$sql->AddParam(':id',$id);
$sql->executeQuery($txt);

$res = $sql->result("REDE_LOGBLOK");

if($res == 's'){
	$sql1 = new Query ($bd);
	$txt1 = "UPDATE  TREDE_USUADMIN SET REDE_LOGBLOK = 'n'
					 WHERE REDE_SEQUSUA = :id";
	$sql1->AddParam(':id',$id);
	$sql1->executeSQL($txt1);
}else{
	$sql1 = new Query ($bd);
	$txt1 = "UPDATE TREDE_USUADMIN  SET REDE_LOGBLOK = 's'
					 WHERE REDE_SEQUSUA = :id";
	$sql1->AddParam(':id',$id);
	$sql1->executeSQL($txt1);
}


$bd->close();
?>
