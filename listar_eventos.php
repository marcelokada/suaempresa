<?php
  
  require_once("comum/autoload.php");
  session_start();
  //error_reporting(0);
  
  $bd = new Database();
  
  $idloja = $_GET['idloja'];
  
  $sql = new Query ($bd);
  $txt = "SELECT NNUMECALE,
                 CNOMECALE,
                 DINI_CALE,
                 DFIM_CALE,
                 SEQUENCIACRE,
                 REDE_SEQUSUA,
                 CSTATCALE,
                 CCOR_CALE,
                 CSITUCALE,
                 CTIPOCALE
            FROM TREDE_CALENDARIO_CRED
			 WHERE SEQUENCIACRE = '".$idloja."'
			 AND CSTATCALE = '1'";
  $sql->executeQuery($txt);
  
  
  while (!$sql->eof()) {
    
    $eventos[] = [
      "id"       => $sql->result("NNUMECALE"),
      "title"    => utf8_encode($func->RetornaTipoEvento($sql->result("CTIPOCALE"))),
      "start"    => $sql->result("DINI_CALE"),
      "end"      => $sql->result("DFIM_CALE"),
      "status"   => $sql->result("CSTATCALE"),
      "situacao" => $sql->result("CSITUCALE"),
      "color"    => $sql->result("CCOR_CALE"),
      "rede"     => $sql->result("SEQUENCIACRE"),
    ];
    
    $sql->next();
  }
  
  
  echo json_encode($eventos);
  
  $bd->close();
?>