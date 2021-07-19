<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$idplano = $seg->antiInjection($_POST['id']);

$sql = new Query($bd);
$txt = "UPDATE TREDE_PAGAPLANO SET CCOMPRPPLAN = (NULL),                                               
       					CNOMCOPPLAN = (NULL) ,                          
      					 DCOMPRPPLAN = (NULL)
				WHERE IDPGSEGPLAN = :id ";
$sql->addParam(':id', $idplano);
$sql->executeSQL($txt);

$bd->close();
?>