<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  //error_reporting(0);
  
  $bd = new Database();
  
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","classrede.html");
  
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
      
      
      $nivelusua = $func->RetornaPermissoes_Admin($id_admin);
      
      if ($nivelusua == 'C') {
        $tpl->DISABLE = "style='display:none;'";
      } else if ($nivelusua == 'CB') {
        $tpl->DISABLE = "style='display:none;'";
      }
      
      $sql = new Query($bd);
      $sql->clear();
      $txt = "SELECT SEQUENCIA,NNUMECLASS,CASHBCLASS
			FROM TREDE_CLASSREDE
			ORDER BY 2";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $star = $sql->result("NNUMECLASS");
        $tpl->NCLASS = $sql->result("NNUMECLASS");
        if ($star == 1) {
          $tpl->ESTRELAS = '<img src="../admin/images/icons/Star.png">';
        } else if ($star == 2) {
          $tpl->ESTRELAS = '<img src="../admin/images/icons/Star.png"><img src="../admin/images/icons/Star.png">';
        } else if ($star == 3) {
          $tpl->ESTRELAS = '<img src="../admin/images/icons/Star.png"><img src="../admin/images/icons/Star.png"><img src="../admin/images/icons/Star.png">';
        } else if ($star == 4) {
          $tpl->ESTRELAS = '<img src="../admin/images/icons/Star.png"><img src="../admin/images/icons/Star.png"><img src="../admin/images/icons/Star.png"><img src="../admin/images/icons/Star.png">';
        } else if ($star == 5) {
          $tpl->ESTRELAS = '<img src="../admin/images/icons/Star.png"><img src="../admin/images/icons/Star.png"><img src="../admin/images/icons/Star.png"><img src="../admin/images/icons/Star.png"><img src="../admin/images/icons/Star.png">';
        }
        
        $tpl->CASHCLASS = $sql->result("CASHBCLASS");
        $tpl->block("CLASS");
        $sql->next();
      }
      
      if (isset($_POST['alterar'])) {
        $class = $_POST['class'];
        $cashb = $_POST['cash'];
        
        $sql = new Query($bd);
        $sql->clear();
        $txt = "UPDATE TREDE_CLASSREDE SET CASHBCLASS = :cash
            WHERE NNUMECLASS = :class";
        $sql->addParam(':cash',$cashb);
        $sql->addParam(':class',$class);
        $sql->executeSQL($txt);
        
        echo "<script>alert('Atualizado com sucesso.');</script>";
        $util->redireciona("classrede.php?idSessao=".$_SESSION['idSessao']);
      }
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  $tpl->show();
  $bd->close();
?>
