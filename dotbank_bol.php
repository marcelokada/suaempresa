<?php
  require_once("comum/autoload.php");
  session_start();
  error_reporting(0);
  
  $bd = new Database();
  
  $idpplano = $_POST['planos'];
  
  $id = explode('p',$idpplano);
  
  $idplan = $id[2];
  
  $id_dotbank = $_POST['dots'];
  
  $link_boleto = $_POST['boleto'];
  
  $carne = $_POST['carne'];
  $due_date = $_POST['due_date'];
  
  /*$sql = new Query($bd);
  $txt = "INSERT INTO TESTE (TESTE,TESTE1) VALUES ('".$idpplano."','".$idplan."')";
  $sql->executeSQL($txt);*/
  
  
  $sql = new Query($bd);
  $txt = "UPDATE TREDE_PAGAPLANO SET  NIDCODIPLAN = '".$id_dotbank."',
                                       LINKBOLETO = '".$link_boleto."',
                                        LINKCARNE = '".$carne."',
                                      DVENCBOPLAN = '".$due_date."'
        WHERE SEQUPAGPLAN = '".$idplan."'";
  $sql->executeSQL($txt);
  
  $bd->close();
?>