<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  error_reporting(0);
  
  $bd = new Database();
  
  $id_sessao = $_SESSION['idSessao'];
  $id_confer = $_GET['idSessao'];
  
  $id_admin = $_SESSION['admin'];
  
  $seg->verificaSession($id_sessao);
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","juros_boleto.html");
  $tpl->ID_SESSAO = $_GET['idSessao'];
  //$tpl->ID_ADMIN 	= $_SESSION['admin'];
  
  $sql1aa = new Query ($bd);
  $txt1aa = "SELECT JUROS_VENC,JUROS_DIA FROM TREDE_JUROS_BOL";
  $sql1aa->executeQuery($txt1aa);
  
  $tpl->JUROSV = $sql1aa->result("JUROS_VENC");
  $tpl->JUROSD = $sql1aa->result("JUROS_DIA");
  
  if (isset($_POST['alterar'])) {
    
    $juros_venc  = $seg->antiInjection($_POST['juros_venc']);
    $juros_dias  = $seg->antiInjection($_POST['juros_dias']);
      
      $sql = new Query();
      $txt = "UPDATE TREDE_JUROS_BOL SET JUROS_VENC = '".$juros_venc."', JUROS_DIA  = '".$juros_dias."'";
      $sql->executeSQL($txt);
      
      $tpl->MSG = "Alteração realizada com Sucesso!";
      $tpl->block("SUCESSO");
      
      echo "<script>setTimeout(function () {window.location.href = window.location.href;}, 3000);</script>";
      
  }
  
  
  $tpl->show();
  $bd->close();
?>