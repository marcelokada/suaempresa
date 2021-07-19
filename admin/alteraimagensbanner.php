<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","alteraimagensbanner.html");
  
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
      
      $idimg = $_GET['idimg'];
      $sql = new Query();
      $txt = "SELECT SEQUENCIABAN,VNOMETITUBAN,VNOMEMANCBAN,VURLLINK1BAN,VPADRATUABAN,CSITUATIVVAN,VNOMEBTN1BAN
          FROM TREDE_BANNER
         WHERE SEQUENCIABAN = :idimg";
      $sql->addParam(':idimg',$idimg);
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $tpl->NOMEBANNER = $sql->result("VNOMETITUBAN");
        
        $tpl->TITULO = utf8_encode($sql->result("VNOMEMANCBAN"));
        $tpl->MANCHETE = utf8_encode($sql->result("VNOMEBTN1BAN"));
        
        $tpl->IMAGEM = $sql->result("VURLLINK1BAN");
        
        $sql->next();
      }
      
      if (isset($_POST['alterar'])) {
        
        $nome = $seg->antiInjection($_POST['titulo']);
        $manc = $seg->antiInjection($_POST['manchete']);
        $imagem = $_FILES['imagem'];
        
        $nome = utf8_decode($nome);
        $manc = utf8_decode($manc);
        
        
        if ($imagem['name'] == "") {
          $sql1 = new Query ($bd);
          $txt1 = "UPDATE TREDE_BANNER SET
                            VNOMEMANCBAN = '".$nome."',
                            VNOMEBTN1BAN = '".$manc."'
                            WHERE SEQUENCIABAN = '".$idimg."'";
          $sql1->executeSQL($txt1);
          
          $util->redireciona("alteraimagensbanner.php?idSessao=".$_SESSION['idSessao']."&idimg=".$idimg."");
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
            
            $dirimg = 'uploads/banner/';
            
            
            if (isset($imagem)) {
              ini_set("max_execution_time",240);
              
              move_uploaded_file($imagem['tmp_name'],$dir."/banner/".$novonome.'.'.$extensao);
              
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
            $txt1 = "UPDATE TREDE_BANNER SET 
                            VNOMEMANCBAN = '".$nome."',
                            VNOMEBTN1BAN = '".$manc."',
                            VURLLINK1BAN = '".$dirimg.$nome_arquivo."'
                            WHERE SEQUENCIABAN = '".$idimg."'";
            $sql1->executeSQL($txt1);
            
            /*
            $sql1 = new Query ($bd);
            $txt1 = "INSERT INTO TREDE_BANNER
                            (VNOMEMANCBAN,
                            VNOMEBTN1BAN,
                            VURLLINK1BAN)
                    VALUES
                            ('" . $nome . "',
                            '" . $manc . "',
                            '" . $dirimg . $nome_arquivo . ") ";
            $sql1->executeSQL($txt1);*/
            
            $util->redireciona("alteraimagensbanner.php?idSessao=".$_SESSION['idSessao']."&idimg=".$idimg."");
            
          }
          
        }
        
      }
    }
  } else {
    $seg->verificaSession($_SESSION['aut_admin']);
  }
  
  
  $tpl->show();
  $bd->close();
?>