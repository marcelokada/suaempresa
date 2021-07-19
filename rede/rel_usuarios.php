<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  error_reporting(0);
  
  $bd = new Database();
  
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","rel_usuarios.html");
  
  if (isset($_SESSION['aut_rede'])) {
    $autenticado = TRUE;
    $_SESSION['aut_rede'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      $id_sessao = $_SESSION['idSessao_rede'];
      $id_confer = $_GET['idSessao'];
      $id_rede = $_SESSION['idRede'];
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao_rede'];
      $tpl->ID_REDE = $_SESSION['idRede'];
      
      $idmsg = $_GET['idMsg'];
      
      if (isset($_POST['pesquisar'])) {
        $tipo = $seg->antiInjection($_POST['tipo']);
        $pesquisar = utf8_encode($seg->antiInjection($_POST['pesquisar']));
        
        if ($tipo == 'n') {
          $cond = "AND REDE_NOMEUSU LIKE '%".$pesquisar."%' ";
        } else if ($tipo == 'c') {
          $cond = "AND REDE_CPFUSUA = '".$pesquisar."'";
        } else if ($tipo == 'e') {
          $cond = "AND REDE_EMAILUS = '".$pesquisar."' ";
        }
        
        $sql1 = new Query($bd);
        $txt1 = "SELECT REDE_SEQUSUA,
									REDE_NOMEUSU,
									REDE_CPFUSUA,
									REDE_EMAILUS,
									REDE_DNASCUS,
									REDE_LOGUSUA,
									REDE_LOGBLOK		  
		  FROM TREDE_USUADMIN 
		  WHERE REDE_ADMINUS = 'n'
		  ".$cond." ";
        $sql1->executeQuery($txt1);
        
        while (!$sql1->eof()) {
          $seqprodu = $sql1->result("REDE_SEQUSUA");
          $tpl->REDE_SEQUSUA = $sql1->result("REDE_SEQUSUA");
          $tpl->REDE_NOMEUSU = ucwords(utf8_encode($sql1->result("REDE_NOMEUSU")));
          $tpl->REDE_EMAILUS = $sql1->result("REDE_EMAILUS");
          $tpl->REDE_CPFUSUA = $sql1->result("REDE_CPFUSUA");
          $tpl->REDE_LOGBLOK = $func->assinaturaUsuario($seqprodu);
          //$tpl->REDE_CASHBACK = number_format($func->RetornaValorCashBackUsuario($bd,$seqprodu),2,',','.');
          $tpl->block("USUARIOS");
          $sql1->next();
        }
        $tpl->block("MOSTRAR");
        
      }
      
      
      if (isset($_POST['aplicar'])) {
        $idusua = $_POST['idusua'];
        $valor_compra = $_POST['valor_compra'];
        $valor_compra = str_replace('.','',$valor_compra);
        $valor_compra = str_replace(',','.',$valor_compra);
        
        
        $sql1aa = new Query ($bd);
        $txt1aa = "SELECT VALCREDREDE FROM TREDE_CREDITOREDE
            WHERE SEQUENCIACRE = :seqcre";
        $sql1aa->addParam(':seqcre',$idrede);
        $sql1aa->executeQuery($txt1aa);
        
        $ver_valor = $sql1aa->result("VALCREDREDE");
        
        $sql2 = new Query($bd);
        $txt2 = "SELECT CLASSIFICCRE
		  FROM TREDE_CREDENCIADOS
		  WHERE SEQUENCIACRE = :idrede ";
        $sql2->addParam(':idrede',$idrede);
        $sql2->executeQuery($txt2);
        
        $res_classrede = $sql2->result("CLASSIFICCRE");
        
        $sql3 = new Query($bd);
        $txt3 = "SELECT CASHBCLASS
		  FROM TREDE_CLASSREDE
		  WHERE NNUMECLASS = :class";
        $sql3->addParam(':class',$res_classrede);
        $sql3->executeQuery($txt3);
        
        $porc_cash = $sql3->result("CASHBCLASS");
        
        $total_cash_gerado = $valor_compra * $porc_cash / 100;
        $total_cash_gerado = str_replace('.','',$total_cash_gerado);
        $total_cash_gerado = str_replace(',','.',$total_cash_gerado);
        
        if ($total_cash_gerado > $ver_valor) {
          $tpl->MSG = "Você nao tem mais pontuação disponível para aplicar essa bonificação.<br>
								Por favor entrar em contato com o Adminstrador do Sistema.";
          $tpl->block("ERRO");
        } else {
          
          $sql1cc = new Query ($bd);
          $txt1cc = "SELECT LAST_INSERT_ID(NSEQUECARR)+1 SEQCART FROM TREDE_CARRINHO
			ORDER BY 1 DESC
			LIMIT 1";
          $sql1cc->executeQuery($txt1cc);
          
          $idcart = $sql1cc->result("SEQCART");
          
          $sql1cc1 = new Query ($bd);
          $txt1cc1 = "INSERT INTO TREDE_CARRINHO (NSEQUECARR) VALUES ('".$idcart."')";
          $sql1cc1->executeSQL($txt1cc1);
          
          $sql4 = new Query ($bd);
          $txt4 = "INSERT INTO TREDE_PAGAGERACASH
						(SEQUENCIACRE,VIDCARCARR,NVALORCPAG,NVAGECSPAG,NIDUSUCPAG,DDATAPGPAG)
						VALUES
						('".$idrede."','".$idcart."','".$valor_compra."','".$total_cash_gerado."','".$idusua."','".date('Y-m-d H:i:s')."')";
          $sql4->executeSQL($txt4);
          
          $valortotal_cash = $func->RetornaValorCashBackUsuario($bd,$idusua);
          
          $valor_atual = $valortotal_cash + $total_cash_gerado;
          $valor_atual = str_replace('.','',$valor_atual);
          $valor_atual = str_replace(',','.',$valor_atual);
          
          
          $sql5 = new Query ($bd);
          $txt5 = "UPDATE TREDE_CASHBACK_USU SET VVALUSCASH = :valor_atual
			 WHERE NIDUSUCASH = :idusua";
          $sql5->addParam(':valor_atual',$valor_atual);
          $sql5->addParam(':idusua',$idusua);
          $sql5->executeSQL($txt5);
          
          //sistema de pontuação
          $sql1a = new Query ($bd);
          $txt1a = "SELECT VALCREDREDE FROM TREDE_CREDITOREDE
            WHERE SEQUENCIACRE = :seqcre";
          $sql1a->addParam(':seqcre',$idrede);
          $sql1a->executeQuery($txt1a);
          
          $valor_ponstuacao_atual = $sql1a->result("VALCREDREDE");
          
          $valor_total_pontoecash = $valor_ponstuacao_atual - $total_cash_gerado;
          $valor_total_pontoecash = str_replace('.','',$valor_total_pontoecash);
          $valor_total_pontoecash = str_replace(',','.',$valor_total_pontoecash);
          
          $sql1b = new Query ($bd);
          $txt1b = "UPDATE TREDE_CREDITOREDE SET VALCREDREDE = '".$valor_total_pontoecash."'
			 WHERE SEQUENCIACRE = :seqcre";
          $sql1b->addParam(':seqcre',$idrede);
          $sql1b->executeSQL($txt1b);
          
          
          $sql1c = new Query ($bd);
          $txt1c = "SELECT LAST_INSERT_ID(NUMTRANSUSU) NUMTRANSUSU FROM TREDE_CREDITOTRANS_USUA
            WHERE REDE_SEQUSUA = :idusua
            ORDER BY 1 DESC
            LIMIT 1";
          $sql1c->AddParam(':idusua',$idusua);
          $sql1c->executeQuery($txt1c);
          
          $seqtransusu = $sql1c->result("NUMTRANSUSU") + 1;
          
          $sql1d = new Query ($bd);
          $txt1d = "INSERT INTO TREDE_CREDITOTRANS_USUA (REDE_SEQUSUA,SEQUENCIACRE,TIPOPAGMTO,VALTRANSUSU,DATTRANSUSU,IDCARRINHO,NUMTRANSUSU)
            VALUES
             ('".$idusua."','".$idrede."','0','".$total_cash_gerado."','".date('Y-m-d H:i:s')."','".$idcart."','".$seqtransusu."')";
          $sql1d->executeSQL($txt1d);
          //sistema de pontuação
          
          
          $sql7 = new Query ($bd);
          $txt7 = "SELECT LAST_INSERT_ID(NSEQUENPAG) NSEQUENPAG FROM TREDE_PAGAGERACASH
			  WHERE SEQUENCIACRE = :seqcred
			    AND VIDCARCARR = :idcart
				AND NIDUSUCPAG = :idusua
				AND SUBSTR(DDATAPGPAG,1,10) = '".date('Y-m-d')."'
				ORDER BY 1 DESC
				LIMIT 1";
          $sql7->addParam(':seqcred',$idrede);
          $sql7->addParam(':idcart',$idcart);
          $sql7->addParam(':idusua',$idusua);
          $sql7->executeQuery($txt7);
          
          $seqcash = $sql7->result("NSEQUENPAG");
          
          
          $sql6 = new Query ($bd);
          $txt6 = "INSERT INTO TREDE_PAGACOMPRA
						(SEQUENCIACRE,VIDCARCARR,NVALORCPAG,NIDUSPAGCOMPRA,DDATAPAGCOMPRA,
						 CSITUPAGCOMPRA,CTIPOPAGCOMPRA,CSITUAPGCOMPRA,NIDCSPAGCOMPRA,
						 TIPOPAGAMENTOP,CTIPPPAGCOMPRA)
						VALUES
						('".$idrede."','".$idcart."','".$valor_compra."',
						'".$idusua."','".date('Y-m-d H:i:s')."','1','1','f','".$seqcash."','n','rede') ";
          $sql6->executeSQL($txt6);
          
          $sql8 = new Query ($bd);
          $txt8 = "SELECT LAST_INSERT_ID(SEQPAGCOMPRA) SEQPAGCOMPRA
			   FROM TREDE_PAGACOMPRA
			  WHERE SEQUENCIACRE = :seqcred
			    AND VIDCARCARR = :idcart
				AND NIDUSPAGCOMPRA = :idusua
				AND SUBSTR(DDATAPAGCOMPRA,1,10) = '".date('Y-m-d')."'
				ORDER BY 1 DESC
				LIMIT 1";
          $sql8->addParam(':seqcred',$idrede);
          $sql8->addParam(':idcart',$idcart);
          $sql8->addParam(':idusua',$idusua);
          $sql8->executeQuery($txt8);
          
          $seqcompra = $sql8->result("SEQPAGCOMPRA");
          
          $seqcomp = 'c'.$seqcompra;
          
          $sql9 = new Query ($bd);
          $txt9 = "UPDATE TREDE_PAGACOMPRA SET IDPAGSEGCOMPRA = :seqcomp
			 WHERE SEQPAGCOMPRA = :seqcompra";
          $sql9->addParam(':seqcomp',$seqcomp);
          $sql9->addParam(':seqcompra',$seqcompra);
          $sql9->executeSQL($txt9);
          
          $tpl->MSG = "Bonificação inserido com sucesso!";
          $tpl->block("SUCESSO");
        }
      }
    }
  } else {
    $seg->verificaSession($_SESSION['aut_rede']);
  }
  
  $tpl->show();
  $bd->close();
?>