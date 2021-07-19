<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","config_basicas.html");
  
  if (isset($_SESSION['aut_admin'])) {
    $autenticado = TRUE;
    $_SESSION['aut_admin'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      $id_sessao = $_SESSION['idSessao_admin'];
      $id_confer = $_GET['idSessao'];
      $id_admin = $_SESSION['usuaAdmin'];
      
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao_admin'];
      $tpl->ID_ADMIN = $_SESSION['usuaAdmin'];
      
      
      $menu_dotbank = $func->RetornaPermissoes('MENU_DOTBANK');
      
      if ($menu_dotbank[0]['STATUS'] != '0') {
        $tpl->block('MENU_DOTBANK1');
      }
  
  
      $sql11 = new Query($bd);
      $txt11 = "SELECT REDE_SEQUSUA FROM TREDE_EMPRESAS_LAYOUT
				WHERE NOMEEMPRESA = '".EMPRESA."'";
      $sql11->executeQuery($txt11);
  
      $rede_usua = $sql11->result("REDE_SEQUSUA");
      
      $sqle = new Query($bd);
      $txte = "SELECT REDE_SEQUSUA,
                      REDE_NOMEUSU,
                      REDE_CPFUSUA,
                      REDE_ADMINUS,
                      REDE_SENHAUS,
                      REDE_TIPOUSU,
                      REDE_USUBLOC,
                      REDE_EMAILUS,
                      REDE_SERECUS,
                      REDE_DRECUSU,
                      REDE_HRECUSU,
                      REDE_DNASCUS,
                      REDE_CELULAR,
                      REDE_LOGUSUA,
                      REDE_PLANUSU,
                      REDE_SITUUSU,
                      REDE_NIVELUS,
                      REDE_LOGBLOK,
                      REDE_ENDE,
                      REDE_NUM,
                      REDE_BAIRRO,
                      REDE_CEP,
                      REDE_CIDADE,
                      REDE_ESTADO,
                      REDE_CI_IBGE,
                      REDE_DATAVENC,
                      REDE_COMPLE,
                      FACEBOOK,
                      INSTAGRAM,
                      TWITER,
                      SKYPE,
                      LINKEDIN
          FROM TREDE_USUADMIN
          WHERE REDE_SEQUSUA = '".$rede_usua."'";
      $sqle->executeQuery($txte);
      
      $tpl->CEP = $sqle->result("REDE_CEP");
      $tpl->RUA = utf8_encode($sqle->result("REDE_ENDE"));
      $tpl->NUMERO = $sqle->result("REDE_NUM");
      $tpl->COMPLE = utf8_encode($sqle->result("REDE_COMPLE"));
      $tpl->BAIRRO = utf8_encode($sqle->result("REDE_BAIRRO"));
      $tpl->CIDA = utf8_encode($sqle->result("REDE_CIDADE"));
      $tpl->UF = $sqle->result("REDE_ESTADO");
      $tpl->IBGE = $sqle->result("REDE_CI_IBGE");
      $tpl->EMAIL = $sqle->result("REDE_EMAILUS");
      $tpl->WHATS = $sqle->result("REDE_CELULAR");
      $tpl->FACEBOOK = $sqle->result("FACEBOOK");
      $tpl->INSTAGRAM = $sqle->result("INSTAGRAM");
      $tpl->TWITER = $sqle->result("TWITER");
      $tpl->SKYPE = $sqle->result("SKYPE");
      $tpl->LINKEDIN = $sqle->result("LINKEDIN");
      
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT VALOR
            FROM TREDE_SAQUE_MIN";
      $sql1->executeQuery($txt1);
      
      $tpl->VALORF = number_format($sql1->result("VALOR"),2,',','.');
      //$tpl->VALOR = $sql1->result("VALOR");
      
      
      $sql1a = new Query($bd);
      $txt1a = "SELECT SEQ,VALOR
            FROM TREDE_SAQUEMIN_UNI";
      $sql1a->executeQuery($txt1a);
      
      $tpl->VALORB = number_format($sql1a->result("VALOR"),2,',','.');
      //$tpl->VALOR = $sql1->result("VALOR");
      
      
      $sql2 = new Query($bd);
      $txt2 = "SELECT ATIVO
            FROM TREDE_CONFIG_BASICS 
            WHERE TIPOCONFIG = 'pagseguro'
            AND EMPRESA = '".EMPRESA."'";
      $sql2->executeQuery($txt2);
      
      $pags = $sql2->result("ATIVO");
      
      if ($pags == 's') {
        $tpl->SN = "SIM";
        $tpl->ID = "s";
      } else {
        $tpl->SN = "NÃO";
        $tpl->ID = "n";
      }
      
      
      $sql3 = new Query($bd);
      $txt3 = "SELECT ATIVO
            FROM TREDE_CONFIG_BASICS 
            WHERE TIPOCONFIG = 'dotbank'
            AND EMPRESA = '".EMPRESA."'";
      $sql3->executeQuery($txt3);
      
      $dots = $sql3->result("ATIVO");
      
      if ($dots == 's') {
        $tpl->SN1 = "SIM";
        $tpl->ID1 = "s";
      } else {
        $tpl->SN1 = "NÃO";
        $tpl->ID1 = "n";
      }
      
      $sql3 = new Query($bd);
      $sql3->clear();
      $txt3 = "SELECT TEXTO
            FROM TREDE_CONFIG_BASICS
            WHERE TIPOCONFIG = 'texto_banner'
              AND EMPRESA = '".EMPRESA."'";
      $sql3->executeQuery($txt3);
      
      $tpl->TEXTO_BANNER = utf8_encode($sql3->result("TEXTO"));
    
      $sql3 = new Query($bd);
      $sql3->clear();
      $txt3 = "SELECT TEXTO
            FROM TREDE_CONFIG_BASICS
            WHERE EMPRESA = '".EMPRESA."'";
      $sql3->executeQuery($txt3);
  
      $tpl->TEXTO_BANNER = utf8_encode($sql3->result("TEXTO"));
  
      $sql4 = new Query($bd);
      $txt4 = "SELECT TEXTO FROM TREDE_CONFIG_BASICS WHERE TIPOCONFIG = 'texto_p1'";
      $sql4->executeQuery($txt4);
  
      $tpl->P1 = utf8_encode($sql4->result("TEXTO"));
  
      $sql5 = new Query($bd);
      $txt5 = "SELECT TEXTO FROM TREDE_CONFIG_BASICS WHERE TIPOCONFIG = 'texto_p2'";
      $sql5->executeQuery($txt5);
  
      $tpl->P2 = utf8_encode($sql5->result("TEXTO"));
    
  
      $sql_rede = new Query($bd);
      $txt_rede = "SELECT TEXTO FROM TREDE_CONFIG_BASICS WHERE TIPOCONFIG = 'rede_cred'";
      $sql_rede->executeQuery($txt_rede);
  
      $tpl->REDE_CRED = utf8_encode($sql_rede->result("TEXTO"));
      
      
      
      /***********************************************************/
      /***********************************************************/
      /***********************************************************/
      /***********************************************************/
      /***********************************************************/
  
      if (isset($_POST['alterar_valormin'])) {
        
        $valor = $_POST['valor1'];
        $valor = str_replace('.','',$valor);
        $valor = str_replace(',','.',$valor);
        
        $sql1 = new Query($bd);
        $txt1 = "UPDATE TREDE_SAQUE_MIN SET VALOR = '".$valor."'
				WHERE SEQ = 1";
        $sql1->executeSQL($txt1);
        
        echo "<script>alert('Alterado com Sucesso.');  window.location.href = window.location.href </script>";
      }
      
      if (isset($_POST['alterar_valormin_b'])) {
        $valor = $_POST['valor2'];
        $valor = str_replace('.','',$valor);
        $valor = str_replace(',','.',$valor);
        
        $sql1a = new Query($bd);
        $txt1a = "UPDATE TREDE_SAQUEMIN_UNI SET VALOR = :valor ";
        $sql1a->addParam(':valor',$valor);
        $sql1a->executeSQL($txt1a);
        
        echo "<script>alert('Alterado com Sucesso.');  window.location.href = window.location.href </script>";
      }
      
      if (isset($_POST['alterar_pagseguro'])) {
        $simounao = $_POST['simounao'];
        
        $sql1 = new Query($bd);
        $txt1 = "UPDATE TREDE_CONFIG_BASICS SET ATIVO = :simounao
				WHERE TIPOCONFIG = 'pagseguro'";
        $sql1->addParam(':simounao',$simounao);
        $sql1->executeSQL($txt1);
        
        echo "<script>alert('Alterado com Sucesso.');  window.location.href = window.location.href </script>";
      }
      
      
      if (isset($_POST['alterar_dotbank'])) {
        $simounao_d = $_POST['simounao_d'];
        
        $sql1 = new Query($bd);
        $txt1 = "UPDATE TREDE_CONFIG_BASICS SET ATIVO = :simounaod
				WHERE TIPOCONFIG = 'dotbank'";
        $sql1->addParam(':simounaod',$simounao_d);
        $sql1->executeSQL($txt1);
        
        echo "<script>alert('Alterado com Sucesso.');  window.location.href = window.location.href </script>";
      }
      
      if (isset($_POST['alterar_texto_banner'])) {
        
        $texto = utf8_decode($_POST['texto_banner']);
        
        $sql1 = new Query($bd);
        $txt1 = "UPDATE TREDE_CONFIG_BASICS SET TEXTO = '".$texto."'
				WHERE EMPRESA = '".EMPRESA."'";
        $sql1->executeSQL($txt1);
        
        echo "<script>alert('Alterado com Sucesso.');  window.location.href = window.location.href </script>";
      }
      
      if (isset($_POST['alterar_dados'])) {

        
        $endereco = utf8_decode($_POST['rua']);
        $cep = $_POST['cep'];
        $numero = $_POST['numero'];
        $comple = utf8_decode($_POST['comple']);
        $bairro = utf8_decode($_POST['bairro']);
        $cidade = utf8_decode($_POST['cidade']);
        $uf = $_POST['uf'];
        $ibge = $_POST['ibge'];
        $whats = $_POST['whats'];
        $email = $_POST['email'];
        
        $sql1 = new Query($bd);
        $txt1 = "UPDATE TREDE_USUADMIN SET  REDE_ENDE     = '".$endereco."',
                                          REDE_CEP      = '".$cep."',
                                          REDE_NUM      = '".$numero."',
                                          REDE_COMPLE   = '".$comple."',
                                          REDE_BAIRRO   = '".$bairro."',
                                          REDE_CIDADE   = '".$cidade."',
                                          REDE_ESTADO   = '".$uf."',
                                          REDE_CI_IBGE  = '".$ibge."',
                                          REDE_CELULAR  = '".$whats."',
                                          REDE_LOGUSUA  = '".$email."'
                                          WHERE REDE_SEQUSUA = '".$rede_usua."'  ";
        $sql1->executeSQL($txt1);

        
        echo "<script>alert('Alterado com Sucesso.');  window.location.href = window.location.href </script > ";
      }
  
  
      if (isset($_POST['alterar_redeso'])) {
  
        $facebook = $_POST['facebook'];
        $instagram = $_POST['instagram'];
        $twiter = $_POST['twiter'];
        $skype = $_POST['skype'];
        $linkedin = $_POST['linked'];
        
        $sql1 = new Query($bd);
        $txt1 = "UPDATE TREDE_USUADMIN SET   FACEBOOK   = '".$endereco."',
                                             INSTAGRAM  = '".$cep."',
                                             TWITER     = '".$numero."',
                                             SKYPE      = '".$comple."',
                                             LINKEDIN   = '".$bairro."'
                                          WHERE REDE_SEQUSUA = '".$rede_usua."'  ";
        $sql1->executeSQL($txt1);
  
  
        echo "<script>alert('Alterado com Sucesso.');  window.location.href = window.location.href </script > ";
        
      }
  
      if (isset($_POST['alterar_breadcrumbs'])) {
    
        $texto = utf8_decode($_POST['bread']);
    
        $sql1 = new Query($bd);
        $txt1 = "UPDATE TREDE_CONFIG_BASICS SET TEXTO = '".$texto."'
				WHERE EMPRESA = '".EMPRESA."'
				AND TIPOCONFIG = 'rede_cred'";
        $sql1->executeSQL($txt1);
    
        echo "<script>alert('Alterado com Sucesso.');  window.location.href = window.location.href </script>";
      }
  
      if (isset($_POST['alterar_texto2'])) {
    
        $parte1 = utf8_decode($_POST['parte1']);
        $parte2 = utf8_decode($_POST['parte2']);
    
        $sql1 = new Query($bd);
        $txt1 = "UPDATE TREDE_CONFIG_BASICS SET TEXTO = '".$parte1."'
                  WHERE TIPOCONFIG = 'texto_p1'
                    AND EMPRESA = '".EMPRESA."'";
        $sql1->executeSQL($txt1);
    
        $sql2 = new Query($bd);
        $txt2 = "UPDATE TREDE_CONFIG_BASICS SET TEXTO = '".$parte2."'
                  WHERE TIPOCONFIG = 'texto_p2'
                    AND EMPRESA = '".EMPRESA."'";
        $sql2->executeSQL($txt2);
    
    
        echo "<script>alert('Alterado com Sucesso.');  window.location.href = window.location.href </script > ";
    
      }
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  
  $tpl->show();
  $bd->close();
?>