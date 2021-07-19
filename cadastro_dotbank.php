<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  require_once("comum/apagaArquivos.php");
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","cadastro_dotbank.html");
  
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
      $tpl->ID_USUA_CR = $_SESSION['idUsuario'];
      $tpl->IDUSUA = $_SESSION['idUsuario'];
      
      $func->AtualizaStatusUsuario($seq);
      
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
      $txt = "SELECT 	REDE_SEQUSUA,
					REDE_NOMEUSU,
					REDE_EMAILUS,
					REDE_CELULAR
					FROM TREDE_USUADMIN
					WHERE REDE_SEQUSUA = :seq";
      $sql->addParam(':seq',$seq);
      $sql->executeQuery($txt);
      
      $tpl->EMAI = $sql->result("REDE_EMAILUS");
      $tpl->CELU = $sql->result("REDE_CELULAR");
      
      $sql2 = new Query ($bd);
      $txt2 = "SELECT 	REDE_SEQUSUA,
									NCPF_DOT,
									NRG__DOT,
									SENHADOT,
									ENDERDOT,
									NENDEDOT,
									CIDADDOT,
									ESTADDOT,
									CCEP_DOT,
									COMPLDOT,
									BAIRRDOT,
       						DNASCDOT,
       						CSITUDOT   
					FROM TREDE_DOTBANK_USUA
					WHERE REDE_SEQUSUA = :seq";
      $sql2->addParam(':seq',$seq);
      $sql2->executeQuery($txt2);
      
      $tpl->NCPF_DOT = $sql2->result("NCPF_DOT");
      $tpl->NRG__DOT = $sql2->result("NRG__DOT");
      $tpl->ENDERDOT = $sql2->result("ENDERDOT");
      $tpl->NENDEDOT = $sql2->result("NENDEDOT");
      $tpl->CIDADDOT = utf8_encode($sql2->result("CIDADDOT"));
      $tpl->ESTADDOT = $sql2->result("ESTADDOT");
      $tpl->CCEP_DOT = $sql2->result("CCEP_DOT");
      $tpl->COMPLDOT = $sql2->result("COMPLDOT");
      $tpl->BAIRRO = $sql2->result("BAIRRDOT");
      $situa = $sql2->result("CSITUDOT");
      $tpl->DNASC = $data->formataData1($sql2->result("DNASCDOT"));
      
      if ($situa == 'P') {
        $tpl->block('BTN_NCAD');
        $tpl->block("PENDENTE");
      } else if ($situa == 'A') {
        $tpl->MSG = "Cadastro Realizado com Sucesso, para acompanhar sua solicitação, entre no site do DotBank, e valida sua conta; <br> Link para Ativação: <a href='https://web.dotbank.com.br/login' target='_blank'>LINK PARA ATIVAÇÃO</a>";
        $tpl->block("SUCESSO");
      }
    }
  } else {
    $seg->verificaSession($_SESSION['idUsuario']);
  }
  
  
  $tpl->show();
  $bd->close();
?>





