<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","altera_senha.html");
  
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
      
      
      if (isset($_POST['alterar'])) {
        
        $senha2 = md5($seg->antiInjection($_POST['senha2']));
        $senhaatual = md5($seg->antiInjection($_POST['senhaatual']));
        
        
        $sql = new Query();
        $txt = "SELECT REDE_SENHAUS, REDE_SEQUSUA FROM TREDE_USUADMIN
				WHERE REDE_SEQUSUA = '".$id_admin."'";
        $sql->executeQuery($txt);
        
        $res_senha = $sql->result("REDE_SENHAUS");
        $res_usua = $sql->result("REDE_SEQUSUA");
        
        if ($senhaatual != $res_senha) {
          $tpl->MSG = "Senha Atual não confere!";
          $tpl->block("ERRO");
        } else {
          
          $sql = new Query();
          $txt = "UPDATE TREDE_USUADMIN SET REDE_SENHAUS = '".$senha2."'
				WHERE REDE_SEQUSUA = '".$res_usua."'";
          $sql->executeSQL($txt);
          
          $tpl->MSG = "Senha Atualizada com Sucesso!";
          $tpl->block("SUCESSO");
        }
        
      }
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
    
  }
  
  
  $tpl->show();
  $bd->close();
?>