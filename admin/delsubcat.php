<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	$bd = new Database();
	
	$id		= 	$seg->antiInjection($_POST['id']);

	$sql = new Query ($bd);
	$txt = "SELECT SEQUENCIACRE FROM TREDE_CREDENCIADOS 
	WHERE NNUMECATESUB = :id";
	$sql->AddParam(':id',$id);
	$sql->executeQuery($txt);
	
	$res_id = $sql->count();
	
	if($res_id > 0){
		echo "Subcategoria não pode ser excluida, pois existe outros que dependem dele!";	
	}else{
	
		$sql1 = new Query ($bd);
		$txt1 = "DELETE FROM TREDE_SUBCATEGORIA 
		WHERE NNUMECATESUB = :id";
		$sql1->AddParam(':id',$id);
		$sql1->executeSQL($txt1);
		
		echo "Subcategoria removida com sucesso!";
   
	}
$bd->close();
?>