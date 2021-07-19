<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","rel_usua_sind.html");
  
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
      
      
      $idmsg = $_GET['idMsg'];
      
      $id_sind = $_GET['idSind'];
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT NNUMESIUS,
       						NNUMESIND,
       						NNUMEUSUA,
									CSITUSIUS,
									DINCLSIUS,
									CSTATSIUS,
       						REDE_EMAILUS,
       						REDE_CIDADE,
       						REDE_ESTADO
		  FROM TREDE_SINDICATO_USUA S, TREDE_USUADMIN U
		  WHERE S.NNUMEUSUA = U.REDE_SEQUSUA
 				AND NNUMESIND = '".$id_sind."'";
      $sql1->executeQuery($txt1);
      
      while (!$sql1->eof()) {
        $tpl->ID = $sql1->result("NNUMESIUS");
        $tpl->NOMEUSUA = $func->RetornaNomeUsuarioSeq($sql1->result("NNUMEUSUA"));
        
        $tpl->PLANO = $func->RetornaNomePlano($func->RetornaUltimoPLanoUsuario($sql1->result("NNUMEUSUA")));
        
        $idplano = $func->RetornaUltimoPLanoUsuario($sql1->result("NNUMEUSUA"));
        
        if ($idplano == '') {
          $tpl->VALOR = "----";
        } else {
          $tpl->VALOR = number_format($func->RetornaValorPlano($idplano),2,',','.');
        }
        
        $tpl->CIDADE = ucwords($sql1->result("REDE_CIDADE")).' - '.$sql1->result("REDE_ESTADO");
        $tpl->EMAIL = $sql1->result("REDE_EMAILUS");
        $tpl->DATAINC = $data->formataData1($sql1->result("DINCLSIUS"));
        
        $stats = $sql1->result("CSITUSIUS");
        
        if ($stats == 'a') {
          $tpl->STATUS = '<font color="green">Ativo</font>';
        } else if ($stats == 'p') {
          $tpl->STATUS = '<font color="blue">Pendente</font>';
        } else if ($stats == 'c') {
          $tpl->STATUS = '<font color="red">Cancelado</font>';
        }
        
        $tpl->block("USUARIOS");
        $sql1->next();
      }
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
    
  }
  
  $tpl->show();
  $bd->close();
?>