<?php
  //error_reporting(0);
  $tpl = new Template("comum/padrao.html");
  
  /* Menu */
  
  $tpl->addFile("MENU_NAV","comum/menuNav.html");
  
  $res_club                = $func->RetornaNomeEmpresa_NOVO(EMPRESA);
  $tpl->NOME_EMPRESA       = strtoupper($res_club);
  $res_club2               = utf8_decode($res_club);
  $_SESSION['nomeEmpresa'] = $func->RetornaNomeEmpresaPG($res_club2);
  
  if (isset($_SESSION['aut'])) {
    $seq = $_SESSION['idUsuario'];
  } else {
    $seq = "";
  }
  
  $loginusua     = $func->RetonaLoginPorSeq($bd,$_SESSION['idUsuario']);
  $tpl->LINKINDI = "https://cb.acesso.in/mimoclub.com.br/novocadastro.php?ind=".$loginusua;
  
  //menu da mimo
  if (EMPRESA == MIMOCLUBE) {
    $tpl->block("MEUS_AGENDA");
  }
  
  $tpl->ANO = date('Y');
  
  if (isset($_SESSION['aut'])) {
    $autenticado     = TRUE;
    $_SESSION['aut'] = TRUE;
  } else {
    $autenticado = FALSE;
    
  }
  
  if (MENU_DOTBANK == 'on') {
    $tpl->block("MENU_DOTBANK");
  }
  
  if (MENU_SINDICATO == 'on') {
    $tpl->block("MENU_SINDICATO");
  }
  
  if (MENU_ADD_MEMBROS == 'on') {
    $tpl->block("MENU_ADD_MEMBROS");
  }
  
  if (MENU_LAT_VOUCHER == 'on') {
    $tpl->block("MENU_LAT_VOUCHER");
  }
  
  if (MENU_LAT_PLANO == 'on') {
    $tpl->block("MENU_LAT_PLANO");
  }
  
  
  if ($seq != "") {
    $tpl->block('CAT_SMENU');
    $tpl->block('LOG_MENU');
    $tpl->block('MENU_LAT');
    $tpl->block('INDEX_MENU_ON');
  } else {
    $tpl->block('CAT_NMENU');
    $tpl->block('LOG_LOGIN');
    $tpl->block('INDEX_MENU_OFF');
  }
  
  $valortotal_cash = $func->RetornaValorCashBackUsuario($bd,$seq);
  $tpl->MEUCASH    = $formata->formataNumero($valortotal_cash);
  
  
  $valortotal_bonus = $func->RetornaValorBonusUsuario($seq);
  $tpl->MEUBONUS    = $formata->formataNumero($valortotal_bonus);
  
  $valortotal_voucher = $func->RetornaValorVoucherUsuario($seq);
  $tpl->MEUVOUCHER    = $formata->formataNumero($valortotal_voucher);
  
  $tpl->MEUPLANO = $func->assinaturaUsuarioMimo($bd,$seq);
  
  /*$loginusua = $func->RetonaLoginPorSeq($bd, $_SESSION['idUsuario']);
  $tpl->LINKINDI = "https://cb.acesso.in/drultraconvenio.com.br/novocadastro.php?ind=".$loginusua;
  
  $menu_afiliados = $func->RetornaPermissoes('MENU_AFILIADOS');
  
  if($menu_afiliados[0]['STATUS'] != '0'){
    $tpl->block('MENU_AFILIADOS');
  }
  
  $menu_caddot = $func->RetornaPermissoes('MENU_CAD_DOT');
  
  if($menu_caddot[0]['STATUS'] != '0'){
    $tpl->block('MENU_CAD_DOT');
  }*/
  
  
  //endereços
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
  
  $tpl->RUA    = utf8_encode($sql_end1->result("REDE_ENDE")).', '.$sql_end1->result("REDE_NUM");
  $tpl->COMPLE = utf8_encode($sql_end1->result("REDE_COMPLE"));
  $tpl->BAIRRO = utf8_encode($sql_end1->result("REDE_BAIRRO"));
  $tpl->CEP    = $sql_end1->result("REDE_CEP");
  $tpl->CIDADE = utf8_encode($sql_end1->result("REDE_CIDADE")).' - '.$sql_end1->result("REDE_ESTADO");
  $tpl->CEL    = $sql_end1->result("REDE_CELULAR");
  $tpl->EMAIL  = $sql_end1->result("REDE_EMAILUS");
  $tpl->FACE   = $sql_end1->result("FACEBOOK");
  $tpl->INSTA  = $sql_end1->result("INSTAGRAM");
  $tpl->TWITER = $sql_end1->result("TWITER");
  $tpl->SKYPE  = $sql_end1->result("SKYPE");
  $tpl->LINKE  = $sql_end1->result("LINKEDIN");
  
  //CATEGORIAS
  $sql = new Query();
  $sql->clear();
  $txt = "SELECT NNUMECATECAT,
                     VNOMECATECAT,
                     VSITUCATECAT
                FROM TREDE_CATEGORIAS
                WHERE NNUMECATECAT = 1";
  $sql->executeQuery($txt);
  
  $tpl->CAT1 = utf8_encode(ucwords($sql->result("VNOMECATECAT")));
  
  $sql = new Query();
  $sql->clear();
  $txt = "SELECT NNUMECATECAT,
                     VNOMECATECAT,
                     VSITUCATECAT
                FROM TREDE_CATEGORIAS
                WHERE NNUMECATECAT = 2";
  $sql->executeQuery($txt);
  
  $tpl->CAT2 = utf8_encode(ucwords($sql->result("VNOMECATECAT")));
  
  $sql = new Query();
  $sql->clear();
  $txt = "SELECT NNUMECATECAT,
                     VNOMECATECAT,
                     VSITUCATECAT
                FROM TREDE_CATEGORIAS
                WHERE NNUMECATECAT = 3";
  $sql->executeQuery($txt);
  
  $tpl->CAT3 = utf8_encode(ucwords($sql->result("VNOMECATECAT")));
  
  $sql = new Query();
  $sql->clear();
  $txt = "SELECT NNUMECATECAT,
                     VNOMECATECAT,
                     VSITUCATECAT
                FROM TREDE_CATEGORIAS
                WHERE NNUMECATECAT = 4";
  $sql->executeQuery($txt);
  
  $tpl->CAT4 = utf8_encode(ucwords($sql->result("VNOMECATECAT")));
  
  $sql = new Query();
  $sql->clear();
  $txt = "SELECT NNUMECATECAT,
                     VNOMECATECAT,
                     VSITUCATECAT
                FROM TREDE_CATEGORIAS
                WHERE NNUMECATECAT = '5'";
  $sql->executeQuery($txt);
  
  $tpl->CAT5 = utf8_encode(ucwords($sql->result("VNOMECATECAT")));