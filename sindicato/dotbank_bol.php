<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
//error_reporting(0);

$bd = new Database();

$idpplano = $seg->antiInjection($_POST['idpplano']);
$id_dotbank = $seg->antiInjection($_POST['id_dotbank']);

$sql = new Query($bd);
$txt = "UPDATE TREDE_PAGAPACOTE SET CIDDPGPAC = :id_dotbank
        WHERE NNUMEPPAC = :id";
$sql->addParam(':id_dotbank', $id_dotbank);
$sql->addParam(':id', $idpplano);
$sql->executeSQL($txt);


$bd->close();
?>