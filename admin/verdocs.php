<?php
  
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  
  $bd = new Database();
  
  $id = $_GET['id'];
  
  $sql = new Query ($bd);
  $txt = "SELECT NNUMEDOC,
                     CNOMEDOC,
                     ARQNODOC,
                     NNUMECRE
            FROM TREDE_DOCS_CRED
            WHERE NNUMEDOC = '".$id."' ";
  $sql->executeQuery($txt);
  
  $eventos['url'] = $sql->result("ARQNODOC");
  
  echo json_encode($eventos);
  
  $bd->close();
?>