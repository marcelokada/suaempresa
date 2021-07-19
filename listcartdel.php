<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	$bd = new Database();

	$id	= 	$seg->antiInjection($_POST['id']);
	
	$sql = new Query ($bd);
	$txt = "SELECT  SEQUENCIACRE
			    FROM TREDE_CARRINHO
				WHERE VIDCARCARR = :id
				GROUP BY SEQUENCIACRE
				LIMIT 1";
	$sql->AddParam(':id',$id);
	$sql->executeQuery($txt);

	$idlojas			= $sql->result("SEQUENCIACRE");
	$eventos['idlojas']	= $idlojas;

echo json_encode($eventos);

$bd->close();
?>