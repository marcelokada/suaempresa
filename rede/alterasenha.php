<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","alterasenha.html");
  
  if (isset($_SESSION['aut_rede'])) {
    $autenticado = TRUE;
    $_SESSION['aut_rede'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      $id_sessao = $_SESSION['idSessao_rede'];
      $id_confer = $_GET['idSessao'];
      $id_rede = $_SESSION['idRede'];
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao_rede'];
      $tpl->ID_REDE = $_SESSION['idRede'];
      
      if (isset($_GET['idMsg'])) {
        $tpl->ID_MSG = $_GET['idMsg'];
        $idmsg = $_GET['idMsg'];
      } else {
        $idmsg = "";
      }
      
      if ($idmsg != '') {
        $tpl->MSG = '<center><font color="green">Alterado com sucesso!</font></center>';
        $tpl->block("SUCESSO");
      }
      
      if (isset($_POST['alterar'])) {
        $senha1 = $_POST['senha1'];
        $senha2 = $_POST['senha2'];
        $senha_atual = $_POST['senha_atual'];
        
        $sql = new Query ($bd);
        $txt = "SELECT VSENHAREDCRE FROM TREDE_CREDENCIADOS
                WHERE SEQUENCIACRE = :idrede";
        $sql->AddParam(':idrede',$idrede);
        $sql->executeQuery($txt);
        
        $res_senha = $sql->result("VSENHAREDCRE");
        $senha22 = md5($senha2);
        
        if ($senha1 != $senha2) {
          $tpl->MSG = "As senhas novas não conferem uma com a outra.";
          $tpl->block("ERRO");
        } else if ($senha_atual != $res_senha) {
          $tpl->MSG = "A senha atual não confere.";
          $tpl->block("ERRO");
        } else if ($senha22 == $res_senha) {
          $tpl->MSG = "A senha nova é igual a senha Atual.";
          $tpl->block("ERRO");
        }else{
          
          $sql = new Query ($bd);
          $txt = "UPDATE TREDE_CREDENCIADOS SET VSENHAREDCRE = '".$senha22."'
                WHERE SEQUENCIACRE = :idrede";
          $sql->AddParam(':idrede',$idrede);
          $sql->executeSQL($txt);
  
          $tpl->MSG = "Senha Alterado com Sucesso.";
          $tpl->block("SUCESSO");
        }
        
        
      }
      
    }
  } else {
    $seg->verificaSession($_SESSION['aut_rede']);
  }
  
  $tpl->show();
  $bd->close();
?>