<?php
  require_once("comum/autoload.php");
  session_start();
  //error_reporting(0);
  
  $bd = new Database();
  
  $id_sessao = $_SESSION['idSessao'];
  $id_confer = $_GET['idSessao'];
  $seq = $_SESSION['idUsuario'];
  
  $seg->verificaSession($id_sessao);
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","principal.html");
  $tpl->ID_SESSAO     = $_SESSION['idSessao'];
  $tpl->ID_USUA       = $_SESSION['idUsuario'];
  $tpl->ID_USUA_CR    = $_SESSION['idUsuario'];
  $_SESSION['idcrus'] = md5($_SESSION['idUsuario']);
  $_SESSION['idUsu']  = md5($_SESSION['idUsuario']);
  
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
  $tpl->block("MEUPLANO");
  //CASHBACK USUARIO
  
  
  //SELECT DESTAQUES
  $sql2 = new Query ($bd);
  $txt2 = "SELECT VNOMECREDCRE,SEQUENCIACRE,CVIMAGEMCCRE
			   FROM TREDE_CREDENCIADOS
			  WHERE CSITUACAOCRE = 'a'
			  ORDER BY RAND()";
  $sql2->executeQuery($txt2);
  
  //AND CDESTAQUECRE = 's'
  
  while (!$sql2->eof()) {
    $nome_qtde = strlen($sql2->result("VNOMECREDCRE"));
    
    $tpl->VNOMECREDCRE 	= utf8_encode($sql2->result("VNOMECREDCRE"));
    $tpl->IDCRED = $sql2->result("SEQUENCIACRE");
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
    }
    else {
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
    $tpl->VNOMECREDCRE  = utf8_encode($sql3->result("VNOMECREDCRE"));
    $tpl->IDCRED2 = $sql3->result("SEQUENCIACRE");
    
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
    }
    else {
      $tpl->LINIMAGEMIMG = "admin/" . $imagem;
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
    $tpl->PRECO1 = number_format($sql7->result("VVALTPLANO"),2,',','.');
    $tpl->MENSA = $sql7->result("MENSAPLANO");
    $tpl->DESC = $sql7->result("CDESCPLANO");
    $tpl->IDPLANO = $sql7->result("SEQPLANO");
    
    $tagass = $func->assinaturaUsuarioTAG($bd,$seq);
    
    if ($tagass == 'S') {
      $tpl->BOTAOASSINAR = 'Mudar de Plano';
      $tpl->block("MUDAR");
    }
    else {
      $tpl->BOTAOASSINAR = 'Assinar';
      $tpl->block("PLANO_A");
    }
    
    $sql7->next();
    
    $tpl->block("PLANO");
  }
  
  $tpl->show();
  $bd->close();
?>