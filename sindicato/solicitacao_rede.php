<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","solicitacao_rede.html");
  
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
      
      if (isset($_GET['idmsg'])) {
        $tpl->ID_MSG = $_GET['idMsg'];
      }
      
      $sql = new Query($bd);
      $txt = "SELECT NNUMEPAC,
							 CNOMEPAC,
							 CDESCPAC,
							 NPONTPAC,
							 CSITUPAC,
       				 NVALOPAC
FROM TREDE_PACOTES_REDE
WHERE CSITUPAC = 'a'";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $tpl->ID     = $sql->result("NNUMEPAC");
        $tpl->NOME   = $sql->result("CNOMEPAC");
        $tpl->DESC   = $sql->result("CDESCPAC");
        $tpl->PONTOS = number_format($sql->result("NPONTPAC"),2,',','.');
        $tpl->VALOR  = number_format($sql->result("NVALOPAC"),2,',','.');
        
        $tpl->block("PACOTES");
        $sql->next();
      }
      
      
      if (isset($_POST['solicitar'])) {
        
        /*	sdebug($_POST['solicitar']);
          $sql1 = new Query($bd);
          $txt1 = "SELECT NNUMEPAC,
                          CNOMEPAC,
                          CDESCPAC,
                          NPONTPAC,
                          CSITUPAC,
                          NVALOPAC
                      FROM TREDE_PACOTES_REDE
                      WHERE NNUMEPAC = '".$_POST['solicitar']."' ";
          $sql1->executeQuery($txt1);
        
          */
      }
    }
  } else {
    $seg->verificaSession($_SESSION['aut_sind']);
  }
  
  $tpl->show();
  $bd->close();
?>