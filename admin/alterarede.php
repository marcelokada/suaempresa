<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","alterarede.html");
  
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
      $txt = "SELECT NNUMECATECAT, VNOMECATECAT
		  FROM TREDE_CATEGORIAS
		 WHERE VSITUCATECAT = 'a'";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $tpl->CAT_NUM = $sql->result("NNUMECATECAT");
        $tpl->CAT_NOME = ucwords(utf8_encode($sql->result("VNOMECATECAT")));
        $sql->next();
        $tpl->block("CATE");
      }
      
      $sql = new Query($bd);
      $txt = "SELECT SEQUENCIACRE,
			   VNOMECREDCRE,
			   VNOMEENDECRE,
			   NNUMEENDECRE,
			   VNOMEBAIRCRE,
			   VNOMECIDAMUN,
			   CESTADOUFMUN,
			   CESTADOUFEST,
			   NNUMECATECRE,
			   NNUMESERVCRE,
			   NNUMEIBGEMUN,
			   CSITUACAOCRE,
			   DDATACREDCRE,
			   NNUMEREGIREG,
			   VCUPOMDESCRE,
			   VLINKDESCCRE,
			   VCOMPLEMECRE,
			   NNUMECATESUB,
			   VIMAGEMCRCRE,
			   VCNPJJURICRE,
			   VNUMECCEPCRE,
			   NNUMETELECRE,
			   NNUMECELUCRE,
			   CVIMAGEMCCRE,
       	 VLOGEMAILCRE,
         CLASSIFICCRE
		  FROM TREDE_CREDENCIADOS
		 WHERE SEQUENCIACRE = :seq";
      $sql->addParam('seq',$seq);
      $sql->executeQuery($txt);
      
      $tpl->SEQ = $sql->result("SEQUENCIACRE");
      $sequenciacre = $sql->result("SEQUENCIACRE");
      $tpl->NOME = ucwords(utf8_encode($sql->result("VNOMECREDCRE")));
      $tpl->RUA = ucwords(utf8_encode($sql->result("VNOMEENDECRE")));
      $tpl->NUMERO = $sql->result("NNUMEENDECRE");
      $tpl->BAIRRO = ucwords(utf8_encode($sql->result("VNOMEBAIRCRE")));
      $tpl->CIDA = ucwords(utf8_encode($sql->result("VNOMECIDAMUN")));
      $tpl->UF = ucwords($sql->result("CESTADOUFMUN"));
      $tpl->EMAIL = $sql->result("VLOGEMAILCRE");
      
      $cat_num = $sql->result("NNUMECATECRE");
      $tpl->CAT_NUM = $sql->result("NNUMECATECRE");
      $tpl->CAT_NOME = ucwords(utf8_encode($func->RetornaNomeCategoria($bd,$cat_num)));
      
      $subcat = $sql->result("NNUMECATESUB");
      $tpl->SUBCAT_NUM = $sql->result("NNUMECATESUB");
      $tpl->SUBCAT_NOME = ucwords(utf8_encode($func->RetornaNomeSubCategoria($bd,$subcat)));
      
      //$tpl->NOME 	= $sql->result("NNUMESERVCRE");
      $tpl->IBGE = $sql->result("NNUMEIBGEMUN");
      //$tpl->NOME 	= $sql->result("CSITUACAOCRE");
      $tpl->DATA = $data->formataData1($sql->result("DDATACREDCRE"));
      $tpl->TEL1 = $sql->result("NNUMETELECRE");
      $tpl->CEL = $sql->result("NNUMECELUCRE");
      $tpl->CUPOM = $sql->result("VCUPOMDESCRE");
      $tpl->LINKS = $sql->result("VLINKDESCCRE");
      $tpl->COMPLE = utf8_encode($sql->result("VCOMPLEMECRE"));
      
      $imagem = $sql->result("CVIMAGEMCCRE");;
      
      $star = $sql->result("CLASSIFICCRE");
      
      if($star == ''){
      
      }else {
  
        if ($star == 1) {
          $tpl->O1   = "fa-star";
          $tpl->O2   = "fa-star-o";
          $tpl->O3   = "fa-star-o";
          $tpl->O4   = "fa-star-o";
          $tpl->O5   = "fa-star-o";
          $tpl->STAR = "1 (uma estrela)";
        } else if ($star == 2) {
          $tpl->O1   = "fa-star";
          $tpl->O2   = "fa-star";
          $tpl->O3   = "fa-star-o";
          $tpl->O4   = "fa-star-o";
          $tpl->O5   = "fa-star-o";
          $tpl->STAR = "2 (duas estrelas)";
        } else if ($star == 3) {
          $tpl->O1   = "fa-star";
          $tpl->O2   = "fa-star";
          $tpl->O3   = "fa-star";
          $tpl->O4   = "fa-star-o";
          $tpl->O5   = "fa-star-o";
          $tpl->STAR = "3 (três estrelas)";
        } else if ($star == 4) {
          $tpl->O1   = "fa-star";
          $tpl->O2   = "fa-star";
          $tpl->O3   = "fa-star";
          $tpl->O4   = "fa-star";
          $tpl->O5   = "fa-star-o";
          $tpl->STAR = "4 (quatro estrelas)";
        } else if ($star == 5) {
          $tpl->O1   = "fa-star";
          $tpl->O2   = "fa-star";
          $tpl->O3   = "fa-star";
          $tpl->O4   = "fa-star";
          $tpl->O5   = "fa-star";
          $tpl->STAR = "5 (cinco estrelas)";
        }
      }
      
      if ($imagem == NULL) {
        $tpl->IMG = 'comum/img/Sem-imagem.jpg';
      } else {
        $tpl->IMG = $imagem;
      }
      
      
      // $tpl->IMG 		= $func->RetornaImagem($bd,$sequenciacre);
      //$tpl->IMG_A 	= $sql->result("VIMAGEMCRCRE");
      $tpl->CNPJ = $sql->result("VCNPJJURICRE");
      $tpl->CEP = $sql->result("VNUMECCEPCRE");
      
      $tpl->block("REDE");
      
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
        
        $categoria = $seg->antiInjection($_POST['categoria']);
        $tel1 = $seg->antiInjection($_POST['tel1']);
        $cel = $seg->antiInjection($_POST['cel']);
        $cupom = $seg->antiInjection($_POST['cupom']);
        $links = $seg->antiInjection($_POST['links']);
        $email = $seg->antiInjection($_POST['email']);
        $class = $seg->antiInjection($_POST['class']);
        
        $subcate = $seg->antiInjection($_POST['subcategoria']);
        
        $imagem = $_FILES['imagem'];
        
        $imagenss = $imagem['name'];
        
        
        $extensao = pathinfo($imagem['name'],PATHINFO_EXTENSION);
        $novonome = md5(date('YmdHis'));
        
        $nome_arquivo = $novonome.'.'.$extensao;
        
        
        if ($imagem['name'] == "") {
          
          
          $sql1 = new Query ($bd);
          $txt1 = "UPDATE TREDE_CREDENCIADOS SET
							VNOMECREDCRE = :nome,
							VNOMEENDECRE = :ende,
							NNUMEENDECRE = :numednde,
							VNOMEBAIRCRE = :bairro,   
							VNOMECIDAMUN = :cidade,       
							CESTADOUFEST = :uf1,
							CESTADOUFMUN = :uf2,
							NNUMECATECRE = :cate,
							NNUMECATESUB = :scate,
							NNUMEIBGEMUN = :ibge,
							CSITUACAOCRE = 'a',
							DDATACREDCRE = :datas,
							VCUPOMDESCRE = :cupom, 
							VLINKDESCCRE = :links,     
							VCOMPLEMECRE = :comple,
							VCNPJJURICRE = :cnpj,
							VNUMECCEPCRE = :ceps,
							NNUMETELECRE = :telefone,
							NNUMECELUCRE = :celular,
              VLOGEMAILCRE = :email,
              CLASSIFICCRE = '".$class."'
			      WHERE SEQUENCIACRE = :seq ";
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
          $sql1->addParam(':uf2',$uf);
          $sql1->addParam(':ibge',$ibge);
          $sql1->addParam(':datas',date('Y-m-d'));
          $sql1->addParam(':cate',$categoria);
          $sql1->addParam(':telefone',$tel1);
          $sql1->addParam(':celular',$cel);
          $sql1->addParam(':cupom',$cupom);
          $sql1->addParam(':links',$links);
          $sql1->addParam(':scate',$subcate);
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
                
                $erros = new DOMDocument('1.0','UTF-8');
                $erros->preserveWhiteSpace = FALSE;
                $erros->formatOutput = TRUE;
                $root = $erros->createElement('erros');
                
                chdir($path);
              }
            }
            
            
            $sql1 = new Query ($bd);
            $txt1 = "UPDATE TREDE_CREDENCIADOS SET
							VNOMECREDCRE = :nome,
							VNOMEENDECRE = :ende,
							NNUMEENDECRE = :numednde,
							VNOMEBAIRCRE = :bairro,   
							VNOMECIDAMUN = :cidade,       
							CESTADOUFEST = :uf1,
							CESTADOUFMUN = :uf2,
							NNUMECATECRE = :cate,
							NNUMECATESUB = :scate,
							NNUMEIBGEMUN = :ibge,
							CSITUACAOCRE = 'a',
							DDATACREDCRE = :datas,
							VCUPOMDESCRE = :cupom, 
							VLINKDESCCRE = :links,     
							VCOMPLEMECRE = :comple,
							VCNPJJURICRE = :cnpj,
							VNUMECCEPCRE = :ceps,
							NNUMETELECRE = :telefone,
							NNUMECELUCRE = :celular,
							CVIMAGEMCCRE = :imagem,
              VLOGEMAILCRE = :email,
              CLASSIFICCRE = '".$class."'
			WHERE SEQUENCIACRE = :seq ";
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
            $sql1->addParam(':uf2',$uf);
            $sql1->addParam(':ibge',$ibge);
            $sql1->addParam(':datas',date('Y-m-d'));
            $sql1->addParam(':cate',$categoria);
            $sql1->addParam(':telefone',$tel1);
            $sql1->addParam(':celular',$cel);
            $sql1->addParam(':cupom',$cupom);
            $sql1->addParam(':links',$links);
            $sql1->addParam(':scate',$subcate);
            $sql1->addParam(':imagem',$dirimg.$nome_arquivo);
            $sql1->addParam(':email',$email);
            $sql1->executeSQL($txt1);
          }
          
          //$util->redireciona('alterarede.php?idSessao='.$_GET['idSessao'].'&seq='.$seq);
        }
        echo "<script>alert('Autorizado com Sucesso')</script>";
        echo "<script>window.location.href = window.location.href</script>";
      }
    }
  } else {
    $seg->verificaSession($_SESSION['aut_admin']);
  }
  
  $tpl->show();
  $bd->close();
?>