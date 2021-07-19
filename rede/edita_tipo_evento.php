<?php
  
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","edita_tipo_evento.html");
  
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
      
      $paginaUrl = explode("/evento/",$_SERVER['REQUEST_URI'],FILTER_SANITIZE_URL);
      
      $sql = new Query($bd);
      $txt = "SELECT NNUMEEVENT,
                     CNOMEEVENT,
                     CSTATEVENT,
                     DCRIAEVENT,
                     SEQUENCIACRE,
                    CTEMPEVENT
                       FROM TREDE_TIPO_EVENTO
                       WHERE NNUMEEVENT = '".$paginaUrl[1]."'";
      $sql->executeQuery($txt);
      
      $tpl->ID    = $sql->result("NNUMEEVENT");
      $tpl->NOME  = utf8_encode($sql->result("CNOMEEVENT"));
      $tpl->STAT  = $sql->result("CSTATEVENT");
      $tpl->TEMPO = $sql->result("CTEMPEVENT");
      $status     = $sql->result("CSTATEVENT");
      
      if ($status == 'a') {
        $tpl->STATUS = "Ativo";
      } else if ($status == 'c') {
        $tpl->STATUS = "Inativo";
      }
      
      if (isset($_POST['alterar'])) {
        
        $id     = $_POST['idevento'];
        $nome   = $_POST['nome'];
        $idrede = $_POST['idrede'];
        $tempo  = $_POST['tempo'];
        
        $sql = new Query($bd);
        $txt = "UPDATE TREDE_TIPO_EVENTO SET CNOMEEVENT = '".$nome."',
                                             CTEMPEVENT = '".$tempo."'
        WHERE NNUMEEVENT = '".$id."'";
        $sql->executeSQL($txt);
        
        echo "<script>alert('Evento alterado com sucesso!'); window.location.href=window.location.href;</script>";
      }
      
    }
  } else {
    $seg->verificaSession($_SESSION['aut_admin']);
  }
  
  
  $tpl->show();
  $bd->close();
?>