<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
error_reporting(0);

$bd = new Database();

$id_sessao 	    = $_SESSION['idSessao'];
$id_confer 	    = $_GET['idSessao'];
$e 				= $_SESSION['idRede'];
$idrede			= $_GET['idRede'];

if (($id_confer != $id_sessao) or ($idrede != $e)) {
	require_once("comum/restrito.html");
	session_destroy();
} elseif (($id_sessao == '') or ($id_confer == '') or ($e == '') or ($idrede == '')) {
	require_once("comum/restrito.html");
	session_destroy();
} else {

	require_once("comum/layout.php");
	$tpl->addFile("CONTEUDO", "produtoscadastrados.html");
	$tpl->ID_SESSAO = $_GET['idSessao'];
	$tpl->ID_REDE 	= $_GET['idRede'];
	$tpl->ID_MSG 	= $_GET['idMsg'];
	$idmsg 			= $_GET['idMsg'];

	$sql1 = new Query($bd);
	$txt1 = "SELECT SEQUENCIACRE,
			   VNOMECREDCRE,
			   VNOMEENDECRE,
			   NNUMEENDECRE,
			   VNOMEBAIRCRE,
			   VNOMECIDAMUN,
			   CESTADOUFMUN,
			   CESTADOUFEST,
			   NNUMECATECRE,
			   NNUMECATESUB,
			   NNUMESERVCRE,
			   NNUMEIBGEMUN,
			   CSITUACAOCRE,
			   DDATACREDCRE,
			   NNUMEREGIREG,
			   VCUPOMDESCRE,
			   VLINKDESCCRE,
			   VCOMPLEMECRE,
			   NNUMECATESUB,
			   VIMAGEMCRCRE,
			   VCNPJJURICRE,
			   VNUMECCEPCRE
		  FROM TREDE_CREDENCIADOS
		 ORDER BY SEQUENCIACRE DESC";
	$sql1->executeQuery($txt1);

	while (!$sql1->eof()) {
		$tpl->SEQ 			= $sql1->result("SEQUENCIACRE");
		$sequenciacre 		= $sql1->result("SEQUENCIACRE");
		$categoria			= $sql1->result("NNUMECATECRE");
		$subcategoria		= $sql1->result("NNUMECATESUB");
		$tpl->NOMECRE 		= ucwords(utf8_encode($sql1->result("VNOMECREDCRE")));
		$tpl->CATEGORIA 	= ucwords(utf8_encode($func->RetornaNomeCategoria($bd, $categoria)));
		$tpl->SUBCATEGORIA 	= ucwords(utf8_encode($func->RetornaNomeSubCategoria($bd, $subcategoria)));
		$tpl->LINIMAGEMIMG 	= $func->RetornaImagem($bd, $sequenciacre);
		//$tpl->STATUS	 	= $sql1->result("CSITUACAOCRE");
		$status	 			= $sql1->result("CSITUACAOCRE");

		if ($status == 'a') {
			$tpl->COR = "";
			$tpl->CHK = "checked";
			$tpl->ATIV = "desativar";
		} else {
			$tpl->COR = "alert alert-danger";
			$tpl->CHK = "";
			$tpl->ATIV = "ativar";
		}

		$tpl->block("CADAREDE");
		$sql1->next();
	}
	$tpl->ID_SESSAO = $_GET['idSessao'];
	$tpl->ID_ADMIN 	= $_SESSION['admin'];
}

$tpl->show();
$bd->close();
?>