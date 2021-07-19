<?php
  
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","mensagens.html");
  
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
      $txt = "SELECT NNUMECONT,
                     CNOMECONT,
                     DRESPCONT,
                     CASSUCONT,
                     CEMAICONT,
                     XTEXTCONT,
                     DENVICONT,
                     DRESPCONT,
                     CSTATCONT
            FROM TREDE_MSG_CONTATO";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $tpl->ID       = $sql->result("NNUMECONT");
        $tpl->NOME     = utf8_encode($sql->result("CNOMECONT"));
        $tpl->EMAIL    = $sql->result("CEMAICONT");
        $tpl->DATAENV  = $data->formataData1($sql->result("DENVICONT")).' - '.$data->formataHora($sql->result("DENVICONT"));
        $tpl->DATARES  = $data->formataData1($sql->result("DRESPCONT")).' - '.$data->formataHora($sql->result("DRESPCONT"));
        $tpl->ASSUNTO  = utf8_encode($sql->result("CASSUCONT"));
        $tpl->MENSAGEM = utf8_encode($sql->result("XTEXTCONT"));
        
        if ($sql->result("CSTATCONT") == 'p') {
          $tpl->STATUS = 'Pendente';
          $tpl->COR    = '';
        } else if ($sql->result("CSTATCONT") == 'r') {
          $tpl->STATUS = 'Respondido';
          $tpl->COR    = '#green';
        }
        
        $tpl->block("AUT");
        $sql->next();
      }
      
      
      if (isset($_POST['arquivar'])) {
        
        $id_msg = $_POST['arquivar'];
        
        $sql = new Query();
        $txt = "UPDATE TREDE_MSG_CONTATO SET DRESPCONT = '".date('Y-m-d H:i:s')."',
                                             CSTATCONT = 'r'
            WHERE NNUMECONT = '".$id_msg."'";
        $sql->executeSQL($txt);
        
        echo "<script>alert('Autorizado com sucesso!'); window.location.href = window.location.href</script>";
      }
      
      
      if (isset($_POST['excluir'])) {
        
        $id_msg = $_POST['excluir'];
        
        $sql = new Query();
        $txt = "DELETE FROM TREDE_MSG_CONTATO
                      WHERE NNUMECONT = '".$id_msg."'";
        $sql->executeSQL($txt);
        
        echo "<script>alert('Deletado com sucesso!'); window.location.href = window.location.href</script>";
      }
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
    
  }
  
  
  $tpl->show();
  $bd->close();
?>