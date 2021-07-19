<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$id_sessao_c = $_GET['idSessao'];
$id_sessao_s = $_SESSION['idSessao'];

$idrede = $_SESSION['idRede'];
$seg->verificaSession($id_sessao_s);

require_once("comum/layout.php");
$tpl->addFile("CONTEUDO", "comprapacotes.html");
$tpl->ID_SESSAO = $_SESSION['idSessao'];

$tpl->ID_REDE = $_SESSION['idRede'];

$tpl->ID_PCT = $_GET['idpacote'];

if (isset($_GET['idmsg'])) {
	$tpl->ID_MSG = $_GET['idMsg'];
}

$pags = $func->AtivoPagSeguro();
if($pags == 's'){
	$tpl->block("PAGS");
}

$dots = $func->AtivoDotBank();
if($dots == 's'){
	$tpl->block("DOTS");
}



$sql = new Query($bd);
$txt = "SELECT NNUMEPAC,
							 CNOMEPAC,
							 CDESCPAC,
							 NPONTPAC,
							 CSITUPAC,
       				 NVALOPAC
FROM TREDE_PACOTES_REDE
WHERE CSITUPAC = 'a'
AND NNUMEPAC = '".$_GET['idpacote']."'";
$sql->executeQuery($txt);

while (!$sql->eof()) {
	$tpl->ID = $sql->result("NNUMEPAC");
	$tpl->NOME = $sql->result("CNOMEPAC");
	$tpl->DESC = $sql->result("CDESCPAC");
	$tpl->PONTOS = number_format($sql->result("NPONTPAC"),2,',','.');
	$tpl->VALOR = number_format($sql->result("NVALOPAC"),2,',','.');
	$tpl->DATA_VENC = date('Y-m-d', strtotime("+3 days", strtotime(date('Y-m-d'))));
	$sql->next();
}


if (isset($_POST['solicitar'])) {
	$valor = $_POST['valor'];
	$valor = str_replace('.', '', $valor);
	$valor = str_replace(',', '.', $valor);

}


$tpl->show();
$bd->close();
?>