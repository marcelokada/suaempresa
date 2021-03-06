<?php
  require_once("comum/autoload.php");
  session_start();
  error_reporting(0);
  
  $bd = new Database();
  $func = new Funcao();
  $seg = new Seguranca();
  
  
  $idPedido = $seg->antiInjection($_POST['idPedido']);
  
  $sqlP = new Query ($bd);
  $txtP = "SELECT SEQUPAGPLAN,
                  NIDUPAGPLAN,
                  NSEQPAGPLAN,
                  NVALPAGPLAN,
                  CSITPAGPLAN,
                  CTEMPAGPLAN,
                  DDATPAGPLAN,
                  DDTINIPPLAN,
                  DDTFIMPPLAN,
                  DDTVERPPLAN,
                  NIDCODIPLAN,
                  CTIPOPGPLAN,
                  CSITUAPPLAN,
                  IDPGSEGPLAN,
                  MENSAPLANO,
                  ADESAOPLANO,
                  CTIPOTRPLAN,
                  CTIPOOPPLAN,
                  LINKBOLETO,
                  NIDCODIPLAN1,
                  CCOMPRPPLAN,
                  CNOMCOPPLAN,
                  DCOMPRPPLAN,
                  DVENCBOPLAN,
                  CIDSEQPPLAN
			   FROM TREDE_PAGAPLANO
			  WHERE IDPGSEGPLAN = '".$idPedido."' ";
  $sqlP->executeQuery($txtP);
  
  
  $idusu        = $sqlP->result("NIDUPAGPLAN");
  
  $idplano      = $sqlP->result("NSEQPAGPLAN");
  $tempo        = $sqlP->result("CTEMPAGPLAN");
  
  $adesao       = $sqlP->result("ADESAOPLANO");
  $mensa        = $sqlP->result("MENSAPLANO");
  $tipo         = $sqlP->result("CTIPOTRPLAN");
  $oppag        = $sqlP->result("CTIPOOPPLAN");
  $tipopags     = $sqlP->result("CTIPOTRPLAN");
  $bol12        = 'n';
  $datavencBOL  = date('Y-m-d');
  $idseq        = $sqlP->result("CIDSEQPPLAN");
  
  
  if($tipo == 'a'){
    $valor        = $adesao;
  }else if($tipo == 'm'){
    $valor        = $mensa;
  }
  
  /*  $idusu      = "147";
    $idplano    = "6";
    $tempo      = "30";
    $valor      = "300.00";
    $adesao     = "300.00";
    $mensa      = "40.00";
    $tipo       = "dotbank";
    $oppag = "dotbank";
    $tipopags = 2;
    $bol12 = 'n';
    $datavencBOL = '2021/03/10';
    $idseq = 1;*/
  
  
  $sql3 = new Query ($bd);
  $txt3 = "SELECT REDE_NOMEUSU,
                REDE_CPFUSUA,
                REDE_EMAILUS,
                REDE_CELULAR,
                REDE_DATAVENC
			   FROM TREDE_USUADMIN
			  WHERE REDE_SEQUSUA = :usua";
  $sql3->AddParam(':usua',$idusu);
  $sql3->executeQuery($txt3);
  
  $resdia = $sql3->result("REDE_DATAVENC");
  
  if ($resdia == "") {
    $dia = "10";
  } else {
    $dia = $sql3->result("REDE_DATAVENC");
  }
  
  if ($bol12 == 's') {
    
    $res_datual = strtotime(date('Y-m-d'));
    //$res_datual = strtotime(date('2021-02-11'));
    $res_dbol = strtotime(date('Y-m-'.$dia));
    
    if ($res_datual < $res_dbol) {
      
      if ($idseq == 1) {
        $mes_posterior = date('Y-m-'.$dia);
      } else if ($idseq == 2) {
        $mes_posterior = Date("Y-m-".$dia,strtotime(" +1 Month"));
      } else if ($idseq == 3) {
        $mes_posterior = Date("Y-m-".$dia,strtotime(" +2 Month"));
      } else if ($idseq == 4) {
        $mes_posterior = Date("Y-m-".$dia,strtotime(" +3 Month"));
      } else if ($idseq == 5) {
        $mes_posterior = Date("Y-m-".$dia,strtotime(" +4 Month"));
      } else if ($idseq == 6) {
        $mes_posterior = Date("Y-m-".$dia,strtotime(" +5 Month"));
      } else if ($idseq == 7) {
        $mes_posterior = Date("Y-m-".$dia,strtotime(" +6 Month"));
      } else if ($idseq == 8) {
        $mes_posterior = Date("Y-m-".$dia,strtotime(" +7 Month"));
      } else if ($idseq == 9) {
        $mes_posterior = Date("Y-m-".$dia,strtotime(" +8 Month"));
      } else if ($idseq == 10) {
        $mes_posterior = Date("Y-m-".$dia,strtotime(" +9 Month"));
      } else if ($idseq == 11) {
        $mes_posterior = Date("Y-m-".$dia,strtotime(" +10 Month"));
      } else if ($idseq == 12) {
        $mes_posterior = Date("Y-m-".$dia,strtotime(" +11 Month"));
      }
      
    } else {
      $mes_posterior = Date("Y-m-".$dia,strtotime(" +".$idseq." Month"));
    }
    
    $datavenc = $mes_posterior;
  } else {
    $mes_posterior = $datavencBOL;
    $datavenc = $datavencBOL;
  }
  
  
  $_SESSION['valor'] = $valor;
  
  $sql2 = new Query ($bd);
  $txt2 = "INSERT INTO TREDE_PAGAPLANO (NIDUPAGPLAN,
										  NSEQPAGPLAN,
										  NVALPAGPLAN,
										  CSITPAGPLAN,
										  CTEMPAGPLAN,
										  DDATPAGPLAN,
                      MENSAPLANO,
                      ADESAOPLANO,
                      CTIPOTRPLAN,
                      CTIPOOPPLAN,
                      CTIPOPGPLAN,
                      CSITUAPPLAN,
                      DVENCBOPLAN,
                      CIDSEQPPLAN,
                      CSTABOLPLAN)
			              VALUES
			                ('".$idusu."',
			                '".$idplano."',
			                '".$adesao."',
			                '1',
			                '".$tempo."',
			                '".date('Y-m-d H:i:s')."',
			                '".$mensa."',
			                '".$adesao."',
			                'm',
			                '".$oppag."',
			                '2',
			                'p',
			                '".$mes_posterior."',
			                '".$idseq."',
			                'a')";
  $sql2->executeSQL($txt2);
  
  $sql = new Query ($bd);
  $txt = "UPDATE TREDE_USUADMIN SET REDE_PLANUSU = :idplan
			 WHERE REDE_SEQUSUA = :idusu";
  $sql->addParam(':idplan',$idplano);
  $sql->addParam(':idusu',$idusu);
  //$sql->executeSQL($txt);
  
  $sql1 = new Query ($bd);
  $txt1 = "SELECT LAST_INSERT_ID(SEQUPAGPLAN) SEQUPAGPLAN, NSEQPAGPLAN FROM TREDE_PAGAPLANO
			  WHERE NIDUPAGPLAN = :idusu
			    AND SUBSTR(DDATPAGPLAN,1,10) = '".date('Y-m-d')."'
			    AND CSITPAGPLAN != '7'
				ORDER BY SEQUPAGPLAN DESC
				LIMIT 1";
  $sql1->addParam(':idusu',$idusu);
  $sql1->executeQuery($txt1);
  
  $res_id_pag = $sql1->result("SEQUPAGPLAN");
  $id_plano = $sql1->result("NSEQPAGPLAN");
  
  $seqplan = 'p'.$id_plano.'p'.$res_id_pag;
  
  
  $sql5 = new Query ($bd);
  $txt5 = "UPDATE TREDE_PAGAPLANO SET IDPGSEGPLAN = :seqplapp
			 WHERE SEQUPAGPLAN = :res_id_pag";
  $sql5->addParam(':res_id_pag',$res_id_pag);
  $sql5->addParam(':seqplapp',$seqplan);
  $sql5->executeSQL($txt5);
  
  $sql3 = new Query ($bd);
  $txt3 = "SELECT CNOMEPLANO,CTEMPPLANO FROM TREDE_PLANOS
			  WHERE SEQPLANO = :idplano";
  $sql3->addParam(':idplano',$idplano);
  $sql3->executeQuery($txt3);
  
  $res_mes = $sql3->result("CTEMPPLANO");
  
  $eventos['id'] = $res_id_pag;
  $eventos['mes'] = $res_mes;
  $eventos['xxx'] = $seqplan;
  
  $sql6 = new Query($bd);
  $txt6 = "SELECT VEMAILPAGSEG,VTOKENPAGSEG FROM TREDE_PAGSEGURO
				WHERE SEQUENCIACRE = 'a' ";
  $sql6->executeQuery($txt6);
  
  $eventos['email'] = $sql6->result("VEMAILPAGSEG");
  $eventos['token'] = $sql6->result("VTOKENPAGSEG");
  
  $sql61 = new Query($bd);
  $txt61 = "SELECT TOKEN
            FROM TREDE_DOTBANK";
  $sql61->executeQuery($txt61);
  
  $eventos['token_dotbank'] = $sql61->result("TOKEN");
  
  $sql8 = new Query($bd);
  $txt8 = "SELECT NNUMEFILI,NNUMENIVE,NPORCNIVE FROM TREDE_NIVEL
            WHERE NIDUSNIVE = :sequ";
  $sql8->addParam(':sequ',$idusu);
  $sql8->executeQuery($txt8);
  
  $seq_patrocinador = $sql8->result("NNUMEFILI");
  $nivel_comprador = $sql8->result("NNUMENIVE");
  $porcetagem_nivel = $sql8->result("NPORCNIVE");
  
  $valor_porct_adesao = $adesao * $porcetagem_nivel / 100;
  
  $sql7 = new Query($bd);
  $txt7 = "SELECT REDE_NIVELUS FROM TREDE_USUADMIN
            WHERE REDE_SEQUSUA = :sequ";
  $sql7->addParam(':sequ',$seq_patrocinador);
  $sql7->executeQuery($txt7);
  
  $nivel_patrocinador = $sql7->result("REDE_NIVELUS");
  
  $sql4 = new Query ($bd);
  $txt4 = "INSERT INTO TREDE_AFILIADOS_BONUS_MENSAL
             (IDUSUABONUSMS, 
              NVLUSUBONUSMS, 
              NVALUSBONUSSM, 
              PORADEBONUSMS, 
              USUAFIBONUSMS, 
              NVLUFIBONUSMS, 
              SEQUPAGPLAN, 
              SEQPLANO, 
              DATAADBONUSMS)  
              VALUES 
              ('".$seq_patrocinador."',
              '".$nivel_patrocinador."',
              '".$mensa."',
              '".$valor_porct_adesao."',
              '".$idusu."',
              '".$nivel_comprador."',
              '".$res_id_pag."',
              '".$idplano."',
              '".date('Y-m-d H:i:s')."')";
  $sql4->executeSQL($txt4);
  
  $sql_usua = new Query();
  $txt_usua = "SELECT CASE NNUMENIVE
	WHEN 0 THEN NIDUSNIVE 
	WHEN 1 THEN (SELECT NNUMEFILI FROM TREDE_NIVEL WHERE NIDUSNIVE = L.NIDUSNIVE)
	WHEN 2 THEN (SELECT NNUMEFILI FROM TREDE_NIVEL WHERE NIDUSNIVE IN (SELECT NNUMEFILI FROM TREDE_NIVEL WHERE NIDUSNIVE  = L.NIDUSNIVE))
	WHEN 3 THEN (SELECT NNUMEFILI FROM TREDE_NIVEL WHERE NIDUSNIVE IN (SELECT NNUMEFILI FROM TREDE_NIVEL WHERE NIDUSNIVE IN (SELECT NNUMEFILI FROM TREDE_NIVEL WHERE NIDUSNIVE  = L.NIDUSNIVE)))
	WHEN 4 THEN (SELECT NNUMEFILI FROM TREDE_NIVEL WHERE NIDUSNIVE IN (SELECT NNUMEFILI FROM TREDE_NIVEL WHERE NIDUSNIVE IN 
		    (SELECT NNUMEFILI FROM TREDE_NIVEL WHERE NIDUSNIVE IN (SELECT NNUMEFILI FROM TREDE_NIVEL WHERE NIDUSNIVE  = L.NIDUSNIVE))))
	END AS NIVEL
  FROM TREDE_NIVEL L
 WHERE NIDUSNIVE = :usua";
  $sql_usua->addParam(':usua',$idusu);
  $sql_usua->executeQuery($txt_usua);
  
  $res_usua = $sql_usua->result("NIVEL");
  
  $sql5 = new Query ($bd);
  $txt5 = "INSERT INTO TREDE_TRANSAOCAO_USUA
             (USUARIO_PATROC,
              DEBITO,
              CREDITO,
              TIPO_TRANS,
              NNUMEUSUA,
              DATAHORA_TRANS)  
              VALUES 
              ('".$res_usua."',
              '0.00',
              '".$valor_porct_adesao."',
              '1',              
              '".$idusu."',
              '".date('Y-m-d H:i:s')."')";
  $sql5->executeSQL($txt5);
  
  $sql9 = new Query($bd);
  $txt9 = "SELECT PORCENTAGEM FROM TREDE_NIVEIS_ATIVOS
            WHERE NUMENIVEL = '".$nivel_patrocinador."'";
  $sql9->executeQuery($txt9);
  
  $porc_ativos = $sql9->result("PORCENTAGEM");
  
  $valor_mensa_porc = $mensa * $porc_ativos / 100;
  
  $nnumeidplano = $func->RetornaIDPlanoPatrocinador($seq_patrocinador);
  
  $sql611 = new Query ($bd);
  $txt611 = "SELECT VALOR
						FROM  TREDE_VALOR_UNILEVEL
           WHERE NNUMEPLAN = :nnumeidplano";
  $sql611->addParam(':nnumeidplano',$nnumeidplano);
  $sql611->executeQuery($txt611);
  
  $valor_do_limite = $sql611->result("VALOR");
  
  $sql6 = new Query ($bd);
  $txt6 = "SELECT VALORTOTAL
           FROM TREDE_ADESAO_MENSA_USU 
          WHERE NIDUPAGPLAN = :idusuas";
  $sql6->addParam(':idusuas',$seq_patrocinador);
  $sql6->executeQuery($txt6);
  
  $res_valorT_res = $sql6->result("VALORTOTAL");
  
  if ($res_valorT_res == "") {
    $res_valorT = '0.00';
  } else {
    $res_valorT = $sql6->result("VALORTOTAL");
  }
  
  $valor_ade_mensal = $res_valorT + $valor_mensa_porc;
  
  if ($valor_ade_mensal > $valor_do_limite) {
    $eventos['validar'] = 1;
  } else {
    $eventos['validar'] = 0;
    $sql61 = new Query ($bd);
    $txt61 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '".$valor_ade_mensal."'
            WHERE NIDUPAGPLAN = :idusuas1";
    $sql61->addParam(':idusuas1',$seq_patrocinador);
    //$sql61->executeSQL($txt61);
  }
  
  /*
   * "TIPO_TRANS" DO BANCO
   * 1 - ADESAO
   * 2 - SAQUE
   * 3 - CASHBACK
   * 4 - MENSALIDADE
   */
  
  $sql = new Query ($bd);
  $txt = "SELECT ADESAOPLANO,MENSAPLANO,CTIPOTRPLAN,DVENCBOPLAN
			    FROM TREDE_PAGAPLANO
				WHERE IDPGSEGPLAN = :id";
  $sql->AddParam(':id',$idPedido);
  $sql->executeQuery($txt);
  
  $tipo_plan = $sql->result("CTIPOTRPLAN");
  $venci_bol = $sql->result("DVENCBOPLAN");
  
  if ($tipo_plan == 'a') {
    $valor = $sql->result("ADESAOPLANO");
  } else if ($tipo_plan == 'm') {
    $valor = $sql->result("MENSAPLANO");
  }
  
  if ($oppag == 'dotbank') {
    $sql1aa = new Query ($bd);
    $txt1aa = "SELECT JUROS_VENC,JUROS_DIA FROM TREDE_JUROS_BOL";
    $sql1aa->executeQuery($txt1aa);
  
    $juros_venc = $sql1aa->result("JUROS_VENC");
    $juros_dia = $sql1aa->result("JUROS_DIA");
    //$data_venc = date('Y-m-d', strtotime('+1 days', strtotime($venci_bol)));
    $data_venc = $venci_bol ;
    
    
    $arquivo = file_get_contents("boletos_adesao_mensa/".$idPedido.'.txt');
  
    //$arquivo = file_get_contents("boletos_adesao_mensa/p6p571.txt");
  
    $json = json_decode($arquivo);
  
    $nomeusua       = $json->customer_name;
    $cpfs           = $json->customer_document;
    $mmailusua      = $json->customer_mail;
    $cell           = $json->customer_phone;
    $enderecos      = $json->address_line1;
    $numbers        = "";
    $bairros        = $json->neighborhood;
    $cidades        = $json->city;;
    $estados        = $json->state;
    $ceps           = $json->zip_code;
    $cell1          = $json->phone_to_send;
    $emailadmin     = $json->mail_to_send;
    $total_valor    = $json->total_value;
    
    
    
    /*    $nomeusua     = "Marcelo";
        $cpfs         = "36883489827";
        $mmailusua    = "marcelookada@outlook.com";
        $cell         = "18997954700";
        $enderecos    = "av brasil";
        $numbers      = "123";
        $bairros      = "vila jos??";
        $cidades      = "marti";
        $estados      = "SP";
        $ceps         = "17690000";
        $cell1        = "11979797855";
        $emailadmin   = "doutroeultra@cb.com.br";
        $total_valor  = "40.00";
        $datavenc     = '2021/03/10';*/
    
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
      "external_number"   => $seqplan,
      "phone_to_send"     => $cell1,
      "mail_to_send"      => $emailadmin,
      "due_date"          => $datavenc,
      //"due_date"          => date('Y-m-d'),
      "total_value"       => $valor,
      "fine_date"         => $datavenc,
      //"fine_date"         => date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d')))),
      "fine_percent"      => $juros_venc,
      "interest_percent"  => $juros_dia,
    );
    
    // Tranforma o array $dados_identificador em JSON
    $dados_json = json_encode($dados_identificador);
    
    $fp = fopen("boletos_adesao_mensa/".$seqplan.".txt","a+");
    // Escreve o conte??do JSON no arquivo
    $escreve = fwrite($fp,$dados_json);
    
    // Fecha o arquivo
    fclose($fp);
  }
  
  $eventos['idPedido'] = $seqplan;
  
  $sql1a1 = new Query ($bd);
  $txt1a1 = "UPDATE TREDE_PAGAPLANO SET CSTABOLPLAN = 'c'
                WHERE IDPGSEGPLAN = '".$idPedido."'";
  $sql1a1->executeSQL($txt1a1);
  
  echo json_encode($eventos);
  
  $bd->close();
?>