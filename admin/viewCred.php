<?php
  require_once("comum/autoload.php");
  //error_reporting(0);
  
  $seg  = new Seguranca();
  $bd   = new Database();
  $func = new Funcao();
  
  $id = $seg->antiInjection($_POST['id']);
  
  $sql = new Query ();
  $txt = "SELECT SEQUENCIACRE FROM TREDE_CREDENCIADOS
				 WHERE SEQUENCIACRE = :id";
  $sql->AddParam(':id',$id);
  $sql->executeQuery($txt);
  
  $sql1 = new Query ();
  $txt1 = "SELECT VALCREDREDE FROM TREDE_CREDITOREDE
				 WHERE SEQUENCIACRE = :id";
  $sql1->AddParam(':id',$id);
  $sql1->executeQuery($txt1);
  
  $json['nomecred'] = $func->RetornaNomeEmpresa($bd,$sql->result("SEQUENCIACRE"));
  $json['idcred']   = $sql->result("SEQUENCIACRE");
  
  if($sql1->result("VALCREDREDE") == ""){
    $json['valor']    = '0.00';
  }else{
    $json['valor']    = $sql1->result("VALCREDREDE");
  }
  
  
  echo json_encode($json);
  
  $bd->close();
?>