<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  require_once("comum/apagaArquivos.php");
  error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","listagem_usuario_aut.html");
  
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
      $txt1 = "SELECT REDE_SEQUSUA,
									REDE_NOMEUSU,
									REDE_CPFUSUA,
									REDE_EMAILUS,
									REDE_DNASCUS,
									REDE_LOGUSUA,
									REDE_LOGBLOK,
       						REDE_SITUUSU,
					        CSITUSIUS,
       						NNUMESIND,
       						NNUMESIUS,
       						CSTATSIUS
		  FROM TREDE_USUADMIN N, TREDE_SINDICATO_USUA U
		  WHERE N.REDE_SEQUSUA = U.NNUMEUSUA
		    AND REDE_ADMINUS = 'n'
		    AND NNUMESIND = '".$idrede."'";
      $sql1->executeQuery($txt1);
      
      while (!$sql1->eof()) {
        $seqprodu          = $sql1->result("REDE_SEQUSUA");
        $tpl->ID           = $sql1->result("NNUMESIUS");
        $tpl->REDE_SEQUSUA = $sql1->result("REDE_SEQUSUA");
        $tpl->REDE_NOMEUSU = ucwords(utf8_encode($sql1->result("REDE_NOMEUSU")));
        $tpl->REDE_EMAILUS = $sql1->result("REDE_EMAILUS");
        $status            = $sql1->result("CSITUSIUS");
        
        $stats = $sql1->result("CSTATSIUS");
        
        if ($stats == '') {
          if ($status == 'a') {
            $tpl->STATUS = '<font color="green">Ativo</font>';
          } else if ($status == 'p') {
            $tpl->STATUS = '<font color="blue">Pendente</font>';
          }
          $tpl->block('BTNEXC');
        } else if ($stats == 'p') {
          $tpl->block('BTNEXC1');
          $tpl->STATUS = '<font color="red">Aguandando aprovação do Administrador</font>';
        } else if ($stats == 'a') {
          $tpl->block('BTNEXC1');
          $tpl->STATUS = '<font color="green">Ativo</font>';
        }
        
        
        $tpl->block("USUARIOS");
        $sql1->next();
      }
      
      if (isset($_POST['excluir'])) {
        $idusua = $_POST['excluir'];
        
        $sql1aa = new Query ($bd);
        $txt1aa = "UPDATE TREDE_SINDICATO_USUA SET CSTATSIUS = 'p'
						WHERE NNUMESIUS = '".$idusua."'";
        $sql1aa->executeSQL($txt1aa);
        
        echo "<script>alert('Solicitação de Exclusão Solicitada com Sucecsso!'); window.location.href = window.location.href</script>";
      }
      
    }
  } else {
    $seg->verificaSession($_SESSION['aut_sind']);
  }
  
  $tpl->show();
  $bd->close();
?>