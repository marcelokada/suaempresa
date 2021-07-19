<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","config_dados_bancarios.html");
  
  
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
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT CNOMEBANCO,
								NAGENBANCO,
								CONTABANCO,
								NCPCJBANCO,
								CNOMFBANCO
            FROM TREDE_DADOS_BANCARIOS";
      $sql1->executeQuery($txt1);
      
      $tpl->NOMEBANCO = utf8_encode($sql1->result("CNOMEBANCO"));
      $tpl->AGENCIA = $sql1->result("NAGENBANCO");
      $tpl->CONTA = $sql1->result("CONTABANCO");
      $tpl->CPFCNPJ = $sql1->result("NCPCJBANCO");
      $tpl->NOME = $sql1->result("CNOMFBANCO");
      
      if (isset($_POST['alterar'])) {
        
        $nomebanco = utf8_decode($_POST['nomebanco']);
        $agencia = $_POST['agencia'];
        $conta = $_POST['conta'];
        $cpfcnpj = $_POST['cpcj'];
        $nomef = $_POST['nome'];
        
        
        $sql1 = new Query($bd);
        $txt1 = "UPDATE TREDE_DADOS_BANCARIOS SET CNOMEBANCO = '".$nomebanco."',
																						NAGENBANCO = '".$agencia."',
																						CONTABANCO = '".$conta."',
																						NCPCJBANCO = '".$cpfcnpj."',
																						CNOMFBANCO = '".$nomef."'
																						WHERE 1";
        $sql1->executeSQL($txt1);
        
        echo "<script>alert('Alterado com Sucesso.');  window.location.href = window.location.href </script>";
      }
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  $tpl->show();
  $bd->close();
?>