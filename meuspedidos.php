<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","meuspedidos.html");
  
  if (isset($_SESSION['aut'])) {
    $autenticado = TRUE;
    $_SESSION['aut'] = TRUE;
    $tpl->block("MENU_USUA");
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    $seg->verificaSession($_SESSION['idUsuario']);
    
    if ($autenticado == TRUE) {
  
      $id_sessao = $_SESSION['idSessao'];
      $id_confer = $_SESSION['idUsuario'];
      $seq = $_SESSION['idUsuario'];
  
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao'];
      $tpl->IDUSUA = $_SESSION['idUsuario'];
      
      //$seq = $_SESSION['idUsuario'];
      $seq = $_SESSION['idUsuario'];
      
      $idusua = $_SESSION['idUsuario'];
      
      //SELECT PARA VERIFICAR O USUARIO
      $tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
      //SELECT PARA VERIFICAR O USUARIO
      
      $func->AtualizaStatusUsuario($seq);
      
      $sql21 = new Query ($bd);
      $txt21 = "SELECT REDE_PLANUSU
			   FROM TREDE_USUADMIN
			  WHERE REDE_SEQUSUA = :usua";
      $sql21->AddParam(':usua',$seq);
      $sql21->executeQuery($txt21);
      
      $res_assi = $sql21->result("REDE_PLANUSU");
      
      $sql22 = new Query ($bd);
      $txt22 = "SELECT DDTFIMPPLAN
			   FROM TREDE_PAGAPLANO
			  WHERE NIDUPAGPLAN = :usua";
      $sql22->AddParam(':usua',$seq);
      $sql22->executeQuery($txt22);
      
      $data_vence = $data->formataData1($sql22->result("DDTFIMPPLAN"));
      
      $sql = new Query ($bd);
      $txt = "SELECT SEQPAGCOMPRA,
				   SEQUENCIACRE,
				   VIDCARCARR,
				   NVALORCPAG,
				   NIDUSPAGCOMPRA,
				   DDATAPAGCOMPRA,
				   CSITUPAGCOMPRA,
				   CTIPOPAGCOMPRA,
				   CIDTRPAGCOMPRA,
				   CSITUAPGCOMPRA,
				   NIDCSPAGCOMPRA,
				   IDPAGSEGCOMPRA 
			 FROM TREDE_PAGACOMPRA
			WHERE NIDUSPAGCOMPRA = :idusua
			ORDER BY SEQPAGCOMPRA DESC";
      $sql->addParam(':idusua',$seq);
      $sql->executeQuery($txt);
      while (!$sql->eof()) {
        
        $idpagplan = $sql->result("SEQPAGCOMPRA");
        $idcart = $sql->result("VIDCARCARR");
        $idcred = $sql->result("SEQUENCIACRE");
        $situcar = $sql->result("CSITUAPGCOMPRA");
        
        $tpl->DATA = $data->formataData1($sql->result("DDATAPAGCOMPRA"));
        
        $tpl->ID = $sql->result("SEQPAGCOMPRA");
        $tpl->VALOR = $formata->formataNumero($sql->result("NVALORCPAG"));
        
        $tpl->NOMEREDE = utf8_encode($func->RetornaNomeRede($bd,$idcred));
        $tpl->NOMEDALOJA = utf8_encode($func->RetornaNomeRede($bd,$idcred));
        
        if ($situcar == 'f') {
          $tpl->STATUSCAR = "Finalizado";
        }
        
        $tipo_pag = $sql->result("CTIPOPAGCOMPRA");
        $situa_pag = $sql->result("CSITUPAGCOMPRA");
        
        if ($situa_pag == '1') {
          if ($tipo_pag == '7') {
            $tpl->SITUA = "Aguardando Administrador Aprovar";
          } else {
            $tpl->SITUA = "Aguardando";
          }
          
          $tpl->COR = "#FAFBFA";
        } else if ($situa_pag == '2') {
          $tpl->SITUA = "Em análise";
          $tpl->COR = "#E0EBE1";
        } else if ($situa_pag == '3') {
          $tpl->SITUA = "<font color='green'>Paga</font>";
          $tpl->COR = "#8FE89C";
        } else if ($situa_pag == '4') {
          $tpl->SITUA = "Disponível";
          $tpl->COR = "#E0EBE1";
        } else if ($situa_pag == '5') {
          $tpl->SITUA = "Em disputa";
        } else if ($situa_pag == '6') {
          $tpl->COR = "#C49AEB";
          $tpl->SITUA = "Devolvida/Extornada";
          $tpl->COR = "#F1BC96";
        } else if ($situa_pag == '7') {
          $tpl->SITUA = "<font color='red'>Cancelada</font>";
          $tpl->COR = "#F9A3A3";
        } else if ($situa_pag == '9') {
          $tpl->SITUA = "<font color='green'>Pago</font>";
          $tpl->COR = "#F9A3A3";
        }
        
        //tipo de pagamento//
        
        if ($tipo_pag == '1') {
          $tpl->TIPOP = "Cartão Crédito";
        } else if ($tipo_pag == '2') {
          $tpl->TIPOP = "Boleto";
        } else if ($tipo_pag == '3') {
          $tpl->TIPOP = "Cartão Débito";
        } else if ($tipo_pag == '4') {
          $tpl->TIPOP = "Saldo PagSeguro";
        } else if ($tipo_pag == '7') {
          $tpl->TIPOP = "Transferência Bancária";
        } else if ($tipo_pag == NULL) {
          $tpl->TIPOP = "Aguardando o PagSeguro";
        } else if ($tipo_pag == '9') {
          $tpl->TIPOP = "<font color='green'>Paga Via CashBack</font>";
        }
        //tipo de pagamento//
        
        $sql1 = new Query ($bd);
        $txt1 = "SELECT NSEQUPRODU,NSEQUECARR,
					VNOMEPCARR,
					NVALORCARR,
					NQUATICARR,
					NVVALOCARR,
					VVACASCARR,
                    SEQUENCIACRE
			 FROM TREDE_CARRINHO
			WHERE VIDCARCARR = :idcart";
        $sql1->addParam(':idcart',$idcart);
        $sql1->executeQuery($txt1);
        
        $valor_total = "0";
        $valor_cash = "0";
        
        while (!$sql1->eof()) {
          $valouni = $sql1->result("NVALORCARR");
          $valtotal = $sql1->result("NVVALOCARR");
          $cashback = $sql1->result("VVACASCARR");
          $seqprodu = $sql1->result("NSEQUPRODU");
          $seq_cre = $sql1->result("SEQUENCIACRE");
          
          $valor_total += $valtotal;
          $tpl->VALOR_TOTAL = 'R$ '.$formata->formataNumero($valor_total);
          
          $valor_cash += $cashback;
          $tpl->TOTALCASH = 'R$ '.$formata->formataNumero($valor_cash);
          
          $tpl->IDCART = $sql1->result("NSEQUECARR");
          $tpl->NOMEPROD = utf8_encode($sql1->result("VNOMEPCARR"));
          $tpl->VALUNITA = 'R$ '.$formata->formataNumero($sql1->result("NVALORCARR"));
          $tpl->QUANTIDA = $sql1->result("NQUATICARR");
          $tpl->VALTPROD = 'R$ '.$formata->formataNumero($sql1->result("NVVALOCARR"));
          
          $sql2c = new Query ($bd);
          $txt2c = "SELECT  SEQUENCIACRE,
					 CLASSIFICCRE
			   FROM TREDE_CREDENCIADOS
		      WHERE SEQUENCIACRE = :idloja";
          $sql2c->addParam(':idloja',$seq_cre);
          $sql2c->executeQuery($txt2c);
          
          $class = $sql2c->result("CLASSIFICCRE");
          
          $sql2cc = new Query ($bd);
          $txt2cc = "SELECT NNUMECLASS,CASHBCLASS
			   FROM TREDE_CLASSREDE
		      WHERE NNUMECLASS = :class";
          $sql2cc->addParam(':class',$class);
          $sql2cc->executeQuery($txt2cc);
          
          //$tpl->CASHBACK		= 'Cashback de '.$sql2cc->result("CASHBCLASS").'%';
          
          $sql_cash = new Query($bd);
          $txt_cash = "SELECT VCASHPRODU
					   FROM  TREDE_PRODUTOS
					  WHERE NSEQUPRODU = :seqprodu";
          $sql_cash->addParam(':seqprodu',$seqprodu);
          $sql_cash->executeQuery($txt_cash);
          
          if ($tipo_pag == '9') {
            $tpl->CASHPORC = "------";
            $tpl->CASHBACK = "------";
            $tpl->TOTALCASH = "------";
            
          } else {
            //$tpl->CASHPORC = $sql_cash->result("VCASHPRODU") . " %";
            $tpl->CASHPORC = $sql2cc->result("CASHBCLASS")." %";
            $tpl->CASHBACK = 'R$ '.$formata->formataNumero($sql1->result("VVACASCARR"));
            $tpl->TOTALCASH = 'R$ '.$formata->formataNumero($valor_cash);
          }
          
          $tpl->block("PRODUTOS");
          $sql1->next();
        }
        
        $tpl->block("COMPRAS");
        $sql->next();
      }
      
/*      if ($tipo_pag == '9') {
        //$tpl->NAOGERA = "<font color='red'>Compras paga com Cashback não geram cashback</font>";
        $tpl->CASHBACK = "------";
      }*/
      
      
      $sql_qc = new Query ($bd);
      $txt_qc = "SELECT VIDCARCARR FROM TREDE_CARRINHO
				WHERE VFECHACARR = 'n'
				AND REDE_SEQUSUA = :idusua
				GROUP BY VIDCARCARR";
      $sql_qc->addParam(':idusua',$seq);
      $sql_qc->executeQuery($txt_qc);
      
      if ($sql_qc->count() > 0) {
        $tpl->OPENCART = $sql_qc->count();
        $tpl->TITULO_OPEN = "Existe ".$sql_qc->count()." carrinho em aberto. Clique e veja.";
        $tpl->block("OPEN");
      }
      
    }//aut
    
  }//empresa
  else{
    $seg->verificaSession($_SESSION['idUsuario']);
  }
  
  $tpl->show();
  $bd->close();
?>