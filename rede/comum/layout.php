<?php
  
  $tpl = new Template("comum/padrao.html");
  
  /* Menu */
  $tpl->addFile("MENU_NAV","comum/menuNav.html");
  
  $idrede = $_SESSION['idRede'];
  
  $sqlv = new Query($bd);
  $txtv = "SELECT  LAST_INSERT_ID(AT_ID) AT_ID,
                 AT_DATA,
                 AT_VERSAO_CODE,
                 AT_VERSAO_BANCO,
                 AT_VERSAO_REALEASE,
                 AT_RESPONSAVEL
				FROM ATUALIZACOES";
  $sqlv->executeQuery($txtv);
  
  $id = $sqlv->result("AT_ID");
  $dt = $sqlv->result("AT_DATA");
  $vc = $sqlv->result("AT_VERSAO_CODE");
  $vb = $sqlv->result("AT_VERSAO_BANCO");
  $vr = $sqlv->result("AT_VERSAO_REALEASE");
  $rp = $sqlv->result("AT_RESPONSAVEL");
  
  $tpl->ANO = date('Y');
  $tpl->VERSAO = SYS.' - DB - '.$vb;
  
  if (isset($_SESSION['aut_rede'])) {
    $autenticado          = TRUE;
    $_SESSION['aut_rede'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  
  if (EMPRESA != 'MimoClube') {
    $tpl->block("MENU_AGENDAMENTO_MIMO");
    $tpl->block("MENU_TIPOEVENTO_MIMO");
  }
  
  $tpl->NOMEEMPRESA = EMPRESA;
  
  $sql = new Query ($bd);
  $txt = "SELECT VNOMECREDCRE,CLASSIFICCRE
			FROM TREDE_CREDENCIADOS
			WHERE SEQUENCIACRE = :idrede";
  $sql->AddParam(':idrede',$idrede);
  $sql->executeQuery($txt);
  
  $tpl->NOMEREDE = ucwords(utf8_encode($sql->result("VNOMECREDCRE")));
  $star          = $sql->result("CLASSIFICCRE");
  
  if ($star == 1) {
    $tpl->O1   = "fa-star";
    $tpl->O2   = "fa-star-o";
    $tpl->O3   = "fa-star-o";
    $tpl->O4   = "fa-star-o";
    $tpl->O5   = "fa-star-o";
    $tpl->STAR = "1 (uma estrela)";
  } else if ($star == 2) {
    $tpl->O1   = "fa-star";
    $tpl->O2   = "fa-star";
    $tpl->O3   = "fa-star-o";
    $tpl->O4   = "fa-star-o";
    $tpl->O5   = "fa-star-o";
    $tpl->STAR = "2 (duas estrelas)";
  } else if ($star == 3) {
    $tpl->O1   = "fa-star";
    $tpl->O2   = "fa-star";
    $tpl->O3   = "fa-star";
    $tpl->O4   = "fa-star-o";
    $tpl->O5   = "fa-star-o";
    $tpl->STAR = "3 (três estrelas)";
  } else if ($star == 4) {
    $tpl->O1   = "fa-star";
    $tpl->O2   = "fa-star";
    $tpl->O3   = "fa-star";
    $tpl->O4   = "fa-star";
    $tpl->O5   = "fa-star-o";
    $tpl->STAR = "4 (quatro estrelas)";
  } else if ($star == 5) {
    $tpl->O1   = "fa-star";
    $tpl->O2   = "fa-star";
    $tpl->O3   = "fa-star";
    $tpl->O4   = "fa-star";
    $tpl->O5   = "fa-star";
    $tpl->STAR = "5 (cinco estrelas)";
  }
  
  $sql2 = new Query($bd);
  $txt2 = "SELECT VALCREDREDE FROM TREDE_CREDITOREDE
					WHERE SEQUENCIACRE = :cred";
  $sql2->addParam(':cred',$idrede);
  $sql2->executeQuery($txt2);
  
  $tpl->CASHBACK_REDE = number_format($sql2->result("VALCREDREDE"),2,',','.');
  
  
  $sql4 = new Query($bd);
  $txt4 = "SELECT  SUM(VVACASCARR) VVACASCARR FROM TREDE_CARRINHO
					WHERE SEQUENCIACRE = :cred
						AND SUBSTR(DDATACCARR,6,2) = '".date('m')."' ";
  $sql4->addParam(':cred',$idrede);
  $sql4->executeQuery($txt4);
  
  $tpl->CASHBACKT = number_format($sql4->result("VVACASCARR"),2,',','.');
  
  $tpl->CASHMES = date('m');
  
  //$tpl->addFile("MENU_NAV","comum/admin_menu_nav.html");
  //require_once("comum/admin_menu_nav.php");

?>