<?php
  require_once("comum/autoload.php");
  session_start();
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","login.html");
  
  
  if (EMPRESA == 'SuaEmpresa') {
    $tpl->block("SUAEMPRESA");
  }
  
  if ($_SESSION['contar'] >= 2) {
    $tpl->block('GOOGLE');
    $tpl->KEY_GOOGLE = KEY_GOOGLE;
    
  } else {
    $tpl->block('BTN_LIMPO');
  }
  
  if (isset($_POST['acessar'])) {
  
    $_SESSION['contar'] = $_SESSION['contar'] + 1;
    
    
    $email_login = strtolower(trim($seg->antiInjection($_POST['login'])));
    $senha_login = md5($seg->antiInjection($_POST['senha']));
    
    $sqlc = new Query ($bd);
    $txtc = "SELECT NNUMESIND
				FROM TREDE_SINDICATOS
				WHERE CEMAISIND = :email";
    $sqlc->addPAram(':email',$email_login);
    $sqlc->executeQuery($txtc);
    
    $email_sindicatio = $sqlc->result("NNUMESIND");
    
    if ($email_sindicatio != "") {
      
      $tipo_usua = "sindicato";
      
    } else {
      
      $sqla = new Query ($bd);
      $txta = "SELECT REDE_SEQUSUA,REDE_ADMINUS
				FROM TREDE_USUADMIN
				WHERE REDE_EMAILUS = :email";
      $sqla->addPAram(':email',$email_login);
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
          $sqlb->addPAram(':email',$email_login);
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
    }
    
    //CONTAR A QUANTIDADES DE TENTATIVAS
    
    if ($tipo_usua == 'usua') {
      
      $sql2 = new Query ($bd);
      $txt2 = "SELECT COUNT(*) TENTATIVAS
				FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email";
      $sql2->addPAram(':email',$email_login);
      $sql2->executeQuery($txt2);
      
      $tentativas = $sql2->result("TENTATIVAS") + 1;
      
      if ($tentativas >= 5) { //APÓS 5 TENTATIVAS O USUARIO É BLOQUADO
        
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
        $tpl->block("ERRO");
        
        $redefinicao       = rand(30,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor_redefinicao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$redefinicao);
        
        $_SESSION['idDef']     = $valor_redefinicao;
        $_SESSION['idSession'] = $valor_redefinicao;
        $_SESSION['emailUsua'] = $email_login;
        
        $sql4 = new Query ($bd);
        $sql4->clear();
        $txt4 = "UPDATE TREDE_USUADMIN SET REDE_USUBLOC = 's',
                                           REDE_SERECUS = '".$valor_redefinicao."'
					WHERE REDE_EMAILUS = '".$email_login."' ";
        $sql4->executeSQL($txt4);
        
        $util->redireciona("recupera_senha.php?idDef=".$_SESSION['idDef'],"N","","0");
        
        //$_SESSION['redefinir'] = '<h5><font color="red">Seu login foi bloqueado, redefina a sua senha.</font></h5>';
 
        $tpl->MSG = '<font color="red">Login Bloqueado!<br>';
        $tpl->MSG .= " <a href=recupera_senha.php?idDef=".$valor_redefinicao.">Redefinir Senha</a></font>";
      }
      
      //SELECT PARA VERIFICAR A SENHA
      $sql = new Query ($bd);
      $txt = "SELECT REDE_SEQUSUA,
                    REDE_NOMEUSU,
                    REDE_CPFUSUA,
                    REDE_ADMINUS,
                    REDE_EMAILUS,
                    REDE_SENHAUS,
                    REDE_TIPOUSU,
                    REDE_USUBLOC,
       							REDE_LOGBLOK
				FROM TREDE_USUADMIN
				WHERE REDE_EMAILUS = :email_login";
      $sql->addPAram(':email_login',$email_login);
      $sql->executeQuery($txt);
      
      $seq_usuas = $sql->result("REDE_SEQUSUA");
      $res_senha = $sql->result("REDE_SENHAUS");
      $res_admin = $sql->result("REDE_ADMINUS");
      $res_block = $sql->result("REDE_USUBLOC");
      $res_login = $sql->result("REDE_EMAILUS");
      $ubloqueio = $sql->result("REDE_USUBLOC");
      
      $res = strcmp($res_senha,$senha_login);
      
      if ($seq_usuas == '') {
        $tpl->MSG = '<font color="red">USUÁRIO NÃO EXISTE!!!</font>';
        $tpl->block("ERRO");
      } else if ($ubloqueio == 's') {
        $redefinicao       = rand(30,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor_redefinicao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$redefinicao);
        
        $sql4 = new Query ($bd);
        $sql4->clear();
        $txt4 = "UPDATE TREDE_USUADMIN SET REDE_SERECUS = '".$valor_redefinicao."'
					WHERE REDE_EMAILUS = '".$email_login."' ";
        $sql4->executeSQL($txt4);
        
        $tpl->MSG = '<font color="red">Login Bloqueado!<br>';
        $tpl->MSG .= " <a href=recupera_senha.php?idDef=".$valor_redefinicao.">Redefinir Senha</a></font>";
        $tpl->block("ERRO");
      } else if ($ubloqueio == 'p') {
        $tpl->MSG = '<font color="red">ATIVAR SUA CONTA PELO LINK ENVIADO PELO EMAIL.</font>';
        $tpl->block("ERRO");
      } else if ($res_login <> $email_login) {
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
        $tpl->block("ERRO");
      } else if ($res <> 0) {
        $tpl->MSG  = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
        $tpl->MSG1 = 'Tentativa '.$tentativas.' de 5.';
        $tpl->block("ERRO");
        $seg->registraLogin($bd,$res_login);
      } else if ($res_login <> $email_login && $res <> 0) {
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
        $tpl->block("ERRO");
      } else if ($email_login == '' && md5($senha == '')) {
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
        $tpl->block("ERRO");
      } else if ($res_block == 'S') {
        $tpl->MSG = '<font color="red">SEU LOGIN ESTA BLOQUEADO, CLIQUE EM REDEFINIR A SENHA.</font>';
        $tpl->block("ERRO");
      } else {
        
        $aleatorio = mt_rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor     = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$aleatorio);
        
        $_SESSION['idSessao']  = $valor;
        $_SESSION['idUsuario'] = $seq_usuas;
        $_SESSION['aut']       = TRUE;
        
        //Apaga todos os registros do dia
        $sql3 = new Query($bd);
        $txt3 = "DELETE FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email
					AND DDATATENTSEG = :data";
        $sql3->addParam(":email",$email_login);
        $sql3->addParam(":data",date('Y-m-d'));
        $sql3->executeSQL($txt3);
        
        $sql34 = new Query($bd);
        $txt34 = "UPDATE TREDE_USUADMIN SET REDE_USUBLOC = 'n',
                                            REDE_SERECUS = ''
				           WHERE REDE_EMAILUS = :email ";
        $sql34->addParam(":email",$email_login);
        $sql34->executeSQL($txt34);
        
        $util->redireciona("index.php?idSessao=".$_SESSION['idSessao']);
      }
    } else if ($tipo_usua == 'rede') {
      
      $sql2 = new Query ($bd);
      $txt2 = "SELECT COUNT(*) TENTATIVAS
				FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email";
      $sql2->addPAram(':email',$email_login);
      $sql2->executeQuery($txt2);
      
      $tentativas = $sql2->result("TENTATIVAS") + 1;
      
      if ($tentativas >= 5) { //APÓS 5 TENTATIVAS O USUARIO É BLOQUADO
        
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
        $tpl->block("ERRO");
        
        $redefinicao       = rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor_redefinicao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$redefinicao);
        
        $_SESSION['idDef']     = $valor_redefinicao;
        $_SESSION['idSession'] = $valor_redefinicao;
        
        $sql4 = new Query ($bd);
        $sql4->clear();
        $txt4 = "UPDATE TREDE_CREDENCIADOS SET CBLOCKLOGCRE = 's',
                                               CESESSONCRE = '".$valor_redefinicao."'
					WHERE VLOGEMAILCRE = '".$email_login."' ";
        $sql4->executeSQL($txt4);
        
        $util->redireciona("recupera_senha_rede.php?idDef=".$_SESSION['idDef'],"N","","0");
        
        //$_SESSION['redefinir'] = '<h5><font color="red">Seu login foi bloqueado, redefina a sua senha.</font></h5>';
 
        $tpl->MSG = '<font color="red">Login Bloqueado!<br>';
        $tpl->MSG .= " <a href=recupera_senha.php?idDef=".$valor_redefinicao.">Redefinir Senha</a></font>";
      }
      
      $sql = new Query ($bd);
      $txt = "SELECT SEQUENCIACRE,
                    VSENHAREDCRE,
                    VLOGEMAILCRE,
                    CBLOCKLOGCRE,
                    CSITUACAOCRE
				FROM TREDE_CREDENCIADOS
				WHERE VLOGEMAILCRE = :email_login ";
      $sql->addPAram(':email_login',$email_login);
      $sql->executeQuery($txt);
      
      $seq_usuas   = $sql->result("SEQUENCIACRE");
      $res_senha   = $sql->result("VSENHAREDCRE");
      $res_block   = $sql->result("CBLOCKLOGCRE");
      $res_login   = strtolower($sql->result("VLOGEMAILCRE"));
      $status_cred = $sql->result("CSITUACAOCRE");
      
      $res = strcmp($res_senha,$senha_login);
      
      
      if ($seq_usuas == '') {
        $tpl->MSG = '<font color="red">USUÁRIO ADMIN NÃO EXISTE!!!</font>';
        $tpl->block("ERRO");
      } else if ($res_login <> $email_login) {
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 1!!!</font>';
        $tpl->block("ERRO");
      } else if ($res <> 0) {
        $tpl->MSG  = '<font color="red">LOGIN OU SENHA INCORRETOS 2!!!</font>';
        $tpl->MSG1 = 'Tentativa '.$tentativas.' de 5.';
        $tpl->block("ERRO");
        $seg->registraLogin($bd,$res_login);
      } else if ($res_login <> $email_login && $res <> 0) {
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 3!!!</font>';
        $tpl->block("ERRO");
      } else if ($email_login == '' && md5($senha == '')) {
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 4!!!</font>';
        $tpl->block("ERRO");
      } else if ($res_block == 's') {
        
        $redefinicao       = rand(30,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor_redefinicao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$redefinicao);
        
        $sql4 = new Query ($bd);
        $sql4->clear();
        $txt4 = "UPDATE TREDE_CREDENCIADOS SET CESESSONCRE = '".$valor_redefinicao."'
					WHERE VLOGEMAILCRE = '".$email_login."' ";
        $sql4->executeSQL($txt4);
        
        $tpl->MSG = '<font color="red">Login Bloqueado! <br>';
        $tpl->MSG .= " <a href=recupera_senha_rede.php?idDef=".$valor_redefinicao.">Redefinir Senha</a></font>";
        $tpl->block("ERRO");
      } else if ($status_cred == 'p') {
        $tpl->MSG = '<font color="red">SEU LOGIN ESTÁ PENDENTE, POR FAVOR AGUARDE O ADMINISTRADOR ANALISAR.</font>';
        $tpl->block("ERRO");
      } else if ($status_cred == 'c') {
        $tpl->MSG = '<font color="red">SEU LOGIN FOI CANCELADO. ENTRAR EM CONTATO COM O ADMINISTRADOR</font>';
        $tpl->block("ERRO");
      } else {
        
        $aleatorio = mt_rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor     = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$aleatorio);
        
        $_SESSION['idSessao_rede'] = $valor;
        $_SESSION['idRede']        = $seq_usuas;
        $_SESSION['aut_rede']      = TRUE;
        
        //Apaga todos os registros do dia
        $sql3 = new Query($bd);
        $txt3 = "DELETE FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email
					AND DDATATENTSEG = :data";
        $sql3->addParam(":email",$email_login);
        $sql3->addParam(":data",date('Y-m-d'));
        $sql3->executeSQL($txt3);
        
        $util->redireciona('rede/index.php?idSessao='.$_SESSION['idSessao_rede']);
      }
      
      
    } else if ($tipo_usua == 'admin') {
      
      $sql2 = new Query ($bd);
      $txt2 = "SELECT COUNT(*) TENTATIVAS
				FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email";
      $sql2->addPAram(':email',$email_login);
      $sql2->executeQuery($txt2);
      
      $tentativas = $sql2->result("TENTATIVAS") + 1;
      
      if ($tentativas >= 5) { //APÓS 5 TENTATIVAS O USUARIO É BLOQUADO
        
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
        $tpl->block("ERRO");
        
        $redefinicao       = rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor_redefinicao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$redefinicao);
        
        $_SESSION['idDef']     = $valor_redefinicao;
        $_SESSION['idSession'] = $valor_redefinicao;
        $_SESSION['usuaAdmin'] = $admin;
        $_SESSION['admin']     = $admin;
        
        $sql4 = new Query ($bd);
        $sql4->clear();
        $txt4 = "UPDATE TREDE_USUADMIN SET REDE_USUBLOC = 's',
                                           REDE_SERECUS = '".$valor_redefinicao."'
					WHERE REDE_EMAILUS = '".$email_login."' ";
        $sql4->executeSQL($txt4);
        
        $util->redireciona("recupera_senha.php?idDef=".$_SESSION['idDef'],"N","","0");
        
        //$_SESSION['redefinir'] = '<h5><font color="red">Seu login foi bloqueado, redefina a sua senha.</font></h5>';
 
        $tpl->MSG = '<font color="red">Login Bloqueado!<br>';
        $tpl->MSG .= " <a href=recupera_senha.php?idDef=".$valor_redefinicao.">Redefinir Senha</a></font>";
      }
      
      
      //SELECT PARA VERIFICAR A SENHA
      $sql = new Query ($bd);
      $txt = "SELECT REDE_SEQUSUA,
                    REDE_NOMEUSU,
                    REDE_CPFUSUA,
                    REDE_ADMINUS,
                    REDE_EMAILUS,
                    REDE_SENHAUS,
                    REDE_TIPOUSU,
                    REDE_USUBLOC
				FROM TREDE_USUADMIN
				WHERE REDE_EMAILUS = :email_login
				  AND REDE_ADMINUS = 's' ";
      $sql->addPAram(':email_login',$email_login);
      $sql->executeQuery($txt);
      
      $seq_usuas = $sql->result("REDE_SEQUSUA");
      $res_senha = $sql->result("REDE_SENHAUS");
      $res_admin = $sql->result("REDE_ADMINUS");
      $res_block = $sql->result("REDE_USUBLOC");
      $res_login = $sql->result("REDE_EMAILUS");
      $admin     = $sql->result("REDE_SEQUSUA");
      
      
      $res = strcmp($res_senha,$senha_login);
      
      if ($seq_usuas == '') {
        $tpl->MSG = '<font color="red">USUÁRIO ADMIN NÃO EXISTE!!!</font>';
        $tpl->block("ERRO");
      } else if ($res_login <> $email_login) {
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 1!!!</font>';
        $tpl->block("ERRO");
      } else if ($res <> 0) {
        $tpl->MSG  = '<font color="red">LOGIN OU SENHA INCORRETOS 2!!!</font>';
        $tpl->MSG1 = 'Tentativa '.$tentativas.' de 5.';
        $tpl->block("ERRO");
        $seg->registraLogin($bd,$res_login);
      } else if ($res_login <> $email_login && $res <> 0) {
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 3!!!</font>';
        $tpl->block("ERRO");
      } else if ($email_login == '' && md5($senha == '')) {
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 4!!!</font>';
        $tpl->block("ERRO");
      } else if ($res_block == 'S') {
        $tpl->MSG = '<font color="red">SEU LOGIN ESTA BLOQUEADO, CLIQUE EM REDEFINIR A SENHA.</font>';
        $tpl->block("ERRO");
      } else {
        
        $aleatorio = mt_rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor     = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$aleatorio);
        
        $_SESSION['idSessao_admin'] = $valor;
        $_SESSION['idAdmin']        = $seq_usuas;
        $_SESSION['usuaAdmin']      = $admin;
        $_SESSION['admin']          = $admin;
        $_SESSION['aut_admin']      = TRUE;
        
        //Apaga todos os registros do dia
        $sql3 = new Query($bd);
        $txt3 = "DELETE FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email
					AND DDATATENTSEG = :data";
        $sql3->addParam(":email",$email_login);
        $sql3->addParam(":data",date('Y-m-d'));
        $sql3->executeSQL($txt3);
        
        $util->redireciona('admin/index.php?idSessao='.$_SESSION['idSessao'].'&idAdmin='.$_SESSION['idAdmin']);
      }
    } else if ($tipo_usua == 'sindicato') {
      
      $sql2 = new Query ($bd);
      $txt2 = "SELECT COUNT(*) TENTATIVAS
				FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email";
      $sql2->addPAram(':email',$email_login);
      $sql2->executeQuery($txt2);
      
      $tentativas = $sql2->result("TENTATIVAS") + 1;
      
      if ($tentativas >= 5) { //APÓS 5 TENTATIVAS O USUARIO É BLOQUADO
        
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
        $tpl->block("ERRO");
        
        $redefinicao       = rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor_redefinicao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$redefinicao);
        
        $_SESSION['idDef']     = $valor_redefinicao;
        $_SESSION['idSession'] = $valor_redefinicao;
        $_SESSION['usuadmin']  = $admin;
        $_SESSION['admin']     = $admin;
        
        //DAR O UPDATE E BLOQUEAR O USUARIO
        $sql4 = new Query ($bd);
        $txt4 = "UPDATE TREDE_SINDICATOS SET BLOCKSIND = 's',
                                            CSESSSIND = '".$valor_redefinicao."'
					WHERE CEMAISIND = '".$email_login."' ";
        $sql4->executeSQL($txt4);
        
        $util->redireciona("recupera_senha_sind.php?idDef=".$_SESSION['idDef'],"N","","0");
        
        //$_SESSION['redefinir'] = '<h5><font color="red">Seu login foi bloqueado, redefina a sua senha.</font></h5>';
  
        $tpl->MSG = '<font color="red">Login Bloqueado!<br>';
        $tpl->MSG .= " <a href=recupera_senha.php?idDef=".$valor_redefinicao.">Redefinir Senha</a></font>";
      }
      
      
      //SELECT PARA VERIFICAR A SENHA
      $sql = new Query ($bd);
      $txt = "SELECT CNPJ_SIND,
									 NNUMESIND,
									 CNOMESIND,
									 CEMAISIND,
									 CSENHSIND ,
       						 BLOCKSIND
				FROM TREDE_SINDICATOS
				WHERE CEMAISIND = :email_login";
      $sql->addPAram(':email_login',$email_login);
      $sql->executeQuery($txt);
      
      $seq_usuas = $sql->result("NNUMESIND");
      $res_senha = $sql->result("CSENHSIND");
      $res_block = $sql->result("BLOCKSIND");
      $res_login = $sql->result("CEMAISIND");
      $admin     = $sql->result("NNUMESIND");
      
      
      $res = strcmp($res_senha,$senha_login);
      
      if ($seq_usuas == '') {
        $tpl->MSG = '<font color="red">USUÁRIO ADMIN NÃO EXISTE!!!</font>';
        $tpl->block("ERRO");
      } else if ($res_login <> $email_login) {
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 1!!!</font>';
        $tpl->block("ERRO");
      } else if ($res <> 0) {
        $tpl->MSG  = '<font color="red">LOGIN OU SENHA INCORRETOS 2!!!</font>';
        $tpl->MSG1 = 'Tentativa '.$tentativas.' de 5.';
        $tpl->block("ERRO");
        $seg->registraLogin($bd,$res_login);
      } else if ($res_login <> $email_login && $res <> 0) {
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 3!!!</font>';
        $tpl->block("ERRO");
      } else if ($email_login == '' && md5($senha == '')) {
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 4!!!</font>';
        $tpl->block("ERRO");
      } else if ($res_block == 's') {
        $redefinicao       = rand(30,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor_redefinicao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$redefinicao);
        
        $sql4 = new Query ($bd);
        $sql4->clear();
        $txt4 = "UPDATE TREDE_SINDICATOS SET CSESSSIND = '".$valor_redefinicao."'
					WHERE CEMAISIND = '".$email_login."' ";
        $sql4->executeSQL($txt4);
        
        $tpl->MSG = '<font color="red">Login Bloqueado!<br>';
        $tpl->MSG .= " <a href=recupera_senha_sind.php?idDef=".$valor_redefinicao.">Redefinir Senha</a></font>";
        $tpl->block("ERRO");
      
      } else {
        
        $aleatorio = mt_rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor     = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$aleatorio);
        
        $_SESSION['idSessao_sind'] = $valor;
        $_SESSION['idSind']        = $seq_usuas;
        $_SESSION['usuaSind']      = $admin;
        $_SESSION['aut_sind']      = true;
        
        //sdebug($seq_usuas,true);
        
        //Apaga todos os registros do dia
        $sql3 = new Query($bd);
        $txt3 = "DELETE FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email
					AND DDATATENTSEG = :data";
        $sql3->addParam(":email",$email_login);
        $sql3->addParam(":data",date('Y-m-d'));
        $sql3->executeSQL($txt3);
  
        $util->redireciona('sindicato/index.php?idSessao='.$_SESSION['idSessao_sind'].'&idAdmin='.$_SESSION['idSind']);
      }
    }//
  }
  
  $tpl->show();
  $bd->close();
?>