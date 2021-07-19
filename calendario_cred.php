<?php
  
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","calendario_cred.html");
  
  
  if (isset($_SESSION['aut'])) {
    $autenticado     = TRUE;
    $_SESSION['aut'] = TRUE;
    $tpl->block("LOGADO");
    $tpl->block("BTNLOGADO");
  } else {
    $autenticado = FALSE;
    $tpl->block("NAO_LOGADO");
    $tpl->block("BTNNAO_LOGADO");
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    $sql_rede = new Query($bd);
    $txt_rede = "SELECT TEXTO FROM TREDE_CONFIG_BASICS WHERE TIPOCONFIG = 'rede_cred'";
    $sql_rede->executeQuery($txt_rede);
    $tpl->REDE_CRED = utf8_encode($sql_rede->result("TEXTO"));
    
    if ($_SESSION['cat'] != NULL) {
      $tpl->CATE = $_SESSION['cat'];
    } else {
      $tpl->CATE = 1;
    }
    
    $id_rede = $_GET['id'];
    
    $sql21 = new Query ($bd);
    $txt21 = "SELECT SEQUENCIACRE,
								VNOMECREDCRE,
								NNUMECATECRE,
								NNUMECATESUB,
								VCUPOMDESCRE,
								CVIMAGEMCCRE
						FROM TREDE_CREDENCIADOS
					WHERE SEQUENCIACRE = '".$id_rede."'";
    $sql21->executeQuery($txt21);
    
    
    while (!$sql21->eof()) {
      $tpl->NOMEREDE  = utf8_encode($sql21->result("VNOMECREDCRE"));
      $tpl->CATE_REDE = $func->RetonaNomeCategoria($bd,$sql21->result("NNUMECATECRE"));
      $tpl->IDCRED    = $sql21->result("SEQUENCIACRE");
      
      $id     = $sql21->result("SEQUENCIACRE");
      $subcat = $sql21->result("NNUMECATESUB");
      
      $tpl->SUBCATS = ucwords(utf8_encode($func->RetornaNomeSubCategoria($bd,$subcat)));
      
      $imagem = $sql21->result("CVIMAGEMCCRE");
      
      if (($imagem == NULL) or (substr($imagem,0,7) != 'uploads')) {
        $tpl->IMAGEM = '../comum/img/Sem-imagem.jpg';
      } else {
        $tpl->IMAGEM = 'admin/'.$imagem;
      }
      
      $sql21->next();
    }
    
    if ($autenticado != TRUE) {
      $seg->verificaSession($_SESSION['idSessao']);
    } else {
      
      $tpl->ID_SESSAO = $_SESSION['idSessao'];
      
      $id_sessao = $_SESSION['idSessao'];
      $id_confer = $_GET['idSessao'];
      $seq       = $_SESSION['idUsuario'];
      
      $seg->verificaSession($id_sessao);
      
      $func->AtualizaStatusUsuario($seq);
      $tpl->IDUSUA = $_SESSION['idUsuario'];
      //$tpl->ID = "#";
      
      //INFORMAÇÕES DO USUARIO
      $tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
      //INFORMAÇÕES DO USUARIO
      
      $func->AtualizaStatusUsuario($seq);
      
    }
    
  }
  
  $tpl->show();
  $bd->close();
?>