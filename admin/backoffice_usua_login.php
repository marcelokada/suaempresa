<?php
  require_once("comum/autoload.php");
  
  $seq       = $_POST['sequsua'];
  
  $aleatorio = mt_rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
  $valor     = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$aleatorio);
  
  $_SESSION['idSessao']  = $valor;
  $_SESSION['idUsuario'] = $seq;
  $_SESSION['aut']       = TRUE;
  $_COOKIE['idUsuario']  = $seq;
  
  $sess['valor'] = $valor;
  
  echo json_encode($sess);
?>