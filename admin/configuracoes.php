<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
error_reporting(0);

$bd = new Database();

$id_sessao 	    = $_SESSION['idSessao'];
$id_confer 	    = $_GET['idSessao'];
$e 				= $_SESSION['admin'];
$ver_admin		= $_SESSION['admin'];


	
require_once("comum/layout.php"); 
$tpl->addFile("CONTEUDO","redecredenciada.html");
$tpl->ID_SESSAO = $_GET['idSessao'];
$tpl->ID_ADMIN 	= $_SESSION['admin'];
$msg = $_GET['msg'];


if($msg == 's'){
	$tpl->MSG = '<font color="green">**Cadastro Realizado com Sucesso!!** <a href="cadastrarede.php?idSessao={ID_SESSAO}&admin={ID_ADMIN}">Cadastrar novamente</a></font><br>';   
	$tpl->block("SUCESSO");

$sql0 = new Query($bd);
$txt0 = "SELECT LAST_INSERT_ID(SEQUENCIACRE) SEQ FROM TREDE_CREDENCIADOS
ORDER BY 1 DESC
LIMIT 1";
$sql0->executeQuery($txt0);

$seq = $sql0->result("SEQ");

$sql = new Query($bd);
$txt = "SELECT SEQUENCIACRE,
			   VNOMECREDCRE,
			   VNOMEENDECRE,
			   NNUMEENDECRE,
			   VNOMEBAIRCRE,
			   VNOMECIDAMUN,
			   CESTADOUFMUN,
			   CESTADOUFEST,
			   NNUMECATECRE,
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
		 WHERE SEQUENCIACRE = :seq";
$sql->addParam('seq',$seq);
$sql->executeQuery($txt);

$tpl->SEQ 		= $sql->result("SEQUENCIACRE");
$sequenciacre 	= $sql->result("SEQUENCIACRE");
$tpl->NOME 		= utf8_encode(ucwords($sql->result("VNOMECREDCRE")));
$tpl->RUA 		= utf8_encode(ucwords($sql->result("VNOMEENDECRE")));
$tpl->NUMERO 	= $sql->result("NNUMEENDECRE");
$tpl->BAIRRO 	= utf8_encode(ucwords($sql->result("VNOMEBAIRCRE")));
$tpl->CIDA	 	= utf8_encode(ucwords($sql->result("VNOMECIDAMUN")));
$tpl->UF 		= ucwords($sql->result("CESTADOUFMUN"));
$tpl->CAT_NUM 	= $sql->result("NNUMECATECRE");

$cat_num 		= $sql->result("NNUMECATECRE");
$tpl->CAT_NOME 	= utf8_encode(ucwords($func->RetornaNomeCategoria($bd,$sql->result("NNUMECATECRE"))));

//$tpl->SCAT_NOME 	= $sql->result("NNUMESERVCRE");
//$tpl->NOME 	= $sql->result("NNUMESERVCRE");
$tpl->IBGE 		= $sql->result("NNUMEIBGEMUN");
//$tpl->NOME 	= $sql->result("CSITUACAOCRE");
//$tpl->DATA 	= $sql->result("DDATACREDCRE");
//$tpl->NOME 	= $sql->result("NNUMEREGIREG");
$tpl->CUPOM 	= $sql->result("VCUPOMDESCRE");
$tpl->LINKS 	= $sql->result("VLINKDESCCRE");
$tpl->COMPLE 	= $sql->result("VCOMPLEMECRE");
$tpl->SCAT_NOME = utf8_encode(ucwords($func->RetornaNomeSubCategoria($bd,$sql->result("NNUMECATESUB"))));
$tpl->IMG 		= $func->RetornaImagem($bd,$sequenciacre);
$tpl->CNPJ 		= $sql->result("VCNPJJURICRE");
$tpl->CEP 		= $sql->result("VNUMECCEPCRE");

$tpl->block("REDE");

}else{
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
		 ORDER BY VNOMECREDCRE ASC";
	$sql1->executeQuery($txt1);

while(!$sql1->eof()){
	$tpl->SEQ 			= $sql1->result("SEQUENCIACRE");
	$sequenciacre 		= $sql1->result("SEQUENCIACRE");
	$categoria			= $sql1->result("NNUMECATECRE");
	$subcategoria		= $sql1->result("NNUMECATESUB");
	$tpl->NOMECRE 		= ucwords($sql1->result("VNOMECREDCRE"));
	$tpl->CATEGORIA 	= ucwords(utf8_encode($func->RetornaNomeCategoria($bd,$categoria)));
	$tpl->SUBCATEGORIA 	= ucwords(utf8_encode($func->RetornaNomeSubCategoria($bd,$subcategoria)));
	$tpl->LINIMAGEMIMG 	= $func->RetornaImagem($bd,$sequenciacre);
	//$tpl->STATUS	 	= $sql1->result("CSITUACAOCRE");
	$status	 			= $sql1->result("CSITUACAOCRE");
	
	if($status == 'a'){
		$tpl->COR = "";
		$tpl->CHK = "checked";
		$tpl->ATIV = "desativar";
	}else{
		$tpl->COR = "alert alert-danger";
		$tpl->CHK = "";
		$tpl->ATIV = "ativar";
	}
	
	
	
$tpl->block("CADAREDE");	
$sql1->next();

}

$tpl->block("REDE1");
}//FINALIZAÇÃO DO CADASTRO





$tpl->ID_SESSAO = $_GET['idSessao'];
$tpl->ID_ADMIN 	= $_SESSION['admin'];

$tpl->show(); 
$bd->close();
?>