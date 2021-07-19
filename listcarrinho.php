<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	$bd = new Database();

	$idcart	= 	$seg->antiInjection($_POST['idcart']);
	$idloja	= 	$seg->antiInjection($_POST['idloja']);
	
	//$idcart	= 	$_GET['idcart'];
	//$idloja	= 	$_GET['idloja'];
	
	$sql = new Query ($bd);
	$txt = "SELECT  NSEQUECARR,
					NSEQUPRODU,
					SEQUENCIACRE,
					VIDCARCARR,
					NVALORCARR,
					NQUATICARR,
					VNOMEPCARR
			   FROM TREDE_CARRINHO
		      WHERE SEQUENCIACRE = :idloja 
				AND VIDCARCARR = :idcart";
	$sql->addParam(':idloja',$idloja);
	$sql->addParam(':idcart',$idcart);
	$sql->executeQuery($txt);
    
	
		//$eventos['nome']		= ucwords(utf8_encode($sql->result("VNOMEPCARR")));
		//$eventos['valor']		= $sql->result("NVALORCARR");
		
		$nome		= ucwords(utf8_encode($sql->result("VNOMEPCARR")));
		$valor		= $sql->result("NVALORCARR");
		$contador	= $sql->count();
		$eventos['cont'] = $contador;
	
	while(!$sql->eof()){
		$eventos['nome']		= ucwords(utf8_encode($sql->result("VNOMEPCARR")));
		$eventos['valor']		= $sql->result("NVALORCARR");
		
	$sql->next();	
	}
	
		/*$eventos[] = [
			'nome' 		=> $nome,
			'valor'  	=> $valor,
			];*/
			
echo json_encode($eventos);

$bd->close();
?>