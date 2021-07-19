<?php
require_once("comum/autoload.php");
session_start();
ob_start();

$bd = new Database();

$sql = new Query($bd);
$sql->clear();
$txt = "SELECT LAST_INSERT_ID(SEQ), NOME 
			FROM TREDE_CONTRATOS
			ORDER BY 1 DESC
			LIMIT 1";
$sql->executeQuery($txt);

$filename = $sql->result('NOME');

$file = '../admin/uploads/contrato/'.$filename ;

header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="' .$filename. '"');


readfile($file);
ob_end_flush();
?>