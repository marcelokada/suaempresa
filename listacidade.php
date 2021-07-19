<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	$tpl = new Template("listacidade.html");

	$bd = new Database();

	$idestado	= 	$seg->antiInjection($_POST['idestado']);
	
	$sql = new Query ($bd);
	$txt = "SELECT NNUMEIBGEMUN,VNOMECIDAMUN,CESTADOUFEST 
				  FROM TREDE_MUNICIPIO
			   WHERE CESTADOUFEST = :id_estado";
	$sql->AddParam(':id_estado',$idestado);
	$sql->executeQuery($txt);
    
	while(!$sql->eof()){
		$tpl->ID_CIDADE 	= $sql->result("NNUMEIBGEMUN");
		$tpl->NOME_CIDADE  	= utf8_encode($sql->result("VNOMECIDAMUN"));
		$tpl->block("CIDADE");
		$sql->next();
	}
	
$tpl->show();
$bd->close();
?>