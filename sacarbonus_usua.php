<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  error_reporting(0);
  
  $bd = new Database();
  $formatar = new Formata();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","sacarbonus_usua.html");
  
  if (isset($_SESSION['aut'])) {
    $autenticado = TRUE;
    $_SESSION['aut'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      
      $id_sessao = $_SESSION['idSessao'];
      $id_confer = $_GET['idSessao'];
      $seq = $_SESSION['idUsuario'];
      
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao'];
      $tpl->IDUSUA = $_SESSION['idUsuario'];
      
      $idusua = $_SESSION['idUsuario'];
      
      //SELECT PARA VERIFICAR O USUARIO
      $tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
      //SELECT PARA VERIFICAR O USUARIO
      
      //CASHBACK USUARIO
      $valortotal_cash = $func->RetornaValorCashBackUsuario($bd,$seq);
      $tpl->MEUCASH = $formata->formataNumero($valortotal_cash);
      
      $valortotal_bonus = $func->RetornaValorBonusUsuario($seq);
      $tpl->MEUBONUS = $formata->formataNumero($valortotal_bonus);
      
      $valortotal_voucher = $func->RetornaValorVoucherUsuario($seq);
      $tpl->MEUVOUCHER = $formata->formataNumero($valortotal_voucher);
      
      $tpl->MEUPLANO = $func->assinaturaUsuarioMimo($bd,$seq);
      
      //CASHBACK USUARIO
      
      
      $sql = new Query ($bd);
      $txt = "SELECT SEQUPAGPLAN,NSEQPAGPLAN,CSITPAGPLAN,CTIPOPGPLAN,DDTINIPPLAN,DDTFIMPPLAN
			 FROM TREDE_PAGAPLANO
			WHERE NIDUPAGPLAN = :idusua
			ORDER BY DDATPAGPLAN DESC";
      $sql->addParam(':idusua',$seq);
      $sql->executeQuery($txt);
      
      $res_data_inicio = $sql->result("DDTINIPPLAN");
      $res_data_final = $sql->result("DDTFIMPPLAN");
      
      $sql21 = new Query ($bd);
      $txt21 = "SELECT REDE_PLANUSU
			   FROM TREDE_USUADMIN
			  WHERE REDE_SEQUSUA = :usua";
      $sql21->AddParam(':usua',$seq);
      $sql21->executeQuery($txt21);
      
      $res_assi = $sql21->result("REDE_PLANUSU");
      
      
      while (!$sql->eof()) {
        
        $idpagplan = $sql->result("NSEQPAGPLAN");
        //$tpl->ID = $sql->result("SEQUPAGPLAN");
        
        $data_inicio = $sql->result("DDTINIPPLAN");
        $data_final = $sql->result("DDTFIMPPLAN");
        $data_atual = date('Y-m-d');
        
        /* if ($data_inicio == null or $data_final == null) {
             $vencida = "";
             $tpl->VALIDADE = "Em analise";
         } else if ($data_final < $data_atual) {
             $vencida = "<font color='red'>(Vencida)</font>";
             $tpl->VALIDADE = $data->formataData1($data_inicio) . ' - ' . $data->formataData1($data_final) . ' ' . $vencida;
         } else {
             $vencida = "";
             $tpl->VALIDADE = $data->formataData1($data_inicio) . ' - ' . $data->formataData1($data_final) . ' ' . $vencida;
         }*/
        $sql->next();
      }
      
      $tpl->VALOR = $formata->formataNumero($valortotal_bonus);
      
      
      $sql2 = new Query($bd);
      $txt2 = "SELECT SEQUENCIA,REDE_SEQUSUA,VALORSAQUE,DATASAQUE,DATAPAGO,SITUSAQUE
			FROM TREDE_SOLICITASAQUE_UNI
            WHERE REDE_SEQUSUA = '".$seq."'
			ORDER BY DATASAQUE ASC";
      $sql2->executeQuery($txt2);
      
      while (!$sql2->eof()) {
        $tpl->ID = $sql2->result("SEQUENCIA");
        $tpl->NOME1 = ucwords(utf8_encode($func->RetonaNomeUsuarioPorSeq($bd,$sql2->result("REDE_SEQUSUA"))));
        $seq2 = $sql2->result("REDE_SEQUSUA");
        $tpl->VALORSAQUE_F = 'R$ '.number_format($sql2->result("VALORSAQUE"),2,',','.');
        $tpl->VALORSAQUE_N = $sql2->result("VALORSAQUE");
        $tpl->DATASAQUE = $data->formataData1($sql2->result("DATASAQUE")).' '.substr($sql2->result("DATASAQUE"),10,10);
        $tpl->DATAPAGO = $sql2->result("DATAPAGO");
        $situplan = $sql2->result("SITUSAQUE");
        
        if ($situplan == '1') {
          $tpl->SITUSAQUE = 'Aguardando o Admin autorizar';
          $tpl->COR1 = "warning";
        } else if ($situplan == '2') {
          $tpl->SITUSAQUE = 'Autorizado';
          $tpl->COR1 = "success";
        } else if ($situplan == '9') {
          $tpl->SITUSAQUE = 'Cancelado';
          $tpl->COR1 = "danger";
        }
        
        $sql2->next();
        $tpl->block("AUT");
      }
      
      ////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////
      
      if (isset($_POST['solicitar'])) {
        
        $valor_soli = $_POST['valor'];
        $valor_soli = str_replace('.','',$valor_soli);
        $valor_soli = str_replace(',','.',$valor_soli);
        
        $sql_saque = new Query($bd);
        $txt_saque = "SELECT VALOR FROM TREDE_SAQUEMIN_UNI";
        $sql_saque->executeQuery($txt_saque);
        
        $res_saque = $sql_saque->result("VALOR");
        
        $res_saquef = $formatar->formataNumero($sql_saque->result("VALOR"));
        
        $sql44 = new Query ($bd);
        $txt44 = "SELECT LAST_INSERT_ID(SEQUPAGPLAN),CSITPAGPLAN,CSITUAPPLAN
				    FROM TREDE_PAGAPLANO
					WHERE NIDUPAGPLAN = :id
					ORDER BY 1 DESC
					LIMIT 1";
        $sql44->AddParam(':id',$seq);
        $sql44->executeQuery($txt44);
        
        $situplan = $sql44->result("CSITPAGPLAN");
        $situpagp = $sql44->result("CSITUAPPLAN");
        
        $res_valor_adesao_mensal = $valortotal_bonus;
        
        if (($situplan == '1') and ($situpagp == NULL)) {
          echo "<script>alert('Seu pagamento esta em analise.')</script>";
        } else if (($situplan == '1') and ($situpagp == 'c')) {
          echo "<script>alert('Seu pagamento esta em analise.')</script>";
        } else if (($situplan == '1') and ($situpagp == 'p')) {
          echo "<script>alert('Seu pagamento esta em analise.')</script>";
        } else if (($situplan == '2') and ($situpagp == 'c')) {
          echo "<script>alert('Seu pagamento esta em analise.')</script>";
        } else if (($situplan == '2') and ($situpagp == NULL)) {
          echo "<script>alert('Seu pagamento esta em analise.')</script>";
        } else if (($situplan == '3') and ($situpagp == 'p')) {
          echo "<script>alert('Seu pagamento foi efetivado, aguardando a aprovação do Administrador.')</script>";
        } else if (($situplan == '3') and ($situpagp == 'c')) {
          echo "<script>alert('Seu plano está cancelado.')</script>";
        } else if (($situplan == '3') and ($situpagp == 'c')) {
          echo "<script>alert('Seu plano está cancelado.')</script>";
        } else if (($situplan == '7') and ($situpagp == 'c')) {
          echo "<script>alert('Seu plano está cancelado.')</script>";
        } else if (($situplan == '7') and ($situpagp == 'a')) {
          echo "<script>alert('Seu plano está cancelado')</script>";
        } else if (($situplan == '9') and ($situpagp == 'c')) {
          echo "<script>alert('Seu plano expirou, faça uma ativação.')</script>";
        } else if (($situplan == '9') and ($situpagp == 'a')) {
          echo "<script>alert('Seu plano expirou, faça uma ativação.')</script>";
        } else if ($valor_soli < $res_saque) {
          echo "<script>alert('O saque mínimo é de R$ '+$res_saque+' reais (lembrando que o saldo do voucher tem que ser igual ou superior ao do cashback')</script>";
        } else if ($valor_soli > $res_valor_adesao_mensal) {
          echo "<script>alert('Você não tem saldo suficiente.(Saldo Unilevel)')</script>";
        } else {
          
          $sql1 = new Query($bd);
          $txt1 = "INSERT INTO TREDE_SOLICITASAQUE_UNI
                (REDE_SEQUSUA,VALORSAQUE,DATASAQUE,SITUSAQUE)
                VALUES
                ('".$seq."','".$valor_soli."','".date('Y-m-d H:i:s')."','1')";
          $sql1->executeSQL($txt1);
          
          $valot_total_uni = $res_valor_adesao_mensal - $valor_soli;
          
          $sql5 = new Query($bd);
          $txt5 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '".$valot_total_uni."'
            WHERE NIDUPAGPLAN = :seq";
          $sql5->addParam(':seq',$seq);
          $sql5->executeSQL($txt5);
          
          echo "<script>alert('Seu pedido de saque foi solicitado.Aguarde o Admin.')</script>";
          echo "<script>window.location.href = window.location.href</script>";
          //$util->redireciona("alterar_planusua.php?idSessao=" . $_SESSION['idSessao']);
        }
        
      }
    }
  } else {
    $seg->verificaSession($_SESSION['idUsuario']);
  }
  
  $tpl->show();
  $bd->close();
?>