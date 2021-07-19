<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  $func = new Funcao();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","comum/index.html");
  
  
  if (isset($_SESSION['aut_rede'])) {
    $autenticado = TRUE;
    $_SESSION['aut_rede'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      $id_sessao = $_SESSION['idSessao_rede'];
      $id_confer = $_GET['idSessao'];
      $id_rede = $_SESSION['idRede'];
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao_rede'];
      $tpl->ID_REDE = $_SESSION['idRede'];
      
      $sql1 = new Query ($bd);
      $txt1 = "SELECT VVALCASHREDE
			FROM TREDE_CASHBACKREDE
			WHERE SEQUENCIACRE = :idrede";
      $sql1->AddParam(':idrede',$idrede);
      $sql1->executeQuery($txt1);
      
      $tpl->CASHBACK_REDE = '0';
      $totaldarede = 0;
      
      while (!$sql1->eof()) {
        $totaldarede += $sql1->result("VVALCASHREDE");
        $tpl->CASHBACK_REDE = $formata->formatanumero($totaldarede);
        $sql1->next();
      }
      
      $sql41 = new Query($bd);
      $txt41 = "SELECT LAST_INSERT_ID(NSEQUPRODU) NSEQUPRODU,NVALORCARR, DDATACCARR,NQUATICARR FROM TREDE_CARRINHO
					WHERE SEQUENCIACRE = :cred
					ORDER BY DDATACCARR DESC";
      $sql41->addParam(':cred',$idrede);
      $sql41->executeQuery($txt41);
      
      while (!$sql41->eof()) {
        $idprodu = $sql41->result("NSEQUPRODU");
        $tpl->IDPRODU = $sql41->result("NSEQUPRODU");
        $tpl->NOMEPRODU = $func->RetornaNomeProduto($idprodu);
        $tpl->VALORPRODU = $sql41->result("NVALORCARR");
        $tpl->QTDEPRODU = $sql41->result("NQUATICARR");
        $tpl->DATAPRODU = $data->formataData1($sql41->result("DDATACCARR"));
        
        $sql41->next();
        $tpl->block("PRODUTOS");
      }
    }
  } else {
    $seg->verificaSession($_SESSION['aut_rede']);
  }
  
  $tpl->show();
  $bd->close();
?>