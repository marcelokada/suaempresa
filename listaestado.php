<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	$tpl = new Template("listaestado.html");

	$bd = new Database();

	$idregiao	= 	$seg->antiInjection($_POST['idregiao']);
	
	$sql = new Query ($bd);
	$txt = "SELECT VNOMEESTAEST,CESTADOUFEST,NNUMEREGIREG FROM TREDE_ESTADO
			 WHERE NNUMEREGIREG = :id_regiao";
	$sql->AddParam(':id_regiao',$idregiao);
	$sql->executeQuery($txt);
    
	while(!$sql->eof()){
		$tpl->ID_ESTADO 		= $sql->result("CESTADOUFEST");
		$tpl->NOME_ESTADO 	= utf8_encode($sql->result("VNOMEESTAEST"));
		$tpl->block("ESTADO");
		$sql->next();
	}
	
$tpl->show();
$bd->close();
?>