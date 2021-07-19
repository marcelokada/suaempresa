<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
//error_reporting(0);

$bd = new Database();

$id		= 	$seg->antiInjection($_POST['id']);

$sql = new Query ($bd);
$txt = "SELECT NIDUSUCASH,VVALUSCASH,NIDUSUCASH
			FROM TREDE_CASHBACK_USU
				 WHERE NIDUSUCASH = :id";
$sql->AddParam(':id',$id);
$sql->executeQuery($txt);

$json['id'] 	    = $sql->result("NIDUSUCASH");
$json['nome'] 	    = utf8_encode($func->RetonaNomeUsuarioPorSeq($bd,$sql->result("NIDUSUCASH")));
$json['valor'] 	    = number_format($sql->result("VVALUSCASH"),2,',','.');

echo json_encode($json);

$bd->close();
?>
