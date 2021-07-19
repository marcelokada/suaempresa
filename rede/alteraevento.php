<?php
  
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  //error_reporting(0);
  
  $bd = new Database();
  
  $id     = $seg->antiInjection($_POST['idev']);
  $tipovg = $seg->antiInjection($_POST['tipovg']);
  
  if ($tipovg == 'S') {
    $sql = new Query($bd);
    $txt = "UPDATE TREDE_TIPO_EVENTO SET CSTATEVENT = 'a'
                WHERE NNUMEEVENT = '".$id."'";
    $sql->executeSQL($txt);
  } else {
    $sql = new Query($bd);
    $txt = "UPDATE TREDE_TIPO_EVENTO SET CSTATEVENT = 'c'
                WHERE NNUMEEVENT = '".$id."'";
    $sql->executeSQL($txt);
  }
  
  
  $bd->close();
?>