<?php
  
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","tipo_evento.html");
  
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
      
      $sql = new Query($bd);
      $txt = "SELECT NNUMEEVENT,
                     CNOMEEVENT,
                     CSTATEVENT,
                     DCRIAEVENT,
                     SEQUENCIACRE,
                     CTEMPEVENT
                       FROM TREDE_TIPO_EVENTO
                       WHERE SEQUENCIACRE = '".$id_rede."'";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $tpl->ID    = $sql->result("NNUMEEVENT");
        $tpl->NOME  = utf8_encode($sql->result("CNOMEEVENT"));
        $tpl->TEMPO = $sql->result("CTEMPEVENT");
        $status     = $sql->result("CSTATEVENT");
        
        if ($status == 'a') {
          $tpl->STATUS = "Ativo";
          $tpl->CHK    = "checked";
        } else if ($status == 'c') {
          $tpl->STATUS = "Inativo";
          $tpl->CHK    = "";
        }
        
        $tpl->block("EVENTS");
        $sql->next();
      }
      
      
      if (isset($_POST['inserir'])) {
        $nome   = utf8_decode($_POST['nome']);
        $idrede = $_POST['idrede'];
        $tempo  = $_POST['tempo'];
        
        $sql = new Query($bd);
        $txt = "INSERT INTO TREDE_TIPO_EVENTO
                                (CNOMEEVENT,
                                CSTATEVENT,
                                DCRIAEVENT,
                                SEQUENCIACRE,
                                 CTEMPEVENT)
                                VALUES
                                ('".$nome."',
                                'a',
                                '".date('Y-m-d H:i:s')."',
                                '".$idrede."',
                                '".$tempo."')";
        $sql->executeSQL($txt);
        
        //$tpl->MSG = "Evento inserido com sucesso!";
        //$tpl->block('SUCESSO');
        
        echo "<script>alert('Evento inserido com sucesso!'); window.location.href=window.location.href;</script>";
      }
      
      if (isset($_POST['apagar'])) {
        
        $id = $_POST['apagar'];
        
        $sql1 = new Query($bd);
        $txt1 = "SELECT NNUMECALE FROM TREDE_CALENDARIO_CRED
        WHERE CTIPOCALE = '".$id."'";
        $sql1->executeQuery($txt1);
        
        $reseven = $sql1->result("NNUMECALE");
        
        if (($reseven != "") or ($reseven != NULL)) {
          echo "<script>alert('Esse evento não pode ser apagado, já foi utilizado, ele ficará Inativo.'); window.location.href=window.location.href;</script>";
          
          $sql = new Query($bd);
          $txt = "UPDATE TREDE_TIPO_EVENTO SET CSTATEVENT = 'c'
                WHERE NNUMEEVENT = '".$id."'";
          $sql->executeSQL($txt);
          
        } else {
          
          $sql = new Query($bd);
          $txt = "DELETE FROM TREDE_TIPO_EVENTO
                        WHERE NNUMEEVENT = '".$id."'";
          $sql->executeSQL($txt);
          
          echo "<script>alert('Evento apagado com sucesso!'); window.location.href=window.location.href;</script>";
          
        }
        
      }
      
    }
  } else {
    $seg->verificaSession($_SESSION['aut_admin']);
  }
  
  
  $tpl->show();
  $bd->close();
?>