<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  error_reporting(0);
  
  $bd = new Database();
  
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","naoassinantes.html");
  
  if (isset($_SESSION['aut_admin'])) {
    $autenticado = TRUE;
    $_SESSION['aut_admin'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      $id_sessao = $_SESSION['idSessao_admin'];
      $id_confer = $_GET['idSessao'];
      $id_admin = $_SESSION['usuaAdmin'];
      
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao_admin'];
      $tpl->ID_ADMIN = $_SESSION['usuaAdmin'];
      
      
      $msg = $_GET['msg'];
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT REDE_NOMEUSU,REDE_SEQUSUA,REDE_EMAILUS
				FROM TREDE_USUADMIN
				WHERE  REDE_PLANUSU = 'c'";
      $sql1->executeQuery($txt1);
      
      while (!$sql1->eof()) {
        $tpl->ID = $sql1->result("REDE_SEQUSUA");
        $idusuario = $sql1->result("REDE_SEQUSUA");
        $tpl->NOME = ucwords(utf8_encode($func->RetonaNomeUsuarioPorSeq($bd,$idusuario)));
        $tpl->STATUS = "Não assinantes";
        
        $tpl->block("PLANO");
        $sql1->next();
      }
      
      $tpl->ID_SESSAO = $_GET['idSessao'];
      //$tpl->ID_ADMIN 	= $_SESSION['admin'];
  
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
    }

  
  $tpl->show();
  $bd->close();
?>