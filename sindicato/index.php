<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  $bd   = new Database();
  $func = new Funcao();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","comum/index.html");
  
  if (isset($_SESSION['aut_sind'])) {
    $autenticado          = TRUE;
    $_SESSION['aut_sind'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      $id_sessao_c    = $_GET['idSessao'];
      $id_sessao_s    = $_SESSION['idSessao_sind'];
      $idrede         = $_SESSION['idSind'];
      $tpl->ID_SESSAO = $_SESSION['idSessao_sind'];
      $tpl->ID_SIND   = $_SESSION['idSind'];
      
      $seg->verificaSession($id_sessao_s);
      
    }
  }else {
    $seg->verificaSession($_SESSION['aut_sind']);
  }
  $tpl->show();
  $bd->close();
?>