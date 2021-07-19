<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","listagem_usuario.html");
  
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
      
      
      if (isset($_POST['listar'])) {
  
        $cpf = $_POST['cpf'];
  
        $cpf = $func->retirarPontostracosundelinebarra($cpf);
  
        if (isset($_POST['listar'])) {
    
          $cpf = $_POST['cpf'];
    
          $cpf = $func->retirarPontostracosundelinebarra($cpf);
    
          $sql1 = new Query($bd);
          $txt1 = "SELECT REDE_SEQUSUA,
                      REDE_NOMEUSU,
                      REDE_CPFUSUA,
                      REDE_EMAILUS,
                      REDE_DNASCUS,
                      REDE_LOGUSUA,
                      REDE_LOGBLOK,
                       REDE_SITUUSU
          FROM TREDE_USUADMIN
          WHERE REDE_ADMINUS = 'n'
            AND REDE_CPFUSUA = '".$cpf."'
            AND REDE_SEQUSUA NOT IN(SELECT NNUMEUSUA FROM TREDE_SINDICATO_USUA
                WHERE CSITUSIUS IN ('p','a'))";
          $sql1->executeQuery($txt1);
    
          while (!$sql1->eof()) {
      
            if ($sql1->count("REDE_SEQUSUA") == 0) {
              $tpl->block("NUSUARIOS");
            } else {
              $seqprodu          = $sql1->result("REDE_SEQUSUA");
              $tpl->REDE_SEQUSUA = $sql1->result("REDE_SEQUSUA");
              $tpl->REDE_CPFUSUA = $sql1->result("REDE_CPFUSUA");
              $tpl->REDE_NOMEUSU = ucwords(utf8_encode($sql1->result("REDE_NOMEUSU")));
              $tpl->REDE_EMAILUS = $sql1->result("REDE_EMAILUS");
              $tpl->block("USUARIOS");
              $sql1->next();
            }
      
      
          }
    
        }
  
        if (isset($_POST['aplicar'])) {
          $indi   = $_POST['indi'];
          $idusua = $_POST['idusua'];
    
          $sql1aa = new Query ($bd);
          $txt1aa = "INSERT INTO TREDE_SINDICATO_USUA (NNUMESIND,
																								NNUMEUSUA,
																								CSITUSIUS,
																								DINCLSIUS)
									VALUES
									('".$idrede."','".$idusua."','p','".date('Y-m-d H:i:s')."')";
          $sql1aa->executeSQL($txt1aa);
    
    
          $sql2 = new Query ($bd);
          $txt2 = "INSERT INTO TREDE_AFILIADOS_VEND
			(NNUMEUSUA,NNUMEVEND,DINCLAFIL)
				VALUES
			('".$idusua."','".$indi."','".date('Y-m-d H:i:s')."')";
          $sql2->executeSQL($txt2);
    
          echo "<script>alert('Solicitação Enviada com Sucecsso!'); window.location.href = window.location.href</script>";
        }
        
      }
    }
  }else {
    $seg->verificaSession($_SESSION['aut_rede']);
  }
  $tpl->show();
  $bd->close();
?>