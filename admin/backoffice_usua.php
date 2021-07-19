<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  //error_reporting(0);
  
  $bd = new Database();
  $func = new Funcao();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","backoffice_usua.html");
  
  
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
      
      
      /////////////////ALIMENTOS E BEBIDAS/////////////////////
      $sql1 = new Query($bd);
      $txt1 = "SELECT REDE_SEQUSUA,
                    REDE_NOMEUSU,
                    REDE_EMAILUS,
                    REDE_SITUUSU,
                    REDE_PLANUSU,
                    REDE_LOGUSUA,
       						  REDE_LOGBLOK
			FROM TREDE_USUADMIN
			WHERE REDE_TIPOUSU = 3
			ORDER BY REDE_SEQUSUA";
      $sql1->executeQuery($txt1);
      
      
      while (!$sql1->eof()) {
        $tpl->ID = $sql1->result("REDE_SEQUSUA");
        $seq_usua = $sql1->result("REDE_SEQUSUA");
        $tpl->NOME = ucwords(utf8_encode($sql1->result("REDE_NOMEUSU")));
        $tpl->EMAIL = ucwords(utf8_encode($sql1->result("REDE_EMAILUS")));
        //$tpl->PLANO = ucwords(utf8_encode($sql1->result("REDE_PLANUSU")));
        $tpl->LOGIN = $sql1->result("REDE_LOGUSUA");
        $tpl->USUA = $sql1->result("REDE_SEQUSUA");
        
        $sql = new Query ($bd);
        $txt = "SELECT LAST_INSERT_ID(SEQUPAGPLAN), CSITUAPPLAN
                FROM TREDE_PAGAPLANO
				WHERE NIDUPAGPLAN = :seq
				ORDER BY 1 DESC				
				LIMIT 1";
        $sql->addPAram(':seq',$seq_usua);
        $sql->executeQuery($txt);
        
        $status = $sql->result("CSITUAPPLAN");
        
        $blok = $sql1->result("REDE_LOGBLOK");
        
        if ($status == 'a') {
          $tpl->STATUS1 = "Ativo";
        } else {
          $tpl->STATUS1 = "<font color='red'>Inativo</font>";
        }
        
        if ($blok == 's') {
          $tpl->COR_BLOK = '#FD7C7D';
          $tpl->CADEADO = 'Lock';
          $tpl->TITULO_B = 'Desbloquear';
        } else {
          $tpl->COR_BLOK = "";
          $tpl->CADEADO = "Unlock";
          $tpl->TITULO_B = 'Bloquear';
        }
        
        
        $tpl->PLANO_U = $func->RetornaNomePlano($func->RetornaUltimoPLanoUsuario($seq_usua));
        $tpl->LIMIT_PLAN = number_format($func->RetornaLimitePlano($func->RetornaUltimoPLanoUsuario($seq_usua)),2,',','.');
        
        
        $sql12 = new Query($bd);
        $txt12 = "SELECT  VLOGIINDCOL ,NSEQPATRCOL
							  FROM TREDE_AFILIADOS
							  WHERE NSEQUSUACOL = :usua
							  ORDER BY 1 DESC			
							  LIMIT 1";
        $sql12->addParam(':usua',$seq_usua);
        $sql12->executeQuery($txt12);
        
        $idpat = $sql12->result("NSEQPATRCOL");
        
        if ($idpat == "") {
          $tpl->PAT = 'Não tem';
        } else {
          $tpl->PAT = $func->RetonaNomeUsuarioPorSeq($bd,$idpat).'/'.$sql12->result("VLOGIINDCOL");
        }
        $sql1->next();
        $tpl->block("SUB1");
      }
      
      
      if (isset($_POST['login'])) {
        
        $seq_usuas = $_POST['idusua'];
        $seq = $_POST['sequsua'];
        
        $aleatorio = mt_rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$aleatorio);
        
        $_SESSION['idSessao'] = $valor;
        $_SESSION['idUsuario'] = $seq;
        $_SESSION['aut'] = TRUE;
        $_COOKIE['idUsuario'] = $_POST['login'];
        
        $util->redireciona("../index.php?idSessao=".$_SESSION['idSessao']);
      }
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
    }

  
  $tpl->show();
  $bd->close();
?>