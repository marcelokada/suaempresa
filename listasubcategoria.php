<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  //error_reporting(0);
  
  $tpl = new Template("listasubcategoria.html");
  
  $bd = new Database();
  
  $id = $seg->antiInjection($_POST['id']);
  
  $sql = new Query ($bd);
  $txt = "SELECT NNUMECATESUB,VNOMECATESUB,NNUMECATECAT,VSITUCATESUB
			  FROM TREDE_SUBCATEGORIA
			 WHERE NNUMECATECAT = :id
			 ORDER BY VNOMECATESUB ";
  $sql->AddParam(':id',$id);
  $sql->executeQuery($txt);
  
  while (!$sql->eof()) {
    $tpl->SCAT_NUM  = $sql->result("NNUMECATESUB");
    $tpl->SCAT_NOME = ucwords(utf8_encode($sql->result("VNOMECATESUB")));
    $tpl->block("SUBCATE");
    $sql->next();
  }
   
  
  $tpl->show();
  $bd->close();
?>