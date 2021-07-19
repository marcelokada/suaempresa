<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","email_welcome.html");
  
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
      $txt1 = "SELECT TITULO, TEXTO
            FROM TREDE_EMAIL";
      $sql1->executeQuery($txt1);
      
      $tpl->TITULO = utf8_encode($sql1->result("TITULO"));
      $tpl->TEXTO = utf8_encode($sql1->result("TEXTO"));
      
      
      if (isset($_POST['alterar'])) {
        $titulo = utf8_decode($_POST['titulo']);
        $texto = $_POST['texto'];
        
        $sql1 = new Query($bd);
        $txt1 = "UPDATE  TREDE_EMAIL SET TITULO = '".$titulo."', TEXTO = '".$texto."'";
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