<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","rel_usua_membros.html");
  
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
      
      //$tpl->USUA = $ver_admin;
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT REDE_SEQUSUA,REDE_NOMEUSU,REDE_PLANUSU
FROM TREDE_USUADMIN
WHERE REDE_TIPOUSU = 3
ORDER BY 2 ASC";
      $sql1->executeQuery($txt1);
      
      
      while (!$sql1->eof()) {
        $id = $sql1->result("REDE_SEQUSUA");
        $tpl->ID1 = $sql1->result("REDE_SEQUSUA");
        //$nome = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $id)));
        $tpl->NOME1 = utf8_encode($sql1->result("REDE_NOMEUSU"));
        
        $tpl->SITUACAO = $func->assinaturaUsuario($bd,$id);
        
        $sql2 = new Query($bd);
        $txt2 = "SELECT SEQ,CNOMEUSUA,CTIPOUSUA,CGRUPUSUA
					 FROM TREDE_MEMBROS
						WHERE NNUMETITU = :titu
					 ORDER BY 1 ASC";
        $sql2->addParam(':titu',$id);
        $sql2->executeQuery($txt2);
        
        while (!$sql2->eof()) {
          
          //$tpl->IDM = $sql2->result("SEQ");
          $ssss = $sql2->result("SEQ");
          
          $tpl->MEMBROS2 = ucwords(utf8_encode($sql2->result("CNOMEUSUA"))).' - '.$func->RetonaTipoUsuario($sql2->result("CTIPOUSUA")).' - '.$func->RetonaGrupoMembros($sql2->result("CGRUPUSUA")).'<br>';
          //		$tpl->NOMEM = ucwords(utf8_encode($sql2->result("CNOMEUSUA")));
          //		$tpl->PARENT = $func->RetonaTipoUsuario($sql2->result("CTIPOUSUA"));
          //		$tpl->GRUPO = $func->RetonaGrupoMembros($sql2->result("CGRUPUSUA"));
          $tpl->block("MEMBROS1");
          $sql2->next();
          
        }
        
        $sql1->next();
        $tpl->block("MEMBROS");
      }
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  $tpl->show();
  $bd->close();
?>