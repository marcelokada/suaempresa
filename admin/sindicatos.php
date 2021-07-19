<?php
  require_once("comum/autoload.php");
  if(!isset($_SESSION)){
    session_start();
  }
  
  //error_reporting(0);
  
  $bd = new Database();
  $func = new Funcao();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","sindicatos.html");
  
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
      
      
      $nivelusua = $func->RetornaPermissoes_Admin($id_admin);
      
      if ($nivelusua == 'C') {
        $tpl->DISABLE = "style='display:none;'";
      } else if ($nivelusua == 'CB') {
        $tpl->DISABLE = "style='display:none;'";
      }
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT NNUMESIND,
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
       					  CSITUSIND
		  FROM TREDE_SINDICATOS
		 ORDER BY NNUMESIND DESC";
      $sql1->executeQuery($txt1);
      
      $cont = $sql1->count();
      
      if ($cont == 0) {
        $tpl->block("SIND1");
      } else {
        
        while (!$sql1->eof()) {
          $tpl->SEQ = $sql1->result("NNUMESIND");
          $sequenciacre = $sql1->result("NNUMESIND");
          
          $tpl->NOMECRE = $sql1->result("CNOMESIND");
          
          $tpl->CEP = $sql1->result("CCEP_SIND");
          
          $imagem = $sql1->result("CIMAGSIND");
          
          if ($imagem == NULL) {
            $tpl->IMAGEM = 'comum/img/Sem-imagem.jpg';
          } else {
            $tpl->IMAGEM = $imagem;
          }
          
          // $tpl->IMAGEM 		= $imagem;
          
          $status = $sql1->result("CSITUSIND");
          
          if ($status == 'a') {
            $tpl->COR = "";
            $tpl->CHK = "checked";
            $tpl->ATIV = "desativar";
          } else {
            $tpl->COR = "alert alert-danger";
            $tpl->CHK = "";
            $tpl->ATIV = "ativar";
          }
          
          $tpl->block("SIND");
          $sql1->next();
          
        }
        
      }
      $tpl->ID_SESSAO = $_SESSION['aut_admin'];
      $tpl->ID_ADMIN = $_SESSION['usuaAdmin'];
    
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  
  }

  
  $tpl->show();
  $bd->close();
?>