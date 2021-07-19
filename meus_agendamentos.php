<?php
  
  require_once("./comum/autoload.php");
  $seg->secureSessionStart();
  $bd = new Database();
  
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","meus_agendamentos.html");
  
  if (isset($_SESSION['aut'])) {
    $autenticado     = TRUE;
    $_SESSION['aut'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    $sql_rede = new Query($bd);
    $txt_rede = "SELECT TEXTO FROM TREDE_CONFIG_BASICS WHERE TIPOCONFIG = 'rede_cred'";
    $sql_rede->executeQuery($txt_rede);
    
    $tpl->REDE_CRED = utf8_encode($sql_rede->result("TEXTO"));
    
    if ($autenticado == TRUE) {
      
      $id_sessao = $_SESSION['idSessao'];
      $id_confer = $_GET['idSessao'];
      $seq       = $_SESSION['idUsuario'];
      
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO  = $_SESSION['idSessao'];
      $tpl->IDUSUA     = $_SESSION['idUsuario'];
      
      //SELECT PARA VERIFICAR O USUARIO
      $tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
      //SELECT PARA VERIFICAR O USUARIO
      
      $func->AtualizaStatusUsuario($seq);
      
      $sql = new Query ($bd);
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
                     DDAGECALE,
                     DPAGACALE
                   FROM TREDE_CALENDARIO_CRED
                   WHERE REDE_SEQUSUA = :seq";
      $sql->addParam(':seq',$seq);
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $seqcre      = $sql->result("SEQUENCIACRE");
        $status      = $sql->result("CSTATCALE");
        $tpl->ID     = $sql->result("NNUMECALE");
        $nnumecale   = $sql->result("NNUMECALE");
        $tpl->IDREDE = $sql->result("SEQUENCIACRE");
        //$tpl->OBS     = utf8_encode($sql->result("CNOMECALE"));
        $tpl->DEVENTO = $data->formataData1($sql->result("DINI_CALE")).' - '.$data->formataHora($sql->result("DINI_CALE"));
        //$tpl->DANGEDA = $data->formataData1($sql->result("DDAGECALE")).' - '.$data->formataHora($sql->result("DDAGECALE"));
        $tpl->DPAGMTO = $data->formataData1($sql->result("DPAGACALE")).' - '.$data->formataHora($sql->result("DPAGACALE"));
        $tpl->TIPO    = utf8_encode($func->RetornaTipoEvento($sql->result("CTIPOCALE")));
        
        
        $sqlC = new Query ($bd);
        $txtC = "SELECT NNUMEPCAL,
                        NNUMEUSUA,
                        SEQUENCIACRE,
                        NNUMECALE,
                        DRESEPCAL,
                        STAPGPCAL,
                        CTIPPGCAL,
                        PAGLOPCAL,
                        DCRIAPCAL
                   FROM TREDE_PAGACALENDARIO
                   WHERE NNUMEUSUA = :seq
                     AND NNUMECALE = '".$nnumecale."'
                     AND SEQUENCIACRE = '".$seqcre."'
                ";
        $sqlC->addParam(':seq',$seq);
        $sqlC->executeQuery($txtC);
        
        $tppago = $sqlC->result("CTIPPGCAL");
        $local  = $sqlC->result("PAGLOPCAL");
        
        if($local == 's'){
          $tpl->LOCAL = "Sim";
        }else{
          $tpl->LOCAL = "Não";
        }
        
        if ($tppago == 'c') {
          $tpl->TPAGMTO = "Cartão";
        } else if ($tppago == 'x') {
          $tpl->TPAGMTO = "Transf. ou PIX";
        } else if ($tppago == 'd') {
          $tpl->TPAGMTO = "Dinheiro";
        } else if ($tppago == 'p') {
          $tpl->TPAGMTO = "PIX";
        }
        
        
        if ($status == '1') {
          $tpl->STATUS = "Liberado";
          $tpl->block("DESM1");
        } else if ($status == '2') {
          $tpl->STATUS = "Aguardando/Em Análise";
          $tpl->block("DESM1");
        } else if ($status == '3') {
          $tpl->STATUS = "Pago";
          $tpl->block("DESM2");
        }
        
        $sql1 = new Query($bd);
        $txt1 = "SELECT VNOMECREDCRE,
                      NNUMECATECRE,
                      NNUMECATESUB,
                      CTIPOCREDCRE
                   FROM TREDE_CREDENCIADOS
                   WHERE SEQUENCIACRE = '".$seqcre."' ";
        $sql1->executeQuery($txt1);
        
        $tpl->NOMEREDE      = utf8_encode($sql1->result("VNOMECREDCRE"));
        $tpl->CATEREDE      = utf8_encode(ucwords($func->RetornaNomeCategoria($bd,$sql1->result("NNUMECATECRE"))));
        $tpl->SUBCATE       = utf8_encode(ucwords($func->RetornaNomeSubCategoria($bd,$sql1->result("NNUMECATESUB"))));
        $tpl->ESPECIALIDADE = utf8_encode(ucwords($func->RetornaNomeProfissao($sql1->result("CTIPOCREDCRE"))));
        
        $tpl->block('AGENDAS');
        $sql->next();
      }
      
      if (isset($_POST['desmarcar'])) {
        
        $idcale = $_POST['desmarcar'];
        $idrede = $_POST['idrede'];
        
        $sql1 = new Query ($bd);
        $txt1 = "UPDATE TREDE_CALENDARIO_CRED SET REDE_SEQUSUA = '".(NULL)."',
                                                  CSTATCALE = 1,
                                                  CSITUCALE = 1,
                                                  CCOR_CALE = '#257e4a',
                                                  DDAGECALE = '".(NULL)."',
                                                  DPAGACALE = '".(NULL)."'
                    WHERE NNUMECALE = '".$idcale."'
                      AND SEQUENCIACRE = '".$idrede."' ";
        $sql1->executeSQL($txt1);
        
        $sql1b = new Query ($bd);
        $txt1b = "UPDATE TREDE_PAGACALENDARIO SET NNUMEUSUA = '".(NULL)."',
                                                  STAPGPCAL = '".(NULL)."',
                                                  PAGLOPCAL = '".(NULL)."'
                    WHERE NNUMECALE = '".$idcale."'
                      AND SEQUENCIACRE = '".$idrede."' ";
        $sql1b->executeSQL($txt1b);
        
        echo "<script>alert('Evento desmarcado com sucesso'); window.location.href=window.location.href;</script>";
      }
      
      
    } else {
      $seg->verificaSession($_SESSION['idUsuario']);
    }
  }
  
  $tpl->show();
  $bd->close();
?>





