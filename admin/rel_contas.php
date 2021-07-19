<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","rel_contas.html");
  
  
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
      $idmsg = $_GET['idMsg'];
      
      $id_sind = $_GET['idSind'];
      
      $tpl->DATA_INI = date('Y-m-d');
      
      $mes = date('m');
      $ano = date('Y');
      $dia_final = cal_days_in_month(CAL_GREGORIAN,$mes,$ano);
      $tpl->DATA_FIM = $ano.'-'.$mes.'-'.$dia_final;
      
      
      if (isset($_POST['filtrar'])) {
        
        $dataini = $_POST['dataini'];
        $datafim = $_POST['datafim'];
        
        
        $sql1 = new Query($bd);
        $txt1 = "SELECT NNUMEXTRA,
										NNUMEUSUA,
										DEBITO,
										CREDITO,
										DTRAEXTRA,
										NPATEXTRA,
										CTIPEXTRA,
										CTPOEXTRA,
										SEQUPAGPLAN,
										NNUMEUSUA1 
							FROM TREDE_EXTRATO_USUA
							WHERE DTRAEXTRA BETWEEN '".$dataini."' AND '".$datafim."' ";
        $sql1->executeQuery($txt1);
        
        
        while (!$sql1->eof()) {
          $tpl->NOMEUSUA = $func->RetornaNomeUsuarioSeq($sql1->result("NNUMEUSUA"));
          $tipo = $sql1->result("CTIPEXTRA");
          
          
          if ($tipo == 'a') {
            $tpl->TIPO = 'Adesão';
          } else if ($tipo == 'm') {
            $tpl->TIPO = 'Mensalidade';
          } else if ($tipo == 's') {
            $tpl->TIPO = 'Saque';
          }
          
          $tpl->DEBITO = number_format($sql1->result("DEBITO"),2,',','.');
          $tpl->CREDITO = number_format($sql1->result("CREDITO"),2,',','.');
          $tpl->DATAS = $data->formataData1($sql1->result("DTRAEXTRA"));
          
          $tpl->block("CONTAS");
          $sql1->next();
        }
      }
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  $tpl->show();
  $bd->close();
?>