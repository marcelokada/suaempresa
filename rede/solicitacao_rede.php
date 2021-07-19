<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","solicitacao_rede.html");
  
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
      
      if (isset($_GET['idmsg'])) {
        $tpl->ID_MSG = $_GET['idMsg'];
      }
      
      $sql = new Query($bd);
      $txt = "SELECT NNUMEPAC,
							 CNOMEPAC,
							 CDESCPAC,
							 NPONTPAC,
							 CSITUPAC,
       				 NVALOPAC
FROM TREDE_PACOTES_REDE
WHERE CSITUPAC = 'a'";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $tpl->ID = $sql->result("NNUMEPAC");
        $tpl->NOME = $sql->result("CNOMEPAC");
        $tpl->DESC = $sql->result("CDESCPAC");
        $tpl->PONTOS = number_format($sql->result("NPONTPAC"),2,',','.');
        $tpl->VALOR = number_format($sql->result("NVALOPAC"),2,',','.');
        
        $tpl->block("PACOTES");
        $sql->next();
      }
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT NNUMEPPAC,
								NNUMEREDE,
								NNUMEPAC,
								SITPAGPAC,
								NVALOPPAC,
								NPONTPPAC,
								TIPOPPPAC,
								DDATAPPAC,
								CIDPGPPAC,
								CIDDPGPAC,
								CSITUAPAC,
								DPAGPCPAC,
								CTPBAIPAC,
       					LINKBOLETO
FROM TREDE_PAGAPACOTE
WHERE NNUMEREDE = '".$idrede."'";
      $sql1->executeQuery($txt1);
      
      while (!$sql1->eof()) {
        $tpl->ID1 = $sql1->result("NNUMEPPAC");
        $tpl->PLANO1 = $func->RetornaNomePacote($sql1->result("NNUMEPAC"));
        $status = $sql1->result("SITPAGPAC");
        
        
        $tpl->STATUS1 = $func->RetornaSituaPagamento($status);
        $tpl->DATA1 = $data->formataData1($sql1->result("DDATAPPAC"));
        $tpl->PONTOS1 = number_format($sql1->result("NPONTPPAC"),2,',','.');
        $tpl->VALOR1 = number_format($sql1->result("NVALOPPAC"),2,',','.');
        $tpl->TIPO1 = $sql1->result("TIPOPPPAC");
        $link = $sql1->result("LINKBOLETO");
        
        
        if (($status == '1') or ($link != "")) {
          $tpl->LINK1 = $sql1->result("LINKBOLETO");
          $tpl->block("BOL1");
        } else if (($status == '2') or ($link != "")) {
          $tpl->LINK1 = $sql1->result("LINKBOLETO");
          $tpl->block("BOL1");
        } else if (($status == '1') and ($link == NULL)) {
          $tpl->LINK = 'SEM BOLETO';
          $tpl->block("BOL");
        } else if (($status == '2') and ($link == NULL)) {
          $tpl->LINK = 'SEM BOLETO';
          $tpl->block("BOL");
        } else {
          $tpl->block("BOL");
        }
        
        $situacao = $sql1->result("CSITUAPAC");
        
        if ($situacao == 'p') {
          $tpl->SITUA = "Pendente";
        } else if ($situacao == 'a') {
          $tpl->SITUA = "Já Pago";
        }
        $tpl->block("SOLIS");
        $sql1->next();
      }
      
      
      if (isset($_POST['solicitar'])) {
        
        /*	sdebug($_POST['solicitar']);
          $sql1 = new Query($bd);
          $txt1 = "SELECT NNUMEPAC,
                          CNOMEPAC,
                          CDESCPAC,
                          NPONTPAC,
                          CSITUPAC,
                          NVALOPAC
                      FROM TREDE_PACOTES_REDE
                      WHERE NNUMEPAC = '".$_POST['solicitar']."' ";
          $sql1->executeQuery($txt1);
        
          */
      }
    }
  } else {
    $seg->verificaSession($_SESSION['aut_admin']);
  }
  
  $tpl->show();
  $bd->close();
?>