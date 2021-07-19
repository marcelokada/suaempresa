<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  error_reporting(0);
  
  $bd = new Database();
  
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","dotbank.html");
  
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
      
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT TOKEN
		  FROM TREDE_DOTBANK_REDE 
		  WHERE SEQUENCIACRE = '".$idrede."'";
      $sql1->executeQuery($txt1);
      
      $tokens = $sql1->result("TOKEN");
      $tpl->TOKEN = $tokens;
      
      
      $sql2 = new Query($bd);
      $txt2 = "SELECT SENHA, CNPJ	FROM TREDE_DOTBANK_CONFIG_REDE WHERE SEQUENCIACRE = '".$idrede."'";
      $sql2->executeQuery($txt2);
      
      $tpl->CNPJ = $sql2->result("CNPJ");
      $tpl->SENHA = $sql2->result("SENHA");
      
      
      if (isset($_POST['alterar'])) {
        
        $idusua = $_POST['idusua'];
        $idrede = $_POST['idrede'];
        $token = $_POST['token'];
        
        $sql9 = new Query ($bd);
        $txt9 = "UPDATE TREDE_DOTBANK_REDE SET TOKEN = :token
			 WHERE SEQUENCIACRE = :seqtoken";
        $sql9->addParam(':token',$token);
        $sql9->addParam(':seqtoken',$idrede);
        $sql9->executeSQL($txt9);
        
        echo '<script>alert ("Atualizado com sucesso!"); window.location.href = window.location.href</script>';
        
      }
      
      
      if (isset($_POST['alterar_cnpj'])) {
        
        $idusua = $_POST['idusua'];
        $idrede = $_POST['idrede'];
        $cnpj = $_POST['cnpj'];
        $senha = $_POST['senha'];
        
        $sql9 = new Query ($bd);
        $txt9 = "UPDATE TREDE_DOTBANK_CONFIG_REDE SET CNPJ = :cnpj, SENHA = :senha
			 WHERE SEQUENCIACRE = :seqtoken";
        $sql9->addParam(':seqtoken',$idrede);
        $sql9->addParam(':cnpj',$cnpj);
        $sql9->addParam(':senha',$senha);
        $sql9->executeSQL($txt9);
        
        echo '<script>alert ("Atualizado com sucesso!"); window.location.href = window.location.href</script>';
        
      }
    }
  } else {
    $seg->verificaSession($_SESSION['aut_rede']);
  }
  $tpl->show();
  $bd->close();
?>