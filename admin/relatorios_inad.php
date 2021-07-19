<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$id_sessao = $_SESSION['idSessao'];
$id_confer = $_GET['idSessao'];

$seg->verificaSession($id_sessao);

require_once("comum/layout.php");
$tpl->addFile("CONTEUDO", "relatorios_inad.html");
$tpl->ID_SESSAO = $_GET['idSessao'];

$sql = new Query($bd);
$sql->clear();
$txt = "SELECT DISTINCT SUBSTR(DDATPAGPLAN,1,4) DDATPAGPLAN
			FROM TREDE_PAGAPLANO
			GROUP BY DDATPAGPLAN
			ORDER BY 1 DESC";
$sql->executeQuery($txt);

while (!$sql->eof()) {
	$tpl->ANO = $sql->result("DDATPAGPLAN");
	$tpl->block("ANO1");
	$sql->next();
}

$sql = new Query($bd);
$sql->clear();
$txt = "SELECT SEQPLANO, CNOMEPLANO
			FROM TREDE_PLANOS
			ORDER BY 2";
$sql->executeQuery($txt);

while (!$sql->eof()) {
	$tpl->SEQPLAN = $sql->result("SEQPLANO");
	$tpl->CNOMEPLANO = $sql->result("CNOMEPLANO");
	$tpl->block("PLANOS");
	$sql->next();
}

/*$sql = new Query($bd);
$sql->clear();
$txt = "SELECT DISTINCT CSITPAGPLAN
			FROM TREDE_PAGAPLANO
			ORDER BY 1";
$sql->executeQuery($txt);

while (!$sql->eof()) {
	$tpl->IDSITU = $sql->result("CSITPAGPLAN");
	$tpl->CSITPAGPLAN = $func->RetornaSituaPagamento($sql->result("CSITPAGPLAN"));
	$tpl->block("SITUPAG");
	$sql->next();
}*/

$sql = new Query($bd);
$sql->clear();
$txt = "SELECT DISTINCT CTIPOPGPLAN
			FROM TREDE_PAGAPLANO
			ORDER BY 1";
$sql->executeQuery($txt);

/*while (!$sql->eof()) {
	$tpl->IDTPAG = $sql->result("CTIPOPGPLAN");
	$tpl->CTIPOPGPLAN = $func->Tipopagamento($sql->result("CTIPOPGPLAN"));
	$tpl->block("TPAG");
	$sql->next();
}*/


if (isset($_POST['listar'])) {

	$mes = $seg->antiInjection($_POST['mes']);
	if ($mes == 'todos') {
		$cond1 = "WHERE DDATPAGPLAN IS NOT NULL";
	}
	else {
		$cond1 = "WHERE SUBSTR(DDATPAGPLAN,6,2) = '" . $mes . "' ";
	}

	$ano = $seg->antiInjection($_POST['ano']);

	if ($ano != 'todos') {
		$cond2 = "AND SUBSTR(DDATPAGPLAN,1,4) = " . $ano . "";
	}
	else {
		$cond2 = "";
	}

	$plano = $seg->antiInjection($_POST['plano']);

	if ($plano == 'todos') {
		$cond3 = "AND NSEQPAGPLAN IS NOT NULL";
	}
	else {
		$cond3 = "AND NSEQPAGPLAN = '" . $plano . "' ";
	}

/*	$situa = $seg->antiInjection($_POST['situa']);

	if ($situa == 'todos') {
		$cond4 = "AND CSITPAGPLAN IS NOT NULL";
	}
	else {
		$cond4 = "AND CSITPAGPLAN = '" . $situa . "' ";
	}*/

	$tipopg = $seg->antiInjection($_POST['tpag']);

	if ($tipopg == 'todos') {
		$cond5 = "AND CTIPOPGPLAN IS NOT NULL";
	}
	else {
		//$cond5 = "AND CTIPOPGPLAN = '" . $tipopg . "' ";
		$cond5 = "AND CTIPOPGPLAN = '2' ";
	}

	$sql = new Query($bd);
	$sql->clear();
	$txt = "SELECT   SEQUPAGPLAN,
                   NSEQPAGPLAN,
                   NIDUPAGPLAN,
                   NSEQPAGPLAN,
                   NVALPAGPLAN,
                   CSITPAGPLAN,
                   DDATPAGPLAN,
                   DDTINIPPLAN,
                   DDTFIMPPLAN,
                   CTIPOPGPLAN,
                   DVENCBOPLAN,
                   CSITUAPPLAN,
                   MENSAPLANO
			FROM TREDE_PAGAPLANO
			" . $cond1 . " 
			" . $cond2 . " 
			" . $cond3 . "
			" . $cond5 . "
			AND DVENCBOPLAN < '".date('Y-m-d')."'
			AND CTIPOTRPLAN = 'm'
			AND  CSITPAGPLAN = 1";
	$sql->executeQuery($txt);

	while (!$sql->eof()) {
		$tpl->NOMEUSUA = utf8_encode($func->RetonaNomeUsuarioPorSeq($bd, $sql->result("NIDUPAGPLAN")));
		$seq_usua = $sql->result("NIDUPAGPLAN");
		$tpl->NOMEPLANO = $func->RetornaNomePlano($sql->result("NSEQPAGPLAN"));
		$tpl->DATA = $data->formataData1($sql->result("DDATPAGPLAN"));
		$tpl->STATUSPAG = $func->RetornaSituaPagamento($sql->result("CSITPAGPLAN"));
		$tpl->TIPOPAG = $func->Tipopagamento($sql->result("CTIPOPGPLAN"));
		$tpl->VALORM = number_format($sql->result("MENSAPLANO"), 2, ',', '.');
    $tpl->DATAVENC = $data->formataData1($sql->result("DVENCBOPLAN"));
    //$tpl->STATUSPLAN = $sql->result("CSITUAPPLAN");
				
		$sql12 = new Query($bd);
		$txt12 = "SELECT  VLOGIINDCOL ,NSEQPATRCOL
							  FROM TREDE_AFILIADOS
							  WHERE NSEQUSUACOL = :usua
							  ORDER BY 1 DESC			
							  LIMIT 1";
		$sql12->addParam(':usua', $seq_usua);
		$sql12->executeQuery($txt12);

		$idpat = $sql12->result("NSEQPATRCOL");

		if($idpat == ""){
			$tpl->PAT = 'Não tem';
		}else{
			$tpl->PAT = $func->RetonaNomeUsuarioPorSeq($bd,$idpat).'/'.$sql12->result("VLOGIINDCOL");
		}

		$tpl->block("REL1");
		$sql->next();
	}

	$sql = new Query($bd);
	$sql->clear();
	$txt = "SELECT SUM(MENSAPLANO) VALOR
			FROM TREDE_PAGAPLANO
			" . $cond1 . " 
			" . $cond2 . " 
			" . $cond3 . " 
			" . $cond4 . " 
			" . $cond5 . "
			AND DVENCBOPLAN < '".date('Y-m-d')."'
			AND CTIPOTRPLAN = 'm'
			AND  CSITPAGPLAN = 1
			";
	$sql->executeQuery($txt);

	$tpl->TOTAL = number_format($sql->result("VALOR"), 2, ',', '.');
}

$tpl->show();
$bd->close();
?><?php
