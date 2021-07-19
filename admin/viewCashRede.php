<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
//error_reporting(0);

$bd = new Database();

$id		= 	$seg->antiInjection($_POST['id']);

$sql = new Query ($bd);
$txt = "SELECT SEQUENCIA,NNUMECLASS,CASHBCLASS  
			FROM TREDE_CLASSREDE
				 WHERE NNUMECLASS = :id";
$sql->AddParam(':id',$id);
$sql->executeQuery($txt);

$json['id'] 	    = $sql->result("SEQUENCIA");
$json['nivel'] 	    = $sql->result("NNUMECLASS");
$json['cash'] 	    = $sql->result("CASHBCLASS");

echo json_encode($json);


$bd->close();
?>
