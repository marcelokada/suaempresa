<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","pontuacaorede.html");
  
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
      
      
      if (isset($_SESSION['msg'])) {
        $msg = $_SESSION['msg'];
      }
      
      
      if ($msg == 's') {
        $tpl->MSG = "Pontuação Inserida com Sucesso.";
        $tpl->block("SUCESSO");
      }
      
      unset($_SESSION['msg']);
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT SEQUENCIACRE,
			   VNOMECREDCRE,
			   VNOMEENDECRE,
			   NNUMEENDECRE,
			   VNOMEBAIRCRE,
			   VNOMECIDAMUN,
			   CESTADOUFMUN,
			   CESTADOUFEST,
			   NNUMECATECRE,
			   NNUMECATESUB,
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
			   CVIMAGEMCCRE,
			   CLASSIFICCRE
		  FROM TREDE_CREDENCIADOS
		 ORDER BY SEQUENCIACRE DESC";
      $sql1->executeQuery($txt1);
      
      
      while (!$sql1->eof()) {
        $tpl->SEQ = $sql1->result("SEQUENCIACRE");
        $sequenciacre = $sql1->result("SEQUENCIACRE");
        
        $sql2a = new Query ($bd);
        $txt2a = "SELECT VALCREDREDE FROM TREDE_CREDITOREDE
                WHERE SEQUENCIACRE = :cred";
        $sql2a->addParam(':cred',$sequenciacre);
        $sql2a->executeQuery($txt2a);
        
        $pontuacao = $sql2a->result("VALCREDREDE");
        if ($pontuacao == '') {
          $tpl->PONTOS = '0,00';
        } else {
          $tpl->PONTOS = number_format($sql2a->result("VALCREDREDE"),2,',','.');
        }
        
        $categoria = $sql1->result("NNUMECATECRE");
        $subcategoria = $sql1->result("NNUMECATESUB");
        $tpl->NOMECRE = ucwords(utf8_encode($sql1->result("VNOMECREDCRE")));
        $tpl->CATEGORIA = ucwords(utf8_encode($func->RetornaNomeCategoria($bd,$categoria)));
        $tpl->SUBCATEGORIA = ucwords(utf8_encode($func->RetornaNomeSubCategoria($bd,$subcategoria)));
        // $tpl->LINIMAGEMIMG 	= $func->RetornaImagem($bd,$sequenciacre);
        $imagem = $sql1->result("CVIMAGEMCCRE");
        
        if ($imagem == NULL) {
          $tpl->IMAGEM = 'comum/img/Sem-imagem.jpg';
        } else {
          $tpl->IMAGEM = $imagem;
        }
        // $tpl->IMAGEM 		= $imagem;
        //$tpl->STATUS	 	= $sql1->result("CSITUACAOCRE");
        $status = $sql1->result("CSITUACAOCRE");
        
        $tpl->block("CADAREDE");
        $sql1->next();
        
      }
      $tpl->block("REDE1");
      $tpl->ID_SESSAO = $_SESSION['idSessao'];
      
      $sql_1 = new Query($bd);
      $txt_1 = "SELECT SEQCREDTREDE, SEQUENCIACRE, DATCREDTREDE, VALCREDTREDE FROM TREDE_CREDITOTRANS_REDE
            ORDER BY DATCREDTREDE DESC
            LIMIT 4";
      $sql_1->executeQuery($txt_1);
      
      while (!$sql_1->eof()) {
        $tpl->ID = $sql_1->result("SEQCREDTREDE");
        $tpl->NOMEREDE = $func->RetornaNomeEmpresa($bd,$sql_1->result("SEQUENCIACRE"));
        $tpl->DATATRANS = $data->formataData1($sql_1->result("DATCREDTREDE"));
        $tpl->VALORTRANS = number_format($sql_1->result("VALCREDTREDE"),2,',','.');
        $sql_1->next();
        $tpl->block("VAL");
      }
      
      if (isset($_POST['inserir'])) {
        $idcred = $seg->antiInjection($_POST['idcredh']);
        $pontuacao = $seg->antiInjection($_POST['pontos']);
        $pontuacao = str_replace('.','',$pontuacao);
        $pontuacao = str_replace(',','.',$pontuacao);
        
        $valor_atual = $seg->antiInjection($_POST['valor']);
        $valor_atual = str_replace('.','',$valor_atual);
        $valor_atual = str_replace(',','.',$valor_atual);
        
        
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
	                             'admin') ";
        $sql4->executeSQL($txt4);
        
        $valor_somado = $valor_atual + $pontuacao;
        
        
        $sql2 = new Query ($bd);
        $txt2 = "UPDATE TREDE_CREDITOREDE SET VALCREDREDE = :valores
            WHERE SEQUENCIACRE = :idcre";
        $sql2->AddParam(':valores',$valor_somado);
        $sql2->AddParam(':idcre',$idcred);
        $sql2->executeSQL($txt2);
        
        $util->redireciona("pontuacaorede.php?idSessao=".$_SESSION['idSessao']);
        $_SESSION['msg'] = 's';
      }
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  
  $tpl->show();
  $bd->close();
?>