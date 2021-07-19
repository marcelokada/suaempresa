<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","comum/index.html");
  
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
      
      $tpl->ID_SESSAO = $_SESSION['idSessao_admin'];
      $tpl->ID_ADMIN = $_SESSION['usuaAdmin'];
      
      $nivelusua = $func->RetornaPermissoes_Admin($id_admin);
  
  
      if (EMPRESA != 'MimoClube') {
        
        $sql_rede1 = new Query($bd);
        $txt_rede1 = "SELECT IFNULL(COUNT(*),0) QTDE_REDE FROM TREDE_CREDENCIADOS WHERE CSITUACAOCRE = 'p'";
        $sql_rede1->executeQuery($txt_rede1);
  
        $tpl->TOTAL_AUT_CRED = $sql_rede1->result("QTDE_REDE");
        
      $tpl->block("EMPRESAS_MIMOS");
      }
      
      
      //caixa amarela
      $sqlP1 = new Query($bd);
      $txtP1 = "SELECT IFNULL(COUNT(*),0) QTDE FROM TREDE_PAGAPLANO
                WHERE CSITUAPPLAN = 'p'";
      $sqlP1->executeQuery($txtP1);
      $tpl->TOTAL_AUT = $sqlP1->result('QTDE');
  
      //caixa verde
      $sqlP2 = new Query($bd);
      $txtP2 = "SELECT IFNULL(COUNT(*),0) QTDE_REDE FROM TREDE_CREDENCIADOS
                WHERE CSITUACAOCRE = 'a'
                ";
      $sqlP2->executeQuery($txtP2);
      $tpl->QTDE_REDE = $sqlP2->result('QTDE_REDE');
  
      $sqlP2->clear();
      $txtP2 = "SELECT TEXTO FROM TREDE_CONFIG_BASICS
                WHERE TIPOCONFIG = 'rede_cred'
                AND EMPRESA = '".EMPRESA."'  ";
      $sqlP2->executeQuery($txtP2);
      $tpl->REDE_NOME = ucwords(utf8_encode($sqlP2->result('TEXTO')));
      
      //caixa azul celeste
      $sqlP3 = new Query($bd);
      $txtP3 = "SELECT IFNULL(COUNT(*),0) QTDE FROM TREDE_USUADMIN
                WHERE REDE_ADMINUS = 'n'
                AND REDE_TIPOUSU = 3";
      $sqlP3->executeQuery($txtP3);
      $tpl->TOTAL_USUA = $sqlP3->result('QTDE');

      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
    
  }
  
  $tpl->show();
  $bd->close();
?>