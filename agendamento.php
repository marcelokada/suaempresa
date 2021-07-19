<?php
  
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","agendamento.html");
  
  
  if (isset($_SESSION['aut'])) {
    $autenticado     = TRUE;
    $_SESSION['aut'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      $tpl->ID_SESSAO = $_SESSION['idSessao'];
      
      $id_sessao = $_SESSION['idSessao'];
      $id_confer = $_GET['idSessao'];
      $seq       = $_SESSION['idUsuario'];
      
      $paginaUrl = explode("/schedule/",$_SERVER['REQUEST_URI'],FILTER_SANITIZE_URL);
      $usua      = explode("/",$paginaUrl[0],FILTER_SANITIZE_URL);
      $evento    = explode("/",$paginaUrl[1],FILTER_SANITIZE_URL);
      
      //$seg->verificaSession($id_sessao);
      
      $func->AtualizaStatusUsuario($seq);
      $tpl->IDUSUA     = $_SESSION['idUsuario'];
      
      //INFORMAÇÕES DO USUARIO
      $tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
      //INFORMAÇÕES DO USUARIO
      
      $func->AtualizaStatusUsuario($seq);
      
      $sql = new Query($bd);
      $txt = "SELECT NNUMECALE,
                     CNOMECALE,
                     DINI_CALE,
                     DFIM_CALE,
                     SEQUENCIACRE,
                     REDE_SEQUSUA,
                     CSTATCALE,
                     CSITUCALE,
                     CCOR_CALE,
                     CTIPOCALE,
                     NVALOCALE
                   FROM TREDE_CALENDARIO_CRED
                   WHERE NNUMECALE = '".$evento[1]."' ";
      $sql->executeQuery($txt);
      
      $seqcre      = $sql->result("SEQUENCIACRE");
      $tpl->IDCALE = $sql->result("NNUMECALE");
      $tpl->IDCRED = $sql->result("SEQUENCIACRE");
      $tpl->VALOR  = number_format($sql->result("NVALOCALE"),2,',','.');
      $status      = $sql->result("CSTATCALE");
      
      
      if ($status == 1) {
        $tpl->block('BTN_AGENDAR1');
        $tpl->block('MSG_AGENDAR1');
      } else if ($status == 2) {
        $tpl->block('BTN_AGENDAR2');
        $tpl->block('MSG_AGENDAR2');
      }
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT VNOMECREDCRE,
                      NNUMECATECRE,
                      NNUMECATESUB,
                      CTIPOCREDCRE
                   FROM TREDE_CREDENCIADOS
                   WHERE SEQUENCIACRE = '".$seqcre."' ";
      $sql1->executeQuery($txt1);
      
      $tpl->NOMEREDE = utf8_encode($sql1->result("VNOMECREDCRE"));
      $tpl->CATEREDE = ucwords(utf8_encode($func->RetornaNomeCategoria($bd,$sql1->result("NNUMECATECRE"))));
      $tpl->SUBCATE  = ucwords(utf8_encode($func->RetornaNomeSubCategoria($bd,$sql1->result("NNUMECATESUB"))));
      $tpl->TIPO     = ucwords(utf8_encode($func->RetornaNomeProfissao($sql1->result("CTIPOCREDCRE"))));
      
      $tpl->DATA = $data->formataData1($sql->result("DINI_CALE")).' - '.$data->formataHora($sql->result("DINI_CALE"));
      
      
      if (isset($_POST['agendar2'])) {
        
        $idusua = $_POST['idusua'];
        $idcale = $_POST['idcale'];
        $idloja = $_POST['idloja'];
        $idloca = $_POST['local'];
        $tipopg = $_POST['tipopg'];
        
        $sql = new Query($bd);
        $txt = "UPDATE TREDE_CALENDARIO_CRED SET CSTATCALE = '2',
                                                 CSITUCALE = '2',
                                                REDE_SEQUSUA = '".$idusua."',
                                                DDAGECALE = '".date('Y-m-d H:i:s')."',
                                                CCOR_CALE = '#FF0000'
                   WHERE NNUMECALE = '".$idcale."'
                    AND SEQUENCIACRE = '".$idloja."'";
        $sql->executeSQL($txt);
        
        $sql1b = new Query ($bd);
        $txt1b = "UPDATE TREDE_PAGACALENDARIO SET NNUMEUSUA = '".$idusua."',
                                                  STAPGPCAL = '2',
                                                  PAGLOPCAL = '".$idloca."',
                                                  CTIPPGCAL = '".$tipopg."'
                    WHERE NNUMECALE = '".$idcale."'
                      AND SEQUENCIACRE = '".$idloja."' ";
        $sql1b->executeSQL($txt1b);
        
        echo "<script>alert('Agendamento realizado com sucesso'); window.location.href=window.location.href;</script>";
        
      }
      
      if (isset($_POST['agendar3'])) {
        
        $idusua = $_POST['idusua'];
        $idcale = $_POST['idcale'];
        $idloja = $_POST['idloja'];
        $idloca = $_POST['local'];
        $tipopg = $_POST['tipopg'];
        
        $sql = new Query($bd);
        $txt = "UPDATE TREDE_CALENDARIO_CRED SET CSTATCALE = '2',
                                                 CSITUCALE = '2',
                                                REDE_SEQUSUA = '".$idusua."',
                                                DDAGECALE = '".date('Y-m-d H:i:s')."',
                                                CCOR_CALE = '#FF0000'
                   WHERE NNUMECALE = '".$idcale."'
                    AND SEQUENCIACRE = '".$idloja."'";
        $sql->executeSQL($txt);
        
        $sql1b = new Query ($bd);
        $txt1b = "UPDATE TREDE_PAGACALENDARIO SET NNUMEUSUA = '".$idusua."',
                                                  STAPGPCAL = '2',
                                                  PAGLOPCAL = '".$idloca."',
                                                  CTIPPGCAL = '".$tipopg."',
                                                  NNUMECALE = '".$idcale."',
                                                  DRESEPCAL = '".date('Y-m-d H:i:s')."'
                    WHERE NNUMEPCAL = '".$idcale."'
                      AND SEQUENCIACRE = '".$idloja."' ";
        $sql1b->executeSQL($txt1b);
        
        
        echo "<script>alert('Agendamento realizado com sucesso'); window.location.href=window.location.href;</script>";
        
      }
      
      
    } else {
      $seg->verificaSession($_SESSION['idUsuario']);
    }
    
    
  }
  
  $tpl->show();
  $bd->close();
?>