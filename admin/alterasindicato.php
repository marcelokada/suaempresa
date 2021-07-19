<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  $func = new Funcao();
  $util = new Util();
  $data = new Data();
  
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","alterasindicato.html");
  
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
      $seq = $_GET['seq'];
      
      $sql = new Query($bd);
      $txt = "SELECT NNUMESIND,
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
		  FROM TREDE_SINDICATOS
		 WHERE NNUMESIND = :seq";
      $sql->addParam('seq',$seq);
      $sql->executeQuery($txt);
      
      $tpl->SEQ = $sql->result("NNUMESIND");
      $sequenciacre = $sql->result("NNUMESIND");
      $tpl->NOME = ucwords(utf8_encode($sql->result("CNOMESIND")));
      $tpl->RUA = ucwords(utf8_encode($sql->result("CENDESIND")));
      $tpl->NUMERO = $sql->result("NENDESIND");
      $tpl->BAIRRO = ucwords(utf8_encode($sql->result("CBAIRSIND")));
      $tpl->CIDA = ucwords(utf8_encode($sql->result("CCIDASIND")));
      $tpl->UF = ucwords($sql->result("CESTASIND"));
      $tpl->EMAIL = $sql->result("CEMAISIND");
      $tpl->COMPLE = $sql->result("CCOMPSIND");
      
      //$tpl->NOME 	= $sql->result("CSITUACAOCRE");
      $tpl->DATA = $data->formataData1($sql->result("DINCLSIND"));
      $tpl->TEL1 = $sql->result("CTELESIND");
      $tpl->CEL = $sql->result("CCELUSIND");
      
      $imagem = $sql->result("CIMAGSIND");;
      
      if ($imagem == NULL) {
        $tpl->IMG = 'comum/img/Sem-imagem.jpg';
      } else {
        $tpl->IMG = $imagem;
      }
      
      $tpl->CNPJ = $sql->result("CNPJ_SIND");
      $tpl->CEP = $sql->result("CCEP_SIND");
      
      
      if (isset($_POST['salvar'])) {
        
        $nome = $seg->antiInjection($_POST['nome']);
        $nome = utf8_decode($nome);
        
        $cnpj = $seg->antiInjection($_POST['cnpj']);
        $cep = $seg->antiInjection($_POST['cep']);
        
        $rua = $seg->antiInjection($_POST['rua']);
        $rua = utf8_decode($rua);
        
        $numero = $seg->antiInjection($_POST['numero']);
        $comple = $seg->antiInjection($_POST['comple']);
        $comple = utf8_decode($comple);
        
        $bairro = $seg->antiInjection($_POST['bairro']);
        $bairro = utf8_decode($bairro);
        
        $cidade = $seg->antiInjection($_POST['cidade']);
        $cidade = utf8_decode($cidade);
        
        $uf = $seg->antiInjection($_POST['uf']);
        $ibge = $seg->antiInjection($_POST['ibge']);
        
        $tel1 = $seg->antiInjection($_POST['tel1']);
        $cel = $seg->antiInjection($_POST['cel']);
        $email = $seg->antiInjection($_POST['email']);
        
        $imagem = $_FILES['imagem'];
        
        $imagenss = $imagem['name'];
        
        $extensao = pathinfo($imagem['name'],PATHINFO_EXTENSION);
        $novonome = md5(date('YmdHis'));
        
        $nome_arquivo = $novonome.'.'.$extensao;
        
        if ($imagem['name'] == "") {
          
          $sql1 = new Query ($bd);
          $txt1 = "UPDATE TREDE_SINDICATOS SET
							CNOMESIND = :nome,
							CENDESIND = :ende,
							NENDESIND = :numednde,
							CBAIRSIND = :bairro,   
							CCIDASIND = :cidade,       
							CESTASIND = :uf1,
							CSITUSIND = 'a',
							DINCLSIND = :datas,    
							CCOMPSIND = :comple,
							CNPJ_SIND = :cnpj,
							CCEP_SIND = :ceps,
							CTELESIND = :telefone,
							CCELUSIND = :celular,
              CEMAISIND = :email                              
			WHERE NNUMESIND = :seq ";
          $sql1->addParam(':seq',$seq);
          $sql1->addParam(':nome',$nome);
          $sql1->addParam(':cnpj',$cnpj);
          $sql1->addParam(':ceps',$cep);
          $sql1->addParam(':ende',$rua);
          $sql1->addParam(':numednde',$numero);
          $sql1->addParam(':comple',$comple);
          $sql1->addParam(':bairro',$bairro);
          $sql1->addParam(':cidade',$cidade);
          $sql1->addParam(':uf1',$uf);
          $sql1->addParam(':datas',date('Y-m-d'));
          $sql1->addParam(':telefone',$tel1);
          $sql1->addParam(':celular',$cel);
          $sql1->addParam(':email',$email);
          $sql1->executeSQL($txt1);
          
        } else {
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
            
            $dir = "uploads/sindicatos/";
            $dir = $util->criaDiretorio($dir);
            
            $dirimg = 'uploads/sindicatos/';
            
            if (isset($imagem)) {
              ini_set("max_execution_time",240);
              
              move_uploaded_file($imagem['tmp_name'],$dir."/".$novonome.'.'.$extensao);
              
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
            
            
            $sql1 = new Query ($bd);
            $txt1 = "UPDATE TREDE_SINDICATOS SET
							CNOMESIND = :nome,
							CENDESIND = :ende,
							NENDESIND = :numednde,
							CBAIRSIND = :bairro,   
							CCIDASIND = :cidade,       
							CESTASIND = :uf1,
							CSITUSIND = 'a',
							DINCLSIND = :datas,    
							CCOMPSIND = :comple,
							CNPJ_SIND = :cnpj,
							CCEP_SIND = :ceps,
							CTELESIND = :telefone,
							CCELUSIND = :celular,
							CIMAGSIND = :imagem,
              CEMAISIND = :email                              
			WHERE NNUMESIND = :seq ";
            $sql1->addParam(':seq',$seq);
            $sql1->addParam(':nome',$nome);
            $sql1->addParam(':cnpj',$cnpj);
            $sql1->addParam(':ceps',$cep);
            $sql1->addParam(':ende',$rua);
            $sql1->addParam(':numednde',$numero);
            $sql1->addParam(':comple',$comple);
            $sql1->addParam(':bairro',$bairro);
            $sql1->addParam(':cidade',$cidade);
            $sql1->addParam(':uf1',$uf);
            $sql1->addParam(':datas',date('Y-m-d'));
            $sql1->addParam(':telefone',$tel1);
            $sql1->addParam(':celular',$cel);
            $sql1->addParam(':imagem',$dirimg.$nome_arquivo);
            $sql1->addParam(':email',$email);
            $sql1->executeSQL($txt1);
          }
          
          //$util->redireciona('alterarede.php?idSessao='.$_GET['idSessao'].'&seq='.$seq);
        }
        echo "<script>alert('Autorizado com Sucesso'); window.location.href = window.location.href</script>";
        //echo "<script>window.location.href = window.location.href</script>";
      }
      
    }
  } else {
    $seg->verificaSession($_SESSION['aut_admin']);
  }
  
  $tpl->show();
  $bd->close();
?>