<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$id		= 	$seg->antiInjection($_POST['id']);

$sql = new Query ($bd);
$txt = "UPDATE TREDE_CREDENCIADOS SET CSITUACAOCRE = :status
				 WHERE SEQUENCIACRE = :id";
$sql->AddParam(':id',$id);
$sql->AddParam(':status',$status);
$sql->executeSQL($txt);


if($status == 'a'){
    echo "Rede Ativada com sucesso!";
}else{
    echo "Rede inativada com sucesso!";
}




$bd->close();
?>