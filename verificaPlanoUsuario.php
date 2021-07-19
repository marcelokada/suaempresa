<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  //error_reporting(0);
  
  //$tpl = new Template("modal.html");
  
  $bd = new Database();
  
  $id = $seg->antiInjection($_POST['idusua']);
  
  $sql1 = new Query ($bd);
  $txt1 = "SELECT LAST_INSERT_ID(SEQUPAGPLAN) SEQUPAGPLAN,CSITPAGPLAN,CSITUAPPLAN,CSICARNPLAN
			    FROM TREDE_PAGAPLANO
				WHERE NIDUPAGPLAN = :id
				AND CSITPAGPLAN IN ('1','3')
				ORDER BY 1 ASC
				LIMIT 1";
  $sql1->AddParam(':id',$id);
  $sql1->executeQuery($txt1);
  
  $res =  $sql1->result("SEQUPAGPLAN");
  $res1 =  $sql1->result("CSICARNPLAN");

  if($res1 == 's'){
    $sql = new Query ($bd);
    $txt = "SELECT LAST_INSERT_ID(SEQUPAGPLAN) SEQUPAGPLAN,CSITPAGPLAN,CSITUAPPLAN
			    FROM TREDE_PAGAPLANO
				WHERE NIDUPAGPLAN = :id
	        AND CSITPAGPLAN IN ('1','3')
				ORDER BY 1 ASC
				LIMIT 1";
    $sql->AddParam(':id',$id);
    $sql->executeQuery($txt);
    
    $eventos['id'] = $sql->result("SEQUPAGPLAN");
    $eventos['situplan'] = $sql->result("CSITPAGPLAN");
    $eventos['situpagp'] = $sql->result("CSITUAPPLAN");
  }else if($res1 == 'n'){
    $sql = new Query ($bd);
    $txt = "SELECT LAST_INSERT_ID(SEQUPAGPLAN) SEQUPAGPLAN,CSITPAGPLAN,CSITUAPPLAN
			    FROM TREDE_PAGAPLANO
				WHERE NIDUPAGPLAN = :id
				ORDER BY 1 DESC
				LIMIT 1";
    $sql->AddParam(':id',$id);
    $sql->executeQuery($txt);
  
    $eventos['id'] = $sql->result("SEQUPAGPLAN");
    $eventos['situplan'] = $sql->result("CSITPAGPLAN");
    $eventos['situpagp'] = $sql->result("CSITUAPPLAN");
  }else{
    $sql = new Query ($bd);
    $txt = "SELECT LAST_INSERT_ID(SEQUPAGPLAN) SEQUPAGPLAN,CSITPAGPLAN,CSITUAPPLAN
			    FROM TREDE_PAGAPLANO
				WHERE NIDUPAGPLAN = :id
	        AND CSITPAGPLAN IN ('1','3')
				ORDER BY 1 ASC
				LIMIT 1";
    $sql->AddParam(':id',$id);
    $sql->executeQuery($txt);
  
    $eventos['id'] = $sql->result("SEQUPAGPLAN");
    $eventos['situplan'] = $sql->result("CSITPAGPLAN");
    $eventos['situpagp'] = $sql->result("CSITUAPPLAN");
  }
  

  
  echo json_encode($eventos);
  
  $bd->close();
?>