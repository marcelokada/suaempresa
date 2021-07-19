<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","contrato.html");
  
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
      $tpl->LINKEMPRESA = LINK_EMPRESA;
      
      $sql = new Query($bd);
      $sql->clear();
      $txt = "SELECT LAST_INSERT_ID(SEQ), NOME
			FROM TREDE_CONTRATOS
			ORDER BY 1 DESC
			LIMIT 1";
      $sql->executeQuery($txt);
      
      //$tpl->NOME = $sql->result('NOME');
      
      /*if(isset($_POST['salvar'])){
        $texto = utf8_decode($_POST['contrato']);
      
        $sql1 = new Query($bd);
        $sql1->clear();
        $txt1 = "UPDATE TREDE_CONTRATO_TERMOS SET TEXTO = :texto";
        $sql1->addParam(':texto',$texto);
        $sql1->executeSQL($txt1);
      
        echo "<script> alert('Atualizado com sucesso!'); window.location.href=window.location.href; </script>";
      
      }*/
      
      $msg = FALSE;
      
      if (isset($_POST['enviou']) && $_POST['enviou'] == 1) {
        
        // arquivo
        $arquivo = $_FILES['arquivo'];
        
        
        // Tamanho máximo do arquivo (em Bytes)
        $tamanhoPermitido = 1024 * 1024 * 6; // 2Mb
        
        //Define o diretorio para onde enviaremos o arquivo
        $diretorio = "../admin/uploads/contrato/";
        
        // verifica se arquivo foi enviado e sem erros
        if ($arquivo['error'] == UPLOAD_ERR_OK) {
          
          // pego a extensão do arquivo
          $extensao = $func->extensao($arquivo['name']);
          
          // valida a extensão
          if (in_array($extensao,array("pdf"))) {
            
            // verifica tamanho do arquivo
            if ($arquivo['size'] > $tamanhoPermitido) {
              
              $tpl->MSG = "<strong>Aviso!</strong> O arquivo enviado é muito grande, envie arquivos de até ".$tamanhoPermitido / MB." MB.";
              $tpl->block("ERRO");
              
            } else {
              
              // atribui novo nome ao arquivo
              $novo_nome = md5(time()).".".$extensao;
              //$novo_nome  = date('YmdHis').".".$extensao;
              //$novo_nome  = $arquivo['name'];
              
              // faz o upload
              $enviou = move_uploaded_file($_FILES['arquivo']['tmp_name'],"$diretorio".$novo_nome);
              
              if ($enviou) {
                $sql = new Query($bd);
                $sql->clear();
                $txt = "INSERT INTO TREDE_CONTRATOS (NOME,DATA) VALUES ('".$novo_nome."','".date('Y-m-d H:i:s')."')";
                $sql->executeSQL($txt);
                
                $tpl->MSG = "<strong>Sucesso!</strong> Arquivo enviado corretamente.";
                $tpl->block("SUCESSO");
              } else {
                $tpl->MSG = "<strong>Erro!</strong> Falha ao enviar o arquivo.";
                $tpl->block("ERRO");
              }
            }
            
          } else {
            $tpl->MSG = "<strong>Erro!</strong> Somente arquivos PDF são permitidos.";
            $tpl->block("ERRO");
          }
          
        } else {
          $tpl->MSG = "<strong>Atenção!</strong> Você deve enviar um arquivo.";
          $tpl->block("ERRO");
        }
      }
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  
  $tpl->show();
  $bd->close();
?>
