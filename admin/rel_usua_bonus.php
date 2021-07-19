<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","rel_usua_bonus.html");
  
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
      $txt1 = "SELECT SEQUPAGPLAN,
                NIDUPAGPLAN,
                NSEQPAGPLAN,
                NVALPAGPLAN,
                CSITPAGPLAN,
                CTIPOPGPLAN,
                DDATPAGPLAN,
                CSITUAPPLAN,    
                CTIPOTRPLAN,
                MENSAPLANO,
                CTIPOOPPLAN
			FROM TREDE_PAGAPLANO
			ORDER BY SEQUPAGPLAN DESC";
      $sql1->executeQuery($txt1);
      
      while (!$sql1->eof()) {
        $tpl->ID = $sql1->result("SEQUPAGPLAN");
        $seq_usua = $sql1->result("NIDUPAGPLAN");
        $tpl->USUA = $sql1->result("NIDUPAGPLAN");
        $tpl->NOME = ucwords($func->RetonaNomeUsuarioPorSeq1($bd,$sql1->result("NIDUPAGPLAN")));
        $tpl->PLANO = ucwords(utf8_encode($func->RetornaNomePlano($sql1->result("NSEQPAGPLAN"))));
        $tpl->DATA = $data->formataData1($sql1->result("DDATPAGPLAN"));
        
        $tipopag = $sql1->result("CTIPOTRPLAN");
        
        if ($tipopag == 'a') {
          $tpl->TIPO = 'Adesão';
          $tpl->VALORPLANO = $formata->formataNumero($sql1->result("NVALPAGPLAN"));
        } else if ($tipopag == 'm') {
          $tpl->TIPO = 'Mensalidade';
          $tpl->VALORPLANO = $formata->formataNumero($sql1->result("MENSAPLANO"));
        }
        
        $tpl->BONIFICACAO = $formata->formataNumero($func->RetornaValorTotalExtratoUsuario($seq_usua));
        
        $tpl->block("NIVEIS1");
        $sql1->next();
      }
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  $tpl->show();
  $bd->close();
?>