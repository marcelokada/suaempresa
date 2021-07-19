<?php
  require_once("comum/autoload.php");
  session_start();
  error_reporting(0);
  
  $bd = new Database();
  
  $id_dotbank = $_POST['dots'];
  
  $sql = new Query($bd);
  $txt = "DELETE FROM TREDE_PAGAPLANO
        WHERE IDPGSEGPLAN = '".$idplan."'";
  $sql->executeSQL($txt);
  
  $bd->close();
?>