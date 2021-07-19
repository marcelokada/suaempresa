<?php
  require_once "comum/autoload.php";
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once "comum/layout.php";
  $tpl->addFile("CONTEUDO","cadastrausuario.html");
  
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
      
      if (isset($_POST['salvar'])) {
        
        $nome = utf8_decode($seg->antiInjection($_POST['nome']));
        $email = trim($seg->antiInjection($_POST['email']));
        $senha = $seg->antiInjection($_POST['senha']);
        $senha = md5($senha);
        $nivel = $seg->antiInjection($_POST['nivel']);
        
        $sql1 = new Query ($bd);
        $txt1 = "INSERT INTO TREDE_USUADMIN (REDE_NOMEUSU, REDE_ADMINUS,REDE_SENHAUS,REDE_TIPOUSU,REDE_EMAILUS)
															 VALUES 
									   					('".$nome."','s','".$senha."','1','".$email."') ";
        $sql1->executeSQL($txt1);
        
        
        $sql2 = new Query($bd);
        $txt2 = "SELECT LAST_INSERT_ID(REDE_SEQUSUA) REDE_SEQUSUA FROM TREDE_USUADMIN
						WHERE REDE_EMAILUS = '".$email."' ";
        $sql2->executeSQL($txt2);
        
        $rede_usua = $sql2->result("REDE_SEQUSUA");
        
        $sql = new Query($bd);
        $txt = "INSERT INTO TREDE_ADMINS
    		(CNOMEADMIN,
				EMAILADMIN,
				NIVELADMIN,
        SENHAADMIN,
    		 REDE_SEQUSUA,
    		 CSITUADMIN)
       VALUES
    		('".$nome."',
    		'".$email."',
    		'".$nivel."',
    		'".$senha."',
    		'".$rede_usua."',
    		'a')";
        $sql->executeSQL($txt);
        
        
        $tpl->MSG = "Cadastro realizado com sucesso!";
        $tpl->block("SUCESSO");
      }
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  $tpl->show();
  $bd->close();
