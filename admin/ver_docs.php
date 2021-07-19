<?php
  
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","ver_docs.html");
  
  if (isset($_SESSION['aut_admin'])) {
    $autenticado           = TRUE;
    $_SESSION['aut_admin'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      $id_sessao = $_SESSION['idSessao_admin'];
      $id_confer = $_GET['idSessao'];
      $id_redec  = $_GET['id'];
      $id_admin  = $_SESSION['usuaAdmin'];
      
      $sql_rede = new Query($bd);
      $txt_rede = "SELECT TEXTO FROM TREDE_CONFIG_BASICS WHERE TIPOCONFIG = 'rede_cred'";
      $sql_rede->executeQuery($txt_rede);
      
      $tpl->REDE_CRED1 = utf8_encode($sql_rede->result("TEXTO"));
      
      $sql_rede1 = new Query($bd);
      $txt_rede1 = "SELECT VNOMECREDCRE FROM TREDE_CREDENCIADOS WHERE SEQUENCIACRE = '".$id_redec."'";
      $sql_rede1->executeQuery($txt_rede1);
      
      $tpl->NOME_CRED = utf8_encode($sql_rede1->result("VNOMECREDCRE"));
      
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO1 = $_SESSION['idSessao_admin'];
      $tpl->ID_ADMIN   = $_SESSION['usuaAdmin'];
      
      $nivelusua = $func->RetornaPermissoes_Admin($id_admin);
      
      if ($nivelusua == 'C') {
        $tpl->DISABLE = "style='display:none;'";
      } else if ($nivelusua == 'CB') {
        $tpl->DISABLE = "style='display:none;'";
      } else if ($nivelusua == 'CCAB') {
        $tpl->DISABLE = "style='display:none;'";
      }
      
      $sql = new Query();
      $sql->clear();
      $txt = "SELECT NNUMEDOC,
                     CNOMEDOC,
                     ARQNODOC,
                     NNUMECRE
            FROM TREDE_DOCS_CRED
            WHERE NNUMECRE = '".$id_redec."' ";
      $sql->executeQuery($txt);
      
      if ($sql->count() > 0) {
        while (!$sql->eof()) {
          
          $tpl->ID       = $sql->result("NNUMEDOC");
          $tpl->NOMEDOCS = $sql->result("CNOMEDOC");
          $nomeArquivo   = $sql->result("ARQNODOC");
          
          $extensao = pathinfo($nomeArquivo,PATHINFO_EXTENSION);
          
          if ($extensao == 'pdf') {
            $tpl->block('PDF');
            $tpl->URL = 'https://cb.acesso.in/'.LINK_EMPRESA.'/uploads/arquivos/'.$nomeArquivo;
          } else {
            $tpl->block('IMG');
          }
          
          $tpl->block("DOCS");
          $sql->next();
        }
      } else {
        
        $tpl->block("DOCS1");
      }
      
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
    
  }
  
  
  $tpl->show();
  $bd->close();
?>