<?php
  require_once("comum/autoload.php");
  session_start();
  //error_reporting(0);
  $bd = new Database();
  
  $id_sessao = $_GET['idDef'];
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","recupera_senha_sind.html");
  
  $tpl->block("MOSTRAR_EMAIL");
  
  if ($_SESSION['contar'] >= 2) {
    $tpl->block('GOOGLE');
    $tpl->KEY_GOOGLE = KEY_GOOGLE;
    
  } else {
    $tpl->block('BTN_LIMPO');
  }
  

  $sql = new Query ($bd);
  $txt = "SELECT CSESSSIND,
                 BLOCKSIND,
                 DSESSSIND,
                 HSESSSIND,
                 CEMAISIND
		         FROM TREDE_SINDICATOS
		        WHERE CSESSSIND = :session";
  $sql->addPAram(':session',$id_sessao);
  $sql->executeQuery($txt);
  
  $email   = $sql->result("CEMAISIND");
  $email1  = explode('@',$email);
  $email01 = substr($email1[0],0,3);
  $email02 = $email1[1];
  
  $sessao         = $sql->result("CSESSSIND");
  $hora           = $sql->result("HSESSSIND");
  $horafixa       = $hora;
  $horaatual      = strtotime(date('H:i:s'));
  $resultado_hora = $horafixa - $horaatual;
  
  $res_hora  = gmp_sign($resultado_hora); //verifica se o numero é negativo ou nao, positivo 1, igual 0 e negativo -1
  $res_hora1 = md5($res_hora);            //coloquei tudo em md5 por causa de fraude no site
  $conf_hora = md5("-1");
  
  if ($id_sessao != $sessao) {
    $tpl->MSG = '<font color="red">Houve um erro na comunicação, por favor fazer o login novamente.</font><br>';
    $tpl->block("ERRO1");
  } else {
    
    $tpl->EMAIL_M = $email01.'*****@'.$email02;
    
    if (isset($_POST['enviar'])) {
      
      $_SESSION['contar'] += 1;
      
      $email_input = trim($seg->antiInjection($_POST['email']));
      
      if ($email_input == '') {
        $tpl->MSG = '<font color="red">Preencher o Email</font><br>';
        $tpl->block("ERRO1");
      } else if ($sessao == "") {
        $tpl->MSG = '<font color="red">Sessão Inválida ou Expirada, fazer o login novamente.</font><br>';
        $tpl->block("ERRO1");
      } else if ($email_input != $email) {
        $tpl->MSG = '<font color="red">O email informado está diferente do que foi registrado.</font><br>';
        $tpl->block("ERRO1");
        
      } else {
        
        unset($_SESSION['contar']);
  
        $aleatorio 	= rand(30, 30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $session 	= substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"), 0, $aleatorio);
  
        $sql1 = new Query ($bd);
        $txt1 = "UPDATE TREDE_SINDICATOS SET    CSESSSIND = :sessao,
									                              DSESSSIND = :data,
									                              BLOCKSIND = 's',
									                              HSESSSIND = :hora
			  WHERE CEMAISIND = '".$email_input."'";
        $sql1->addParam(':sessao',$session);
        $sql1->addParam(':data',date('Y-m-d H:i:s'));
        $sql1->addParam(':hora',strtotime(date('H:i:s')) + 900);
        $sql1->executeSQL($txt1);
        
        $tpl->MSG = '<font color="green">Link foi enviado no seu email com sucesso! Abra seu email e siga as instruções para recuperação da senha.</font>';
        $tpl->block("SUCESSO1");
        $tpl->clear("MOSTRAR_EMAIL");
        
      }
    }
  }
  
  $tpl->show();
  $bd->close();
?>