<?php
  require_once("comum/autoload.php");
  if(!isset($_SESSION)){
    session_start();
  }
  
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","pagseguro.html");
  
  if (isset($_SESSION['aut_rede'])) {
    $autenticado = TRUE;
    $_SESSION['aut_rede'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      $id_sessao = $_SESSION['idSessao_rede'];
      $id_confer = $_GET['idSessao'];
      $id_rede = $_SESSION['idRede'];
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao_rede'];
      $tpl->ID_REDE = $_SESSION['idRede'];
      
      if(isset($_GET['idMsg'])) {
        $tpl->ID_MSG = $_GET['idMsg'];
        $idmsg = $_GET['idMsg'];
      }else{
        $idmsg = "";
      }
      
      if ($idmsg != '') {
        $tpl->MSG = '<center><font color="green">Alterado com sucesso!</font></center>';
        $tpl->block("SUCESSO");
      }
      
      $sql = new Query ($bd);
      $txt = "SELECT VEMAILPAGSEG,VTOKENPAGSEG
			FROM TREDE_PAGSEGURO
			WHERE SEQUENCIACRE = :idrede";
      $sql->AddParam(':idrede',$id_rede);
      $sql->executeQuery($txt);
      
      $count = $sql->count();
      
      if ($count > 0) {
        $tpl->EMAIL1 = $sql->result("VEMAILPAGSEG");
        $tpl->TOKEN1 = $sql->result("VTOKENPAGSEG");
        $tpl->EMAIL = $sql->result("VEMAILPAGSEG");
        $tpl->TOKEN = $sql->result("VTOKENPAGSEG");
      } else if ($count == 0) {
        $tpl->EMAIL1 = "<font color='red'>Não Cadastrado</font>";
        $tpl->TOKEN1 = "<font color='red'>Não Cadastrado</font>";
        $tpl->DISA = "disabled";
      }
      
      $sql1 = new Query ($bd);
      $txt1 = "SELECT SEQUENCIACRE
			FROM TREDE_CREDENCIADOS
			WHERE SEQUENCIACRE = :idrede";
      $sql1->AddParam(':idrede',$id_rede);
      $sql1->executeQuery($txt1);
      
      $seqcred = $sql1->result("SEQUENCIACRE");
      $tpl->ID_CRED = $seqcred;
      
      if (isset($_POST['salvar'])) {
        $email = $seg->antiInjection($_POST['email']);
        $token = $seg->antiInjection($_POST['token']);
        $seqcred = $seg->antiInjection($_POST['seqcred']);
        
        if ($email == '') {
          $tpl->MSG = '<center><font color="RED">Email vazio</font></center>';
          $tpl->block("ERRO");
        } else if ($token == '') {
          $tpl->MSG = '<center><font color="RED">Token vazio</font></center>';
          $tpl->block("ERRO");
        } else if (($email == '') and ($token == '')) {
          $tpl->MSG = '<center><font color="RED">Preencher os campos abaixo</font></center>';
          $tpl->block("ERRO");
        } else {
          
          $sql2 = new Query ($bd);
          $txt2 = "SELECT SEQUENCIACRE
			FROM TREDE_PAGSEGURO
			WHERE SEQUENCIACRE = :seqcred";
          $sql2->AddParam(':seqcred',$seqcred);
          $sql2->executeQuery($txt2);
          
          $resseqcred = $sql2->result("SEQUENCIACRE");
          
          if ($resseqcred == '') {
            $sql3 = new Query ($bd);
            $txt3 = "INSERT INTO TREDE_PAGSEGURO (SEQUENCIACRE,VEMAILPAGSEG,VTOKENPAGSEG)
		VALUES ('".$seqcred."','".$email."','".$token."')";
            $sql3->executeSQL($txt3);
          } else {
            $sql3 = new Query ($bd);
            $txt3 = "UPDATE TREDE_PAGSEGURO SET VEMAILPAGSEG = :email, VTOKENPAGSEG = :token
				WHERE SEQUENCIACRE = :seqcred";
            $sql3->AddParam(':email',$email);
            $sql3->AddParam(':token',$token);
            $sql3->AddParam(':seqcred',$seqcred);
            $sql3->executeSQL($txt3);
          }
          
          $_SESSION['msg'] = 's';
          header("Location: pagseguro.php?idSessao=".$_GET['idSessao']."&idRede=".$_GET['idRede']."&idMsg=".$_SESSION['msg']);
          
        }
      }
    }
  } else {
    $seg->verificaSession($_SESSION['aut_rede']);
  }
  
  $tpl->show();
  $bd->close();
?>