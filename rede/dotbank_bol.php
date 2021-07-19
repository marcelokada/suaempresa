<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
//error_reporting(0);

$bd = new Database();

$idpplano = $seg->antiInjection($_POST['planos']);

$id = explode('r',$idpplano);

$idplan = $id[2];

$id_dotbank = $seg->antiInjection($_POST['dots']);
$linkboleto = $_POST['boleto'];

$sql = new Query($bd);
$txt = "UPDATE TREDE_PAGAPACOTE SET CIDDPGPAC = :id_dotbank, LINKBOLETO = '".$linkboleto."'
        WHERE NNUMEPPAC = :id";
$sql->addParam(':id_dotbank', $id_dotbank);
$sql->addParam(':id', $idplan);
$sql->executeSQL($txt);


$bd->close();
?>