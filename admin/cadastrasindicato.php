<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  $util = new Util();
  
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","cadastrasindicato.html");
  
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
        $cnpj = $seg->antiInjection($_POST['cnpj']);
        $cnpj = $func->retirarPontostracosundelinebarra($cnpj);
        $cep = $_POST['cep'];
        $rua = utf8_decode($seg->antiInjection($_POST['rua']));
        $numero = $seg->antiInjection($_POST['numero']);
        $comple = utf8_decode($seg->antiInjection($_POST['comple']));
        $bairro = utf8_decode($seg->antiInjection($_POST['bairro']));
        $cidade = utf8_decode($seg->antiInjection($_POST['cidade']));
        $uf = $seg->antiInjection($_POST['uf']);
        $ibge = $seg->antiInjection($_POST['ibge']);
        $tel1 = $seg->antiInjection($_POST['tel1']);
        $cel = $seg->antiInjection($_POST['cel']);
        $senha = $seg->antiInjection($_POST['senha']);
        $email = $seg->antiInjection($_POST['email']);
        
        
        $senha = md5($senha);
        
        $imagem = $_FILES['imagem'];
        
        $sql7 = new Query ($bd);
        $txt7 = "SELECT VLOGEMAILCRE
                FROM TREDE_CREDENCIADOS
                WHERE VLOGEMAILCRE = :email";
        $sql7->AddParam(':email',$email);
        $sql7->executeQuery($txt7);
        
        $res_email = $sql7->result("VLOGEMAILCRE");
        
        $sql71 = new Query ($bd);
        $txt71 = "SELECT REDE_EMAILUS
                FROM TREDE_USUADMIN
                WHERE REDE_EMAILUS = :email";
        $sql71->AddParam(':email',$email);
        $sql71->executeQuery($txt71);
        
        $res_email1 = $sql71->result("REDE_EMAILUS");
        
        if ($ibge == '') {
          $ibge = '0';
        }
        
        if (($res_email != '') or ($res_email != NULL)) {
          $tpl->MSG = '<font color="red">**Já existe esse email em nosso sitesma!**</font><br>';
          $tpl->block("ERRO");
        } else if (($res_email1 != '') or ($res_email1 != NULL)) {
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
            
            $dirimg = 'uploads/img/sindicato/';
            
            if (isset($imagem)) {
              ini_set("max_execution_time",240);
              
              move_uploaded_file($imagem['tmp_name'],$dir."/img/sindicato/".$novonome.'.'.$extensao);
              
              if ((file_exists($dir."/".$nome_arquivo)) and ($nome_arquivo <> '')) {
                
                libxml_use_internal_errors(TRUE);
                
                $objDom = new DomDocument('1.0','ISO-8859-1');
                $objDom->load($dir."/".$nome_arquivo);
                
                $erros = new DOMDocument('1.0','UTF-8');
                $erros->preserveWhiteSpace = FALSE;
                $erros->formatOutput = TRUE;
                $root = $erros->createElement('erros');
                
                chdir($path);
              }
            }
            
            
            $sql3 = new Query ($bd);
            $txt3 = "SELECT CNPJ_SIND
                FROM TREDE_SINDICATOS
                WHERE CNPJ_SIND = :cnpj";
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
              
              $diretorios = $dirimg.$nome_arquivo;
              
              $sql1 = new Query ($bd);
              $txt1 = "INSERT INTO TREDE_SINDICATOS(
                             CNOMESIND,
														CNPJ_SIND,
														CENDESIND,
														NENDESIND,
														CCEP_SIND,
														CBAIRSIND,
														CCIDASIND,
														CESTASIND,
														CEMAISIND,
														CSENHSIND,
														CIMAGSIND,
														CTELESIND,
														CCELUSIND,
                            CSITUSIND,
                            DINCLSIND,	
                            CCOMPSIND 
														)
        					VALUES
                            ('".$nome."',
                            '".$cnpj."',
                             '".$rua."',
                             '".$numero."',
                            '".$cep."',
                            '".$bairro."',
                            '".$cidade."',
                            '".$uf."',
                            '".$email."',
                            '".$senha."',
                             '".$diretorios."',
                            '".$tel1."',
                            '".$cel."',
                            'a',
                            '".date('Y-m-d')."',
                            '".$comple."'
                            ) ";
              $sql1->executeSQL($txt1);
              
              //header("Location: redecredenciada.php?idSessao=" . $_GET['idSessao'] . "&msg=" . $_SESSION['msg']);
              
              $util->redireciona("sindicatos.php?idSessao=".$_GET['idSessao']);
            }
            
          }
        }
        
        $tpl->NOME = $seg->antiInjection($_POST['nome']);
        $tpl->CNPJ = $seg->antiInjection($_POST['cnpj']);
        $tpl->CEP = $seg->antiInjection($_POST['cep']);
        $tpl->RUA = $seg->antiInjection($_POST['rua']);
        $tpl->NUMERO = $seg->antiInjection($_POST['numero']);
        $tpl->COMPLE = $seg->antiInjection($_POST['comple']);
        $tpl->BAIRRO = $seg->antiInjection($_POST['bairro']);
        $tpl->CIDA = $seg->antiInjection($_POST['cidade']);
        $tpl->UF = $seg->antiInjection($_POST['uf']);
        $tpl->IBGE = $seg->antiInjection($_POST['ibge']);
        $tpl->TEL1 = $seg->antiInjection($_POST['tel1']);
        $tpl->CEL = $seg->antiInjection($_POST['cel']);
        //$tpl->CUPOM = $seg->antiInjection($_POST['cupom']);
        $tpl->EMAIL = $seg->antiInjection($_POST['email']);
        //$tpl->AAAAA 	= $seg->antiInjection($_POST['subcategoria']);
        
      }
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  
  $tpl->show();
  $bd->close();
?>