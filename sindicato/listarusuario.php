<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
error_reporting(0);

$bd = new Database();

$id = $_POST['id'];

$sql1 = new Query($bd);
$txt1 = "SELECT REDE_SEQUSUA,
									REDE_NOMEUSU,
									REDE_CPFUSUA,
									REDE_EMAILUS,
									REDE_DNASCUS,
									REDE_LOGUSUA,
									REDE_LOGBLOK		  
		  FROM TREDE_USUADMIN 
		  WHERE REDE_SEQUSUA = :id";
$sql1->addParam(':id',$id);
$sql1->executeQuery($txt1);

$eventos = utf8_encode($sql1->result("REDE_NOMEUSU"));

echo $eventos;

$bd->close();
?>