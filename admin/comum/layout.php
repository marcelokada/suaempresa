<?php
  session_start();
  
  
  $tpl = new Template("comum/padrao.html");
  $func = new Funcao();
  
  $sql = new Query($bd);
  $txt = "SELECT  LAST_INSERT_ID(AT_ID) AT_ID,
                 AT_DATA,
                 AT_VERSAO_CODE,
                 AT_VERSAO_BANCO,
                 AT_VERSAO_REALEASE,
                 AT_RESPONSAVEL
				FROM ATUALIZACOES";
  $sql->executeQuery($txt);
  
  $id = $sql->result("AT_ID");
  $dt = $sql->result("AT_DATA");
  $vc = $sql->result("AT_VERSAO_CODE");
  $vb = $sql->result("AT_VERSAO_BANCO");
  $vr = $sql->result("AT_VERSAO_REALEASE");
  $rp = $sql->result("AT_RESPONSAVEL");
  
  $tpl->ANO = date('Y');
  $tpl->VERSAO = SYS.' - DB - '.$vb;
  
  $usua_admin = $_SESSION['usuaAdmin'];
  $id_admin = $_SESSION['usuaAdmin'];
  
  $seg->verificaSessionNovo($_SESSION['usuaAdmin']);
  
  $admin_master = $func->RetornaAdminMaster($usua_admin);
  
  if ($admin_master == 's') {
    $tpl->addFile("MENU_NAV","comum/menuNav_master.html");
  } else {
    $tpl->addFile("MENU_NAV","comum/menuNav.html");
  
  
  
  //menu da mimo
  if(MENU_PROFISSAO == 'on') {
    $tpl->block("PROFISSAO");
  }
  
  //menu da mimo
  if(NOME_AUT_MENU == 'on'){
    
    $sql_rede = new Query($bd);
    $txt_rede = "SELECT TEXTO
                 FROM TREDE_CONFIG_BASICS
                 WHERE TIPOCONFIG = 'rede_cred'";
    $sql_rede->executeQuery($txt_rede);
    
    $tpl->REDE_CRED = utf8_encode($sql_rede->result("TEXTO"));
  }
  
  $tpl->NOME_EMPRESA = strtoupper(EMPRESA);
  
  $permissao_admin = $func->RetornaNivelPermissaoAdmin($usua_admin);
  
  $sql = new Query();
  $txt = "SELECT REDE_NOMEUSU FROM TREDE_USUADMIN
            WHERE REDE_SEQUSUA = ".$usua_admin."";
  $sql->executeQuery($txt);
  
  $tpl->LOGADO = ' - '.$sql->result("REDE_NOMEUSU");
  
  if ($admin_master != 's') {
    
    $menu_afiliados = $func->RetornaPermissoes('MENU_AFILIADOS');
    
    if ($menu_afiliados[0]['STATUS'] != '0') {
      $tpl->block('MENU_AFILIADOS');
    }
    
    $menu_dotbank = $func->RetornaPermissoes('MENU_DOTBANK');
    
    if ($menu_dotbank[0]['STATUS'] != '0') {
      $tpl->block('MENU_DOTBANK');
    }
    
    $menu_porcet_rede = $func->RetornaPermissoes('MENU_PORCENTAGEM_REDE');
    
    if ($menu_porcet_rede[0]['STATUS'] != '0') {
      $tpl->block('MENU_PORCENTAGEM_REDE_S');
    }
    
    $menu_rel_sind = $func->RetornaPermissoes('MENU_REL_SINDICATOS');
    
    if ($menu_rel_sind[0]['STATUS'] != '0') {
      $tpl->block('MENU_REL_SINDICATOS');
    }
  
    $menu_contrato = $func->RetornaPermissoes('MENU_CONTRATO');
  
    if ($menu_contrato[0]['STATUS'] != '0') {
      $tpl->block('MENU_CONTRATO');
    }
  
  }
    
    $nivelusua = $func->RetornaPermissoes_Admin($id_admin);
    
    if ($nivelusua == 'C') {
      $tpl->DISABLE = "style='display:none;'";
    } else if ($nivelusua == 'CB') {
      $tpl->DISABLE = "style='display:none;'";
      $tpl->block('NIVEL4_BACKOFIICE');
      $tpl->block('NIVEL234_CADASTRO');
      $tpl->block('NIVEL234_CADASTRO_SIND');
    } else if ($nivelusua == 'CCAB') {
      $tpl->DISABLE1 = "style='display:none;'";
      $tpl->block('NIVEL34_CONFIG_BAS');
      $tpl->block('NIVEL4_PACOTES');
      $tpl->block('NIVEL4_BACKOFIICE');
      $tpl->block('NIVEL4_DOTBANK');
      $tpl->block('NIVEL234_CADASTRO');
      $tpl->block('NIVEL234_CADASTRO_SIND');
      $tpl->block('NIVEL34_CONFIG_IMG');
      $tpl->block('NIVEL34_CONFIG_PORC_NVL');
      $tpl->block('NIVEL34_CONFIG_PORC_NVL2');
      $tpl->block('NIVEL4_EMAIL_WELCOME');
    } else if ($nivelusua == 'CCABA') {
      $tpl->block('NIVEL34_CONFIG_BAS');
      $tpl->block('NIVEL4_PACOTES');
      $tpl->block('NIVEL4_DOTBANK');
      $tpl->block('NIVEL4_BACKOFIICE');
      $tpl->block('NIVEL234_AUTORIZACOES');
      $tpl->block('NIVEL234_CADASTRO');
      $tpl->block('NIVEL4_CADASTRO_ADMIN');
      $tpl->block('NIVEL234_CADASTRO_SIND');
      $tpl->block('NIVEL34_CONFIG_IMG');
      $tpl->block('NIVEL34_CONFIG_PORC_NVL');
      $tpl->block('NIVEL34_CONFIG_PORC_NVL2');
      $tpl->block('NIVEL4_CONTRATO');
      $tpl->block('NIVEL4_SENHA');
      $tpl->block('NIVEL4_EMAIL_WELCOME');
    }else{
      $tpl->block('ERROS');
      
      $tpl->ERRONIVEL = "CONFIGURAR NO BANCO PERMISSAO_ADMINS.<hr>";
      
    }
  }

?>