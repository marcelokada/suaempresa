<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$id_sessao 		= $_SESSION['idSessao'];
$id_confer 		= $_GET['idSessao'];
$seq 			= $_SESSION['idUsuario'];

if ($id_confer != $id_sessao) {
	require_once("comum/restrito.html");
	session_destroy();
} else {
	require_once("comum/layout.php");
	$tpl->addFile("CONTEUDO", "principal.html");
	$tpl->ID_SESSAO = $_SESSION['idSessao'];

	//SELECT PARA VERIFICAR O USUARIO
	$tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $seq)));
	//SELECT PARA VERIFICAR O USUARIO

	//SELECT BANNER
	/*$sql1 = new Query ($bd);
	$txt1 = "SELECT REDE_SEQUBAN,REDE_TITUBAN,REDE_MANCBAN,REDE_BTN1BAN,  
					REDE_BTN2BAN,REDE_DATABAN,REDE_DINIBAN,REDE_DFIMBAN,
					REDE_LIK1BAN,REDE_LIK2BAN,REDE_ATUABAN
			   FROM TREDE_BANNER
			   WHERE '".date('Y-m-d H:i:s')."' >= REDE_DINIBAN
				 AND '".date('Y-m-d H:i:s')."' <= REDE_DFIMBAN";
	$sql1->executeQuery($txt1); 
	
	while(!$sql1->eof()){
		$tpl->TITUBAN = utf8_encode($sql1->result("REDE_TITUBAN"));
		$tpl->MANCBAN = utf8_encode($sql1->result("REDE_MANCBAN"));
		$tpl->BTN1BAN = utf8_encode($sql1->result("REDE_BTN1BAN"));
		$tpl->LIK1BAN = $sql1->result("REDE_LIK1BAN");
		$bnt2 		  = $sql1->result("REDE_BTN2BAN");
		$active 	  = $sql1->result("REDE_ATUABAN");
	
	if($active == 's'){
		//$tpl->AC = 'active';
	}
	
	if($bnt2 <> ''){
		$btn2ban = utf8_encode($sql1->result("REDE_BTN2BAN"));
		$lik2ban = $sql1->result("REDE_LIK2BAN");
		$tpl->BTN2 = "<a href='".$lik2ban."' class='animated3 slider btn btn-default btn-min-block'>".$btn2ban."</a>";
	}
	
	$sql1->next();
	
	$tpl->block("BANNER");
	}*/
	//SELECT BANNER


	//SELECT DESTAQUES
	$sql2 = new Query($bd);
	$txt2 = "SELECT I.LINIMAGEMIMG,C.VNOMECREDCRE,C.SEQUENCIACRE 
			   FROM TREDE_CREDENCIADOS C, TREDE_IMAGEMCRED I
			  WHERE C.SEQUENCIACRE = I.SEQUENCIACRE
			    AND CSITUACAOCRE = 'a'
			  ORDER BY RAND()
			  LIMIT 12";
	$sql2->executeQuery($txt2);

	while (!$sql2->eof()) {
		$tpl->VNOMECREDCRE 	= utf8_encode($sql2->result("VNOMECREDCRE"));
		$sequenciacre 		= $sql2->result("SEQUENCIACRE");
		$tpl->IDCRED		= $sql2->result("SEQUENCIACRE");
		//$tpl->CUPOM		= $sql2->result("VCUPOMDESCRE ");	
		$id					= $sql2->result("SEQUENCIACRE");
		$tpl->IMG_DESTAQUE = $func->RetornaImagem($bd, $id);
		$sql2->next();

		$tpl->block("DESTAQUE");
	}
	//SELECT DESTAQUES



	//SELECT NOVOS
	$sql3 = new Query($bd);
	$txt3 = "SELECT I.LINIMAGEMIMG,
					C.VNOMECREDCRE,
					C.SEQUENCIACRE,
					SUBSTR(DDATACREDCRE,1,4) DATA,
					VCUPOMDESCRE
			   FROM TREDE_CREDENCIADOS C, TREDE_IMAGEMCRED I
			  WHERE C.SEQUENCIACRE = I.SEQUENCIACRE
			    AND CSITUACAOCRE = 'a'
				AND SUBSTR(DDATACREDCRE,1,4) = '" . date('Y') . "' ";
	$sql3->executeQuery($txt3);

	while (!$sql3->eof()) {
		$sequenciacre1 		= $sql3->result("SEQUENCIACRE");
		$tpl->VNOMECREDCRE  = $sql3->result("VNOMECREDCRE");
		$tpl->IDCRED		= $sql3->result("SEQUENCIACRE");
		$tpl->CUPOM			= $sql3->result("VCUPOMDESCRE");
		$tpl->LINIMAGEMIMG  = $func->RetornaImagem($bd, $sequenciacre1);
		$sql3->next();

		$tpl->block("NOVOS");
	}
	//SELECT NOVOS

}

$tpl->show();
$bd->close();
?>