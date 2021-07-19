<?php
require_once("comum/autoload.php");
session_start();
ob_start();

$bd = new Database();

$idpag = $_GET['idpag'];

$sql = new Query($bd);
$sql->clear();
$txt = "SELECT CNOMCOPPLAN,CTIPOTRPLAN
			FROM TREDE_PAGAPLANO
			WHERE IDPGSEGPLAN = '".$idpag."'";
$sql->executeQuery($txt);

$tipo = $sql->result('CTIPOTRPLAN');
$filename = $sql->result('CNOMCOPPLAN');

if($tipo == 'a'){
	$file = 'comprovantes/adesao/'.$filename ;
}else if ($tipo == 'm'){
	$file = 'comprovantes/mensalidade/'.$filename ;
}

$ext = $func->extensao($filename);

if($ext == 'pdf'){
	header('Content-type: application/pdf');
	header('Content-Disposition: inline; filename="' .$filename. '"');
	readfile($file);
	ob_end_flush();
}else{
	echo '<center><img color="black" src='.$file.'></center>';
}




?>