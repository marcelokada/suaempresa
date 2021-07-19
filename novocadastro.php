<?php
  
  require_once("comum/autoload_log.php");
  session_start();
  //error_reporting(0);
  
  $bd   = new Database();
  $func = new Funcao();
  $data = new Data();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","novocadastro.html");
  
  session_destroy();
  
  if (isset($_GET['ind'])) {
    $indget    = $_GET['ind'];
    $tpl->INDI = $indget;
  }
  
  $sql_rede = new Query($bd);
  $txt_rede = "SELECT TEXTO FROM TREDE_CONFIG_BASICS WHERE TIPOCONFIG = 'rede_cred'";
  $sql_rede->executeQuery($txt_rede);
  
  $tpl->REDE_CRED = utf8_encode($sql_rede->result("TEXTO"));
  
  if ($_SESSION['nomeEmpresa'] == SUAEMPRESA) {
    $tpl->block("SUAEMPRESA");
  }
  
  if (CAD_REDE == 'on') {
    $tpl->block('CAD_REDE');
  }
  
  if (DOCS_MIMO == 'on') {
    $tpl->block('DOCS_MIMO');
  }
  
  if (CAD_PROFISSAO == 'on') {
    $tpl->block('CAD_PROFISSAO');
  }
  
  if (CLASS_REDE == 'on') {
    $tpl->block('CLASS_REDE');
  }
  
  if (CLASS_PONTUCAO == 'on') {
    $tpl->block('CLASS_PONTUCAO');
  }
  
  
  if (CAD_PROFISSAO == 'on') {
    
    
    $sql = new Query($bd);
    $txt = "SELECT NNUMECATECAT, VNOMECATECAT
               FROM TREDE_CATEGORIAS
              WHERE VSITUCATECAT = 'a'";
    $sql->executeQuery($txt);
    
    while (!$sql->eof()) {
      
      $tpl->CAT_NUM  = $sql->result("NNUMECATECAT");
      $tpl->CAT_NOME = ucwords(utf8_encode($sql->result("VNOMECATECAT")));
      
      $sql->next();
      $tpl->block("CATE");
    }
    
    $sqlP = new Query($bd);
    $txtP = "SELECT NNUMEPROF, CNOMEPROF
               FROM TREDE_PROFISSAO
              WHERE CSITUPROF = 'a'";
    $sqlP->executeQuery($txtP);
    
    while (!$sqlP->eof()) {
      $tpl->NNUMEPROF = $sqlP->result("NNUMEPROF");
      $tpl->CNOMEPROF = utf8_encode($sqlP->result("CNOMEPROF"));
      
      $tpl->block('PROFISSAO');
      $sqlP->next();
    }
    
    
  }
  
  if (isset($_POST['cadastrar'])) {
    
    $indi    = $seg->antiInjection($_POST['indi']);
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
    $senha1   = trim($seg->antiInjection($_POST['senha1']));
    $senha2   = trim($seg->antiInjection($_POST['senha2']));
    $venc     = $seg->antiInjection($_POST['venc']);
    
    $verifica_login  = $seg->antiInjection($_POST['verifica_login']);
    $verifica_email  = $seg->antiInjection($_POST['verifica_email']);
    $verifica_idade  = $seg->antiInjection($_POST['verifica_idade']);
    $verifica_senha3 = $seg->antiInjection($_POST['verifica_senha3']);
    $checkbox_termos = $seg->antiInjection($_POST['concordo']);
    
    $rua         = utf8_decode($seg->antiInjection($_POST['rua_usua']));
    $nrua        = $seg->antiInjection($_POST['num_usua']);
    $bairro_usua = utf8_decode($seg->antiInjection($_POST['bairro_usua']));
    $comple_usua = utf8_decode($seg->antiInjection($_POST['comple_usua']));
    $cida_usua   = utf8_decode($seg->antiInjection($_POST['cida_usua']));
    $ibge_usua   = $seg->antiInjection($_POST['ibge_usua']);
    $esta_usua   = $seg->antiInjection($_POST['esta_usua']);
    $cep_usua    = $seg->antiInjection($_POST['cep_usua']);
    
    $tpl->LOGIN       = $login;
    $tpl->NOME        = utf8_encode($nome);
    $tpl->EMAIL       = $email;
    $tpl->DNASC       = $dtnasc;
    $tpl->CELU        = $celular;
    $tpl->INDI        = $indicado;
    $tpl->RUA_USUA    = utf8_encode($rua);
    $tpl->NUM_USUA    = $nrua;
    $tpl->BAIRRO_USUA = utf8_encode($bairro_usua);
    $tpl->COM_USUA    = utf8_encode($comple_usua);
    $tpl->CIDA_USUA   = utf8_encode($cida_usua);
    $tpl->IBGE_USUA   = $ibge_usua;
    $tpl->EST_USUA    = $esta_usua;
    
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
    
    if ($verifica_login == '1') {
      $tpl->MSG .= '<div class="alert alert-danger">Verificar o login</div><br>';
      $tpl->block("ERRO");
    } else if ($verifica_email == '1') {
      $tpl->MSG .= '<div class="alert alert-danger">Verificar o Email (Já Existe)</div><br>';
      $tpl->block("ERRO");
    } else if ($verifica_idade == '1') {
      $tpl->MSG .= '<div class="alert alert-danger">Verificar a Idade</div><br>';
      $tpl->block("ERRO");
    } else if ($verifica_senha3 == '1') {
      $tpl->MSG .= '<div class="alert alert-danger">Verificar as senhas</div><br>';
      $tpl->block("ERRO");
    } else if ($ver_email != "") {
      $tpl->MSG .= '<div class="alert alert-danger">Verificar o Email (Já Existe)</div><br>';
      $tpl->block("ERRO");
    } else if ($ver_logs != "") {
      $tpl->MSG .= '<div class="alert alert-danger">Verificar o Login (Já Existe)</div><br>';
      $tpl->block("ERRO");
    } else if ($senha1 != $senha2) {
      $tpl->MSG .= '<div class="alert alert-danger">Senhas Diferentes</div><br>';
      $tpl->block("ERRO");
    } else if ($checkbox_termos != 'on') {
      $tpl->MSG .= '<div class="alert alert-danger">Por favor marcar "Eu Li e Concordo com o Contrato/Termo.</div><br>';
      $tpl->block("ERRO");
    } else if (($log_ind == "") or ($log_ind == NULL)) {
      $tpl->MSG .= '<div class="alert alert-danger">Patrocinador inválido, por favor preencher corretamente.</div><br>';
      $tpl->block("ERRO");
    } else {
      
      $dtnasc1 = $data->dataInvertida($dtnasc);
      $nome1   = strtoupper($nome);
      $nome1   = utf8_decode($nome1);
      
      $nivel = $func->RetornaNivelMMN(trim($indicado));
      
      $sql = new Query ($bd);
      $txt = "INSERT INTO TREDE_USUADMIN (REDE_NOMEUSU,
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
                     REDE_USUBLOC,
                     REDE_ENDE,
                     REDE_NUM,
                     REDE_BAIRRO,
                     REDE_COMPLE,
                     REDE_CIDADE,
                     REDE_ESTADO,
                     REDE_CI_IBGE,
                     REDE_CEP)
			              VALUES
									   (:nome,
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
										'n',
										'".$rua."',
										'".$nrua."',
										'".$bairro_usua."',
										'".$comple_usua."',
										'".$cida_usua."',
										'".$esta_usua."',
										'".$ibge_usua."',
										'".$cep_usua."')";
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
      
      if ($indicado == "") {
        $sql3 = new Query ($bd);
        $txt3 = "SELECT REDE_SEQUSUA,REDE_LOGUSUA
			    FROM TREDE_USUADMIN
				WHERE REDE_LOGUSUA = 'admin'
				  AND REDE_ADMINUS = 's' ";
        $sql3->AddParam(':login',trim($indicado));
        $sql3->executeQuery($txt3);
        
        $seqadmin = $sql3->result("REDE_SEQUSUA");
        $logadmin = $sql3->result("REDE_LOGUSUA");
      } else {
        $sql3 = new Query ($bd);
        $txt3 = "SELECT REDE_SEQUSUA
			    FROM TREDE_USUADMIN
				WHERE REDE_LOGUSUA = :login";
        $sql3->AddParam(':login',trim($indicado));
        $sql3->executeQuery($txt3);
        
        $res_ind = $sql3->result("REDE_SEQUSUA");
        
        if ($res_ind == "") {
          $sql4 = new Query ($bd);
          $txt4 = "SELECT REDE_SEQUSUA,REDE_LOGUSUA
			        FROM TREDE_USUADMIN
				    WHERE REDE_LOGUSUA = :login";
          $sql4->AddParam(':login','admin');
          $sql4->executeQuery($txt4);
          $seqadmin = $sql4->result("REDE_SEQUSUA");
          $logadmin = $sql4->result("REDE_LOGUSUA");
        } else {
          
          $sql3 = new Query ($bd);
          $txt3 = "SELECT REDE_SEQUSUA,REDE_LOGUSUA
			        FROM TREDE_USUADMIN
				    WHERE REDE_LOGUSUA = :login";
          $sql3->AddParam(':login',trim($indicado));
          $sql3->executeQuery($txt3);
          
          $seqadmin = $sql3->result("REDE_SEQUSUA");
          $logadmin = $sql3->result("REDE_LOGUSUA");
        }
        
      }
      
      
      //insert na tabela de permissoes
      $sql27 = new Query ($bd);
      $txt27 = "INSERT INTO TREDE_RULES
			(REDE_SEQUSUA,NPERMRULE,CSITURULE)
				VALUES 
			('".$seq_usua."','6','A')";
      //$sql27->executeSQL($txt27);
      //insert na tabela de permissoes
      
      
      #usava o $indi como padrao. mas foi alterado id
      #usava o $log_ind como padrao. mas foi alterado - login do admin ou usuarios
      
      $porcentagem = $func->RetornaPorcentagemnivel($nivel);
      
      $sql2 = new Query ($bd);
      $txt2 = "INSERT INTO TREDE_AFILIADOS
			(VLOGUSUACOL,NSEQUSUACOL,DDATAINCCOL,VLOGIINDCOL,NSEQPATRCOL,NIVELUSUA)
				VALUES 
			('".$login."','".$seq_usua."','".date('Y-m-d')."','".$logadmin."','".$seqadmin."','".$nivel."')";
      $sql2->executeSQL($txt2);
      
      $sql24 = new Query ($bd);
      $txt24 = "INSERT INTO TREDE_NIVEL
			(NIDUSNIVE,NNUMEFILI,NNUMENIVE,NPORCNIVE,IDNIVEL_1)
				VALUES 
			('".$seq_usua."','".$seqadmin."','".$nivel."','".$porcentagem."', '1')";
      $sql24->executeSQL($txt24);
      
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
      
      $tpl->MSG = "Cadastro Realizado com Sucesso";
      $tpl->block("SUCESSO");
      
      echo "<script>alert('Cadastro Realizado com Sucesso'); window.location.href = window.location.href;</script>";
      
    }
    
    $tpl->LOGIN       = $login;
    $tpl->NOME        = utf8_encode($nome);
    $tpl->EMAIL       = $email;
    $tpl->DNASC       = $dtnasc;
    $tpl->CELU        = $celular;
    $tpl->INDI        = $indicado;
    $tpl->RUA_USUA    = utf8_encode($rua);
    $tpl->NUM_USUA    = $nrua;
    $tpl->BAIRRO_USUA = utf8_encode($bairro_usua);
    $tpl->COM_USUA    = utf8_encode($comple_usua);
    $tpl->CIDA_USUA   = utf8_encode($cida_usua);
    $tpl->IBGE_USUA   = $ibge_usua;
    $tpl->EST_USUA    = $esta_usua;
    
    
  }
  
  if (isset($_POST['cad_rede'])) {
    
    $nome      = utf8_decode($seg->antiInjection($_POST['nome_rede']));
    $cnpj      = $seg->antiInjection($_POST['cnpj']);
    $cnpj      = $func->retirarPontostracosundelinebarra($cnpj);
    $cep       = $seg->antiInjection($_POST['cep']);
    $rua       = utf8_decode($seg->antiInjection($_POST['rua']));
    $numero    = $seg->antiInjection($_POST['numero']);
    $comple    = utf8_decode($seg->antiInjection($_POST['comple']));
    $bairro    = utf8_decode($seg->antiInjection($_POST['bairro']));
    $cidade    = utf8_decode($seg->antiInjection($_POST['cidade']));
    $uf        = $seg->antiInjection($_POST['uf']);
    $ibge      = $seg->antiInjection($_POST['ibge']);
    $categoria = $seg->antiInjection($_POST['categoria']);
    $tel1      = $seg->antiInjection($_POST['tel1']);
    $cel       = $seg->antiInjection($_POST['cel']);
    $tipo_serv = $seg->antiInjection($_POST['tipo_serv']);
    //$cupom = $seg->antiInjection($_POST['cupom']);
    $cupom = '';
    //$links = $seg->antiInjection($_POST['links']);
    $links       = '';
    $subcate     = $seg->antiInjection($_POST['subcategoria']);
    $senha_cred  = $seg->antiInjection($_POST['senha_cred']);
    $senha_cred2 = $seg->antiInjection($_POST['senha_cred2']);
    $email       = $seg->antiInjection($_POST['email_rede']);
    $class       = $seg->antiInjection($_POST['fb']);
    $pontuacao   = $seg->antiInjection($_POST['pontuacao']);
    $pontuacao   = str_replace('.','',$pontuacao);
    $pontuacao   = str_replace(',','.',$pontuacao);
    
    $profissao = $seg->antiInjection($_POST['profissao']);
    $ncon      = $seg->antiInjection($_POST['ncon']);
    $estacon   = $seg->antiInjection($_POST['estacon']);
    $tipo_serv = $seg->antiInjection($_POST['tipo_serv']);
    
    
    $sql7 = new Query ($bd);
    $txt7 = "SELECT VLOGEMAILCRE
                FROM TREDE_CREDENCIADOS
                WHERE VLOGEMAILCRE = :email";
    $sql7->AddParam(':email',$email);
    $sql7->executeQuery($txt7);
    
    $res_email = $sql7->result("VLOGEMAILCRE");
    
    $nomeImagem       = $_FILES["imagem"]["name"];
    $tamanhoImagem    = $_FILES["imagem"]["size"];
    $nomeImagemTmp    = $_FILES["imagem"]["tmp_name"];
    $tamanhoIMGMaximo = 5000000;
    $imagem           = $_FILES['imagem'];
    $substituir1      = FALSE;
    
    $extensaoImg = pathinfo($imagem['name'],PATHINFO_EXTENSION);
    $novonomeImg = md5(date('YmdHis'));
    
    $nome_arquivo = $novonomeImg.'.'.$extensaoImg;
    
    $conteudo = $nome_arquivo;
    
    $extensoes_img = array(
      ".jpeg",
      ".jpg",
      ".png",
    );
    
    $dirimg = 'uploads/img/';
    
    if ($ibge == '') {
      $ibge = '0';
    }
    
    $sql3 = new Query ($bd);
    $txt3 = "SELECT VNOMECREDCRE
                FROM TREDE_CREDENCIADOS
                WHERE VCNPJJURICRE = :cnpj";
    $sql3->AddParam(':cnpj',$cnpj);
    $sql3->executeQuery($txt3);
    
    if ($class == "") {
      $tpl->MSG = '<font color="red">**Por Favor preencher a Classificação**</font><br>';
      $tpl->block("ERRO");
    } else if (($res_email != '') or ($res_email != NULL)) {
      $tpl->MSG = '<font color="red">**Já existe esse email em nosso sitesma!**</font><br>';
      $tpl->block("ERRO");
    } else if ($nomeImagem == "") {
      $tpl->MSG = '<center><font color="RED">Insira uma imagem.</font></center>';
      $tpl->block("ERRO");
    } else if ($tamanhoImagem > $tamanhoIMGMaximo) {
      $tpl->MSG = "O arquivo ".$nomeImagem." não deve ultrapassar ".$tamanhoIMGMaximo." bytes";
      $tpl->block("ERRO");
    } // Verifica se a extensão está entre as aceitas
    else if (!in_array(strrchr($nomeImagem,"."),$extensoes_img)) {
      $tpl->MSG = "A extensão do arquivo <b>".$nomeImagem."</b> não é válida";
      $tpl->block("ERRO");
    } // Verifica se o arquivo existe e se é para substituir
    else if (file_exists($dirimg.$nomeImagem) and !$substituir1) {
      $tpl->MSG = "O arquivo <b>".$nomeImagem."</b> já existe";
      $tpl->block("ERRO");
    } else if ($senha_cred != $senha_cred2) {
      $tpl->MSG = '<font color="red">Senhas não se conferem.</font><br>';
      $tpl->block("ERRO");
    } else if ($sql3->count() > 0) {
      $tpl->MSG = '<font color="red">**Já existe um C.N.P.J no nosso sitesma!**</font><br>';
      $tpl->block("ERRO");
      
    } else {
      
      $senha_md5 = md5($senha_cred2);
      
      $sql11 = new Query ($bd);
      $txt11 = "SELECT NNUMEREGIREG FROM TREDE_REGIAO
                        WHERE NNUMEREGIREG IN (SELECT NNUMEREGIREG FROM TREDE_ESTADO WHERE CESTADOUFEST IN
                            (SELECT CESTADOUFEST FROM TREDE_MUNICIPIO WHERE NNUMEIBGEMUN = :ibge))";
      $sql11->addParam(':ibge',$ibge);
      $sql11->executeQuery($txt11);
      
      $regiao = $sql11->result("NNUMEREGIREG");
      $sql1   = new Query ($bd);
      $txt1   = "INSERT INTO TREDE_CREDENCIADOS(VNOMECREDCRE,
                            VCNPJJURICRE,
                            VNUMECCEPCRE,
                            VNOMEENDECRE,
                            NNUMEENDECRE,
                            VCOMPLEMECRE,
                            VNOMEBAIRCRE,
                            VNOMECIDAMUN,
                            NNUMEIBGEMUN,
                            CESTADOUFMUN,
                            CESTADOUFEST,
                            NNUMECATECRE,
                            NNUMECATESUB,
                            CSITUACAOCRE,
                            DDATACREDCRE,
                            VCUPOMDESCRE,
                            VLINKDESCCRE,
                            VIMAGEMCRCRE,
                            NNUMETELECRE,
                            NNUMECELUCRE,
                            NNUMEREGIREG,
                            VSENHAREDCRE,
                            CVIMAGEMCCRE,
                            VLOGEMAILCRE,
                            CLASSIFICCRE,
                            CTIPOCREDCRE,
                            NCONSELHOCRE,
                            ESTACONSECRE,
                            CTIPOCRED)
        VALUES
                            ('".$nome."',
                            '".$cnpj."',
                            '".$cep."',
                            '".$rua."',
                            '".$numero."',
                            '".$comple."',
                            '".$bairro."',
                            '".$cidade."',
                            '".$ibge."',
                            '".$uf."',
                            '".$uf."',
                            '".$categoria."',
                            '".$subcate."',
                            'p',
                            '".date('Y-m-d')."',
                            '".$cupom."',
                            '".$links."',
                            '".$conteudo."',
                            '".$tel1."',
                            '".$cel."',
                            '".$regiao."',
                            '".$senha_md5."',
                            '".$dirimg.$nome_arquivo."',
                            '".$email."',
                            '".$class."',
                            '".$profissao."',
                            '".$ncon."',
                            '".$estacon."',
                            '".$tipo_serv."'
                            ) ";
      $sql1->executeSQL($txt1);
      
      $sql2 = new Query ($bd);
      $txt2 = "SELECT LAST_INSERT_ID(SEQUENCIACRE) IDCRED FROM TREDE_CREDENCIADOS
                            ORDER BY 1 DESC
                            LIMIT 1";
      $sql2->executeQuery($txt2);
      
      $idcred = $sql2->result("IDCRED");
      
      
      if (!empty($nomeImagem)) {
        // Move o arquivo para o caminho definido
        move_uploaded_file($nomeImagemTmp,($dirimg.$novonomeImg.'.'.$extensaoImg));
      }//verifica se o arquivo foi colocado no campo
      
      // DEFINIÇÕES DOS DOCS
      // Numero de campos de upload
      $numeroCampos = 5;
      // Tamanho máximo do arquivo (em bytes)
      $tamanhoMaximo = 5000000;
      // Extensões aceitas
      $extensoes = array(
        ".pdf",
        ".jpeg",
        ".jpg",
        ".png",
      );
      // Caminho para onde o arquivo será enviado
      $caminho = "uploads/arquivos/";
      // Substituir arquivo já existente (true = sim; false = nao)
      $substituir = FALSE;
      
      if (!is_dir($caminho)) {
        $tpl->MSG = "Pasta $caminho nao existe";
        $tpl->block('ERRO');
      } else {
        
        for ($ii = 0; $ii < $numeroCampos; $ii++) {
          $nomeArquivo1    = $_FILES["docs"]["name"][$ii];
          $tamanhoArquivo1 = $_FILES["docs"]["size"][$ii];
          $nomeTemporario1 = $_FILES["docs"]["tmp_name"][$ii];
          
          $erro1 = 0;
          
          if ($tamanhoArquivo1 > $tamanhoMaximo) {
            $erro1   += 1;
            $erro2[] = "O arquivo ".$nomeArquivo1." não deve ultrapassar ".$tamanhoMaximo." bytes";
          } // Verifica se a extensão está entre as aceitas
          else if (!in_array(strrchr($nomeArquivo1,"."),$extensoes)) {
            $erro1   += 1;
            $erro2[] = "A extensão do arquivo <b>".$nomeArquivo1."</b> não é válida";
          } // Verifica se o arquivo existe e se é para substituir
          else if (file_exists($caminho.$nomeArquivo1) and !$substituir) {
            $erro1   += 1;
            $erro2[] = "O arquivo <b>".$nomeArquivo1."</b> já existe";
          } else {
            $erro1 = 0;
          }
          
          //$tpl->block('ERRO');
        }
        
        $tpl->MSG = "";
        
        if ($erro1 > 0) {
          
          foreach ($erro2 as $e) {
            $tpl->MSG .= "<p class='alert-danger'>".$e."</p>";
          }
          
          $tpl->block('ERRO');
        } else {
          
          for ($i = 0; $i < $numeroCampos; $i++) {
            
            // Informações do arquivo enviado
            $nomeArquivo    = $_FILES["docs"]["name"][$i];
            $tamanhoArquivo = $_FILES["docs"]["size"][$i];
            $nomeTemporario = $_FILES["docs"]["tmp_name"][$i];
            $extensao       = pathinfo($nomeArquivo,PATHINFO_EXTENSION);
            
            // Verifica se o arquivo foi colocado no campo
            if (!empty($nomeArquivo)) {
              
              $erro = FALSE;
              
              // Verifica se o tamanho do arquivo é maior que o permitido
              if ($tamanhoArquivo > $tamanhoMaximo) {
                $erro = "O arquivo ".$nomeArquivo." não deve ultrapassar ".$tamanhoMaximo." bytes";
              } // Verifica se a extensão está entre as aceitas
              else if (!in_array(strrchr($nomeArquivo,"."),$extensoes)) {
                $erro = "A extensão do arquivo <b>".$nomeArquivo."</b> não é válida";
              } // Verifica se o arquivo existe e se é para substituir
              else if (file_exists($caminho.$nomeArquivo) and !$substituir) {
                $erro = "O arquivo <b>".$nomeArquivo."</b> já existe";
              }
              
              // Se não houver erro
              if (!$erro) {
                
                $novoNome = date('YmdHis').$func->GerarChaveAleatoria(20,20);
                
                // Move o arquivo para o caminho definido
                move_uploaded_file($nomeTemporario,($caminho.$novoNome.'.'.$extensao));
                
                $sql_insert = new Query($bd);
                $txt_insert = "INSERT INTO TREDE_DOCS_CRED (CNOMEDOC,ARQNODOC,NNUMECRE)
                VALUES ('".$nomeArquivo."','".$novoNome.'.'.$extensao."','".$idcred."')";
                $sql_insert->executeSQL($txt_insert);
                
                // Mensagem de sucesso
                $tpl->MSG = "O arquivo <b>".$nomeArquivo."</b> foi enviado com sucesso. <br />";
                $tpl->block('SUCESSO');
              } // Se houver erro
              else {
                // Mensagem de erro
                $tpl->MSG = $erro."<br />";
                $tpl->block('ERRO');
              }
              
            }//verifica se o arquivo foi colocado no campo
            
          }//for
          
          
          $sql3 = new Query ($bd);
          $txt3 = "INSERT INTO TREDE_CREDITOREDE(
                               SEQUENCIACRE,
                               VALCREDREDE
                            )
                            VALUES
                            (
                             '".$idcred."',
                             '".$pontuacao."'
                            ) ";
          $sql3->executeSQL($txt3);
          
          $sql4 = new Query ($bd);
          $txt4 = "INSERT INTO TREDE_CREDITOTRANS_REDE
                              (SEQUENCIACRE,
                               DATCREDTREDE,
                               VALCREDTREDE,
                               TIPCREDTREDE)
                            VALUES
                            ('".$idcred."',
                             '".date('Y-m-d H:i:s')."',
                             '".$pontuacao."',
                             1) ";
          $sql4->executeSQL($txt4);
          
          $token = utf8_decode("NÃO CADASTRADO");
          
          $sql5 = new Query ($bd);
          $txt5 = "INSERT INTO TREDE_DOTBANK_REDE (SEQUENCIACRE, TOKEN) VALUES ('".$idcred."','".$token."') ";
          $sql5->executeSQL($txt5);
          
          $sql6 = new Query ($bd);
          $txt6 = "INSERT INTO TREDE_DOTBANK_CONFIG_REDE (SEQUENCIACRE, CNPJ) VALUES ('".$idcred."','".$cnpj."') ";
          $sql6->executeSQL($txt6);
          
          $sql7 = new Query ($bd);
          $txt7 = "INSERT INTO TREDE_PAGSEGURO_REDE (SEQUENCIACRE,TOKEN) VALUES ('".$idcred."','".$token."') ";
          $sql7->executeSQL($txt7);
          
          // Mensagem de sucesso
          $tpl->MSG = "<p class='text-center'>Cadastro Realizado com Sucesso.</p>";
          $tpl->block('SUCESSO');
          
        }//se tem erros no upload dos arquivos
      }//se existe caminho pasta
      
      
    }//se tem erros ELSE 1º
  }//post
  
  /*********************************************************************************/
  /*********************************************************************************/
  /*********************************************************************************/
  /*********************************************************************************/
  
  if (isset($_POST['acessar'])) {
    
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
        
        //DAR O UPDATE E BLOQUEAR O USUARIO
        $sql4 = new Query ($bd);
        $txt4 = "UPDATE TREDE_USUADMIN SET REDE_USUBLOC = 's'
					WHERE REDE_EMAILUS = '".$email_login."' ";
        $sql4->executeSQL($txt4);
        
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
        $tpl->block("ERRO");
        
        $redefinicao       = rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor_redefinicao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789@#&"),0,$redefinicao);
        
        $_SESSION['idDef']     = $valor_redefinicao;
        $_SESSION['idSession'] = $valor_redefinicao;
        
        $util->redireciona("redefinicao.php?idDef=".$_SESSION['idDef'],"N","","0");
        
        $_SESSION['redefinir'] = '<h5><font color="red">Seu login foi bloqueado, redefina a sua senha.</font></h5>';
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
      $ubloqueio = $sql->result("REDE_LOGBLOK");
      
      $res = strcmp($res_senha,$senha_login);
      
      if ($seq_usuas == '') {
        $tpl->MSG = '<font color="red">USUÁRIO NÃO EXISTE!!!</font>';
        $tpl->block("ERRO");
      } else if ($ubloqueio == 's') {
        $tpl->MSG = '<font color="red">LOGIN BLOQUEADO, ENTRAR EM CONTATO COM ADMINISTRADOR!!!</font>';
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
        $_COOKIE['idUsuario']  = $seq_usuas;
        
        
        //Apaga todos os registros do dia
        $sql3 = new Query($bd);
        $txt3 = "DELETE FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email
					AND DDATATENTSEG = :data";
        $sql3->addParam(":email",$email_login);
        $sql3->addParam(":data",date('Y-m-d'));
        $sql3->executeSQL($txt3);
        
        //            $sql4 = new Query($bd);
        //            $txt4 = "INSERT INTO ";
        //            $sql4->addParam(":email", $email_login);
        //            $sql4->addParam(":data", date('Y-m-d'));
        //            $sql4->executeSQL($txt4);
        
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
        
        //DAR O UPDATE E BLOQUEAR O USUARIO
        $sql4 = new Query ($bd);
        $txt4 = "UPDATE TREDE_CREDENCIADOS SET CBLOCKLOGCRE = 's'
					WHERE VLOGEMAILCRE = '".$email_login."' ";
        $sql4->executeSQL($txt4);
        
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
        $tpl->block("ERRO");
        
        $redefinicao       = rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor_redefinicao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789@#&"),0,$redefinicao);
        
        $_SESSION['idDef']     = $valor_redefinicao;
        $_SESSION['idSession'] = $valor_redefinicao;
        
        $util->redireciona("redefinicao.php?idDef=".$_SESSION['idDef'],"N","","0");
        
        $_SESSION['redefinir'] = '<h5><font color="red">Seu login foi bloqueado, redefina a sua senha.</font></h5>';
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
      } else if ($res_block == 'S') {
        $tpl->MSG = '<font color="red">SEU LOGIN ESTA BLOQUEADO, CLIQUE EM REDEFINIR A SENHA.</font>';
        $tpl->block("ERRO");
      } else if ($status_cred == 'p') {
        $tpl->MSG = '<font color="red">SEU CADASTRO ESTÁ PENDENTE, POR FAVOR AGUARDE O ADMINISTRADOR ANALISAR.</font>';
        $tpl->block("ERRO");
      } else if ($status_cred == 'c') {
        $tpl->MSG = '<font color="red">SEU CADASTRO FOI CANCELADO. ENTRAR EM CONTATO COM O ADMINISTRADOR</font>';
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
        
        $util->redireciona('rede/index.php?idSessao='.$_SESSION['idSessao']);
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
        
        //DAR O UPDATE E BLOQUEAR O USUARIO
        $sql4 = new Query ($bd);
        $txt4 = "UPDATE TREDE_USUADMIN SET REDE_USUBLOC = 's'
					WHERE REDE_EMAILUS = '".$email_login."' ";
        $sql4->executeSQL($txt4);
        
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
        $tpl->block("ERRO");
        
        $redefinicao       = rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor_redefinicao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789@#&"),0,$redefinicao);
        
        $_SESSION['idDef']     = $valor_redefinicao;
        $_SESSION['idSession'] = $valor_redefinicao;
        $_SESSION['usuadmin']  = $admin;
        $_SESSION['admin']     = $admin;
        
        $util->redireciona("redefinicao.php?idDef=".$_SESSION['idDef'],"N","","0");
        
        $_SESSION['redefinir'] = '<h5><font color="red">Seu login foi bloqueado, redefina a sua senha.</font></h5>';
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
        $_SESSION['usuaAdmin']      = $seq_usuas;
        $_SESSION['aut_admin']      = TRUE;
        
        //sdebug($seq_usuas,true);
        
        //Apaga todos os registros do dia
        $sql3 = new Query($bd);
        $txt3 = "DELETE FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email
					AND DDATATENTSEG = :data";
        $sql3->addParam(":email",$email_login);
        $sql3->addParam(":data",date('Y-m-d'));
        $sql3->executeSQL($txt3);
        
        $util->redireciona('admin/index.php?idSessao='.$_SESSION['idSessao_admin'].'&idAdmin='.$_SESSION['usuaAdmin']);
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
        
        //DAR O UPDATE E BLOQUEAR O USUARIO
        $sql4 = new Query ($bd);
        $txt4 = "UPDATE TREDE_SINDICATOS SET BLOCKSIND = 's'
					WHERE CEMAISIND = '".$email_login."' ";
        $sql4->executeSQL($txt4);
        
        $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
        $tpl->block("ERRO");
        
        $redefinicao       = rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor_redefinicao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789@#&"),0,$redefinicao);
        
        $_SESSION['idDef']     = $valor_redefinicao;
        $_SESSION['idSession'] = $valor_redefinicao;
        $_SESSION['usuadmin']  = $admin;
        $_SESSION['admin']     = $admin;
        
        $util->redireciona("redefinicao.php?idDef=".$_SESSION['idDef'],"N","","0");
        
        $_SESSION['redefinir'] = '<h5><font color="red">Seu login foi bloqueado, redefina a sua senha.</font></h5>';
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
      } else if ($res_block == 'S') {
        $tpl->MSG = '<font color="red">SEU LOGIN ESTA BLOQUEADO, CLIQUE EM REDEFINIR A SENHA.</font>';
        $tpl->block("ERRO");
      } else {
        
        $aleatorio = mt_rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor     = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$aleatorio);
        
        $_SESSION['idSessao'] = $valor;
        $_SESSION['idAdmin']  = $seq_usuas;
        $_SESSION['usuadmin'] = $admin;
        $_SESSION['admin']    = $admin;
        
        //sdebug($seq_usuas,true);
        
        //Apaga todos os registros do dia
        $sql3 = new Query($bd);
        $txt3 = "DELETE FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email
					AND DDATATENTSEG = :data";
        $sql3->addParam(":email",$email_login);
        $sql3->addParam(":data",date('Y-m-d'));
        $sql3->executeSQL($txt3);
        
        $util->redireciona('sindicato/index.php?idSessao='.$_SESSION['idSessao'].'&idAdmin='.$_SESSION['idAdmin']);
      }
    }//
  }//acessar
  
  $tpl->show();
  $bd->close();
?>