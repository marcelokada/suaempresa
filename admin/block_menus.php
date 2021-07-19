<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  error_reporting(0);
  
  $bd = new Database();
  $func = new Funcao();
  $seg = new Seguranca();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","block_menus.html");
  
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
      
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT SEQPERMIS,MENUS,SITUACAO,DESCR
					FROM TREDE_PERMISSAO 
					ORDER BY 1";
      $sql1->executeQuery($txt1);
      
      
      while (!$sql1->eof()) {
        $tpl->ID = $sql1->result("SEQPERMIS");
        $tpl->DESC = utf8_encode($sql1->result("DESCR"));
        $seq = $sql1->result("SEQPERMIS");
        $tpl->NOME = ucwords(utf8_encode($sql1->result("MENUS")));
        
        $situa = $sql1->result("SITUACAO");
        
        
        if ($situa == '0') {
          $tpl->TIPOS = 'OK';
          $tpl->SITUACAO = "<font color='red'>Desativado</font>";
          $tpl->TITU = "Ativar";
          
        } else {
          $tpl->TIPOS = 'No';
          $tpl->SITUACAO = "<font color='green'> Ativo</font>";
          $tpl->TITU = "Desativar";
          
        }
        $sql1->next();
        $tpl->block("MENUS");
      }
      
    }
  } else {
    $seg->verificaSession($_SESSION['aut_admin']);
  }
  
  
  $tpl->show();
  $bd->close();
?>