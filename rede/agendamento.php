<?php
  
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  error_reporting(0);
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","agendamento.html");
  
  if (isset($_SESSION['aut_rede'])) {
    $autenticado          = TRUE;
    $_SESSION['aut_rede'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      $id_sessao = $_SESSION['idSessao_rede'];
      $id_confer = $_GET['idSessao'];
      $id_rede   = $_SESSION['idRede'];
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao_rede'];
      $tpl->ID_REDE   = $_SESSION['idRede'];
      
      $sql = new Query ($bd);
      $txt = "SELECT NNUMEEVENT,
                     CNOMEEVENT,
                     SEQUENCIACRE,
                     CTEMPEVENT
            FROM TREDE_TIPO_EVENTO
            WHERE CSTATEVENT = 'a'
            AND SEQUENCIACRE = '".$id_rede."'";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $tpl->NNUMEEVENT = $sql->result("NNUMEEVENT");
        $tpl->CNOMEEVENT = $sql->result("CNOMEEVENT");
        
        $tpl->block("EVENTOS");
        $sql->next();
      }
      
      if (isset($_POST['agendar'])) {
        
        $idrede      = $_POST['idrede'];
        $nome_evento = utf8_decode($_POST['nome_evento']);
        $data_evento = $_POST['data_evento'];
        $hora_string = strtotime($_POST['hora_evento'].':00');
        $hora_evento = $_POST['hora_evento'].':00';
        $tipo        = $_POST['tipo_evento'];
        $duracao     = $_POST['tempo_evento'];
        $duracao1    = strtotime($_POST['tempo_evento'].':00');
        
        $valor = $_POST['valo_evento'];
        $valor = str_replace('.','',$valor);
        $valor = str_replace(',','.',$valor);
        
        $datahora = $data_evento.' '.$hora_evento;
        
        $sqlt = new Query ($bd);
        $txtt = "SELECT STIMECALE,NNUMECALE,SUBSTR(DINI_CALE,12,10) TEMPO,CTIMECALE
            FROM TREDE_CALENDARIO_CRED
            WHERE SUBSTR(DINI_CALE,1,10) = '".$data_evento."'
            ORDER BY STIMECALE DESC
            LIMIT 1";
        $sqlt->executeQuery($txtt);
        
        //$ultimo_tempo = $sqlt->result("STIMECALE");
        $duracao_anterior = $sqlt->result("CTIMECALE");
        $ultimo_tempo     = $sqlt->result("TEMPO");
        $ultimo_tempo1    = strtotime($sqlt->result("TEMPO"));
        
        $dif_tempo = $hora_string - $ultimo_tempo1;
        
        $tempo_sol = explode(':',$duracao_anterior);
        $hora_sol  = $tempo_sol[0] * 60;
        $minu_sol  = $tempo_sol[1];
        
        $hora_minu_sol = ($hora_sol * 60) + ($minu_sol * 60);
        
        /*        sdebug('hora do evento anterior: '.$ultimo_tempo);
                sdebug('hora do evento anterior str: '.$ultimo_tempo1);
                sdebug('--------');
                sdebug('hora do evento: '.$hora_evento);
                sdebug('hora do evento str: '.$hora_string);
                sdebug('--------');
                sdebug('Diferenca: '.$dif_tempo);
                sdebug('--------');
                sdebug('duracao evento anterior :'.$duracao_anterior);
                sdebug('hora/min evento anterior :'.$hora_minu_sol);
                sdebug('asd',true);*/
        
       
        
        if ($duracao == "") {
          
          $sqlt1 = new Query ($bd);
          $txtt1 = "SELECT NNUMEEVENT,
                     CNOMEEVENT,
                     SEQUENCIACRE,
                     CTEMPEVENT
            FROM TREDE_TIPO_EVENTO
            WHERE CSTATEVENT = 'a'
            AND SEQUENCIACRE = '".$id_rede."'
            AND NNUMEEVENT = '".$tipo."'";
          $sqlt1->executeQuery($txtt1);
          
          $res_duracao = $sqlt1->result("CTEMPEVENT");
          
        } else {
          
          $res_duracao = $duracao;
          
        }
        
        
        if ($dif_tempo < $hora_minu_sol) {
          
          $tpl->MSG = "Horário indisponível. O intervalo não pode ser menor que ".$hora_minu_sol / 60 ." minutos, da sessão anterior.";
          $tpl->block("ERRO");
          
        } else {
          
          $sql1 = new Query ($bd);
          $txt1 = "INSERT INTO TREDE_CALENDARIO_CRED (CNOMECALE,DINI_CALE,SEQUENCIACRE,CSTATCALE,CSITUCALE,CCOR_CALE,CTIPOCALE,NVALOCALE,STIMECALE,CTIMECALE)
                    VALUES
                ('".$nome_evento."','".$datahora."','".$idrede."','1','1','#257e4a','".$tipo."','".$valor."','".$hora_string."','".$res_duracao."') ";
          $sql1->executeSQL($txt1);
          
          $sql1a = new Query ($bd);
          $txt1a = "SELECT LAST_INSERT_ID(NNUMECALE) NNUMECALE FROM TREDE_CALENDARIO_CRED
				ORDER BY NNUMECALE DESC
				LIMIT 1";
          $sql1a->executeQuery($txt1a);
          
          $last_idcal = $sql1a->result("NNUMECALE");
          
          $sql2 = new Query ($bd);
          $txt2 = "INSERT INTO TREDE_PAGACALENDARIO (NNUMECALE,STAPGPCAL,SEQUENCIACRE,DCRIAPCAL)
                    VALUES
                ('".$last_idcal."','1','".$idrede."','".date('Y-m-d H:i:s')."') ";
          $sql2->executeSQL($txt2);
          
          $tpl->MSG = "Evento cadastrado com sucesso";
          $tpl->block("SUCESSO");
          //echo "<script>alert('Evento cadastrado com sucesso'); window.location.href=window.location.href;</script>";
          
        }
      }
      
      if (isset($_POST['confirmar'])) {
        $idrede = $_POST['idred'];
        $idcale = $_POST['idcale'];
        
        $sql1 = new Query ($bd);
        $txt1 = "UPDATE TREDE_CALENDARIO_CRED SET CSTATCALE = 3,
                                                  CSITUCALE = 3,
                                                  DPAGACALE = '".date('Y-m-d H:i:s')."',
                                                  CCOR_CALE = '#8A2BE2'
                    WHERE NNUMECALE = '".$idcale."'
                      AND SEQUENCIACRE = '".$idrede."' ";
        $sql1->executeSQL($txt1);
        
        $sql1b = new Query ($bd);
        $txt1b = "UPDATE TREDE_PAGACALENDARIO SET STAPGPCAL = '3',
                                                  PAGOLOCAL = 's'
                    WHERE NNUMECALE = '".$idcale."'
                      AND SEQUENCIACRE = '".$idrede."' ";
        $sql1b->executeSQL($txt1b);
        
      }
      
      if (isset($_POST['limpar'])) {
        
        $idrede = $_POST['idred'];
        $idcale = $_POST['idcale'];
        
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
      
      
      if (isset($_POST['apagar'])) {
        
        $idrede = $_POST['idred'];
        $idcale = $_POST['idcale'];
        
        $sql = new Query ($bd);
        $txt = "SELECT REDE_SEQUSUA
            FROM TREDE_CALENDARIO_CRED
            WHERE NNUMECALE = '".$idcale."'
            AND SEQUENCIACRE = '".$idrede."'";
        $sql->executeQuery($txt);
        
        $res_cale = $sql->result("REDE_SEQUSUA");
        
        if (($res_cale != NULL) or ($res_cale != "")) {
          $tpl->MSG = "Você não pode apagar esse evento, pois o mesmo já se encontra reservado.";
          $tpl->block("ERRO");
        } else {
          $sql1 = new Query ($bd);
          $txt1 = "DELETE FROM TREDE_CALENDARIO_CRED
                    WHERE NNUMECALE = '".$idcale."'
                      AND SEQUENCIACRE = '".$idrede."' ";
          $sql1->executeSQL($txt1);
          
          $sql1b = new Query ($bd);
          $txt1b = "DELETE FROM TREDE_PAGACALENDARIO
                    WHERE NNUMECALE = '".$idcale."'
                      AND SEQUENCIACRE = '".$idrede."' ";
          $sql1b->executeSQL($txt1b);
          
          
          $tpl->MSG = "Evento apagado com sucesso";
          $tpl->block("SUCESSO");
        }
        
        //echo "<script>alert('Evento cadastrado com sucesso'); window.location.href=window.location.href;</script>";
        
      }
      
    }
  } else {
    $seg->verificaSession($_SESSION['aut_rede']);
  }
  
  $tpl->show();
  $bd->close();
?>