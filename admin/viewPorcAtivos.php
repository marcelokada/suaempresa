<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
//error_reporting(0);

$bd = new Database();

$id		= 	$seg->antiInjection($_POST['idp']);

$sql = new Query ($bd);
$txt = "SELECT NUMENIVEL,PORCENTAGEM,SEQ
			FROM TREDE_NIVEIS_ATIVOS
				 WHERE SEQ = :id";
$sql->AddParam(':id',$id);
$sql->executeQuery($txt);

$json['id'] 	    = $sql->result("SEQ");
$json['nivel'] 	    = $sql->result("NUMENIVEL");
$json['porc'] 	    = $sql->result("PORCENTAGEM");

echo json_encode($json);


$bd->close();
?>
