<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  if (!isset($_SESSION)) {
    session_start();
  }
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","altera_senha.html");
  
  if (isset($_SESSION['aut_sind'])) {
    $autenticado          = TRUE;
    $_SESSION['aut_sind'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      $id_sessao_c    = $_GET['idSessao'];
      $id_sessao_s    = $_SESSION['idSessao_sind'];
      $idrede         = $_SESSION['idSind'];
      $tpl->ID_SESSAO = $_SESSION['idSessao_sind'];
      $tpl->ID_SIND   = $_SESSION['idSind'];
      
      $seg->verificaSession($id_sessao_s);
      
      if(isset($_GET['idMsg'])){
        $idmsg = $_GET['idMsg'];
      }
      
      
      if (isset($_POST['alterar'])) {
        
        $senha2     = md5($seg->antiInjection($_POST['senha2']));
        $senhaatual = md5($seg->antiInjection($_POST['senhaatual']));
        
        
        $sql = new Query();
        $txt = "SELECT NNUMESIND,CSENHSIND FROM TREDE_SINDICATOS
				WHERE NNUMESIND = '".$id_admin."'";
        $sql->executeQuery($txt);
        
        $res_senha = $sql->result("CSENHSIND");
        $res_usua  = $sql->result("NNUMESIND");
        
        if ($senhaatual != $res_senha) {
          $tpl->MSG = "Senha Atual não confere!";
          $tpl->block("ERRO");
          
        } else {
          
          $sql = new Query();
          $txt = "UPDATE TREDE_SINDICATOS SET CSENHSIND = '".$senha2."'
				           WHERE NNUMESIND = '".$res_usua."'";
          $sql->executeSQL($txt);
          
          $tpl->MSG = "Senha Atualizada com Sucesso!";
          $tpl->block("SUCESSO");
          
        }
        
        
      }
    }
  } else {
    $seg->verificaSession($_SESSION['aut_sind']);
  }
  
  $tpl->show();
  $bd->close();
?>