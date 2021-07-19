<?php

require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$id = $seg->antiInjection($_POST['idusua']);

$sql = new Query ($bd);
$txt = "SELECT LAST_INSERT_ID(NNUMEPPAC) NNUMEPPAC,	SITPAGPAC			
			    FROM TREDE_PAGAPACOTE
				WHERE NNUMEREDE = :id
 					AND SITPAGPAC = 1
				ORDER BY 1 DESC
				LIMIT 1";
$sql->AddParam(':id', $id);
$sql->executeQuery($txt);

$eventos['id'] = $sql->result("NNUMEPPAC");
//$eventos['situpaga'] = $sql->result("SITPAGPAC");
$eventos['situpaga'] = $id;

echo json_encode($eventos);

$bd->close();
