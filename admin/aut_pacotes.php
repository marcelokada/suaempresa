<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","aut_pacotes.html");
  
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
      } else if ($nivelusua == 'CCAB') {
        $tpl->DISABLE = "style='display:none;'";
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
										CSITUAPAC
			FROM TREDE_PAGAPACOTE
			WHERE CSITUAPAC = 'p'
			ORDER BY NNUMEPPAC DESC";
      $sql1->executeQuery($txt1);
      
      //WHERE CSITUAPPLAN = 'p'
      
      while (!$sql1->eof()) {
        
        $tpl->ID = $sql1->result("NNUMEPPAC");
        $seq_usua = $sql1->result("NNUMEPPAC");
        //$tpl->IDUSU = $sql1->result("NIDUPAGPLAN");
        $tpl->NOME = ucwords(utf8_encode($func->RetornaNomeEmpresa($bd,$sql1->result("NNUMEREDE"))));
        $tpl->PLANO = ucwords(utf8_encode($func->RetornaNomePacote($sql1->result("NNUMEPAC"))));
        $tpl->DATA = $data->formataData1($sql1->result("DDATAPPAC"));
        $tpl->STATUS_PAG = $func->RetornaSituaPagamento($sql1->result("SITPAGPAC"));
        $tpl->TPPG = ucwords($sql1->result("TIPOPPPAC"));
        $tpl->VALORPLANO = number_format($sql1->result("NVALOPPAC"),2,',','.');
        $tpl->IDPEDIDO = $sql1->result("CIDPGPPAC");
        
        
        $situplan = $sql1->result("CSITUAPAC");
        
        
        if ($situplan == 'p') {
          $tpl->DISA = "";
          $tpl->DISA1 = "";
          $tpl->DISA2 = "";
          $tpl->block("CANCELAR");
          $tpl->STATUSUSUA = 'Aguardando o Admin autorizar';
          $tpl->COR = "warning";
          $tpl->COR1 = "primary";
        } else if ($situplan == 'a') {
          $tpl->DISA = "disabled";
          $tpl->DISA1 = "disabled";
          $tpl->DISA2 = "disabled";
          $tpl->STATUSUSUA = 'Autorizado';
          $tpl->COR = "success";
          $tpl->COR1 = "secondary";
        } else if ($situplan == 'c') {
          $tpl->DISA = "disabled";
          $tpl->DISA1 = "disabled";
          $tpl->DISA2 = "disabled";
          $tpl->STATUSUSUA = 'Plano cancelado';
          $tpl->COR = "red";
          $tpl->COR1 = "secondary";
        } else if (($situplan == '') and ($situplan == NULL)) {
          $tpl->DISA = "";
          $tpl->DISA1 = "";
          $tpl->DISA2 = "";
          $tpl->block("CANCELAR");
          $tpl->STATUSUSUA = 'Aguardando';
          $tpl->COR = "black";
          $tpl->COR1 = "primary";
        }
        
        
        $sql1->next();
        $tpl->block("AUT");
      }
      
      $sql1 = new Query($bd);
      $sql1->clear();
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
										CSITUAPAC
			FROM TREDE_PAGAPACOTE
			WHERE CSITUAPAC = 'a'
			ORDER BY NNUMEPPAC DESC";
      $sql1->executeQuery($txt1);
      
      //WHERE CSITUAPPLAN = 'p'
      
      while (!$sql1->eof()) {
        
        $tpl->ID = $sql1->result("NNUMEPPAC");
        $seq_usua = $sql1->result("NNUMEPPAC");
        //$tpl->IDUSU = $sql1->result("NIDUPAGPLAN");
        $tpl->NOME = ucwords(utf8_encode($func->RetornaNomeEmpresa($bd,$sql1->result("NNUMEREDE"))));
        $tpl->PLANO = ucwords(utf8_encode($func->RetornaNomePacote($sql1->result("NNUMEPAC"))));
        $tpl->DATA = $data->formataData1($sql1->result("DDATAPPAC"));
        $tpl->STATUS_PAG = $func->RetornaSituaPagamento($sql1->result("SITPAGPAC"));
        $tpl->TPPG = ucwords($sql1->result("TIPOPPPAC"));
        $tpl->VALORPLANO = number_format($sql1->result("NVALOPPAC"),2,',','.');
        $tpl->IDPEDIDO = $sql1->result("CIDPGPPAC");
        
        $situplan = $sql1->result("CSITUAPAC");
        
        
        if ($situplan == 'p') {
          $tpl->DISA = "";
          $tpl->DISA1 = "";
          $tpl->DISA2 = "";
          $tpl->block("CANCELAR");
          $tpl->STATUSUSUA = 'Aguardando o Admin autorizar';
          $tpl->COR = "warning";
          $tpl->COR1 = "primary";
        } else if ($situplan == 'a') {
          $tpl->DISA = "disabled";
          $tpl->DISA1 = "disabled";
          $tpl->DISA2 = "disabled";
          $tpl->STATUSUSUA = 'Autorizado';
          $tpl->COR = "success";
          $tpl->COR1 = "secondary";
        } else if ($situplan == 'c') {
          $tpl->DISA = "disabled";
          $tpl->DISA1 = "disabled";
          $tpl->DISA2 = "disabled";
          $tpl->STATUSUSUA = 'Plano cancelado';
          $tpl->COR = "red";
          $tpl->COR1 = "secondary";
        } else if (($situplan == '') and ($situplan == NULL)) {
          $tpl->DISA = "";
          $tpl->DISA1 = "";
          $tpl->DISA2 = "";
          $tpl->block("CANCELAR");
          $tpl->STATUSUSUA = 'Aguardando';
          $tpl->COR = "black";
          $tpl->COR1 = "primary";
        }
        
        
        $sql1->next();
        $tpl->block("AUT1");
      }
      
      $sql1 = new Query($bd);
      $sql1->clear();
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
										CSITUAPAC
			FROM TREDE_PAGAPACOTE
			WHERE CSITUAPAC = 'c'
			ORDER BY NNUMEPPAC DESC";
      $sql1->executeQuery($txt1);
      
      //WHERE CSITUAPPLAN = 'p'
      
      while (!$sql1->eof()) {
        
        $tpl->ID = $sql1->result("NNUMEPPAC");
        $seq_usua = $sql1->result("NNUMEPPAC");
        //$tpl->IDUSU = $sql1->result("NIDUPAGPLAN");
        $tpl->NOME = ucwords(utf8_encode($func->RetornaNomeEmpresa($bd,$sql1->result("NNUMEREDE"))));
        $tpl->PLANO = ucwords(utf8_encode($func->RetornaNomePacote($sql1->result("NNUMEPAC"))));
        $tpl->DATA = $data->formataData1($sql1->result("DDATAPPAC"));
        $tpl->STATUS_PAG = $func->RetornaSituaPagamento($sql1->result("SITPAGPAC"));
        $tpl->TPPG = ucwords($sql1->result("TIPOPPPAC"));
        $tpl->VALORPLANO = number_format($sql1->result("NVALOPPAC"),2,',','.');
        $tpl->IDPEDIDO = $sql1->result("CIDPGPPAC");
        
        $situplan = $sql1->result("CSITUAPAC");
        
        
        if ($situplan == 'p') {
          $tpl->DISA = "";
          $tpl->DISA1 = "";
          $tpl->DISA2 = "";
          $tpl->block("CANCELAR");
          $tpl->STATUSUSUA = 'Aguardando o Admin autorizar';
          $tpl->COR = "warning";
          $tpl->COR1 = "primary";
        } else if ($situplan == 'a') {
          $tpl->DISA = "disabled";
          $tpl->DISA1 = "disabled";
          $tpl->DISA2 = "disabled";
          $tpl->STATUSUSUA = 'Autorizado';
          $tpl->COR = "success";
          $tpl->COR1 = "secondary";
        } else if ($situplan == 'c') {
          $tpl->DISA = "disabled";
          $tpl->DISA1 = "disabled";
          $tpl->DISA2 = "disabled";
          $tpl->STATUSUSUA = 'Plano cancelado';
          $tpl->COR = "red";
          $tpl->COR1 = "secondary";
        } else if (($situplan == '') and ($situplan == NULL)) {
          $tpl->DISA = "";
          $tpl->DISA1 = "";
          $tpl->DISA2 = "";
          $tpl->block("CANCELAR");
          $tpl->STATUSUSUA = 'Aguardando';
          $tpl->COR = "black";
          $tpl->COR1 = "primary";
        }
        
        
        $sql1->next();
        $tpl->block("AUT2");
      }
      
      if (isset($_POST['auto'])) {
        
        $nnumepacote = $_POST['auto'];
        $data = date('Y-m-d');
        
        $sql_r = new Query();
        $txt_r = "SELECT NNUMEPPAC,
												NNUMEREDE,
												NNUMEPAC,
												SITPAGPAC,
												NVALOPPAC,
												NPONTPPAC,
												TIPOPPPAC,
												DDATAPPAC,
												CIDPGPPAC,
												CIDDPGPAC
								FROM TREDE_PAGAPACOTE
							 WHERE NNUMEPPAC = :id";
        $sql_r->addParam(':id',$nnumepacote);
        $sql_r->executeQuery($txt_r);
        
        $valor = $sql_r->result("NVALOPPAC");
        $pontuacao = $sql_r->result("NPONTPPAC");
        $idcred = $sql_r->result("NNUMEREDE");
        
        $sql_r1 = new Query();
        $txt_r1 = "SELECT VALCREDREDE
								FROM TREDE_CREDITOREDE
							 WHERE SEQUENCIACRE = :id";
        $sql_r1->addParam(':id',$idcred);
        $sql_r1->executeQuery($txt_r1);
        
        $valor_atual = $sql_r1->result("VALCREDREDE");
        
        $sql = new Query ($bd);
        $txt = "SELECT LAST_INSERT_ID(TIPCREDTREDE) TIPCREDTREDE FROM TREDE_CREDITOTRANS_REDE
		            WHERE SEQUENCIACRE = :idcre
		            ORDER BY 1 DESC
		            LIMIT 1";
        $sql->AddParam(':idcre',$idcred);
        $sql->executeQuery($txt);
        
        $seqtransrede = $sql->result("TIPCREDTREDE") + 1;
        
        $sql4 = new Query ($bd);
        $txt4 = "INSERT INTO TREDE_CREDITOTRANS_REDE
		                              (SEQUENCIACRE,
		                               DATCREDTREDE,
		                               VALCREDTREDE,
		                               TIPCREDTREDE,
		                               CTIPONTOREDE)
		                            VALUES
		                            ('".$idcred."',
		                             '".date('Y-m-d H:i:s')."',
		                             '".$pontuacao."',
		                             '".$seqtransrede."',
		                             'manual') ";
        $sql4->executeSQL($txt4);
        
        $valor_somado = $valor_atual + $pontuacao;
        
        $sql2 = new Query ($bd);
        $txt2 = "UPDATE TREDE_CREDITOREDE SET VALCREDREDE = :valores
		            WHERE SEQUENCIACRE = :idcre";
        $sql2->AddParam(':valores',$valor_somado);
        $sql2->AddParam(':idcre',$idcred);
        $sql2->executeSQL($txt2);
        
        $sql21 = new Query ($bd);
        $txt21 = "UPDATE TREDE_PAGAPACOTE SET SITPAGPAC = '3',
                            							CSITUAPAC = 'a',
                            							DDATAPPAC = '".date('y-m-d H:i:s')."'

		            WHERE NNUMEPPAC = :idcre";
        $sql21->addParam(':idcre',$nnumepacote);
        $sql21->executeSQL($txt21);
        
        
        echo "<script>window.location.href = window.location.href</script>";
        //$util->redireciona("aut_planos.php?idSessao=" . $_SESSION['idSessao']);
      }
      
      
      if (isset($_POST['canc'])) {
        
        $seq_usuas = $_POST['canc'];
        
        $sql1 = new Query($bd);
        $txt1 = "UPDATE TREDE_PAGAPACOTE SET CSITUAPAC = 'c',
                                        SITPAGPAC = '7'
                WHERE NNUMEPPAC = '".$seq_usuas."'";
        $sql1->executeSQL($txt1);
        
        
        echo "<script>window.location.href = window.location.href</script>";
        //$util->redireciona("../principal.php?idSessao=" . $_SESSION['idSessao']);
      }
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
    
  }
  
  
  $tpl->show();
  $bd->close();
?>