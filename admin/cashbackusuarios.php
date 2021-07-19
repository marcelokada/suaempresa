<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","cashbackusuarios.html");
  
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
      $txt = "SELECT VVALUSCASH ,NIDUSUCASH
              FROM TREDE_CASHBACK_USU";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        //$tpl->SEQCASH = $sql->result("SEQCASH");
        $tpl->SEQCASH = $sql->result("NIDUSUCASH");
        $tpl->NOME = utf8_encode($func->RetonaNomeUsuarioPorSeq($bd,$sql->result("NIDUSUCASH")));
        $tpl->VVALUSCASH = number_format($sql->result("VVALUSCASH"),2,',','.');
        $sql->next();
        $tpl->block("CASH");
      }
      
      if (isset($_POST['alterar'])) {
        $valor = $seg->antiInjection($_POST['valor']);
        $valor = str_replace('.','',$valor);
        $valor = str_replace(',','.',$valor);
        
        $idusua = $seg->antiInjection($_POST['idp']);
        
        
        $sql = new Query($bd);
        $txt = "UPDATE TREDE_CASHBACK_USU SET VVALUSCASH = :valor
              WHERE NIDUSUCASH = :idusua";
        $sql->addParam(':valor',$valor);
        $sql->addParam(':idusua',$idusua);
        $sql->executeQuery($txt);
        
        echo "<script>alert('Atualizado com sucesso.');</script>";
        
        $util->redireciona("cashbackusuarios.php?idSessao=".$_SESSION['idSessao']);
  
      }
    }else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  $tpl->show();
  $bd->close();
?>
