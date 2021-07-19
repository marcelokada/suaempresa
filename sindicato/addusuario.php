<?php
  require_once("comum/autoload.php");
  session_start();
  error_reporting(0);
  
  $bd = new Database();
  
  $idusua = $_POST['idusua'];
  $idsind = $_POST['idsind'];
  
  $sql1aa = new Query ($bd);
  $txt1aa = "INSERT INTO TREDE_SINDICATO_USUA (NNUMESIND,
																								NNUMEUSUA,
																								CSITUSIUS,
																								DINCLSIUS)
									VALUES
									('".$idsind."','".$idusua."','p','".date('Y-m-d H:i:s')."')";
  $sql1aa->executeSQL($txt1aa);
  
  //echo "Solicitação Enviada com Sucecsso";
  
  $bd->close();
?>