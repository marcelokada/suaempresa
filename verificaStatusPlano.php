<?php

require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$id = $seg->antiInjection($_POST['idusua']);

$sql = new Query ($bd);
$txt = "SELECT LAST_INSERT_ID(SEQUPAGPLAN) SEQUPAGPLAN,CSITUAPPLAN,CSITPAGPLAN	 				
			    FROM TREDE_PAGAPLANO
				WHERE NIDUPAGPLAN = :id
 					AND CSITUAPPLAN IN ('p','a')
				ORDER BY 1 DESC
				LIMIT 1";
$sql->AddParam(':id', $id);
$sql->executeQuery($txt);

$eventos['id'] = $sql->result("SEQUPAGPLAN");
$eventos['situplan'] = $sql->result("CSITUAPPLAN");
$eventos['situpag'] = $sql->result("CSITPAGPLAN");

echo json_encode($eventos);

$bd->close();
