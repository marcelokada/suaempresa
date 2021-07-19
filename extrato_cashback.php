<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  //error_reporting(0);
  
  $bd = new Database();
  $func = new Funcao();
  
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","extrato_cashback.html");
  
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
      //$tpl->IDUSUA = $_SESSION['idUsuario'];
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
      
      
      $data_incio = mktime(0,0,0,date('m'),1,date('Y'));
      $data_fim = mktime(23,59,59,date('m'),date("t"),date('Y'));
      
      $tpl->VALOR_FILTRO1 = number_format('0.00',2,',','.');
      $tpl->COUNT_LIST1 = 0;
      
      $tpl->DTINI = date('d/m/Y',$data_incio);
      $tpl->DTFIM = date('d/m/Y');
      
      $dt_ini = date('Y-m-d',$data_incio);
      $dt_fim = date('Y-m-d');
      
      
      //////////////////////////////////////////////////////////////////////////
      //////////////////////////////////////////////////////////////////////////
      //////////////////////////////////////////////////////////////////////////
      //////////////////////////////////////////////////////////////////////////
      
      
      if (isset($_POST['filtrar'])) {
        
        $data_ini1 = $_POST['dt_ini'];
        $data_ini = $data->dataInvertida($data_ini1);
        
        $data_fim1 = $_POST['dt_fim'];
        $data_fim = $data->dataInvertida($data_fim1);
        
        $sql11 = new Query ($bd);
        $txt11 = "SELECT NSEQUENPAG,
       						 SEQUENCIACRE,
                   VIDCARCARR,
                   NVALORCPAG,
                   NVAGECSPAG,
                   NIDUSUCPAG,
                   VCPFUSCPAG,
                   DDATAPGPAG,
                   CSITUPGPAG 
		   FROM TREDE_PAGAGERACASH
		  WHERE NIDUSUCPAG = :usua
			AND SUBSTR(DDATAPGPAG,1,10) >= '".$data_ini."'
			AND SUBSTR(DDATAPGPAG,1,10) <= '".$data_fim."' ";
        $sql11->AddParam(':usua',$seq);
        $sql11->executeQuery($txt11);
        $total_chaback = 0;
        $valor_total_usua = 0;
        
        $tpl->COUNT_LIST1 = $sql11->count();
        
        while (!$sql11->eof()) {
          $tpl->IDGERA = $sql11->result("NSEQUENPAG");
          $seqcred = $sql11->result("SEQUENCIACRE");
          
          $tpl->LOJA = $func->RetornaNomeEmpresa($bd,$seqcred);
          $tpl->IDCARR = $sql11->result("VIDCARCARR");
          $tpl->VALORCOMPRA = number_format($sql11->result("NVALORCPAG"),2,',','.');
          $tpl->CASBACK_GERADO = number_format($sql11->result("NVAGECSPAG"),2,',','.');
          $cashtotal = $sql11->result("NVAGECSPAG");
          $usuario = $sql11->result("NIDUSUCPAG");
          
          $datas = $sql11->result("DDATAPGPAG");
          $horario = SUBSTR($sql11->result("DDATAPGPAG"),11,10);
          
          $tpl->DATAT = $data->formataData1($datas).' '.$horario;
          
          $situacao = $sql11->result("CSITUPGPAG");
          $total_chaback += $cashtotal;
          
          $tpl->VALOR_FILTRO1 = number_format($total_chaback,2,',','.');
          
          $tpl->block("LISTAR");
          $sql11->next();
        }
        
        if ($valor_total_usua == 0) {
          $tpl->TOTAL_TOTAL = '0,00';
        } else {
          $tpl->TOTAL_TOTAL = number_format($valor_total_usua,2,',','.');
        }
        
        
      }
    }
  } else {
    $seg->verificaSession($_SESSION['idUsuario']);
  }
  
  
  $tpl->show();
  $bd->close();
?>