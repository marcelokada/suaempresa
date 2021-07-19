<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	$bd = new Database();
	
	$id		= 	$seg->antiInjection($_POST['id']);
	$status	= 	$_GET['status'];

	if($status == 'd'){
		$sql = new Query ($bd);
		$txt = "DELETE FROM TREDE_PRODUTOS
				 WHERE NSEQUPRODU = :id";
		$sql->AddParam(':id',$id);
		$sql->executeSQL($txt);
		
		echo "Produto Deletado com sucesso!";
		
	}else{
	
		$sql = new Query ($bd);
		$txt = "UPDATE TREDE_PRODUTOS SET CSITUPRODU = :status
				 WHERE NSEQUPRODU = :id";
		$sql->AddParam(':id',$id);
		$sql->AddParam(':status',$status);
		$sql->executeSQL($txt);
		
		
		if($status == 'a'){
			echo "Produto Ativado com sucesso!";
		}else if ($status == 'c'){
			echo "Produto Desativado com sucesso!";
		}
	}    

$bd->close();
?>