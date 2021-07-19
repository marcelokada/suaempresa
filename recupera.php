<?php
  require_once("comum/autoload.php");
  session_start();
  //error_reporting(0);
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","recupera.html");
  
  $tpl->KEY_GOOGLE = KEY_GOOGLE;
  
  if (isset($_POST['enviar'])) {
    
    $email_input = trim($seg->antiInjection($_POST['email']));
    
    if ($email_input == '') {
      $tpl->MSG = '<font color="red">Preencher o Email</font><br>';
      $tpl->block("ERRO1");
    } else {
      
      $sqla = new Query ($bd);
      $txta = "SELECT REDE_SEQUSUA,REDE_ADMINUS
				FROM TREDE_USUADMIN
				WHERE REDE_EMAILUS = :email";
      $sqla->addParam(':email',$email_input);
      $sqla->executeQuery($txta);
      
      $res_ctipo_usua = $sqla->result("REDE_ADMINUS");
      
      if ($res_ctipo_usua == 's') {
        $tipo_usua = "admin";
      } else {
        $res_email_usua = $sqla->result("REDE_SEQUSUA");
        
        if ($res_email_usua == '') {
          $sqlb = new Query ($bd);
          $txtb = "SELECT SEQUENCIACRE
			    FROM TREDE_CREDENCIADOS
				WHERE VLOGEMAILCRE = :email";
          $sqlb->addParam(':email',$email_input);
          $sqlb->executeQuery($txtb);
          
          $res_email_rede = $sqlb->result("SEQUENCIACRE");
          
          if ($res_email_rede == '') {
            $tipo_usua = "noexist";
          } else {
            $tipo_usua = "rede";
          }
          
        } else {
          $tipo_usua = "usua";
        }
      }
      
      $aleatorio = rand(30,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
      $session   = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$aleatorio);
      
      if ($tipo_usua == "usua") {
        $sql1 = new Query ($bd);
        $txt1 = "UPDATE TREDE_USUADMIN SET REDE_SERECUS = :sessao,
									  REDE_DRECUSU = :data,
									  REDE_USUBLOC = 's',
									  REDE_HRECUSU = :hora
			  WHERE REDE_EMAILUS = '".$email_input."'";
        $sql1->addParam(':sessao',$session);
        $sql1->addParam(':data',date('Y-m-d H:i:s'));
        $sql1->addParam(':hora',strtotime(date('H:i:s')) + 900);
        $sql1->executeSQL($txt1);
      } else if ($tipo_usua == 'admin') {
        $sql1 = new Query ($bd);
        $txt1 = "UPDATE TREDE_USUADMIN SET REDE_SERECUS = :sessao,
									  REDE_DRECUSU = :data,
									  REDE_USUBLOC = 's',
									  REDE_HRECUSU = :hora
			  WHERE REDE_EMAILUS = '".$email_input."'";
        $sql1->addParam(':sessao',$session);
        $sql1->addParam(':data',date('Y-m-d H:i:s'));
        $sql1->addParam(':hora',strtotime(date('H:i:s')) + 900);
        $sql1->executeSQL($txt1);
      } else if ($tipo_usua == 'rede') {
        $sql1 = new Query ($bd);
        $txt1 = "UPDATE TREDE_CREDENCIADOS SET CESESSONCRE = :sessao,
                DRECUPERACRE = :data,
                CBLOCKLOGCRE = 's',
                HSESSONCCRE = :hora
			  WHERE VLOGEMAILCRE = '".$email_input."'";
        $sql1->addParam(':sessao',$session);
        $sql1->addParam(':data',date('Y-m-d H:i:s'));
        $sql1->addParam(':hora',strtotime(date('H:i:s')) + 900);
        $sql1->executeSQL($txt1);
      } else if ($tipo_usua == 'sindicato') {
        $sql1 = new Query ($bd);
        $txt1 = "UPDATE TREDE_SINDICATOS SET CSESSSIND = :sessao,
									                              DSESSSIND = :data,
									                              BLOCKSIND = 's',
									                              HSESSSIND = :hora
			  WHERE CEMAISIND = '".$email_input."'";
        $sql1->addParam(':sessao',$session);
        $sql1->addParam(':data',date('Y-m-d H:i:s'));
        $sql1->addParam(':hora',strtotime(date('H:i:s')) + 900);
        $sql1->executeSQL($txt1);
      }
      
      $tpl->MSG = '<font color="green">Link foi enviado para o email <b>'.$email_input.'</b> com sucesso!
                  Abra seu email e siga as instruções para recuperação da senha.<br>
                  <i>Obs: o link tem um prazo e expira dentro de minutos.</i><br></font>';
      $tpl->block("SUCESSO1");
      $tpl->clear("MOSTRAR_EMAIL");
      
      
    }
  }
  
  $tpl->show();
  $bd->close();
?>