<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
//error_reporting(0);

$bd = new Database();

$id		= 	$seg->antiInjection($_POST['id']);

$sql = new Query ($bd);
$txt = "SELECT SEQPLANO,  
                CNOMEPLANO,
                CDESCPLANO,
                CTEMPPLANO,
                VVALTPLANO,
                MENSAPLANO
			FROM TREDE_PLANOS
				 WHERE SEQPLANO = :id";
$sql->AddParam(':id',$id);
$sql->executeQuery($txt);

$json['id'] 	    = $sql->result("SEQPLANO");
$json['nome'] 	    = ucwords(utf8_encode($sql->result("CNOMEPLANO")));
$json['desc'] 	    = utf8_encode($sql->result("CDESCPLANO"));
$json['tempo'] 	    = $sql->result("CTEMPPLANO");
$json['valort'] 	= $func->formataNumero($sql->result("VVALTPLANO"));
$json['valorm'] 	= $sql->result("MENSAPLANO");

echo json_encode($json);


$bd->close();
?>
