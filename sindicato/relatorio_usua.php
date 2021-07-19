<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","relatorio_usua.html");
  
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
      
      $idmsg = $_GET['idMsg'];
      
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT NNUMESIUS,
								  NNUMESIND,
								  NNUMEUSUA,
								  CSITUSIUS,
								  DINCLSIUS,
								  CSTATSIUS,
								  DCANCSIUS
		  FROM TREDE_SINDICATO_USUA
		  WHERE NNUMESIND = '".$idrede."'";
      $sql1->executeQuery($txt1);
      
      while (!$sql1->eof()) {
        $tpl->ID           = $sql1->result("NNUMESIUS");
        $tpl->REDE_SEQUSUA = $sql1->result("NNUMEUSUA");
        $tpl->REDE_NOMEUSU = ucwords($func->RetornaNomeUsuario($sql1->result("NNUMEUSUA")));
        $tpl->REDE_EMAILUS = $func->RetornaEmailUsuario($sql1->result("NNUMEUSUA"));
        $tpl->CIDADE       = $func->RetornaCidadeUsuario($sql1->result("NNUMEUSUA"));
        
        $planos = $func->RetornaNomePlano($func->RetornaUltimoPLanoUsuario($sql1->result("NNUMEUSUA")));
        
        if ($planos == "") {
          $tpl->PLANO = "Sem plano";
          
        } else {
          $tpl->PLANO = $func->RetornaNomePlano($func->RetornaUltimoPLanoUsuario($sql1->result("NNUMEUSUA")));
        }
        
        $idplano = $func->RetornaUltimoPLanoUsuario($sql1->result("NNUMEUSUA"));
        
        if ($idplano == '') {
          $tpl->VALOR = "----";
        } else {
          $tpl->VALOR = number_format($func->RetornaValorPlano($idplano),2,',','.');
        }
        
        
        $stats = $sql1->result("CSITUSIUS");
        
        if ($stats == 'a') {
          $tpl->STATUS = '<font color="green">Ativo</font>';
        } else if ($stats == 'p') {
          $tpl->STATUS = '<font color="blue">Pendente</font>';
        } else if ($stats == '') {
          $tpl->STATUS = '<font color="blue">Pendente</font>';
        }
        
        $tpl->block("USUARIOS");
        $sql1->next();
      }
    }
  } else {
    $seg->verificaSession($_SESSION['aut_sind']);
  }
  
  $tpl->show();
  $bd->close();
?>