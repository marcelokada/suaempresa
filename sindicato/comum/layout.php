<?php     
$tpl = new Template("comum/padrao.html");
  
/* Menu */    
$tpl->addFile("MENU_NAV","comum/menuNav.html"); 

//$tpl->addFile("MENU_NAV","comum/admin_menu_nav.html"); 
//require_once("comum/admin_menu_nav.php");
  
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
  
  
  $sql = new Query ($bd);
  $txt = "SELECT  CNOMESIND
			FROM TREDE_SINDICATOS
			WHERE NNUMESIND = :idrede";
  $sql->AddParam(':idrede',$_SESSION['idSind']);
  $sql->executeQuery($txt);
  
  $tpl->NOMEEMPRESA = ucwords($sql->result("CNOMESIND"));
  
?>