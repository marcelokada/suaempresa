<?php
  
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","aut_empresa.html");
  
  if (isset($_SESSION['aut_admin'])) {
    $autenticado           = TRUE;
    $_SESSION['aut_admin'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      $id_sessao = $_SESSION['idSessao_admin'];
      $id_confer = $_GET['idSessao'];
      $id_admin  = $_SESSION['usuaAdmin'];
      
      $sql_rede = new Query($bd);
      $txt_rede = "SELECT TEXTO FROM TREDE_CONFIG_BASICS WHERE TIPOCONFIG = 'rede_cred'";
      $sql_rede->executeQuery($txt_rede);
      
      $tpl->REDE_CRED = utf8_encode($sql_rede->result("TEXTO"));
      
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao_admin'];
      $tpl->ID_ADMIN  = $_SESSION['usuaAdmin'];
      
      $nivelusua = $func->RetornaPermissoes_Admin($id_admin);
      
      if ($nivelusua == 'C') {
        $tpl->DISABLE = "style='display:none;'";
      } else if ($nivelusua == 'CB') {
        $tpl->DISABLE = "style='display:none;'";
      } else if ($nivelusua == 'CCAB') {
        $tpl->DISABLE = "style='display:none;'";
      }
      
      $sql = new Query();
      $sql->clear();
      $txt = "SELECT SEQUENCIACRE,
            VNOMECREDCRE,
            VNOMEENDECRE,
            NNUMEENDECRE,
            VNOMEBAIRCRE,
            VNOMECIDAMUN,
            CESTADOUFEST,
            CESTADOUFMUN,
            NNUMECATECRE,
            NNUMECATESUB,
            NNUMESERVCRE,
            NNUMEIBGEMUN,
            CSITUACAOCRE,
            DDATACREDCRE,
            NNUMEREGIREG,
            VCUPOMDESCRE,
            VLINKDESCCRE,
            VCOMPLEMECRE,
            VIMAGEMCRCRE,
            VCNPJJURICRE,
            VNUMECCEPCRE,
            NNUMETELECRE,
            NNUMECELUCRE,
            VSENHAREDCRE,
            CBLOCKREDCRE,
            VLOGEMAILCRE,
            CVIMAGEMCCRE,
            NGEOLOCALCRE,
            CDESTAQUECRE,
            CBLOCKLOGCRE,
            CLASSIFICCRE,
            FACEBOOK,
            INSTAGRAM,
            TWITER,
            SKYPE,
            LINKEDIN,
            CTIPOCREDCRE,
            NCONSELHOCRE,
            ESTACONSECRE
            FROM TREDE_CREDENCIADOS
            WHERE CSITUACAOCRE = 'p'";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $tpl->ID     = $sql->result("SEQUENCIACRE");
        $tpl->NOME   = utf8_encode($sql->result("VNOMECREDCRE"));
        $tpl->CIDADE = utf8_encode($sql->result("VNOMECIDAMUN")).' - '.$sql->result("CESTADOUFMUN");
        $tpl->EMAILC = $sql->result("VLOGEMAILCRE");
        $tpl->DATA   = $data->formataData1($sql->result("DDATACREDCRE"));
        $tpl->TELCEL = $sql->result("NNUMETELECRE").'/'.$sql->result("NNUMECELUCRE");
        $tpl->TIPO   = ucwords($func->RetornaNomeCategoria($bd,$sql->result("NNUMECATECRE")));
        
        if ($sql->result("CSITUACAOCRE") == 'p') {
          $tpl->STATUS = 'Pendente';
        }
        
        $tpl->block("AUT");
        $sql->next();
      }
  
      $sql2 = new Query();
      $sql2->clear();
      $txt2 = "SELECT SEQUENCIACRE,
            VNOMECREDCRE,
            VNOMEENDECRE,
            NNUMEENDECRE,
            VNOMEBAIRCRE,
            VNOMECIDAMUN,
            CESTADOUFEST,
            CESTADOUFMUN,
            NNUMECATECRE,
            NNUMECATESUB,
            NNUMESERVCRE,
            NNUMEIBGEMUN,
            CSITUACAOCRE,
            DDATACREDCRE,
            NNUMEREGIREG,
            VCUPOMDESCRE,
            VLINKDESCCRE,
            VCOMPLEMECRE,
            VIMAGEMCRCRE,
            VCNPJJURICRE,
            VNUMECCEPCRE,
            NNUMETELECRE,
            NNUMECELUCRE,
            VSENHAREDCRE,
            CBLOCKREDCRE,
            VLOGEMAILCRE,
            CVIMAGEMCCRE,
            NGEOLOCALCRE,
            CDESTAQUECRE,
            CBLOCKLOGCRE,
            CLASSIFICCRE,
            FACEBOOK,
            INSTAGRAM,
            TWITER,
            SKYPE,
            LINKEDIN,
            CTIPOCREDCRE,
            NCONSELHOCRE,
            ESTACONSECRE
            FROM TREDE_CREDENCIADOS
            WHERE CSITUACAOCRE = 'a'";
      $sql2->executeQuery($txt2);
  
      while (!$sql2->eof()) {
        $tpl->ID     = $sql2->result("SEQUENCIACRE");
        $tpl->NOME   = utf8_encode($sql2->result("VNOMECREDCRE"));
        $tpl->CIDADE = utf8_encode($sql2->result("VNOMECIDAMUN")).' - '.$sql2->result("CESTADOUFMUN");
        $tpl->EMAILC = $sql2->result("VLOGEMAILCRE");
        $tpl->DATA   = $data->formataData1($sql2->result("DDATACREDCRE"));
        $tpl->TELCEL = $sql2->result("NNUMETELECRE").'/'.$sql2->result("NNUMECELUCRE");
        $tpl->TIPO   = ucwords($func->RetornaNomeCategoria($bd,$sql2->result("NNUMECATECRE")));
    
        if ($sql2->result("CSITUACAOCRE") == 'a') {
          $tpl->STATUS = 'Ativo';
        }
    
        $tpl->block("AUT1");
        $sql2->next();
      }
  
  
      $sql3 = new Query();
      $sql3->clear();
      $txt3 = "SELECT SEQUENCIACRE,
            VNOMECREDCRE,
            VNOMEENDECRE,
            NNUMEENDECRE,
            VNOMEBAIRCRE,
            VNOMECIDAMUN,
            CESTADOUFEST,
            CESTADOUFMUN,
            NNUMECATECRE,
            NNUMECATESUB,
            NNUMESERVCRE,
            NNUMEIBGEMUN,
            CSITUACAOCRE,
            DDATACREDCRE,
            NNUMEREGIREG,
            VCUPOMDESCRE,
            VLINKDESCCRE,
            VCOMPLEMECRE,
            VIMAGEMCRCRE,
            VCNPJJURICRE,
            VNUMECCEPCRE,
            NNUMETELECRE,
            NNUMECELUCRE,
            VSENHAREDCRE,
            CBLOCKREDCRE,
            VLOGEMAILCRE,
            CVIMAGEMCCRE,
            NGEOLOCALCRE,
            CDESTAQUECRE,
            CBLOCKLOGCRE,
            CLASSIFICCRE,
            FACEBOOK,
            INSTAGRAM,
            TWITER,
            SKYPE,
            LINKEDIN,
            CTIPOCREDCRE,
            NCONSELHOCRE,
            ESTACONSECRE
            FROM TREDE_CREDENCIADOS
            WHERE CSITUACAOCRE = 'c'";
      $sql3->executeQuery($txt3);
  
      while (!$sql3->eof()) {
        $tpl->ID     = $sql3->result("SEQUENCIACRE");
        $tpl->NOME   = utf8_encode($sql3->result("VNOMECREDCRE"));
        $tpl->CIDADE = utf8_encode($sql3->result("VNOMECIDAMUN")).' - '.$sql3->result("CESTADOUFMUN");
        $tpl->EMAILC = $sql3->result("VLOGEMAILCRE");
        $tpl->DATA   = $data->formataData1($sql3->result("DDATACREDCRE"));
        $tpl->TELCEL = $sql3->result("NNUMETELECRE").'/'.$sql3->result("NNUMECELUCRE");
        $tpl->TIPO   = ucwords($func->RetornaNomeCategoria($bd,$sql3->result("NNUMECATECRE")));
    
        if ($sql3->result("CSITUACAOCRE") == 'c') {
          $tpl->STATUS = 'Cancelado';
        }
    
        $tpl->block("AUT2");
        $sql3->next();
      }
      
      
      if (isset($_POST['auto'])) {
        $id_rede = $_POST['id_cred'];
  
        $sql = new Query();
        $txt ="UPDATE TREDE_CREDENCIADOS SET CSITUACAOCRE = 'a'
            WHERE SEQUENCIACRE = '".$id_rede."'";
        $sql->executeSQL($txt);
  
        echo "<script>alert('Autorizado com sucesso!'); window.location.href = window.location.href</script>";
      }
      
      
      if (isset($_POST['canc'])) {
        
        echo "<script>window.location.href = window.location.href</script>";
        //$util->redireciona("../principal.php?idSessao=" . $_SESSION['idSessao']);
      }
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
    
  }
  
  
  $tpl->show();
  $bd->close();
?>