<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  error_reporting(0);
  
  $bd = new Database();
  $func = new Funcao();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","meufaturamento.html");
  
  
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
      //$tpl->IDUSUA    = $seq;
      $tpl->ID_USUA_CR = $_SESSION['idUsuario'];
      $idusua = $_SESSION['idUsuario'];
      
      //SELECT PARA VERIFICAR O USUARIO
      $tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
      //SELECT PARA VERIFICAR O USUARIO
      
      $func->AtualizaStatusUsuario($seq);
      
      //CASHBACK USUARIO
      $valortotal_cash = $func->RetornaValorCashBackUsuario($bd,$seq);
      $tpl->MEUCASH = $formata->formataNumero($valortotal_cash);
      
      $valortotal_bonus = $func->RetornaValorBonusUsuario($seq);
      $tpl->MEUBONUS = $formata->formataNumero($valortotal_bonus);
      $tpl->TOTAL_TOTAL = $formata->formataNumero($valortotal_bonus);
      
      $valortotal_voucher = $func->RetornaValorVoucherUsuario($seq);
      $tpl->MEUVOUCHER = $formata->formataNumero($valortotal_voucher);
      
      $tpl->MEUPLANO = $func->assinaturaUsuarioMimo($bd,$seq);
      //CASHBACK USUARIO
      
      
      $data_incio = mktime(0,0,0,date('m'),1,date('Y'));
      $data_fim = mktime(23,59,59,date('m'),date("t"),date('Y'));
      
      
      $tpl->VALOR_FILTRO1 = number_format('0.00',2,',','.');
      $tpl->COUNT_LIST1 = 0;
      
      
      $tpl->DTINI = date('d/m/Y',$data_incio);
      $tpl->DTFIM = date('d/m/Y');
      
      $dt_ini = date('Y-m-d',$data_incio);
      $dt_fim = date('Y-m-d');
      
      
      $sql11 = new Query ($bd);
      $txt11 = "SELECT NNUMEUSUA
		   FROM TREDE_EXTRATO_USUA
		  WHERE NPATEXTRA = :usua
			GROUP BY NPATEXTRA
 ";
      $sql11->AddParam(':usua',$seq);
      $sql11->executeQuery($txt11);
      
      //AND SUBSTR(DTRAEXTRA,1,10) >= '".$dt_ini."'
      //AND SUBSTR(DTRAEXTRA,1,10) <= '".$dt_fim."'
      $valor_total_usua = $func->RetornaValorTotalExtratoUsuario($seq);
      
      
      while (!$sql11->eof()) {
        $filiado1 = $sql11->result("NNUMEUSUA");
        $valor_total_usua1 = $func->RetornaValorTotalExtratoUsuario($filiado1);
        
        $sql22 = new Query ($bd);
        $txt22 = "SELECT NNUMEUSUA
		   FROM TREDE_EXTRATO_USUA
		  WHERE NPATEXTRA = :usua
			GROUP BY NPATEXTRA";
        $sql22->AddParam(':usua',$filiado1);
        $sql22->executeQuery($txt22);
        
        while (!$sql22->eof()) {
          
          $filiado2 = $sql22->result("NNUMEUSUA");
          
          $valor_total_usua2 = $func->RetornaValorTotalExtratoUsuario($filiado2);
          
          $sql33 = new Query ($bd);
          $txt33 = "SELECT NNUMEUSUA
		                 FROM TREDE_EXTRATO_USUA
		                WHERE NPATEXTRA = :usua
			              GROUP BY NPATEXTRA";
          $sql33->AddParam(':usua',$filiado2);
          $sql33->executeQuery($txt33);
          
          while (!$sql33->eof()) {
            $filiado3 = $sql33->result("NNUMEUSUA");
            $valor_total_usua3 = $func->RetornaValorTotalExtratoUsuario($filiado3);
            
            $sql44 = new Query ($bd);
            $txt44 = "SELECT NNUMEUSUA
		                    FROM TREDE_EXTRATO_USUA
		                   WHERE NPATEXTRA = :usua
			                 GROUP BY NPATEXTRA";
            $sql44->AddParam(':usua',$filiado3);
            $sql44->executeQuery($txt44);
            
            while (!$sql44->eof()) {
              $filiado4 = $sql44->result("NNUMEUSUA");
              $valor_total_usua4 = $func->RetornaValorTotalExtratoUsuario($filiado4);
              
              $sql44->next();
            }
            
            $sql33->next();
          }
          
          $sql22->next();
        }
        
        $sql11->next();
      }
      
      
      $sqlT = new Query ($bd);
      $txtT = "SELECT
                        NNUMEUSUA,
                        DEBITO,
                        CREDITO,
                        DTRAEXTRA,
                        NPATEXTRA,
                        CTIPEXTRA,
                        CTPOEXTRA
                    FROM
                         TREDE_EXTRATO_USUA
                    WHERE NNUMEUSUA = :usua
                    AND SUBSTR(DTRAEXTRA,1,10) >= '".$dt_ini."'
          AND SUBSTR(DTRAEXTRA,1,10) <= '".$dt_fim."'
          ORDER BY 4 DESC
          LIMIT 5";
      $sqlT->addParam(':usua',$seq);
      //$sqlT->addParam(':usua1', $filiado1);
      //$sqlT->addParam(':usua2', $filiado2);
      //$sqlT->addParam(':usua3', $filiado3);
      //$sqlT->addParam(':usua4', $filiado4);
      $sqlT->executeQuery($txtT);
      
      $valor_total_filtro = 0;
      $qtde = $sqlT->count("NNUMEUSUA");
      
      //$tpl->COUNT_LIST = $qtde;
      
      while (!$sqlT->eof()) {
        
        $idusuario = $sqlT->result("NNUMEUSUA");
        $nnumeextra = $sqlT->result("NNUMEUSUA");
        $tipobonus = $sqlT->result("CTIPEXTRA");
        $datainsercao = $sqlT->result("DTRAEXTRA");
        $deb_cred = $sqlT->result("CTPOEXTRA");
        
        $nome_usua = $func->RetonaNomeUsuarioPorSeq1($bd,$idusuario);
        
        $tpl->IDUSUAT = $idusuario;
        
        if ($tipobonus == 'a') {
          $tpl->DESC_BONUS = "Bonificação por Adesão do Plano - ".$nome_usua;
        } else if ($tipobonus == 'm') {
          $tpl->DESC_BONUS = "Bonificação por Ativação Mensal - ".$nome_usua;
        } else if ($tipobonus == 'c') {
          $tpl->DESC_BONUS = "Bonificação por CashBack - ".$nome_usua;
        }
        
        
        $tpl->DATAT = $data->formataData1($datainsercao);
        
        if ($deb_cred == 'C') {
          $tpl->TIPOT = "<font color='green'>Credito</font>";
          $valortotal = $sqlT->result("CREDITO");
          $valor_total_filtro += $valortotal;
        } else if ($deb_cred == 'D') {
          $tpl->TIPOT = "<font color='red'>Débito</font>";
          $valortotal = $sqlT->result("DEBITO");
          $hifen = '-';
          $valortotal_sub = $valortotal;
        }
        
        $valor_total_filtro += $valortotal;
        //$tpl->VALOR_FILTRO  = number_format($valor_total_filtro, 2, ',', '.');
        
        $tpl->VALORT = number_format($valor_total_filtro - $valortotal_sub,2,',','.');
        $tpl->block("LISTAR");
        $sqlT->next();
      }
      
      if (($valor_total_usua == 0) or ($valor_total_usua == NULL)) {
        $tpl->TOTAL_TOTAL = 0;
      } else {
        $tpl->TOTAL_TOTAL = number_format($valor_total_usua,2,',','.');
      }
      
      
      //$tpl->block('LISTAR_INICIO');
      
      
      //////////////////////////////////////////////////////////////////////////
      //////////////////////////////////////////////////////////////////////////
      //////////////////////////////////////////////////////////////////////////
      //////////////////////////////////////////////////////////////////////////
      
      
      if (isset($_POST['filtrar'])) {
        
        $data_ini1 = $_POST['dt_ini'];
        $data_ini = $data->dataInvertida($data_ini1);
        
        $data_fim1 = $_POST['dt_fim'];
        $data_fim = $data->dataInvertida($data_fim1);
        
        $tipo = $_POST['tipo'];
        
        if ($tipo == 't') {
          $cond = "AND CTIPEXTRA IS NOT NULL ";
        } else if ($tipo == 'a') {
          $cond = "AND CTIPEXTRA = 'a' ";
        } else if ($tipo == 's') {
          $cond = "AND CTIPEXTRA = 's' ";
        } else if ($tipo == 'm') {
          $cond = "AND CTIPEXTRA = 'm' ";
        }
        
        $sql11 = new Query ($bd);
        $txt11 = "SELECT NNUMEUSUA, NPATEXTRA
		                 FROM TREDE_EXTRATO_USUA
		                WHERE NNUMEUSUA = :usua
			              AND SUBSTR(DTRAEXTRA,1,10) >= '".$data_ini."'
			        AND SUBSTR(DTRAEXTRA,1,10) <= '".$data_fim."'
			        ".$cond."
			        GROUP BY NPATEXTRA";
        $sql11->AddParam(':usua',$seq);
        $sql11->executeQuery($txt11);
        
        //AND SUBSTR(DTRAEXTRA,1,10) >= '".$dt_ini."'
        //AND SUBSTR(DTRAEXTRA,1,10) <= '".$dt_fim."'
        $valor_total_usua = $func->RetornaValorTotalExtratoUsuario($seq);
        
        while (!$sql11->eof()) {
          $filiado1 = $sql11->result("NNUMEUSUA");
          
          $valor_total_usua1 = $func->RetornaValorTotalExtratoUsuario($filiado1);
          
          $sql22 = new Query ($bd);
          $txt22 = "SELECT NNUMEUSUA
		                     FROM TREDE_EXTRATO_USUA
		                    WHERE NNUMEUSUA = :usua
			                  AND SUBSTR(DTRAEXTRA,1,10) >= '".$data_ini."'
			          AND SUBSTR(DTRAEXTRA,1,10) <= '".$data_fim."'
			          ".$cond."
			          GROUP BY NPATEXTRA";
          $sql22->AddParam(':usua',$filiado1);
          $sql22->executeQuery($txt22);
          
          while (!$sql22->eof()) {
            
            $filiado2 = $sql22->result("NNUMEUSUA");
            
            $valor_total_usua2 = $func->RetornaValorTotalExtratoUsuario($filiado2);
            
            $sql33 = new Query ($bd);
            $txt33 = "SELECT NNUMEUSUA
		                   FROM TREDE_EXTRATO_USUA
		                  WHERE NNUMEUSUA = :usua
			                AND SUBSTR(DTRAEXTRA,1,10) >= '".$data_ini."'
			          AND SUBSTR(DTRAEXTRA,1,10) <= '".$data_fim."'
			          ".$cond."
			          GROUP BY NPATEXTRA";
            $sql33->AddParam(':usua',$filiado2);
            $sql33->executeQuery($txt33);
            
            while (!$sql33->eof()) {
              $filiado3 = $sql33->result("NNUMEUSUA");
              $valor_total_usua3 = $func->RetornaValorTotalExtratoUsuario($filiado3);
              
              $sql44 = new Query ($bd);
              $txt44 = "SELECT NNUMEUSUA
		                     FROM TREDE_EXTRATO_USUA
		                    WHERE NNUMEUSUA = :usua
			                  AND SUBSTR(DTRAEXTRA,1,10) >= '".$data_ini."'
			          AND SUBSTR(DTRAEXTRA,1,10) <= '".$data_fim."'
			          ".$cond."
			          GROUP BY NPATEXTRA";
              $sql44->AddParam(':usua',$filiado3);
              $sql44->executeQuery($txt44);
              
              while (!$sql44->eof()) {
                $filiado4 = $sql44->result("NNUMEUSUA");
                $valor_total_usua4 = $func->RetornaValorTotalExtratoUsuario($filiado4);
                
                $sql44->next();
              }
              
              $sql33->next();
            }
            
            $sql22->next();
          }
          
          $sql11->next();
        }
        
        
        $sqlT = new Query ($bd);
        $txtT = "SELECT NNUMEXTRA,
                 NNUMEUSUA,
                 DEBITO,
                 CREDITO,
                 DTRAEXTRA,
                 NPATEXTRA,
                 CTIPEXTRA,
                 CTPOEXTRA,
             		NNUMEUSUA1
             FROM
                  TREDE_EXTRATO_USUA
             WHERE NPATEXTRA = :usua
             AND SUBSTR(DTRAEXTRA,1,10) >= '".$data_ini."'
             AND SUBSTR(DTRAEXTRA,1,10) <= '".$data_fim."'
            ".$cond."
            ORDER BY 4 DESC ";
        $sqlT->addParam(':usua',$seq);
        //	$sqlT->addParam(':usua1', $filiado1);
        //	$sqlT->addParam(':usua2', $filiado2);
        //	$sqlT->addParam(':usua3', $filiado3);
        //	$sqlT->addParam(':usua4', $filiado4);
        $sqlT->executeQuery($txtT);
        
        $valor_total_filtro = 0;
        $valortotal_sub = 0;
        while (!$sqlT->eof()) {
          
          $idusuario = $sqlT->result("NNUMEUSUA");
          
          $idusuario1 = $sqlT->result("NNUMEUSUA1");
          $nnumeextra = $sqlT->result("NNUMEXTRA");
          $tipobonus = $sqlT->result("CTIPEXTRA");
          $datainsercao = $sqlT->result("DTRAEXTRA");
          $horainsercao = SUBSTR($sqlT->result("DTRAEXTRA"),11,10);
          $deb_cred = $sqlT->result("CTPOEXTRA");
          
          $nome_usua = $func->RetonaNomeUsuarioPorSeq1($bd,$idusuario1);
          
          $tpl->IDUSUAT = $nnumeextra;
          
          if ($tipobonus == 'a') {
            $tpl->DESC_BONUS = "Bonificação por Adesão do Plano - ".$nome_usua;
          } else if ($tipobonus == 'm') {
            $tpl->DESC_BONUS = "Bonificação por Ativação Mensal - ".$nome_usua;
          } else if ($tipobonus == 'c') {
            $tpl->DESC_BONUS = "Bonificação por CashBack - ".$nome_usua;
          } else if ($tipobonus == 's') {
            $tpl->DESC_BONUS = "Solicitação de Saque - ".$nome_usua;
          }
          
          $tpl->DATAT = $data->formataData1($datainsercao).' - '.$horainsercao;
          
          if ($deb_cred == 'C') {
            $tpl->TIPOT = "<font color='green'>Credito</font>";
            $valortotal = $sqlT->result("CREDITO");
            $valor_total_filtro += $valortotal;
            $tpl->VALORT = number_format($valortotal,2,',','.');
          } else if ($deb_cred == 'D') {
            $tpl->TIPOT = "<font color='red'>Débito</font>";
            $valortotal = $sqlT->result("DEBITO");
            $hifen = '-';
            $valortotal_sub = $valortotal;
            $tpl->VALORT = $hifen.number_format($valortotal,2,',','.');
          }
          
          $qtde = $sqlT->count("NNUMEUSUA");
          
          $tpl->VALOR_FILTRO1 = number_format($valor_total_filtro - $valortotal_sub,2,',','.');
          
          $tpl->COUNT_LIST1 = $qtde;
          
          $tpl->block("LISTAR1");
          $sqlT->next();
        }
        
        //$tpl->TOTAL_TOTAL = number_format($valor_total_usua, 2, ',', '.');
        $tpl->block('LISTAR_INICIO1');
      }
    }
  } else {
    $seg->verificaSession($_SESSION['idUsuario']);
  }
  
  
  $tpl->show();
  $bd->close();
?>