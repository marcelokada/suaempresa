<?php
  require_once("comum/autoload.php");
  session_start();
  require_once("comum/apagaArquivos.php");
  //error_reporting(0);
  
  $bd      = new Database();
  $seg     = new Seguranca();
  $formata = new Formata();
  $func    = new Funcao();
  
  $idusua  = $seg->antiInjection($_POST['idusua']);
  $valor   = $seg->antiInjection($_POST['valor']);
  $idcart  = $seg->antiInjection($_POST['idcart']);
  $idloja  = $seg->antiInjection($_POST['idloja']);
  $cash    = $seg->antiInjection($_POST['cash_js']);
  $tipopag = $seg->antiInjection($_POST['tipopag']);
  
  $valortotal_cash = $func->RetornaValorCashBackUsuario($bd,$idusua);
  
  $valor_atual = $cash;
  
  /*$idusua   = 20;
  $valor    = 33.00;
  $idcart   = 23;
  $idloja   = 4;
  $cash     = 1.32;
  $tipopag  = 2;
  
  $valortotal_cash = $func->RetornaValorCashBackUsuario($bd, 20);
  
  $valor_atual = $valortotal_cash + $cash;*/
  
  
  //sistema de pontuação
  $sql1a = new Query ($bd);
  $txt1a = "SELECT VALCREDREDE FROM TREDE_CREDITOREDE
            WHERE SEQUENCIACRE = :seqcre";
  $sql1a->addParam(':seqcre',$idloja);
  $sql1a->executeQuery($txt1a);
  
  $valor_ponstuacao_atual = $sql1a->result("VALCREDREDE");
  
  $valor_total_pontoecash = $valor_ponstuacao_atual - $cash;
  
  $sql1c = new Query ($bd);
  $txt1c = "SELECT LAST_INSERT_ID(NUMTRANSUSU) NUMTRANSUSU FROM TREDE_CREDITOTRANS_USUA
            WHERE REDE_SEQUSUA = :idusua
            ORDER BY 1 DESC
            LIMIT 1";
  $sql1c->AddParam(':idusua',$idusua);
  $sql1c->executeQuery($txt1c);
  
  $seqtransusu = $sql1c->result("NUMTRANSUSU");
  
  if ($seqtransusu == "") {
    $seqtransusu = 1;
  } else {
    $seqtransusu = $sql1c->result("NUMTRANSUSU") + 1;
  }
  
  
  $sql1d = new Query ($bd);
  $txt1d = "INSERT INTO TREDE_CREDITOTRANS_USUA (REDE_SEQUSUA,SEQUENCIACRE,TIPOPAGMTO,VALTRANSUSU,DATTRANSUSU,IDCARRINHO,NUMTRANSUSU)
            VALUES
             ('".$idusua."','".$idloja."','0','".$cash."','".date('Y-m-d H:i:s')."','".$idcart."','".$seqtransusu."')";
  $sql1d->executeSQL($txt1d);
  
  
  $sql2 = new Query ($bd);
  $txt2 = "INSERT INTO TREDE_PAGAGERACASH
				(SEQUENCIACRE,VIDCARCARR,NVALORCPAG,NVAGECSPAG,NIDUSUCPAG,DDATAPGPAG,CSITUPGPAG)
				VALUES
				('".$idloja."','".$idcart."','".$valor."','".$cash."','".$idusua."','".date('Y-m-d H:i:s')."','1')";
  $sql2->executeSQL($txt2);
  
  $sql2 = new Query ($bd);
  $txt2 = "SELECT LAST_INSERT_ID(NSEQUENPAG) NSEQUENPAG FROM TREDE_PAGAGERACASH
			  WHERE SEQUENCIACRE = :seqcred
			    AND VIDCARCARR = :idcart
				AND NIDUSUCPAG = :idusua
				AND SUBSTR(DDATAPGPAG,1,10) = '".date('Y-m-d')."'
				ORDER BY 1 DESC
				LIMIT 1";
  $sql2->addParam(':seqcred',$idloja);
  $sql2->addParam(':idcart',$idcart);
  $sql2->addParam(':idusua',$idusua);
  $sql2->executeQuery($txt2);
  
  $seqcash = $sql2->result("NSEQUENPAG");
  
  $sql3 = new Query ($bd);
  $txt3 = "INSERT INTO TREDE_PAGACOMPRA
	(SEQUENCIACRE,VIDCARCARR,NVALORCPAG,NIDUSPAGCOMPRA,DDATAPAGCOMPRA,CSITUPAGCOMPRA,CTIPOPAGCOMPRA,CSITUAPGCOMPRA,NIDCSPAGCOMPRA,TIPOPAGAMENTOP,CTIPPPAGCOMPRA,NVALCASHCOMPRA)
	VALUES
	('".$idloja."','".$idcart."','".$valor."','".$idusua."','".date('Y-m-d H:i:s')."','1','1','f','".$seqcash."','n','".$tipopag."','".$valor_atual."') ";
  $sql3->executeSQL($txt3);
  
  $sql4 = new Query ($bd);
  $txt4 = "SELECT LAST_INSERT_ID(SEQPAGCOMPRA) SEQPAGCOMPRA
			   FROM TREDE_PAGACOMPRA
			  WHERE SEQUENCIACRE = :seqcred
			    AND VIDCARCARR = :idcart
				AND NIDUSPAGCOMPRA = :idusua
				AND SUBSTR(DDATAPAGCOMPRA,1,10) = '".date('Y-m-d')."'
				ORDER BY 1 DESC
				LIMIT 1";
  $sql4->addParam(':seqcred',$idloja);
  $sql4->addParam(':idcart',$idcart);
  $sql4->addParam(':idusua',$idusua);
  $sql4->executeQuery($txt4);
  
  $seqcompra = $sql4->result("SEQPAGCOMPRA");
  
  $seqcomp = 'c'.$seqcompra.'c'.$idcart;;
  
  $sql5 = new Query ($bd);
  $txt5 = "UPDATE TREDE_PAGACOMPRA SET IDPAGSEGCOMPRA = :seqcomp
			 WHERE SEQPAGCOMPRA = :seqcompra";
  $sql5->addParam(':seqcomp',$seqcomp);
  $sql5->addParam(':seqcompra',$seqcompra);
  $sql5->executeSQL($txt5);
  
  $eventos['seqcompra'] = $seqcompra;
  $eventos['xxx']       = $seqcomp;
  
  $eventos['idcart'] = $idcart;
  
  $val              = $formata->formataNumero($valor);
  $val              = str_replace('.','',$val);
  $val              = str_replace(',','.',$val);
  $eventos['valor'] = $val;
  
  $sql6 = new Query($bd);
  $txt6 = "SELECT VEMAILPAGSEG,VTOKENPAGSEG FROM TREDE_PAGSEGURO
				WHERE SEQUENCIACRE = :seqcred
				  AND CSTATUPAGSEG = 'a'";
  $sql6->addParam(':seqcred','a');
  $sql6->executeQuery($txt6);
  
  $eventos['email'] = $sql6->result("VEMAILPAGSEG");
  $eventos['token'] = $sql6->result("VTOKENPAGSEG");
  
  /*$sql61 = new Query($bd);
  $txt61 = "SELECT TOKEN
              FROM TREDE_DOTBANK";
  $sql61->executeQuery($txt61);
  
  $eventos['token_dotbank'] = $sql61->result("TOKEN");*/
  
  $sql61 = new Query($bd);
  $txt61 = "SELECT TOKEN
            FROM TREDE_DOTBANK_REDE
            WHERE SEQUENCIACRE = :seq";
  $sql61->addParam(':seq',$idloja);
  $sql61->executeQuery($txt61);
  
  $eventos['token_dotbank'] = $sql61->result("TOKEN");
  
  
  if ($tipopag == 'dotbank') {
    
    $nomeusua    = $_POST['nomeusua'];
    $cpfs        = $_POST['cpf'];
    $mmailusua   = $_POST['emails'];
    $cell        = $_POST['cell'];
    $enderecos   = $_POST['endereco'];
    $numbers     = $_POST['numero'];
    $bairros     = $_POST['bairro'];
    $cidades     = $_POST['cidade'];
    $estados     = $_POST['estado'];
    $ceps        = $_POST['ceps'];
    $cell1       = $_POST['cell1'];
    $emailadmin  = $_POST['emailadmin'];
    $datavenc    = $_POST['datavenc'];
    $total_valor = $_POST['totalvalor'];
    
    
    // Adiciona o identificador "Contatos" aos dados
    $dados_identificador = array(
      "customer_name"     => $nomeusua,
      "customer_document" => $cpfs,
      "customer_mail"     => $mmailusua,
      "customer_phone"    => $cell,
      "address_line1"     => $enderecos.', '.$numbers,
      "address_line2"     => "",
      "neighborhood"      => $bairros,
      "city"              => $cidades,
      "state"             => $estados,
      "zip_code"          => $ceps,
      "external_number"   => $seqcomp,
      "phone_to_send"     => $cell1,
      "mail_to_send"      => $emailadmin,
      "due_date"          => $datavenc,
      "total_value"       => $total_valor,
    );
    
    // Tranforma o array $dados_identificador em JSON
    $dados_json = json_encode($dados_identificador);
    
    $fp = fopen("boletos_compras/".$seqcomp.".txt","a");
    // Escreve o conteúdo JSON no arquivo
    $escreve = fwrite($fp,$dados_json);
    
    // Fecha o arquivo
    fclose($fp);
  }
  
  echo json_encode($eventos);
  
  $bd->close();
?>