<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","cadastrarede.html");
  
  
  if (isset($_SESSION['aut_admin'])) {
    $autenticado           = TRUE;
    $_SESSION['aut_admin'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      if (CLASS_REDE == 'on') {
        $tpl->block('CLASS_REDE');
      }
      
      if (CLASS_PONTUCAO == 'on') {
        $tpl->block('CLASS_PONTUCAO');
      }
      
      
      if (EMPRESA == MIMOCLUBE) {
        
        $tpl->block("MIMO_CAD_REDE");
        
        $sqlP = new Query($bd);
        $txtP = "SELECT NNUMEPROF, CNOMEPROF
              FROM TREDE_PROFISSAO
             WHERE CSITUPROF = 'a'";
        $sqlP->executeQuery($txtP);
        
        while (!$sqlP->eof()) {
          $tpl->NNUMEPROF = $sqlP->result("NNUMEPROF");
          $tpl->CNOMEPROF = $sqlP->result("CNOMEPROF");
          
          $tpl->block('PROFISSAO1');
          $sqlP->next();
        }
        
      }
      
      $id_sessao = $_SESSION['idSessao_admin'];
      $id_confer = $_GET['idSessao'];
      $id_admin  = $_SESSION['usuaAdmin'];
      
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao_admin'];
      $tpl->ID_ADMIN  = $_SESSION['usuaAdmin'];
      
      
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
      
      
      if (isset($_POST['salvar'])) {
        
        $nome      = utf8_decode($seg->antiInjection($_POST['nome']));
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
        //$cupom = $seg->antiInjection($_POST['cupom']);
        $cupom = '';
        //$links = $seg->antiInjection($_POST['links']);
        $links     = '';
        $subcate   = $seg->antiInjection($_POST['subcategoria']);
        $senha     = $seg->antiInjection($_POST['senha']);
        $email     = $seg->antiInjection($_POST['email']);
        $class     = $seg->antiInjection($_POST['fb']);
        $pontuacao = $seg->antiInjection($_POST['pontuacao']);
        $pontuacao = str_replace('.','',$pontuacao);
        $pontuacao = str_replace(',','.',$pontuacao);
        
        $senha = md5($senha);
        
        $imagem = $_FILES['imagem'];
        
        $sql7 = new Query ($bd);
        $txt7 = "SELECT VLOGEMAILCRE
                FROM TREDE_CREDENCIADOS
                WHERE VLOGEMAILCRE = :email";
        $sql7->AddParam(':email',$email);
        $sql7->executeQuery($txt7);
        
        $res_email = $sql7->result("VLOGEMAILCRE");
        
        if ($ibge == '') {
          $ibge = '0';
        }
        
        if ($class == "") {
          $tpl->MSG = '<font color="red">**Por Favor preencher a Classificação**</font><br>';
          $tpl->block("ERRO");
        } else if (($res_email != '') or ($res_email != NULL)) {
          $tpl->MSG = '<font color="red">**Já existe esse email em nosso sitesma!**</font><br>';
          $tpl->block("ERRO");
        } else if ($imagem == "") {
          $tpl->MSG = '<center><font color="RED">Insira uma imagem.</font></center>';
          $tpl->block("ERRO");
        } else {
          
          /*$imagem = $_FILES['imagem']['tmp_name'];
          $tamanho = $_FILES['imagem']['size'];
      
          $fp = fopen($imagem, "rb");
          $conteudo = fread($fp, $tamanho);
          $conteudo = addslashes($conteudo);
          fclose($fp);*/
          
          $extensao = pathinfo($imagem['name'],PATHINFO_EXTENSION);
          $novonome = md5(date('YmdHis'));
          
          $nome_arquivo = $novonome.'.'.$extensao;
          
          $_SESSION['nome_arquivo'] = $nome_arquivo;
          
          $conteudo = $nome_arquivo;
          
          if (
            $util->validaExtensaoArquivo($nome_arquivo,array(
              'jpg',
              'png',
              'gif',
              'jpeg',
              'pdf',
            )) == ''
          ) {
            $path = getcwd();
            
            $dir = "uploads/";
            $dir = $util->criaDiretorio($dir);
            
            $dirimg = 'uploads/img/';
            
            if (isset($imagem)) {
              ini_set("max_execution_time",240);
              
              move_uploaded_file($imagem['tmp_name'],$dir."/img/".$novonome.'.'.$extensao);
              
              if ((file_exists($dir."/".$nome_arquivo)) and ($nome_arquivo <> '')) {
                
                libxml_use_internal_errors(TRUE);
                
                $objDom = new DomDocument('1.0','ISO-8859-1');
                $objDom->load($dir."/".$nome_arquivo);
                
                $erros                     = new DOMDocument('1.0','UTF-8');
                $erros->preserveWhiteSpace = FALSE;
                $erros->formatOutput       = TRUE;
                $root                      = $erros->createElement('erros');
                
                chdir($path);
              }
            }
            
            
            $sql3 = new Query ($bd);
            $txt3 = "SELECT VNOMECREDCRE
                FROM TREDE_CREDENCIADOS
                WHERE VCNPJJURICRE = :cnpj";
            $sql3->AddParam(':cnpj',$cnpj);
            $sql3->executeQuery($txt3);
            
            if ($sql3->count() > 0) {
              $tpl->MSG = '<font color="red">**Já existe um C.N.P.J no nosso sitesma!**</font><br>';
              $tpl->block("ERRO");
            } else {
              
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
                            CLASSIFICCRE)
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
                            'a',
                            '".date('Y-m-d')."',
                            '".$cupom."',
                            '".$links."',
                            '".$conteudo."',
                            '".$tel1."',
                            '".$cel."',
                            '".$regiao."',
                            '".$senha."',
                            '".$dirimg.$nome_arquivo."',
                            '".$email."',
                            '".$class."') ";
              $sql1->executeSQL($txt1);
              
              $sql2 = new Query ($bd);
              $txt2 = "SELECT LAST_INSERT_ID(SEQUENCIACRE) IDCRED FROM TREDE_CREDENCIADOS
                            ORDER BY 1 DESC
                            LIMIT 1";
              $sql2->executeQuery($txt2);
              
              $idcred = $sql2->result("IDCRED");
              
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
              
              $_SESSION['msg'] = 's';
              
              //header("Location: redecredenciada.php?idSessao=" . $_GET['idSessao'] . "&msg=" . $_SESSION['msg']);
              
              $util->redireciona("redecredenciada.php?idSessao=".$_GET['idSessao']."&msg=s");
            }
            
          }
        }
        
        $tpl->NOME     = $seg->antiInjection($_POST['nome']);
        $tpl->CNPJ     = $seg->antiInjection($_POST['cnpj']);
        $tpl->CEP      = $seg->antiInjection($_POST['cep']);
        $tpl->RUA      = $seg->antiInjection($_POST['rua']);
        $tpl->NUMERO   = $seg->antiInjection($_POST['numero']);
        $tpl->COMPLE   = $seg->antiInjection($_POST['comple']);
        $tpl->BAIRRO   = $seg->antiInjection($_POST['bairro']);
        $tpl->CIDA     = $seg->antiInjection($_POST['cidade']);
        $tpl->UF       = $seg->antiInjection($_POST['uf']);
        $tpl->IBGE     = $seg->antiInjection($_POST['ibge']);
        $tpl->CAT_NOME = $seg->antiInjection($_POST['categoria']);
        $tpl->TEL1     = $seg->antiInjection($_POST['tel1']);
        $tpl->CEL      = $seg->antiInjection($_POST['cel']);
        //$tpl->CUPOM = $seg->antiInjection($_POST['cupom']);
        $tpl->LINKS = $seg->antiInjection($_POST['links']);
        $tpl->EMAIL_CAD = $seg->antiInjection($_POST['email']);
        //$tpl->AAAAA 	= $seg->antiInjection($_POST['subcategoria']);
        
      }
    } else {
      
      $seg->verificaSession($_SESSION['aut_admin']);
      
    }
  }
  
  
  $tpl->show();
  $bd->close();
?>