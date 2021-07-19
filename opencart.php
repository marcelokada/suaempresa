<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  //error_reporting(0);
  
  $bd = new Database();
  
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","opencart.html");
  
  if (isset($_SESSION['aut'])) {
    $autenticado = TRUE;
    $_SESSION['aut'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    $seg->verificaSession($_SESSION['idUsuario']);
    
    if ($autenticado == TRUE) {
      
      $id_sessao = $_SESSION['idSessao'];
      $id_confer = $_SESSION['idUsuario'];
      $seq = $_SESSION['idUsuario'];
      
      $tpl->ID_SESSAO = $_SESSION['idSessao'];
      $tpl->IDUSUA = $_SESSION['idUsuario'];
      
      $idusua = $_SESSION['idUsuario'];
      
      //SELECT PARA VERIFICAR O USUARIO
      $tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
      //SELECT PARA VERIFICAR O USUARIO
      
      
      $sql21 = new Query ($bd);
      $txt21 = "SELECT REDE_PLANUSU
			   FROM TREDE_USUADMIN
			  WHERE REDE_SEQUSUA = :usua";
      $sql21->AddParam(':usua',$seq);
      $sql21->executeQuery($txt21);
      
      $res_assi = $sql21->result("REDE_PLANUSU");
      
      
      $sql = new Query ($bd);
      $txt = "SELECT SEQUENCIACRE, VIDCARCARR
			 FROM TREDE_CARRINHO
			WHERE REDE_SEQUSUA = :idusua
			  AND VFECHACARR = 'n'
			GROUP BY SEQUENCIACRE, VIDCARCARR
			ORDER BY VIDCARCARR DESC";
      $sql->addParam(':idusua',$seq);
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        
        $idcart = $sql->result("VIDCARCARR");
        $tpl->ID = $sql->result("VIDCARCARR");
        
        $idLoja = $sql->result("SEQUENCIACRE");
        $_SESSION['idLoja'.$idcart] = $idLoja;
        
        $tpl->ID_CRED = $sql->result("SEQUENCIACRE");
        $tpl->REDE = utf8_encode($func->RetornaNomeRede($bd,$sql->result("SEQUENCIACRE")));
        $tpl->NOMEDALOJA = utf8_encode($func->RetornaNomeRede($bd,$sql->result("SEQUENCIACRE")));
        
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
          //$quantida = $sql1->result("NQUATICARR");
          
          $tpl->DATACART = $data->formataData1($sql1->result("DDATACCARR"));
          
          $valor_total += $valtotal;
          $tpl->VALOR_TOTAL = 'R$ '.$formata->formataNumero($valor_total);
          
          $valor_cash += $cashback;
          $tpl->TOTALCASH = 'R$ '.$formata->formataNumero($valor_cash);
  
          $sql1aa = new Query ($bd);
          $txt1aa = "SELECT SUM(NQUATICARR) QTDE FROM TREDE_CARRINHO
			              WHERE VIDCARCARR = :idcart";
          $sql1aa->addParam(':idcart',$idcart);
          $sql1aa->executeQuery($txt1aa);
          
          $qtde = $sql1aa->result("QTDE");
  
          $tpl->QTDE_ITEM = $qtde;
          
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
          
          $tpl->CASHPORC = $sql_cash->result("VCASHPRODU");
          
          $tpl->block("PRODUTOS");
          $sql1->next();
        }
        
        $tpl->block("COMPRAS");
        $sql->next();
      }
    }
  }else{
    $seg->verificaSession($_SESSION['idUsuario']);
  }
  
  $tpl->show();
  $bd->close();
?>