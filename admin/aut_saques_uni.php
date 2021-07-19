<?php
  require_once("comum/autoload.php");
  if(!isset($_SESSION)){
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","aut_saques_uni.html");
  
  
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
      
      
      $nivelusua = $func->RetornaPermissoes_Admin($id_admin);
      
      if ($nivelusua == 'C') {
        $tpl->DISABLE = "style='display:none;'";
      } else if ($nivelusua == 'CB') {
        $tpl->DISABLE = "style='display:none;'";
      } else if ($nivelusua == 'CCAB') {
        $tpl->DISABLE = "style='display:none;'";
      }
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT SEQUENCIA,REDE_SEQUSUA,VALORSAQUE,DATASAQUE,DATAPAGO,SITUSAQUE
			FROM TREDE_SOLICITASAQUE_UNI
            WHERE SITUSAQUE = 1
			ORDER BY SEQUENCIA DESC";
      $sql1->executeQuery($txt1);
      
      while (!$sql1->eof()) {
        $tpl->ID = $sql1->result("SEQUENCIA");
        $tpl->ID_USUARIO = $sql1->result("REDE_SEQUSUA");
        $tpl->NOME = ucwords(utf8_encode($func->RetonaNomeUsuarioPorSeq1($bd,$sql1->result("REDE_SEQUSUA"))));
        $seq2 = $sql1->result("REDE_SEQUSUA");
        $tpl->VALORSAQUE_F = 'R$ '.number_format($sql1->result("VALORSAQUE"),2,',','.');
        $tpl->VALORSAQUE_N = $sql1->result("VALORSAQUE");
        $tpl->DATASAQUE = $data->formataData1($sql1->result("DATASAQUE")).' '.substr($sql1->result("DATASAQUE"),10,10);
        $tpl->DATAPAGO = $sql1->result("DATAPAGO");
        //$tpl->SITUSAQUE = $sql1->result("SITUSAQUE");
        $situplan = $sql1->result("SITUSAQUE");
        
        if ($situplan == '1') {
          $tpl->block("CANCELAR");
          $tpl->SITUSAQUE = 'Aguardando o Admin autorizar';
          $tpl->COR = "warning";
          $tpl->DISA = "";
        } else if ($situplan == '2') {
          $tpl->SITUSAQUE = 'Autorizado';
          $tpl->COR = "success";
          $tpl->block("CANCELAR");
          $tpl->DISA = "";
        } else if ($situplan == '9') {
          $tpl->SITUSAQUE = 'Cancelado';
          $tpl->COR = "danger";
          $tpl->DISA = "";
        }
        
        $sql1->next();
        $tpl->block("AUT");
      }
      
      $sql2 = new Query($bd);
      $txt2 = "SELECT SEQUENCIA,REDE_SEQUSUA,VALORSAQUE,DATASAQUE,DATAPAGO,SITUSAQUE
			FROM TREDE_SOLICITASAQUE_UNI
            WHERE SITUSAQUE = 2
			ORDER BY SEQUENCIA DESC";
      $sql2->executeQuery($txt2);
      
      while (!$sql2->eof()) {
        $tpl->ID1 = $sql2->result("SEQUENCIA");
        $tpl->NOME1 = ucwords(utf8_encode($func->RetonaNomeUsuarioPorSeq1($bd,$sql2->result("REDE_SEQUSUA"))));
        $seq2 = $sql2->result("REDE_SEQUSUA");
        $tpl->VALORSAQUE_F1 = 'R$ '.number_format($sql2->result("VALORSAQUE"),2,',','.');
        $tpl->VALORSAQUE_N1 = $sql2->result("VALORSAQUE");
        $tpl->DATASAQUE1 = $data->formataData1($sql2->result("DATASAQUE")).' '.substr($sql2->result("DATASAQUE"),10,10);
        $tpl->DATAPAGO1 = $sql2->result("DATAPAGO");
        $situplan = $sql2->result("SITUSAQUE");
        
        if ($situplan == '1') {
          $tpl->SITUSAQUE1 = 'Aguardando o Admin autorizar';
          $tpl->COR1 = "warning";
          $tpl->DISA1 = "";
        } else if ($situplan == '2') {
          $tpl->SITUSAQUE1 = 'Autorizado';
          $tpl->COR1 = "success";
          $tpl->DISA1 = "";
        } else if ($situplan == '9') {
          $tpl->SITUSAQUE1 = 'Cancelado';
          $tpl->COR1 = "danger";
          $tpl->DISA1 = "";
        }
        
        $sql2->next();
        $tpl->block("AUT1");
      }
      
      $sql3 = new Query($bd);
      $txt3 = "SELECT SEQUENCIA,REDE_SEQUSUA,VALORSAQUE,DATASAQUE,DATAPAGO,SITUSAQUE,DATACANCELADO
			FROM TREDE_SOLICITASAQUE_UNI
            WHERE SITUSAQUE = 9
			ORDER BY SEQUENCIA DESC";
      $sql3->executeQuery($txt3);
      
      while (!$sql3->eof()) {
        $tpl->ID2 = $sql3->result("SEQUENCIA");
        $tpl->NOME2 = ucwords(utf8_encode($func->RetonaNomeUsuarioPorSeq1($bd,$sql3->result("REDE_SEQUSUA"))));
        $seq2 = $sql3->result("REDE_SEQUSUA");
        $tpl->VALORSAQUE_F2 = 'R$ '.number_format($sql3->result("VALORSAQUE"),2,',','.');
        $tpl->VALORSAQUE_N2 = $sql3->result("VALORSAQUE");
        $tpl->DATASAQUE2 = $data->formataData1($sql3->result("DATASAQUE")).' '.substr($sql3->result("DATASAQUE"),10,10);
        $tpl->DATACANCEL2 = $data->formataData1($sql3->result("DATACANCELADO")).' '.substr($sql3->result("DATACANCELADO"),10,10);
        $situplan = $sql3->result("SITUSAQUE");
        
        if ($situplan == '1') {
          $tpl->SITUSAQUE2 = 'Aguardando o Admin autorizar';
          $tpl->COR2 = "warning";
        } else if ($situplan == '2') {
          $tpl->SITUSAQUE2 = 'Autorizado';
          $tpl->COR2 = "success";
        } else if ($situplan == '9') {
          $tpl->SITUSAQUE2 = 'Cancelado';
          $tpl->COR2 = "danger";
        }
        
        $sql3->next();
        $tpl->block("AUT2");
      }
      
      if (isset($_POST['auto'])) {
        
        
        $seq_soli = $_POST['auto'];
        $valorsoli = $_POST['valorsoli1'];
        $data = date('Y-m-d H:i:s');
        
        $usua = $_POST['usua'];
        
        
        $sql11aa = new Query($bd);
        $txt11aa = "SELECT VALORSAQUE FROM TREDE_SOLICITASAQUE_UNI WHERE SEQUENCIA = :seq";
        $sql11aa->addParam(':seq',$seq_soli);
        $sql11aa->executeQuery($txt11aa);
        
        $valor_saque = $sql11aa->result("VALORSAQUE");
        
        
        // sdebug('----');
        // sdebug('----');
        
        // sdebug($seq_soli);
        // sdebug($valorsoli);
        // sdebug($usua);
        
        $sql1 = new Query($bd);
        $txt1 = "UPDATE TREDE_SOLICITASAQUE_UNI SET SITUSAQUE = 2, DATAPAGO = '".$data."'
                WHERE SEQUENCIA = '".$seq_soli."' ";
        $sql1->executeSQL($txt1);
        
        
        $sql1n = new Query ($bd);
        $txt1n = "INSERT INTO TREDE_EXTRATO_USUA (NNUMEUSUA,DEBITO,CREDITO,DTRAEXTRA,NPATEXTRA,CTIPEXTRA,CTPOEXTRA)
	VALUES
	('".$seq2."','".$valor_saque."',0,'".date('Y-m-d H:i:s')."','".$usua."','s','D')";
        $sql1n->executeSQL($txt1n);
        
        
        echo "<script>alert('Autorizado com Sucesso')</script>";
        echo "<script>window.location.href = window.location.href</script>";
        //$util->redireciona("alterar_planusua.php?idSessao=" . $_SESSION['idSessao']);
        
      }
      
      if (isset($_POST['canc'])) {
        
        $seq_soli = $_POST['canc'];
        $valorsoli = $_POST['valorsoli1'];
        
        $data = date('Y-m-d H:i:s');
        
        $sql11aa = new Query($bd);
        $txt11aa = "SELECT VALORSAQUE FROM TREDE_SOLICITASAQUE_UNI WHERE SEQUENCIA = :seq";
        $sql11aa->addParam(':seq',$seq_soli);
        $sql11aa->executeQuery($txt11aa);
        
        $valor_saque = $sql11aa->result("VALORSAQUE");
        
        $sql1 = new Query($bd);
        $txt1 = "UPDATE TREDE_SOLICITASAQUE_UNI SET SITUSAQUE = 9, DATACANCELADO = '".$data."'
            WHERE SEQUENCIA = :seq";
        $sql1->addParam(':seq',$seq_soli);
        $sql1->executeSQL($txt1);
        
        $sql11 = new Query($bd);
        $txt11 = "SELECT REDE_SEQUSUA FROM TREDE_SOLICITASAQUE_UNI WHERE SEQUENCIA = :seq";
        $sql11->addParam(':seq',$seq_soli);
        $sql11->executeQuery($txt11);
        
        $sequsua = $sql11->result("REDE_SEQUSUA");
        
        
        $sql4 = new Query($bd);
        $txt4 = "SELECT VALORTOTAL FROM TREDE_ADESAO_MENSA_USU
                WHERE NIDUPAGPLAN = :seq";
        $sql4->addParam(':seq',$sequsua);
        $sql4->executeQuery($txt4);
        
        $res_bonus_usua = $sql4->result("VALORTOTAL");
        
        $total_bonus = $res_bonus_usua + $valor_saque;
        
        $sql3 = new Query($bd);
        $txt3 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '".$total_bonus."'
        WHERE NIDUPAGPLAN = :seq";
        $sql3->addParam(':seq',$sequsua);
        $sql3->executeSQL($txt3);
        
        
        $sql3a = new Query($bd);
        $txt3a = "SELECT LAST_INSERT_ID(NNUMEXTRA) NNUMEXTRA FROM TREDE_EXTRATO_USUA WHERE NNUMEUSUA = :seq";
        $sql3a->addParam(':seq',$sequsua);
        $sql3a->executeQuery($txt3a);
        
        $nnumextra = $sql3a->result("NNUMEXTRA");
        
        $sql3B = new Query($bd);
        $txt3B = "DELETE FROM TREDE_EXTRATO_USUA WHERE NNUMEXTRA = :extra";
        $sql3B->addParam(':extra',$nnumextra);
        $sql3B->executeSQL($txt3B);
        
        echo "<script>alert('Você cancelou a solicitação.')</script>";
        echo "<script>window.location.href = window.location.href</script>";
        //$util->redireciona("../principal.php?idSessao=" . $_SESSION['idSessao']);
      }
      
      if (isset($_POST['canc1'])) {
        
        $seq_soli = $_POST['canc1'];
        $valorsoli = $_POST['valorsoli2'];
        
        $sql11aa = new Query($bd);
        $txt11aa = "SELECT VALORSAQUE FROM TREDE_SOLICITASAQUE_UNI WHERE SEQUENCIA = :seq";
        $sql11aa->addParam(':seq',$seq_soli);
        $sql11aa->executeQuery($txt11aa);
        
        $valor_saque = $sql11aa->result("VALORSAQUE");
        
        $data = date('Y-m-d H:i:s');
        
        $sql1 = new Query($bd);
        $txt1 = "UPDATE TREDE_SOLICITASAQUE_UNI SET SITUSAQUE = 9, DATACANCELADO = '".$data."'
            WHERE SEQUENCIA = :seq ";
        $sql1->addParam(':seq',$seq_soli);
        $sql1->executeSQL($txt1);
        
        $sql11a = new Query($bd);
        $txt11a = "SELECT REDE_SEQUSUA FROM TREDE_SOLICITASAQUE_UNI WHERE SEQUENCIA = :seq";
        $sql11a->addParam(':seq',$seq_soli);
        $sql11a->executeQuery($txt11a);
        
        $sequsua = $sql11a->result("REDE_SEQUSUA");
        
        
        $sql2 = new Query($bd);
        $txt2 = "SELECT VALORTOTAL FROM TREDE_ADESAO_MENSA_USU
            WHERE NIDUPAGPLAN = :seq";
        $sql2->addParam(':seq',$sequsua);
        $sql2->executeQuery($txt2);
        
        $res_valor_bonus = $sql2->result("VALORTOTAL");
        
        $valor_total_addmen = $res_valor_bonus + $valor_saque;
        
        $sql3 = new Query($bd);
        $txt3 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '".$valor_total_addmen."'
            WHERE NIDUPAGPLAN = :seq";
        $sql3->addParam(':seq',$sequsua);
        $sql3->executeSQL($txt3);
        
        $sql3a = new Query($bd);
        $txt3a = "SELECT LAST_INSERT_ID(NNUMEXTRA) NNUMEXTRA FROM TREDE_EXTRATO_USUA WHERE NNUMEUSUA = :seq";
        $sql3a->addParam(':seq',$sequsua);
        $sql3a->executeQuery($txt3a);
        
        $nnumextra = $sql3a->result("NNUMEXTRA");
        
        $sql3B = new Query($bd);
        $txt3B = "DELETE FROM TREDE_EXTRATO_USUA WHERE NNUMEXTRA = :extra";
        $sql3B->addParam(':extra',$nnumextra);
        $sql3B->executeSQL($txt3B);
        
        echo "<script>alert('Você cancelou a solicitação.')</script>";
        echo "<script>window.location.href = window.location.href</script>";
        //$util->redireciona("../principal.php?idSessao=" . $_SESSION['idSessao']);
      }
      
      if (isset($_POST['volta'])) {
        
        $seq_soli = $_POST['volta'];
        $valorsoli = $_POST['valorsoli2'];
        
        $data = date('Y-m-d H:i:s');
        
        $sql11 = new Query($bd);
        $txt11 = "UPDATE TREDE_SOLICITASAQUE_UNI SET SITUSAQUE = '1', DATACANCELADO = NULL, DATAPAGO = NULL
            WHERE SEQUENCIA = '".$seq_soli."' ";
        //$sql11->addParam(':seq', $seq_soli);
        $sql11->executeSQL($txt11);
        
        echo "<script>window.location.href=window.location.href</script>";
        //$util->redireciona("../principal.php?idSessao=" . $_SESSION['idSessao']);
      }
  
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
    
  }
  
  $tpl->show();
  $bd->close();
?>