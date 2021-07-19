<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$id		= 	$seg->antiInjection($_POST['idpct']);
$situa		= 	$seg->antiInjection($_POST['situa']);

if($situa == 'c'){
    $tipo = 'a';
}else{
    $tipo = 'c';
}


    $sql1 = new Query ($bd);
    $txt1 = "UPDATE TREDE_PACOTES_REDE SET CSITUPAC = :situa
		WHERE NNUMEPAC = :id";
    $sql1->AddParam(':situa',$tipo);
    $sql1->AddParam(':id',$id);
    $sql1->executeSQL($txt1);

    echo "Alterado com sucesso!";

$bd->close();
?>
