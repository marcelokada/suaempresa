<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","porcentagemniveis.html");
  
  
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
      $txt1 = "SELECT SEQ,NIVEL,PORCENTAGEM
			FROM TREDE_NIVELPORCENT
			ORDER BY NIVEL";
      $sql1->executeQuery($txt1);
      
      while (!$sql1->eof()) {
        $tpl->SEQ = $sql1->result("SEQ");
        $tpl->NIVEL = $sql1->result("NIVEL");
        $tpl->PORCENTAGEM = $sql1->result("PORCENTAGEM");
        $sql1->next();
        $tpl->block("PLANOS");
      }
      /////////////////ALIMENTOS E BEBIDAS/////////////////////
      
      if (isset($_POST['alterar'])) {
        
        $id = $_POST['idp'];
        $nome = $_POST['nivel'];
        $porc = $_POST['porc'];
        
        
        $sql2 = new Query($bd);
        $txt2 = "UPDATE TREDE_NIVELPORCENT SET PORCENTAGEM = :porc
                WHERE SEQ = :seq";
        $sql2->addParam(':porc',$porc);
        $sql2->addParam(':seq',$id);
        $sql2->executeSQL($txt2);
        
        header("location: porcentagemniveis.php?idSessao=".$id_sessao);
      }
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  $tpl->show();
  $bd->close();
?>