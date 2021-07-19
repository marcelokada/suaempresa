<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","rel_sind.html");
  
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
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT NNUMESIND,
							CNOMESIND,
							CNPJ_SIND,
							CENDESIND,
							NENDESIND,
							CCEP_SIND,
							CBAIRSIND,
							CCIDASIND,
							CESTASIND,
							CEMAISIND,
							CSENHSIND,
							CIMAGSIND,
							CTELESIND,
							CCELUSIND,
							CSITUSIND,
							DINCLSIND,
							CCOMPSIND,
							BLOCKSIND 					  
		  FROM TREDE_SINDICATOS
		  WHERE CSITUSIND = 'a'";
      $sql1->executeQuery($txt1);
      
      while (!$sql1->eof()) {
        $tpl->ID = $sql1->result("NNUMESIND");
        $tpl->IDSIND = $sql1->result("NNUMESIND");
        $idsind = $sql1->result("NNUMESIND");
        
        $tpl->NOMESIND = $sql1->result("CNOMESIND");
        $tpl->CIDADE = ucwords(utf8_encode($sql1->result("CCIDASIND"))).' - '.$sql1->result("CESTASIND");
        $tpl->EMAIL = $sql1->result("CEMAISIND");
        $tpl->DATAINC = $data->formataData1($sql1->result("DINCLSIND"));
        
        $stats = $sql1->result("CSITUSIND");
        
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