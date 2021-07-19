<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  include "configuracao_pagseguro.php";
  
  $bd = new Database();
  
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","pagamento_plano.html");
  
  if (isset($_SESSION['aut'])) {
    $autenticado     = TRUE;
    $_SESSION['aut'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      $id_sessao       = $_SESSION['idSessao'];
      $id_confer       = $_GET['idSessao'];
      $seq             = $_SESSION['idUsuario'];
      $tpl->ID_SESSAO  = $_SESSION['idSessao'];
      $tpl->IDUSUA     = $_SESSION['idUsuario'];
      $tpl->ID_USUA    = $_SESSION['idUsuario'];
      $tpl->ID_USUA_CR = $_SESSION['idUsuario'];
      $tpl->IDPLANO    = $_GET['idPlano'];
      $idusua          = $_SESSION['idUsuario'];
      $idplano         = $_GET['idPlano'];
      
      $_SESSION['idcrus'] = md5($_SESSION['idUsuario']);
      $_SESSION['idUsu']  = md5($_SESSION['idUsuario']);
      
      $seg->verificaSession($id_sessao);
      
      $_SESSION['idPlano'] = $idplano;
      
      //SELECT PARA VERIFICAR O USUARIO
      $tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
      //SELECT PARA VERIFICAR O USUARIO
      
      //CASHBACK USUARIO
      $valortotal_cash = $func->RetornaValorCashBackUsuario($bd,$seq);
      $tpl->MEUCASH    = $formata->formataNumero($valortotal_cash);
      //CASHBACK USUARIO
      
      
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
        $tpl->MEUPLANO = $func->assinaturaUsuario($bd,$seq);
        $tpl->block("MEUPLANO");
        
        
      } else if ($res_assi == NULL) {
        $tpl->block("PLANOS");
        $tpl->block("PLANOS1");
        $tpl->block("PLANOS2");
        $tpl->MEUPLANO = $func->assinaturaUsuario($bd,$seq);
        $tpl->block("MEUPLANO");
        
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
					CTEMPPLANO
			 FROM TREDE_PLANOS
			WHERE SEQPLANO = :idplano";
      $sql->addParam(':idplano',$idplano);
      $sql->executeQuery($txt);
      
      $tpl->NOMEPLANO = ucwords(utf8_encode($sql->result("CNOMEPLANO")));
      $tpl->DESC      = ucwords(utf8_encode($sql->result("CDESCPLANO")));
      //$tpl->DESC 			= "Descrição do Plano";
      $tpl->VALOR    = $formata->formataNumero($sql->result("VVALTPLANO"));
      $tpl->VALORPAG = $sql->result("VVALTPLANO");
      $tpl->TEMPO    = $sql->result("CTEMPPLANO");
      
      
      $sql1 = new Query ($bd);
      $txt1 = "SELECT REDE_PLANUSU FROM TREDE_USUADMIN
			 WHERE REDE_SEQUSUA = :idusua";
      $sql1->addParam(':idusua',$seq);
      $sql1->executeQuery($txt1);
      
      $res_usua = $sql1->count();
      
      //$tpl->STATUS 			= ucwords(utf8_encode($sql3->result("VNOMEPCARR")));
    }
  } else {
    $seg->verificaSession($_SESSION['idUsuario']);
  }
  
  $tpl->EMAIL_LOJA       = EMAIL_LOJA;
  $tpl->MOEDA_PAGAMENTO  = MOEDA_PAGAMENTO;
  $tpl->SCRIPT_PAGSEGURO = SCRIPT_PAGSEGURO;
  $tpl->URL_NOTIFICACAO  = URL_NOTIFICACAO;
  $tpl->URL              = URL;
  
  
  $tpl->show();
  $bd->close();
?>