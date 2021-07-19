<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","comprapacotes.html");
  
  if (isset($_SESSION['aut_rede'])) {
    $autenticado          = TRUE;
    $_SESSION['aut_rede'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      
      $id_sessao = $_SESSION['idSessao_rede'];
      $id_confer = $_GET['idSessao'];
      $id_rede   = $_SESSION['idRede'];
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao_rede'];
      $tpl->ID_REDE   = $_SESSION['idRede'];
      
      $seg->verificaSession($id_sessao);
      
      if (isset($_GET['idmsg'])) {
        $tpl->ID_MSG = $_GET['idMsg'];
      }
      
      $pags = $func->AtivoPagSeguro();
      if ($pags == 's') {
        $tpl->block("PAGS");
      }
      
      $dots = $func->AtivoDotBank();
      if ($dots == 's') {
        $tpl->block("DOTS");
      }
      
      $transf = $func->AtivoTransf();
      if ($transf == 's') {
        $tpl->block("TRANSF");
      }
      
      
      $sql = new Query($bd);
      $txt = "SELECT NNUMEPAC,
							 CNOMEPAC,
							 CDESCPAC,
							 NPONTPAC,
							 CSITUPAC,
       				 NVALOPAC
FROM TREDE_PACOTES_REDE
WHERE CSITUPAC = 'a'
AND NNUMEPAC = '".$_GET['idpacote']."'";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $tpl->ID        = $sql->result("NNUMEPAC");
        $tpl->NOME      = $sql->result("CNOMEPAC");
        $tpl->DESC      = $sql->result("CDESCPAC");
        $tpl->PONTOS    = number_format($sql->result("NPONTPAC"),2,',','.');
        $tpl->VALOR     = number_format($sql->result("NVALOPAC"),2,',','.');
        $tpl->DATA_VENC = date('Y/m/d',strtotime("+3 days",strtotime(date('Y-m-d'))));
        
        
        $sql->next();
      }
      
      
      if (isset($_POST['solicitar'])) {
        $valor = $_POST['valor'];
        $valor = str_replace('.','',$valor);
        $valor = str_replace(',','.',$valor);
        
      }
      
    } else {
      $seg->verificaSession($_SESSION['aut_rede']);
      
    }
  }
  $tpl->show();
  $bd->close();


?>