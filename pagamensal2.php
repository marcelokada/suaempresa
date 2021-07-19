<?php
require_once("comum/autoload.php");
session_start();
//error_reporting(0);

$bd = new Database();
$func =  new Funcao();
$seg =  new Seguranca();

$idusu      = $seg->antiInjection($_POST['idusu']);
$idplano    = $seg->antiInjection($_POST['idplano']);
$tempo      = $seg->antiInjection($_POST['tempo']);
$valor      = $seg->antiInjection($_POST['valor']);
$adesao     = $seg->antiInjection($_POST['adesao']);
$mensa      = $seg->antiInjection($_POST['mensa']);
$tipo       = $seg->antiInjection($_POST['tipopg']);
$oppag      = $_POST['oppag'];

/*$idusu      = "20";
$idplano    = "6";
$tempo      = "30";
$valor      = "300.00";
$adesao     = "300.00";
$mensa      = "40.00";
$tipo       = "dotbank";
$oppag = "dotbank";*/

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
                      CSITUAPPLAN)
			VALUES
			('" . $idusu . "','" . $idplano . "','" . $valor . "','1','" . $tempo . "',
			'" . date('Y-m-d H:i:s') . "','" . $mensa . "','" . $adesao . "','m',
			'" . $oppag . "','2','p')";
$sql2->executeSQL($txt2);

$sql = new Query ($bd);
$txt = "UPDATE TREDE_USUADMIN SET REDE_PLANUSU = :idplan
			 WHERE REDE_SEQUSUA = :idusu";
$sql->addParam(':idplan', $idplano);
$sql->addParam(':idusu', $idusu);
//$sql->executeSQL($txt);

$sql1 = new Query ($bd);
$txt1 = "SELECT LAST_INSERT_ID(SEQUPAGPLAN) SEQUPAGPLAN, NSEQPAGPLAN FROM TREDE_PAGAPLANO
			  WHERE NIDUPAGPLAN = :idusu
			    AND SUBSTR(DDATPAGPLAN,1,10) = '" . date('Y-m-d') . "'
			    AND CSITPAGPLAN != '7'
				ORDER BY SEQUPAGPLAN DESC
				LIMIT 1";
$sql1->addParam(':idusu', $idusu);
$sql1->executeQuery($txt1);

$res_id_pag = $sql1->result("SEQUPAGPLAN");
$id_plano = $sql1->result("NSEQPAGPLAN");

$seqplan = 'p' . $id_plano . 'p' . $res_id_pag;

$sql5 = new Query ($bd);
$txt5 = "UPDATE TREDE_PAGAPLANO SET IDPGSEGPLAN = :seqplapp
			 WHERE SEQUPAGPLAN = :res_id_pag";
$sql5->addParam(':res_id_pag', $res_id_pag);
$sql5->addParam(':seqplapp', $seqplan);
$sql5->executeSQL($txt5);

$sql3 = new Query ($bd);
$txt3 = "SELECT CNOMEPLANO,CTEMPPLANO FROM TREDE_PLANOS
			  WHERE SEQPLANO = :idplano";
$sql3->addParam(':idplano', $idplano);
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
$sql8->addParam(':sequ', $idusu);
$sql8->executeQuery($txt8);

$seq_patrocinador = $sql8->result("NNUMEFILI");
$nivel_comprador = $sql8->result("NNUMENIVE");
$porcetagem_nivel = $sql8->result("NPORCNIVE");

$valor_porct_adesao = $adesao * $porcetagem_nivel / 100;

$sql7 = new Query($bd);
$txt7 = "SELECT REDE_NIVELUS FROM TREDE_USUADMIN
            WHERE REDE_SEQUSUA = :sequ";
$sql7->addParam(':sequ', $seq_patrocinador);
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
              ('" . $seq_patrocinador . "',
              '" . $nivel_patrocinador . "',
              '" . $mensa . "',
              '" . $valor_porct_adesao . "',
              '" . $idusu . "',
              '" . $nivel_comprador . "',
              '" . $res_id_pag . "',
              '" . $idplano . "',
              '" . date('Y-m-d H:i:s') . "')";
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
$sql_usua->addParam(':usua', $idusu);
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
              ('" . $res_usua . "',
              '0.00',
              '" . $valor_porct_adesao . "',
              '1',              
              '" . $idusu . "',
              '" . date('Y-m-d H:i:s') . "')";
$sql5->executeSQL($txt5);

$sql9 = new Query($bd);
$txt9 = "SELECT PORCENTAGEM FROM TREDE_NIVEIS_ATIVOS
            WHERE NUMENIVEL = '" . $nivel_patrocinador . "'";
$sql9->executeQuery($txt9);

$porc_ativos = $sql9->result("PORCENTAGEM");

$valor_mensa_porc = $mensa * $porc_ativos / 100;

$nnumeidplano = $func->RetornaIDPlanoPatrocinador($seq_patrocinador);

$sql611 = new Query ($bd);
$txt611 = "SELECT VALOR
						FROM  TREDE_VALOR_UNILEVEL
           WHERE NNUMEPLAN = :nnumeidplano";
$sql611->addParam(':nnumeidplano', $nnumeidplano);
$sql611->executeQuery($txt611);

$valor_do_limite = $sql611->result("VALOR");

$sql6 = new Query ($bd);
$txt6 = "SELECT VALORTOTAL 
           FROM TREDE_ADESAO_MENSA_USU 
          WHERE NIDUPAGPLAN = :idusuas";
$sql6->addParam(':idusuas', $seq_patrocinador);
$sql6->executeQuery($txt6);

$res_valorT_res = $sql6->result("VALORTOTAL");

if($res_valorT_res == ""){
	$res_valorT = '0.00';
}else{
	$res_valorT = $sql6->result("VALORTOTAL");
}

$valor_ade_mensal = $res_valorT + $valor_mensa_porc;

if($valor_ade_mensal > $valor_do_limite){
	$eventos['validar'] = 1;
}else{
	$eventos['validar'] = 0;
	$sql61 = new Query ($bd);
	$txt61 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '" . $valor_ade_mensal . "'
            WHERE NIDUPAGPLAN = :idusuas1";
	$sql61->addParam(':idusuas1', $seq_patrocinador);
	//$sql61->executeSQL($txt61);
}

/*
 * "TIPO_TRANS" DO BANCO
 * 1 - ADESAO
 * 2 - SAQUE
 * 3 - CASHBACK
 * 4 - MENSALIDADE
 */

if($tipo != 'pagseguro') {

	$nomeusua = $_POST['nomeusua'];
	$cpfs = $_POST['cpf'];
	$mmailusua = $_POST['emails'];
	$cell = $_POST['cell'];
	$enderecos = $_POST['endereco'];
	$numbers = $_POST['numero'];
	$bairros = $_POST['bairro'];
	$cidades = $_POST['cidade'];
	$estados = $_POST['estado'];
	$ceps = $_POST['ceps'];
	$cell1 = $_POST['cell1'];
	$emailadmin = $_POST['emailadmin'];
	$datavenc = $_POST['datavenc'];
	$total_valor = $_POST['totalvalor'];

	// Adiciona o identificador "Contatos" aos dados
	$dados_identificador = array("customer_name" => $nomeusua, "customer_document" => $cpfs, "customer_mail" => $mmailusua, "customer_phone" => $cell, "address_line1" => $enderecos . ', ' . $numbers, "address_line2" => "", "neighborhood" => $bairros, "city" => $cidades, "state" => $estados, "zip_code" => $ceps, "external_number" => $seqplan, "phone_to_send" => $cell1, "mail_to_send" => $emailadmin, "due_date" => $datavenc, "total_value" => $total_valor);

	// Tranforma o array $dados_identificador em JSON
	$dados_json = json_encode($dados_identificador);

	$fp = fopen("boletos_adesao_mensa/" . $seqplan . ".txt", "a");
	// Escreve o conteúdo JSON no arquivo
	$escreve = fwrite($fp, $dados_json);

	// Fecha o arquivo
	fclose($fp);
}

echo json_encode($eventos);

$bd->close();
?>