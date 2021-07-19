<?php
  
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","index.html");
  
  if (isset($_SESSION['aut'])) {
    $autenticado     = TRUE;
    $_SESSION['aut'] = TRUE;
    $tpl->block("ACESSO");
  } else {
    $autenticado = FALSE;
    $tpl->block("NACESSO");
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    $sql_end = new Query();
    $sql_end->clear();
    $txt_end = "SELECT REDE_SEQUSUA FROM TREDE_EMPRESAS_LAYOUT
                WHERE NOMEEMPRESA = '".EMPRESA."'";
    $sql_end->executeQuery($txt_end);
    
    $num_seq = $sql_end->result('REDE_SEQUSUA');
    
    $sql_end1 = new Query();
    $sql_end1->clear();
    $txt_end1 = "SELECT REDE_ENDE,
                      REDE_NUM,
                      REDE_BAIRRO,
                      REDE_CEP,
                      REDE_CIDADE,
                      REDE_ESTADO,
                      REDE_CI_IBGE,
                      REDE_DATAVENC,
                      REDE_COMPLE,
                      REDE_EMAILUS,
                      REDE_CELULAR,
                      FACEBOOK,
                      INSTAGRAM,
                      TWITER,
                      SKYPE,
                      LINKEDIN
          FROM TREDE_USUADMIN
          WHERE REDE_SEQUSUA = '".$num_seq."'";
    $sql_end1->executeQuery($txt_end1);
    
    $tpl->CEL1   = $sql_end1->result("REDE_CELULAR");
    $tpl->EMAIL1 = $sql_end1->result("REDE_EMAILUS");
    
    $sql = new Query ($bd);
    $txt = "SELECT TEXTO
			   FROM TREDE_CONFIG_BASICS
			   WHERE EMPRESA = '".EMPRESA."'
			    AND TIPOCONFIG = 'texto_banner'";
    $sql->executeQuery($txt);
    
    $tpl->TEXTO_BANNER = utf8_encode($sql->result("TEXTO"));
    
    $sql4 = new Query($bd);
    $txt4 = "SELECT TEXTO FROM TREDE_CONFIG_BASICS WHERE TIPOCONFIG = 'texto_p1'";
    $sql4->executeQuery($txt4);
    
    $tpl->P1 = utf8_encode($sql4->result("TEXTO"));
    
    $sql5 = new Query($bd);
    $txt5 = "SELECT TEXTO FROM TREDE_CONFIG_BASICS WHERE TIPOCONFIG = 'texto_p2'";
    $sql5->executeQuery($txt5);
    
    $tpl->P2 = utf8_encode($sql5->result("TEXTO"));
    
    if ($autenticado != TRUE) {
      
      
      //SELECT DESTAQUES
      $sql2 = new Query ($bd);
      $sql2->clear();
      $txt2 = "SELECT VNOMECREDCRE,SEQUENCIACRE,CVIMAGEMCCRE
			   FROM TREDE_CREDENCIADOS
			  WHERE CSITUACAOCRE = 'a'
			  ORDER BY RAND()";
      $sql2->executeQuery($txt2);
      
      //AND CDESTAQUECRE = 's'
      
      while (!$sql2->eof()) {
        $nome_qtde = strlen($sql2->result("VNOMECREDCRE"));
        
        $tpl->VNOMECREDCRE = utf8_encode($sql2->result("VNOMECREDCRE"));
        $tpl->IDCRED       = $sql2->result("SEQUENCIACRE");
        //$tpl->CUPOM		= $sql2->result("VCUPOMDESCRE ");
        
        $id = $sql2->result("SEQUENCIACRE");
        
        $sql21 = new Query ($bd);
        $txt21 = "SELECT VCASHPRODU
			   FROM TREDE_PRODUTOS
			  WHERE CSITUPRODU = 'a'
				AND SEQUENCIACRE = :seqcre
				ORDER BY 1 DESC";
        $sql21->addParam(':seqcre',$id);
        $sql21->executeQuery($txt21);
        
        $imagem_desc = $sql2->result("CVIMAGEMCCRE");
        
        if (($imagem_desc == NULL) or (substr($imagem_desc,0,7) != 'uploads')) {
          $tpl->IMG_DESTAQUE = 'comum/img/Sem-imagem.jpg';
        } else {
          $tpl->IMG_DESTAQUE = "admin/".$imagem_desc;
        }
        
        $sql2->next();
        
        $tpl->block("DESTAQUE");
      }
      //SELECT DESTAQUES
      
      
      //SELECT NOVOS
      $sql3 = new Query ($bd);
      $txt3 = "SELECT VNOMECREDCRE,
					SEQUENCIACRE,
					SUBSTR(DDATACREDCRE,1,4) DATA,
					VCUPOMDESCRE,
					CVIMAGEMCCRE
			   FROM TREDE_CREDENCIADOS
			  WHERE CSITUACAOCRE = 'a'
				
				ORDER BY RAND()
				LIMIT 6";
      $sql3->executeQuery($txt3);
      
      //AND CDESTAQUECRE = 's'
      
      while (!$sql3->eof()) {
        $sequenciacre1 = $sql3->result("SEQUENCIACRE");
        
        $tpl->VNOMECREDCRE2 = utf8_encode($sql3->result("VNOMECREDCRE"));
        $tpl->IDCRED2       = $sql3->result("SEQUENCIACRE");
        
        $sql22 = new Query ($bd);
        $txt22 = "SELECT VCASHPRODU
			   FROM TREDE_PRODUTOS
			  WHERE CSITUPRODU = 'a'
				AND SEQUENCIACRE = :seqcre
				ORDER BY 1 DESC";
        $sql22->addParam(':seqcre',$sequenciacre1);
        $sql22->executeQuery($txt22);
        
        $cash = $sql22->result("VCASHPRODU");
        
        /*		if($cash == ''){
                    $tpl->CASHBACK		= '';
                }else{
                    $tpl->CASHBACK		= 'CashBack com até: '.$sql22->result("VCASHPRODU").'%';
                }	*/
        
        $imagem = $sql3->result("CVIMAGEMCCRE");
        
        if (($imagem == NULL) or (substr($imagem,0,7) != 'uploads')) {
          $tpl->LINIMAGEMIMG = 'comum/img/Sem-imagem.jpg';
        } else {
          $tpl->LINIMAGEMIMG = "admin/".$imagem;
        }
        
        // $tpl->LINIMAGEMIMG  = $func->RetornaImagem($bd,$sequenciacre1);
        $sql3->next();
        
        $tpl->block("NOVOS");
      }
      //SELECT NOVOS
      
      $sql7 = new Query ($bd);
      $txt7 = "SELECT SEQPLANO,
					CNOMEPLANO,
					CDESCPLANO,
					CPRIMPLANO,
					CSEGUPLANO,
					CTERCPLANO,
					CQUARPLANO,
					VVALPPLANO,
					VVALSPLANO,
					VVALTPLANO,
					MENSAPLANO
			 FROM TREDE_PLANOS
			 ORDER BY SEQPLANO ASC";
      $sql7->executeQuery($txt7);
      
      while (!$sql7->eof()) {
        $tpl->NOMEPLANO = $sql7->result("CNOMEPLANO");
        $tpl->PRECO1    = number_format($sql7->result("VVALTPLANO"),2,',','.');
        $tpl->MENSA     = $sql7->result("MENSAPLANO");
        $tpl->DESC      = $sql7->result("CDESCPLANO");
        $tpl->IDPLANO   = $sql7->result("SEQPLANO");
        
        
        if (($autenticado != TRUE) or ($autenticado == "")) {
          $tpl->BOTAOASSINAR = 'Faça um cadastro antes de assinar';
          $tpl->block("CAD_PLAN");
        } else {
          $tagass = $func->assinaturaUsuarioTAG($bd,$seq);
          if ($tagass == 'S') {
            $tpl->BOTAOASSINAR = 'Mudar de Plano';
            $tpl->block("MUDAR");
          } else {
            $tpl->BOTAOASSINAR = 'Assinar';
            $tpl->block("PLANO_A");
          }
        }
        
        $sql7->next();
        
        $tpl->block("PLANO");
      }
      
    } else {
      
      $seq       = $_SESSION['idUsuario'];
      $id_sessao = $_SESSION['idSessao'];
      
      if (isset($_GET['idSessao'])) {
        $id_confer = $_GET['idSessao'];
      }
      
      $seg->verificaSession($_SESSION['idUsuario']);
      
      $tpl->ID_SESSAO     = $_SESSION['idSessao'];
      $tpl->ID_USUA       = $_SESSION['idUsuario'];
      $_SESSION['idcrus'] = md5($_SESSION['idUsuario']);
      $_SESSION['idUsu']  = md5($_SESSION['idUsuario']);
      
      //SELECT PARA VERIFICAR O USUARIO
      $tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
      //SELECT PARA VERIFICAR O USUARIO
      
      //CASHBACK USUARIO
      $valortotal_cash = $func->RetornaValorCashBackUsuario($bd,$seq);
      $tpl->MEUCASH    = $formata->formataNumero($valortotal_cash);
      
      $valortotal_bonus = $func->RetornaValorBonusUsuario($seq);
      $tpl->MEUBONUS    = $formata->formataNumero($valortotal_bonus);
      
      $valortotal_voucher = $func->RetornaValorVoucherUsuario($seq);
      $tpl->MEUVOUCHER    = $formata->formataNumero($valortotal_voucher);
      
      $tpl->MEUPLANO = $func->assinaturaUsuarioMimo($bd,$seq);
      
      //CASHBACK USUARIO
      
      //SELECT DESTAQUES
      $sql2 = new Query ($bd);
      $txt2 = "SELECT VNOMECREDCRE,SEQUENCIACRE,CVIMAGEMCCRE,CTIPOCRED
			   FROM TREDE_CREDENCIADOS
			  WHERE CSITUACAOCRE = 'a'
			  ORDER BY RAND()";
      $sql2->executeQuery($txt2);
      
      //AND CDESTAQUECRE = 's'
      
      while (!$sql2->eof()) {
        $nome_qtde = strlen($sql2->result("VNOMECREDCRE"));
        
        $tpl->VNOMECREDCRE = utf8_encode($sql2->result("VNOMECREDCRE"));
        $tpl->IDCRED       = $sql2->result("SEQUENCIACRE");
        $tipocred          = $sql2->result("CTIPOCRED");
        
        //$tpl->CUPOM		= $sql2->result("VCUPOMDESCRE ");
        
        $id = $sql2->result("SEQUENCIACRE");
        
        $sql21 = new Query ($bd);
        $txt21 = "SELECT VCASHPRODU
			   FROM TREDE_PRODUTOS
			  WHERE CSITUPRODU = 'a'
				AND SEQUENCIACRE = :seqcre
				ORDER BY 1 DESC";
        $sql21->addParam(':seqcre',$id);
        $sql21->executeQuery($txt21);
        
        $imagem_desc = $sql2->result("CVIMAGEMCCRE");
        
        if (($imagem_desc == NULL) or (substr($imagem_desc,0,7) != 'uploads')) {
          $tpl->IMG_DESTAQUE = 'comum/img/Sem-imagem.jpg';
        } else {
          $tpl->IMG_DESTAQUE = "admin/".$imagem_desc;
        }
        
        $sql2->next();
        
        $tpl->block("DESTAQUE");
      }
      //SELECT DESTAQUES
      
      
      //SELECT NOVOS
      $sql3 = new Query ($bd);
      $txt3 = "SELECT VNOMECREDCRE,
					SEQUENCIACRE,
					SUBSTR(DDATACREDCRE,1,4) DATA,
					VCUPOMDESCRE,
					CVIMAGEMCCRE
			   FROM TREDE_CREDENCIADOS
			  WHERE CSITUACAOCRE = 'a'
				
				ORDER BY RAND()
				LIMIT 6";
      $sql3->executeQuery($txt3);
      
      //AND CDESTAQUECRE = 's'
      
      while (!$sql3->eof()) {
        $sequenciacre1     = $sql3->result("SEQUENCIACRE");
        $tpl->VNOMECREDCRE = utf8_encode($sql3->result("VNOMECREDCRE"));
        $tpl->IDCRED2      = $sql3->result("SEQUENCIACRE");
        
        $sql22 = new Query ($bd);
        $txt22 = "SELECT VCASHPRODU
			   FROM TREDE_PRODUTOS
			  WHERE CSITUPRODU = 'a'
				AND SEQUENCIACRE = :seqcre
				ORDER BY 1 DESC";
        $sql22->addParam(':seqcre',$sequenciacre1);
        $sql22->executeQuery($txt22);
        
        $cash = $sql22->result("VCASHPRODU");
        
        /*		if($cash == ''){
                    $tpl->CASHBACK		= '';
                }else{
                    $tpl->CASHBACK		= 'CashBack com até: '.$sql22->result("VCASHPRODU").'%';
                }	*/
        
        $imagem = $sql3->result("CVIMAGEMCCRE");
        
        if (($imagem == NULL) or (substr($imagem,0,7) != 'uploads')) {
          $tpl->LINIMAGEMIMG = 'comum/img/Sem-imagem.jpg';
        } else {
          $tpl->LINIMAGEMIMG = "admin/".$imagem;
        }
        
        // $tpl->LINIMAGEMIMG  = $func->RetornaImagem($bd,$sequenciacre1);
        $sql3->next();
        
        $tpl->block("NOVOS");
      }
      //SELECT NOVOS
      
      $sql7 = new Query ($bd);
      $txt7 = "SELECT SEQPLANO,
					CNOMEPLANO,
					CDESCPLANO,
					CPRIMPLANO,
					CSEGUPLANO,
					CTERCPLANO,
					CQUARPLANO,
					VVALPPLANO,
					VVALSPLANO,
					VVALTPLANO,
					MENSAPLANO
			 FROM TREDE_PLANOS
			 ORDER BY SEQPLANO ASC";
      $sql7->executeQuery($txt7);
      
      while (!$sql7->eof()) {
        $tpl->NOMEPLANO = $sql7->result("CNOMEPLANO");
        $tpl->PRECO1    = number_format($sql7->result("VVALTPLANO"),2,',','.');
        $tpl->MENSA     = $sql7->result("MENSAPLANO");
        $tpl->DESC      = $sql7->result("CDESCPLANO");
        $tpl->IDPLANO   = $sql7->result("SEQPLANO");
        
        $tagass = $func->assinaturaUsuarioTAG($bd,$seq);
        
        if ($tagass == 'S') {
          $tpl->BOTAOASSINAR = 'Mudar de Plano';
          $tpl->block("MUDAR");
        } else {
          $tpl->BOTAOASSINAR = 'Assinar';
          $tpl->block("PLANO_A");
        }
        
        $sql7->next();
        
        $tpl->block("PLANO");
      }
    }
    
  } ///NOME DA EMPRESA
  
  
  $tpl->show();
  $bd->close();
?>