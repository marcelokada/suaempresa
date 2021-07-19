<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","vendasprodutos.html");
  
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
      
      $sql = new Query ($bd);
      $txt = "SELECT SEQUENCIACRE,VIDCARCARR,REDE_SEQUSUA
			 FROM TREDE_CARRINHO
			WHERE SEQUENCIACRE = :idRede
			GROUP BY SEQUENCIACRE, VIDCARCARR,REDE_SEQUSUA
			ORDER BY VIDCARCARR DESC";
      $sql->addParam(':idRede',$idrede);
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $tpl->COR = "#CEF6D8";
        $seq = $sql->result("REDE_SEQUSUA");
        
        $tpl->NOMEUSUA = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
        
        $idcart = $sql->result("VIDCARCARR");
        
        $tpl->ID = $sql->result("VIDCARCARR");
        $idLoja = $sql->result("SEQUENCIACRE");
        $tpl->ID_CRED = $sql->result("SEQUENCIACRE");
        
        
        $sql_f = new Query ($bd);
        $txt_f = "SELECT VFECHACARR
			 FROM TREDE_CARRINHO
			WHERE VIDCARCARR = :idcart
			GROUP BY VFECHACARR";
        $sql_f->addParam(':idcart',$idcart);
        $sql_f->executeQuery($txt_f);
        
        $res_fechar = $sql_f->result("VFECHACARR");
        if ($res_fechar == 's') {
          $tpl->STATUSCAR = "Fechado";
        } else {
          $tpl->STATUSCAR = "Aberto";
        }
        
        
        $sql_s = new Query ($bd);
        $txt_s = "SELECT SEQPAGCOMPRA,
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
				   IDPAGSEGCOMPRA,
				   CTIPOCARTAOCOMP,
				   TIPOPAGAMENTOP,
				   DDATAEFETIVCOMP,
       			CTIPPPAGCOMPRA
			 FROM TREDE_PAGACOMPRA
			WHERE NIDUSPAGCOMPRA = :idusua
			  AND SEQUENCIACRE = :idcred
			  AND VIDCARCARR = :idcart
			ORDER BY SEQPAGCOMPRA DESC";
        $sql_s->addParam(':idcart',$idcart);
        $sql_s->addParam(':idusua',$seq);
        $sql_s->addParam(':idcred',$idLoja);
        $sql_s->executeQuery($txt_s);
        
        $numcart = $sql_s->result("CTIPOCARTAOCOMP");
        
        
        $tipocart = $func->RetornaTipoCartao($numcart);
        //$tpl->TIPOPAG = $tipocart;
        $tpl->TIPOPAG = $sql_s->result("CTIPPPAGCOMPRA");;
        
        $tpl->REF = $sql_s->result("IDPAGSEGCOMPRA");
        
        $tpl->DATATRANS = $data->formataData1($sql_s->result("DDATAEFETIVCOMP"));
        
        $tipotrans = $sql_s->result("TIPOPAGAMENTOP");
        
        
        if ($tipotrans == 'n') {
          $tpl->TIPAGCASH = "Via Normal";
        } else if ($tipotrans == 'c') {
          $tpl->TIPAGCASH = "<font color='green'>Via CashBack</font>";
        }
        
        
        $situa_pag = $sql_s->result("CSITUPAGCOMPRA");
        
        if ($situa_pag == '1') {
          $tpl->SITUA = "Aguardando";
        } else if ($situa_pag == '2') {
          $tpl->SITUA = "Em análise";
        } else if ($situa_pag == '3') {
          $tpl->SITUA = "<font color='green'>Paga</font>";
        } else if ($situa_pag == '4') {
          $tpl->SITUA = "Disponível";
        } else if ($situa_pag == '5') {
          $tpl->SITUA = "Em disputa";
        } else if ($situa_pag == '6') {
          $tpl->SITUA = "Devolvida/Extornada";
        } else if ($situa_pag == '7') {
          $tpl->SITUA = "<font color='red'>Cancelada</font>";
        } else if ($situa_pag == '9') {
          $tpl->SITUA = "<font color='green'>Pago</font>";
        }
        
        $sql1 = new Query ($bd);
        $txt1 = "SELECT NSEQUPRODU,
					VNOMEPCARR,
					NVALORCARR,
					NQUATICARR,
					NVVALOCARR,
					VVACASCARR,
					DDATACCARR
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
          
          $tpl->DATACART = $data->formataData1($sql1->result("DDATACCARR"));
          
          $valor_total += $valtotal;
          $tpl->VALOR_TOTAL = 'R$ '.$formata->formataNumero($valor_total);
          
          $valor_cash += $cashback;
          $tpl->TOTALCASH = 'R$ '.$formata->formataNumero($valor_cash);
          
          $tpl->NOMEPROD = utf8_encode($sql1->result("VNOMEPCARR"));
          $tpl->VALUNITA = 'R$ '.$formata->formataNumero($sql1->result("NVALORCARR"));
          $tpl->QUANTIDA = $sql1->result("NQUATICARR");
          $tpl->VALTPROD = 'R$ '.$formata->formataNumero($sql1->result("NVVALOCARR"));
          $tpl->CASHBACK = 'R$ '.$formata->formataNumero($sql1->result("VVACASCARR"));
          
          $sql_cash = new Query($bd);
          $txt_cash = "SELECT VCASHPRODU
					   FROM  TREDE_PRODUTOS
					  WHERE NSEQUPRODU = :seqprodu";
          $sql_cash->addParam(':seqprodu',$seqprodu);
          $sql_cash->executeQuery($txt_cash);
          
          $sqlcreds = new Query($bd);
          $txtcreds = "SELECT CLASSIFICCRE FROM TREDE_CREDENCIADOS WHERE SEQUENCIACRE = :idLoja";
          $sqlcreds->addParam(':idLoja',$idLoja);
          $sqlcreds->executeQuery($txtcreds);
          
          $classredes = $sqlcreds->result("CLASSIFICCRE");
          
          $sqlclass = new Query($bd);
          $txtclass = "SELECT CASHBCLASS FROM TREDE_CLASSREDE WHERE NNUMECLASS = :class";
          $sqlclass->addParam(':class',$classredes);
          $sqlclass->executeQuery($txtclass);
          
          $sqlclasses = $sqlclass->result("CASHBCLASS");
          
          $tpl->CASHPORC = $sqlclasses;
          //$tpl->CASHPORC = $sql_cash->result("VCASHPRODU");
          
          $tpl->block("PRODUTOS");
          $sql1->next();
        }
        
        $tpl->block("COMPRAS");
        $sql->next();
      }
    }
  } else {
    $seg->verificaSession($_SESSION['aut_rede']);
  }
  
  
  $tpl->show();
  $bd->close();
?>