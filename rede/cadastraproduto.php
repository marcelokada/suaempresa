<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","cadastraproduto.html");
  
  if (isset($_SESSION['aut_rede'])) {
    $autenticado          = TRUE;
    $_SESSION['aut_rede'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      if (EMPRESA == 'MimoClube') {
        $tpl->block("CASHPRODUTO");
      }
      
      $id_sessao = $_SESSION['idSessao_rede'];
      $id_confer = $_GET['idSessao'];
      $id_rede   = $_SESSION['idRede'];
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao_rede'];
      $tpl->ID_REDE   = $_SESSION['idRede'];
      
      $idmsg = $_GET['idMsg'];
      
      /*$sql = new Query ($bd);
      $txt = "SELECT VNOMECREDCRE
          FROM TREDE_CREDENCIADOS
          WHERE VCNPJJURICRE = :cnpj";
      $sql->AddParam(':cnpj',$cnpj);
      $sql->executeQuery($txt);*/
      
      if ($idmsg == '1') {
        $tpl->block("IMG");
      } else {
        $tpl->block("IMG1");
      }
      
      
      if ($idmsg == 's') {
        $tpl->MSG = '<font color="green">**Cadastro do Produto Realizado com Sucesso!!** <a href="cadastraproduto.php?idSessao={ID_SESSAO}&idRede={ID_REDE}">Cadastrar novamente</a></font><br>';
        $tpl->block("SUCESSO");
        
        $sql = new Query ($bd);
        $txt = "SELECT LAST_INSERT_ID(NSEQUPRODU) SEQ FROM TREDE_PRODUTOS
				WHERE SEQUENCIACRE = :seqcre
				ORDER BY 1 DESC
				LIMIT 1";
        $sql->AddParam(':seqcre',$idrede);
        $sql->executeQuery($txt);
        
        $seqcre = $sql->result("SEQ");
        
        $sql2 = new Query ($bd);
        $txt2 = "SELECT SEQUENCIACRE,
					   VNOMEPRODU,
					   VDESCPRODU,
					   VVALOPRODU,
					   VCASHPRODU,
					   CSITUPRODU,
					   NQTDEPRODU,
					   IMAGEM,
					   DDATAPRODU,
					   NSEQUPRODU,
					   CIMAGPRODU					   
			FROM TREDE_PRODUTOS
			WHERE SEQUENCIACRE = :seqcre
			  AND NSEQUPRODU = :seqprodu";
        $sql2->AddParam(':seqcre',$idrede);
        $sql2->AddParam(':seqprodu',$seqcre);
        $sql2->executeQuery($txt2);
        
        $tpl->NOME     = ucwords(utf8_encode($sql2->result("VNOMEPRODU")));
        $tpl->DESC     = ucwords(utf8_encode($sql2->result("VDESCPRODU")));
        $valor         = $sql2->result("VVALOPRODU");
        $valor         = str_replace(".",",",$valor);
        $tpl->VALOR    = $valor;
        $tpl->CASH     = $sql2->result("VCASHPRODU");
        $tpl->VAL_SITU = $sql2->result("CSITUPRODU");
        $situa         = $sql2->result("CSITUPRODU");
        
        if ($situa == 'a') {
          $tpl->NOM_SITU = 'Ativo';
        } else {
          $tpl->NOM_SITU = 'Inativo';
        }
        
        $seqprodu = $sql2->result("NSEQUPRODU");
        
        // $tpl->IMAGEM 	= $func->RetornaImagemProdutos($bd,$seqprodu);
        // $tpl->IMAGEM	 	= $sql2->result("CIMAGPRODU");
        
        $imagem = $sql2->result("CIMAGPRODU");
        
        if ($imagem == NULL) {
          $tpl->IMAGEM = 'comum/img/Sem-imagem.jpg';
        } else {
          $tpl->IMAGEM = $imagem;
        }
        
        
        $tpl->DISA = "disabled";
        
        $tpl->block("IMG");
        
      }
      
      
      if (isset($_POST['salvar'])) {
        $nome = utf8_decode($seg->antiInjection($_POST['nome']));
        $desc = utf8_decode($seg->antiInjection($_POST['desc']));
        
        $valor = $seg->antiInjection($_POST['valor']);
        $valor = str_replace(".","",$valor);
        $valor = str_replace(",",".",$valor);
        
        //$cash		  = 	$seg->antiInjection($_POST['cash']);
        $cash  = 0;
        $cupom = $seg->antiInjection($_POST['cupom']);
        $links = $seg->antiInjection($_POST['links']);
        
        $imagem  = $_FILES['imagem'];
        $tamanho = $_FILES['imagem']['size'];
        
        if ($imagem == "") {
          $tpl->MSG = '<center><font color="RED">Insira uma imagem.</font></center>';
          $tpl->block("ERRO");
          $tpl->NOME  = $seg->antiInjection($_POST['nome']);
          $tpl->DESC  = $seg->antiInjection($_POST['desc']);
          $tpl->VALOR = $valor;
          $tpl->CASH  = $seg->antiInjection($_POST['cash']);
          
        } else if ($tamanho >= "1000000") {
          $tpl->MSG = '<center><font color="RED">Tamanho da imagem muito grande.</font></center>';
          $tpl->block("ERRO");
          $tpl->NOME  = $seg->antiInjection($_POST['nome']);
          $tpl->DESC  = $seg->antiInjection($_POST['desc']);
          $tpl->VALOR = $valor;
          $tpl->CASH  = $seg->antiInjection($_POST['cash']);
        } else {
          
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
            
            
            $sql1 = new Query ($bd);
            $txt1 = "INSERT INTO TREDE_PRODUTOS (SEQUENCIACRE,
											VNOMEPRODU,
											VDESCPRODU,
											VVALOPRODU,
											VCASHPRODU,
											CSITUPRODU,
											NQTDEPRODU,
											IMAGEM,
											DDATAPRODU,
											CIMAGPRODU)  
		                VALUES
		                ('".$idrede."','".$nome."','".$desc."','".$valor."','".$cash."','a','1','".$conteudo."','".date('Y-m-d')."','".$dirimg.$nome_arquivo."')";
            $sql1->executeSQL($txt1);
            
            $_SESSION['msg'] = 's';
            //echo "<script>alert('Produto Cadastrado com Sucesso.')</script>";
            //$util->redireciona("Location: cadastraproduto.php?idSessao=".$_GET['idSessao']."&idRede=".$_GET['idRede']."&idMsg=".$_SESSION['msg']);
            $tpl->MSG = '<center><font color="green">Produto Cadastrado com Sucesso.</font></center>';
            $tpl->block("SUCESSO");
          } else {
            echo "<script> alert('Extensão da imagem errada ou arquivo inválido!')</script>";
          }
          
        }
      }
    } else {
      $seg->verificaSession($_SESSION['aut_rede']);
    }
  }
  
  $tpl->show();
  $bd->close();
?>