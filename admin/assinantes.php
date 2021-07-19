<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  $func = new Funcao();
  $data = new Data();
  
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","assinantes.html");
  
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
      
      $seg->verificaSession($id_sessao);
      
      if (isset($_GET['idmsg'])) {
        $msg = $_GET['msg'];
      }
      $sql1 = new Query($bd);
      $txt1 = "SELECT NIDUPAGPLAN,
					NSEQPAGPLAN,
					CTEMPAGPLAN,
					DDTINIPPLAN,
					DDTFIMPPLAN,
       CSITPAGPLAN
				FROM TREDE_PAGAPLANO
				WHERE  '".date('Y-m-d')."' <= DDTFIMPPLAN
				AND CSITUAPPLAN = 'a'";
      $sql1->executeQuery($txt1);
      
      while (!$sql1->eof()) {
        $tpl->ID = $sql1->result("NIDUPAGPLAN");
        $idusuario = $sql1->result("NIDUPAGPLAN");
        $tpl->NOME = ucwords(utf8_encode($func->RetonaNomeUsuarioPorSeq($bd,$idusuario)));
        //$tpl->PAGMTO 	= $func->RetornaSituaPagamento($sql1->result("NSEQPAGPLAN"));
        $tpl->PAGMTO = $func->RetornaSituaPagamento($sql1->result("CSITPAGPLAN"));
        $tpl->TEMPO = $sql1->result("CTEMPAGPLAN");
        $tpl->DATAINI = $data->formataData1($sql1->result("DDTINIPPLAN"));
        $tpl->DATAFIM = $data->formataData1($sql1->result("DDTFIMPPLAN"));
        
        $tpl->block("PLANO");
        $sql1->next();
      }
      
      $tpl->ID_SESSAO = $_GET['idSessao'];
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  $tpl->show();
  $bd->close();
?>