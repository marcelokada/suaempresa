<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$tpl = new Template("listanoticias.html");

$mes = date('m');
$ano = date('Y');
$nome = "";
	
	$mes	= 	$seg->antiInjection($_POST['mes']);
	$ano	= 	$seg->antiInjection($_POST['ano']);
		
	if($nome <> ''){
		$condicao = "AND UPPER(NOT_TITULO) LIKE UPPER ('%".$nome."%')";
	}else{
		$condicao = "AND NOT_TITULO IS NOT NULL";
	}
	
	
	if($mes <> '')
	{
		$condicao1 = "AND NOT_MESES = '".$mes."'";
	}else{
		$condicao1 = "AND NOT_MESES IS NOT NULL";
	}	
	
	
	$sql = new Query ($bd);
	$txt = "SELECT NOT_SEQUE,NOT_TITULO,NOT_MANCH,NOT_FONTE,NOT_DATAS,NOT_HORAS,NOT_FOTOS FROM SITE_NOTICIA
			WHERE NOT_ANOSS = '".$ano."'
			  ".$condicao1."
			  ".$condicao."
			  ORDER BY NOT_SEQUE DESC";
	$sql->executeQuery($txt);
    
	
	while(! $sql->eof()){
	  $tpl->TITULO_FOTO 		= '';
	  $tpl->ID_NOTI			= $sql->result("NOT_SEQUE");
	  $tpl->ID_NOTI2		= $sql->result("NOT_SEQUE");
	  $seqfotos				= $sql->result("NOT_SEQUE");
	  $imagens				= $sql->result("NOT_FOTOS");
	  $tpl->TITULO_FOTO 	.='<img src="../uploads/noticias/'.$imagens.'" width="200px" height="160px" />&nbsp;';
	  $tpl->TITULO_NOTICIA	= utf8_encode($sql->result("NOT_TITULO"));
	  $tpl->TITULO_MANCHETE	= utf8_encode($sql->result("NOT_MANCH"));
	  $tpl->TITULO_FONTE	= utf8_encode($sql->result("NOT_FONTE"));
	  $tpl->HORAS			= $sql->result("NOT_HORAS");
	  $tpl->TITULO_DATA		= $sql->result("NOT_DATAS");
	  
	  $sql->next();
	  $tpl->block("RESULTADO_NOTICIA");
 }
		
	$tpl->show();
	$bd->close(); 
?>