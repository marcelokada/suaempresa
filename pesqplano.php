<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$idplano = $seg->antiInjection($_POST['idplano']);

$sql = new Query($bd);
$txt = "SELECT CNOMEPLANO,
                    CDESCPLANO,
                    CTEMPPLANO,
                    VVALTPLANO,
                    MENSAPLANO 
                 FROM TREDE_PLANOS
				WHERE SEQPLANO = :id ";
$sql->addParam(':id', $idplano);
$sql->executeQuery($txt);

$eventos['nome'] = $sql->result("CNOMEPLANO");
$eventos['tempo'] = $sql->result("CTEMPPLANO");
//$eventos['valo'] = $sql->result("VVALTPLANO") + $sql->result("MENSAPLANO");
$eventos['valo'] = $sql->result("VVALTPLANO");
$eventos['adesao'] = $sql->result("VVALTPLANO");
$eventos['mensa'] = $sql->result("MENSAPLANO");
$eventos['valor_format'] = number_format($sql->result("VVALTPLANO"), 2, ',', '.');
$eventos['mens'] = $sql->result("MENSAPLANO");

echo json_encode($eventos);

$bd->close();
?>