<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$idplano = $seg->antiInjection($_POST['idplano']);

$sql = new Query($bd);
$txt = "SELECT  NNUMEPAC,
								CNOMEPAC,
								CDESCPAC,
								NPONTPAC,
								CSITUPAC,
								NVALOPAC
                 FROM TREDE_PACOTES_REDE
				WHERE NNUMEPAC = :id ";
$sql->addParam(':id', $idplano);
$sql->executeQuery($txt);

$eventos['nome'] = $sql->result("CNOMEPAC");
$eventos['valo'] = $sql->result("NVALOPAC");
$eventos['pontos'] = $sql->result("NPONTPAC");
$eventos['valor_format'] = number_format($sql->result("NVALOPAC"), 2, ',', '.');

echo json_encode($eventos);

$bd->close();
?>