<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","rel_usua_dependentes.html");
  
  
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
      $txt1 = "SELECT *
FROM TREDE_MEMBROS ORDER BY SEQ ASC";
      $sql1->executeQuery($txt1);
      
      
      while (!$sql1->eof()) {
        $id = $sql1->result("SEQ");
        $tpl->ID1 = $id;
        //$nome = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $id)));
        $tpl->NOME = utf8_encode($sql1->result("CNOMEUSUA"));
        $tpl->EMAIL = utf8_encode($sql1->result("EMAILUSUA"));
        $tpl->CPF = $sql1->result("CCPF_USUA");
        $tpl->CELULAR = $sql1->result("NCEL_USUA");
        $tpl->DATA = $data->formataData1($sql1->result("DNASCUSUA"));
        $tpl->TIPO = $func->RetonaTipoUsuario($sql1->result("CTIPOUSUA"));
        $tpl->TITULAR = getNomeTitular($bd,$sql1->result("NNUMETITU"));
        $tpl->GRUPO = $func->RetonaGrupoMembros($sql1->result("CGRUPUSUA"));
        $sql1->next();
        $tpl->block("MEMBROS");
      }
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
    
  }
  
  function getNomeTitular($database,$id) {
    $query = new Query($database);
    $sql = "SELECT REDE_NOMEUSU
	              FROM TREDE_USUADMIN
	              WHERE REDE_SEQUSUA = $id limit 1";
    $query->executeQuery($sql);
    return $query->result("REDE_NOMEUSU");
  }
  
  $tpl->show();
  $bd->close();
?>