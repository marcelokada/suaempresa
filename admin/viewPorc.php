<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
//error_reporting(0);

$bd = new Database();

$id		= 	$seg->antiInjection($_POST['idp']);

$sql = new Query ($bd);
$txt = "SELECT SEQ,NIVEL,PORCENTAGEM  
			FROM TREDE_NIVELPORCENT
				 WHERE SEQ = :id";
$sql->AddParam(':id',$id);
$sql->executeQuery($txt);

$json['id'] 	    = $sql->result("SEQ");
$json['nivel'] 	    = $sql->result("NIVEL");
$json['porc'] 	    = $sql->result("PORCENTAGEM");

echo json_encode($json);


$bd->close();
?>
