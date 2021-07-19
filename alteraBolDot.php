<?php
  require_once("comum/autoload.php");
  session_start();
  //error_reporting(0);
  
  $bd = new Database();
  
  $idPedido_old = $_POST['idPedido'];
  $idPedido_new = $_POST['idPedido_new'];
  $datavenc = $_POST['datavenc'];
  
  /*$sql = new Query($bd);
  $txt = "INSERT INTO TESTE (TESTE,TESTE1) VALUES ('".$idpplano."','".$idplan."')";
  $sql->executeSQL($txt);*/
  
  $sql1a1 = new Query ($bd);
  $txt1a1 = "UPDATE TREDE_PAGAPLANO SET CSTABOLPLAN = 'c'
                WHERE IDPGSEGPLAN = '".$idPedido_old."'";
  $sql1a1->executeSQL($txt1a1);
  
  $sql1a2 = new Query ($bd);
  $txt1a2 = "UPDATE TREDE_PAGAPLANO SET DVENCBOPLAN = '".date('Y-m-d')."'
                WHERE IDPGSEGPLAN = '".$idPedido_new."'";
  $sql1a2->executeSQL($txt1a2);
  
  
  $bd->close();
?>