<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
   //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","pagamentoplano.html");
  
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
      
      $func->AtualizaStatusUsuario($seq);
      
      $idusua = $_SESSION['idUsuario'];
      $tpl->IDPLANO = $_GET['idPlano'];
      $idplano = $_GET['idPlano'];
      
      $tpl->IDUSUA = $idusua;
      $tpl->ID_USUA_CR = md5($idusua);
      
      $_SESSION['idPlano'] = $idplano;
      
      //SELECT PARA VERIFICAR O USUARIO
      $tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
      //SELECT PARA VERIFICAR O USUARIO
      
      $pags = $func->AtivoPagSeguro();
      if ($pags == 's') {
        $tpl->block("PAGS");
      }
      
      $dots = $func->AtivoDotBank();
      if ($dots == 's') {
        $tpl->block("DOTS");
      }
      
      $transf = $func->AtivoTransf();
      if ($transf == 's') {
        $tpl->block("TRANSF");
      }
      
      $minha_assinatura = $func->RetornaStatusAssinaturaUsuario($seq);
      
      //CASHBACK USUARIO
      $valortotal_cash = $func->RetornaValorCashBackUsuario($bd,$seq);
      $tpl->MEUCASH = $formata->formataNumero($valortotal_cash);
      //CASHBACK USUARIO
      
      
      if ($minha_assinatura == 7) {
        $tpl->block("JA");
        $tpl->MEUPLANO1 = $func->RetornaStatusDoPlanoUsuario($seq);;
      } else if ($minha_assinatura == 3) {
        $tpl->block("JA");
        $tpl->MEUPLANO1 = $func->RetornaStatusDoPlanoUsuario($seq);
      } else {
        
        $sql21 = new Query ($bd);
        $txt21 = "SELECT SEQUPAGPLAN
			   FROM TREDE_PAGAPLANO
			  WHERE NIDUPAGPLAN = :usua
			    AND CSITUAPPLAN = 'a'";
        $sql21->AddParam(':usua',$seq);
        $sql21->executeQuery($txt21);
        
        $res_assi = $sql21->result("SEQUPAGPLAN");
        
        if ($res_assi != '') {
          $tpl->block("PLANOS_VAZIO");
          $tpl->block("PLANOS_VAZIO1");
        } else if ($res_assi == NULL) {
          $tpl->block("PLANOS");
          $tpl->block("PLANOS1");
          $tpl->block("PLANOS2");
        }
        
        $sql = new Query ($bd);
        $txt = "SELECT SEQPLANO,
					CNOMEPLANO,
					CDESCPLANO,
					CPRIMPLANO,
					CSEGUPLANO,
					CTERCPLANO,
					CQUARPLANO,
					VVALPPLANO,
					VVALSPLANO,
					VVALTPLANO,
					CTEMPPLANO,
            MENSAPLANO
			 FROM TREDE_PLANOS
			WHERE SEQPLANO = :idplano";
        $sql->addParam(':idplano',$idplano);
        $sql->executeQuery($txt);
        
        $tpl->NOMEPLANO = ucwords($sql->result("CNOMEPLANO"));
        $tpl->DESC = ucwords($sql->result("CDESCPLANO"));
        //$tpl->DESC 			= "Descrição do Plano";
        $tpl->VALOR = $formata->formataNumero($sql->result("VVALTPLANO"));
        $tpl->MENSA = $formata->formataNumero($sql->result("MENSAPLANO"));
        $tpl->TEMPO = $sql->result("CTEMPPLANO");
        
        $sql1 = new Query ($bd);
        $txt1 = "SELECT REDE_PLANUSU FROM TREDE_USUADMIN
			 WHERE REDE_SEQUSUA = :idusua";
        $sql1->addParam(':idusua',$seq);
        $sql1->executeQuery($txt1);
        
        $res_usua = $sql1->count();
        
        //$tpl->STATUS 			= ucwords(utf8_encode($sql3->result("VNOMEPCARR")));
        
        $sql3 = new Query ($bd);
        $txt3 = "SELECT REDE_NOMEUSU,
                REDE_CPFUSUA,
                REDE_EMAILUS,
                REDE_CELULAR,
                REDE_DATAVENC
			   FROM TREDE_USUADMIN
			  WHERE REDE_SEQUSUA = :usua";
        $sql3->AddParam(':usua',$seq);
        $sql3->executeQuery($txt3);
        
        $resdia = $sql3->result("REDE_DATAVENC");
        
        if ($resdia == "") {
          $dia = "10";
        } else {
          $dia = $sql3->result("REDE_DATAVENC");
        }
        
        $res_datual = strtotime(date('Y-m-d'));
        //$res_datual = strtotime(date('2021-02-11'));
        $res_dbol = strtotime(date('Y-m-'.$dia));
        
        if ($res_datual > $res_dbol) {
          
          $mes = date('m',strtotime('+1 months',strtotime(date('Y-m-d'))));
          
          $data_bol = date('Y/'.$mes.'/'.$dia);
        } else {
          $data_bol = date('Y/m/'.$dia);
        }
        
        //$tpl->DATA_VENC = date('Y/m/d', strtotime("+3 days", strtotime(date('Y-m-d'))));
        $tpl->DATA_VENC = $data_bol;
        
        //$tpl->NOME_USUA = $sql3->result("REDE_NOMEUSU");
        $tpl->CPF_USUA = $sql3->result("REDE_CPFUSUA");
        $tpl->EMAIL_USUA = $sql3->result("REDE_EMAILUS");
        $tpl->CELU_USUA = $sql3->result("REDE_CELULAR");
        
        $tpl->block('NASS');
      }
    }
    else {
      $seg->verificaSession($_SESSION['idUsuario']);
    }
  }
  
  $tpl->show();
  $bd->close();
?>