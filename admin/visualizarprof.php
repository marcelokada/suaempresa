<?php
  
  require_once("comum/autoload.php");
  
  $bd = new Database();
  
  $id = $_GET['id'];
  
  $sql1 = new Query ($bd);
  $txt1 = "SELECT NNUMEPROF,CNOMEPROF,NCATEPROF
                 FROM TREDE_PROFISSAO
				        WHERE NNUMEPROF = :id";
  $sql1->addParam(':id',$id);
  $sql1->executeQuery($txt1);
  
  
  $eventos['nnumeprof'] = $sql1->result("NNUMEPROF");
  $eventos['cnomeprof'] = $sql1->result("CNOMEPROF");
  $eventos['ncateprof'] = $sql1->result("NCATEPROF");
  
  $ncateprof = $sql1->result("NCATEPROF");
  
  $sql = new Query ($bd);
  $txt = "SELECT NNUMECATECAT,VNOMECATECAT
                 FROM TREDE_CATEGORIAS
				        WHERE NNUMECATECAT = :ncateprof";
  $sql->addParam(':ncateprof',$ncateprof);
  $sql->executeQuery($txt);
  
  $eventos['nocatprof'] = utf8_encode(strtoupper($sql->result("VNOMECATECAT")));
  
  echo json_encode($eventos);
  
  $bd->close();
?>