<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  //error_reporting(0);
  
  $bd = new Database();
  
  $email_login = strtolower(trim($seg->antiInjection($_POST['email'])));
  $senha_login = md5($seg->antiInjection($_POST['senha']));
  
  $sqlc = new Query ($bd);
  $txtc = "insert into teste(teste,teste1)
				values
				()";
  $sqlc->executeQuery($txtc);
  
  echo json_encode($eventos);
  
  $bd->close();
?>