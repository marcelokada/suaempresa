<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  error_reporting(0);
  
  $bd = new Database();
  $seg->verificaSession($_SESSION['idSessao_admin']);
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","admin_categorias.html");
  
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
      
      $tpl->ID_SESSAO = $_SESSION['idSessao_admin'];
      $tpl->ID_ADMIN = $_SESSION['usuaAdmin'];
  
      $sql = new Query();
      $sql->clear();
      $txt = "SELECT NNUMECATECAT,
                     VNOMECATECAT,
                     VSITUCATECAT
                FROM TREDE_CATEGORIAS
                WHERE NNUMECATECAT = 1";
      $sql->executeQuery($txt);
      
      $tpl->CAT1 = utf8_encode($sql->result("VNOMECATECAT"));
      $tpl->NCAT1 = utf8_encode($sql->result("NNUMECATECAT"));
  
      $sql = new Query();
      $sql->clear();
      $txt = "SELECT NNUMECATECAT,
                     VNOMECATECAT,
                     VSITUCATECAT
                FROM TREDE_CATEGORIAS
                WHERE NNUMECATECAT = 2";
      $sql->executeQuery($txt);
  
      $tpl->CAT2 = utf8_encode($sql->result("VNOMECATECAT"));
      $tpl->NCAT2 = utf8_encode($sql->result("NNUMECATECAT"));
  
      $sql = new Query();
      $sql->clear();
      $txt = "SELECT NNUMECATECAT,
                     VNOMECATECAT,
                     VSITUCATECAT
                FROM TREDE_CATEGORIAS
                WHERE NNUMECATECAT = 3";
      $sql->executeQuery($txt);
  
      $tpl->CAT3 = utf8_encode($sql->result("VNOMECATECAT"));
      $tpl->NCAT3 = utf8_encode($sql->result("NNUMECATECAT"));
  
      $sql = new Query();
      $sql->clear();
      $txt = "SELECT NNUMECATECAT,
                     VNOMECATECAT,
                     VSITUCATECAT
                FROM TREDE_CATEGORIAS
                WHERE NNUMECATECAT = 4";
      $sql->executeQuery($txt);
  
      $tpl->CAT4 = utf8_encode($sql->result("VNOMECATECAT"));
      $tpl->NCAT4 = utf8_encode($sql->result("NNUMECATECAT"));
  
      $sql = new Query();
      $sql->clear();
      $txt = "SELECT NNUMECATECAT,
                     VNOMECATECAT,
                     VSITUCATECAT
                FROM TREDE_CATEGORIAS
                WHERE NNUMECATECAT = '5'";
      $sql->executeQuery($txt);
  
      $tpl->CAT5 = utf8_encode($sql->result("VNOMECATECAT"));
      $tpl->NCAT5 = utf8_encode($sql->result("NNUMECATECAT"));
      
      if (isset($_POST['alterar'])) {
        
        $cat1 =  utf8_decode($_POST['cat1']);
        $ncat1 =  $_POST['ncat1'];
        
        $cat2 =  utf8_decode($_POST['cat2']);
        $ncat2 =  $_POST['ncat2'];
        
        $cat3 =  utf8_decode($_POST['cat3']);
        $ncat3 =  $_POST['ncat3'];
        
        $cat4 =  utf8_decode($_POST['cat4']);
        $ncat4 =  $_POST['ncat4'];
        
        $cat5 =  utf8_decode($_POST['cat5']);
        $ncat5 =  $_POST['ncat5'];
        
        $sql = new Query();
        $sql->clear();
        $txt = "UPDATE TREDE_CATEGORIAS SET VNOMECATECAT = '".$cat1."'
                WHERE NNUMECATECAT = '".$ncat1."'";
        $sql->executeSQL($txt);
  
        $sql = new Query();
        $sql->clear();
        $txt = "UPDATE TREDE_CATEGORIAS SET VNOMECATECAT = '".$cat2."'
                WHERE NNUMECATECAT = '".$ncat2."'";
        $sql->executeSQL($txt);
  
        $sql = new Query();
        $sql->clear();
        $txt = "UPDATE TREDE_CATEGORIAS SET VNOMECATECAT = '".$cat3."'
                WHERE NNUMECATECAT = '".$ncat3."'";
        $sql->executeSQL($txt);
  
        $sql = new Query();
        $sql->clear();
        $txt = "UPDATE TREDE_CATEGORIAS SET VNOMECATECAT = '".$cat4."'
                WHERE NNUMECATECAT = '".$ncat4."'";
        $sql->executeSQL($txt);
  
        $sql = new Query();
        $sql->clear();
        $txt = "UPDATE TREDE_CATEGORIAS SET VNOMECATECAT = '".$cat5."'
                WHERE NNUMECATECAT = '".$ncat5."'";
        $sql->executeSQL($txt);
        
        echo "<script>window.location.href = window.location.href;</script>";
        
      }
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
    
  }
  
  
  $tpl->show();
  $bd->close();
?>