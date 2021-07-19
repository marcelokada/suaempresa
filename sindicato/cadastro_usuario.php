<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","cadastro_usuario.html");
  
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
      
      $sql = new Query ($bd);
      $txt = "SELECT SEQPLANO, CNOMEPLANO FROM TREDE_PLANOS
            WHERE LIBERAVEND = 's'";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        
        $tpl->NNUMEPLAN = $sql->result('SEQPLANO');
        $tpl->CNOMEPLAN = $sql->result('CNOMEPLANO');
        
        $tpl->block('PLANOS');
        $sql->next();
      }
      
      
      if (isset($_POST['salvar'])) {
        
        
        $nnumeplan = $seg->antiInjection($_POST['plano']);
        $indi      = $seg->antiInjection($_POST['indi']);
        $cpf       = $seg->antiInjection($_POST['cpf']);
        $cpf       = $func->retirarPontostracosundelinebarra($cpf);
        
        $log_ind = $seg->antiInjection($_POST['log_ind']);
        
        $login = $seg->antiInjection($_POST['login']);
        $login = str_replace(' ','',$login);
        $login = str_replace('  ','',$login);
        //$login = ereg_replace("[^a-zA-Z0-9_]", "", strtr($login, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "aaaaeeiooouucAAAAEEIOOOUUC"));
        
        $nome     = $seg->antiInjection($_POST['nome']);
        $email    = $seg->antiInjection($_POST['email']);
        $dtnasc   = $seg->antiInjection($_POST['dtnasc']);
        $celular  = $seg->antiInjection($_POST['celular']);
        $indicado = $seg->antiInjection($_POST['indicador']);
        $indicado = $_SESSION['indicador'];
        $senha1   = trim($seg->antiInjection($_POST['senha1']));
        $senha2   = trim($seg->antiInjection($_POST['senha2']));
        $venc     = $seg->antiInjection($_POST['venc']);
        
        
        $verifica_login  = $seg->antiInjection($_POST['verifica_login']);
        $verifica_email  = $seg->antiInjection($_POST['verifica_email']);
        $verifica_idade  = $seg->antiInjection($_POST['verifica_idade']);
        $verifica_senha3 = $seg->antiInjection($_POST['verifica_senha3']);
        $checkbox_termos = $seg->antiInjection($_POST['concordo']);
        
        
        $rua    = utf8_decode($seg->antiInjection($_POST['rua']));
        $number = $seg->antiInjection($_POST['nume']);
        $bairro = utf8_decode($seg->antiInjection($_POST['bairro']));
        $cep    = $seg->antiInjection($_POST['cep']);
        $cidade = utf8_decode($seg->antiInjection($_POST['cidade']));
        $numibg = $seg->antiInjection($_POST['ibge']);
        $estado = $seg->antiInjection($_POST['uf']);
        $comp   = utf8_decode($seg->antiInjection($_POST['comp']));
        
        $tpl->LOGIN = $login;
        $tpl->NOME  = $nome;
        $tpl->EMAIL = $email;
        $tpl->DNASC = $dtnasc;
        $tpl->CELU  = $celular;
        $tpl->INDI  = $_SESSION['indicador'];
        
        $tpl->MSG = '';
        
        $sql4 = new Query ($bd);
        $sql4->clear();
        $txt4 = "SELECT REDE_SEQUSUA
			    FROM TREDE_USUADMIN
				WHERE REDE_EMAILUS = :login";
        $sql4->AddParam(':login',trim($email));
        $sql4->executeQuery($txt4);
        
        $ver_email = $sql4->result("REDE_SEQUSUA");
        //$ver_email = '';
        
        $sql41 = new Query ($bd);
        $sql41->clear();
        $txt41 = "SELECT REDE_SEQUSUA
			    FROM TREDE_USUADMIN
				WHERE REDE_LOGUSUA = :login";
        $sql41->AddParam(':login',trim($login));
        $sql41->executeQuery($txt41);
        
        $ver_logs = $sql41->result("REDE_SEQUSUA");
        //$ver_email = '';
        
        $dtnasc1 = $data->dataInvertida($dtnasc);
        $nome1   = strtoupper($nome);
        $nome1   = utf8_decode($nome1);
        
        $nivel = $func->RetornaNivelMMN(trim($indicado));
        
        $sql = new Query ($bd);
        $txt = "INSERT INTO TREDE_USUADMIN (REDE_NOMEUSU,
                            REDE_CPFUSUA,
									   REDE_ADMINUS,
									   REDE_SENHAUS,
									   REDE_TIPOUSU,
									   REDE_EMAILUS,
									   REDE_DNASCUS,
									   REDE_CELULAR,
									   REDE_LOGUSUA,
									   REDE_NIVELUS,
									   REDE_PLANUSU,
									   REDE_SITUUSU,
									   REDE_LOGBLOK,
									   REDE_DATAVENC,
                     REDE_ENDE,
                     REDE_NUM,
                     REDE_BAIRRO,
                     REDE_CEP,
                     REDE_CIDADE,
                     REDE_ESTADO,
                     REDE_CI_IBGE,
                     REDE_COMPLE )
			VALUES
									   (:nome,
									    '".$cpf."',
										'n',
										:senha,
										'3',
										:email,
										:dnasc,
										:celu,
										:login,
										:nivel,
										'c',
										'a',
										'n',
										'".$venc."',
										'".$rua."',
										'".$number."',
										'".$bairro."',
										'".$cep."',
										'".$cidade."',
										'".$estado."',
										'".$numibg."',
										'".$comp."'										)";
        $sql->AddParam(':nome',$nome1);
        $sql->AddParam(':senha',md5($senha2));
        $sql->AddParam(':email',$email);
        $sql->AddParam(':dnasc',$dtnasc1);
        $sql->AddParam(':celu',$celular);
        $sql->AddParam(':login',$login);
        $sql->AddParam(':nivel',$nivel);
        $sql->executeSQL($txt);
        
        $sql1 = new Query ($bd);
        $txt1 = "SELECT LAST_INSERT_ID(REDE_SEQUSUA) REDE_SEQUSUA FROM TREDE_USUADMIN
				WHERE REDE_EMAILUS = :email";
        $sql1->AddParam(':email',$email);
        $sql1->executeQuery($txt1);
        
        $seq_usua = $sql1->result("REDE_SEQUSUA");
        
        $sql1aa = new Query ($bd);
        $txt1aa = "INSERT INTO TREDE_SINDICATO_USUA (NNUMESIND,
																								NNUMEUSUA,
																								CSITUSIUS,
																								DINCLSIUS)
									VALUES
									('".$idrede."','".$seq_usua."','a','".date('Y-m-d H:i:s')."')";
        $sql1aa->executeSQL($txt1aa);
        
        #usava o $indi como padrao. mas foi alterado id
        #usava o $log_ind como padrao. mas foi alterado - login do admin ou usuarios
        
        $porcentagem = $func->RetornaPorcentagemnivel($nivel);
        
        $sql2 = new Query ($bd);
        $txt2 = "INSERT INTO TREDE_AFILIADOS_VEND
			(NNUMEUSUA,NNUMEVEND,DINCLAFIL,NNUMEPLAN)
				VALUES
			('".$seq_usua."','".$indi."','".date('Y-m-d H:i:s')."','".$nnumeplan."')";
        $sql2->executeSQL($txt2);
        
        $sql2 = new Query ($bd);
        $txt2 = "INSERT INTO TREDE_AFILIADOS
			(VLOGUSUACOL,NSEQUSUACOL,DDATAINCCOL,VLOGIINDCOL,NSEQPATRCOL,NIVELUSUA)
				VALUES
			('".$login."','".$seq_usua."','".date('Y-m-d')."','".$logadmin."','".$seqadmin."','".$nivel."')";
        //$sql2->executeSQL($txt2);
        
        $sql24 = new Query ($bd);
        $txt24 = "INSERT INTO TREDE_NIVEL
			(NIDUSNIVE,NNUMEFILI,NNUMENIVE,NPORCNIVE,IDNIVEL_1)
				VALUES
			('".$seq_usua."','".$seqadmin."','".$nivel."','".$porcentagem."', '1')";
        //$sql24->executeSQL($txt24);
        
        $sql241 = new Query ($bd);
        $txt241 = "INSERT INTO TREDE_ADESAO_MENSA_USU
			(NIDUPAGPLAN,VALORTOTAL)
				VALUES
			('".$seq_usua."','0')";
        $sql241->executeSQL($txt241);
        
        $sql242 = new Query ($bd);
        $txt242 = "INSERT INTO TREDE_CASHBACK_USU
			(NIDUSUCASH,VVALUSCASH)
				VALUES
			('".$seq_usua."','0')";
        $sql242->executeSQL($txt242);
        
        $sql243 = new Query ($bd);
        $txt243 = "INSERT INTO TREDE_VOUCHER
			(NNUMEUSUA,NVALORVOUCH)
				VALUES
			('".$seq_usua."','0')";
        $sql243->executeSQL($txt243);
        
        $sql244 = new Query ($bd);
        $txt244 = "INSERT INTO TREDE_DOTBANK_USUA
			(REDE_SEQUSUA,CSITUDOT)
				VALUES
			('".$seq_usua."','P')";
        $sql244->executeSQL($txt244);
        
        $_SESSION['msg']         = 's';
        $_SESSION['idusua_plan'] = $seq_usua;
        
        $sql245 = new Query ($bd);
        $txt245 = "SELECT TITULO,TEXTO FROM TREDE_EMAIL";
        $sql245->executeQuery($txt245);
        
        $titulo = $sql245->result("TITULO");
        $texto  = $sql245->result("TEXTO");
        
        //$func->EnviarEmail($email,$titulo,$texto);
        
        //        $util->redireciona("cadplano.php");
        //$util->redireciona("index.php?idmsg=1");
        echo "<script>alert('Cadastro Realizado com Sucecsso!'); window.location.href = window.location.href</script>";
        
      }
      
      if (isset($_POST['aplicar'])) {
        $idusua = $_POST['idusua'];
        
        $sql1aa = new Query ($bd);
        $txt1aa = "INSERT INTO TREDE_SINDICATO_USUA (NNUMESIND,
																								NNUMEUSUA,
																								CSITUSIUS,
																								DINCLSIUS)
									VALUES
									('".$idrede."','".$idusua."','p','".date('Y-m-d H:i:s')."')";
        $sql1aa->executeSQL($txt1aa);
        
        echo "<script>alert('Solicitação Enviada com Sucecsso!'); window.location.href = window.location.href</script>";
      }
    }
  } else {
    $seg->verificaSession($_SESSION['aut_sind']);
  }
  
  $tpl->show();
  $bd->close();
?>