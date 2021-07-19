<?php
header("access-control-allow-origin: https://sandbox.pagseguro.uol.com.br");
require_once("comum/autoload.php");

$bd = new Database();
$func = new Funcao();

$reference = 'r2r34';

$idref = substr($reference, 0, 1);

	if ($idref == 'r'){

		$ref = explode('r', $reference);

		$numepagapct = $ref[1];
		$nnumepacote = $ref[2];

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
		$idcred  = $sql_r->result("NNUMEREDE");

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
	                             'pagseguro') ";
		$sql4->executeSQL($txt4);

		$valor_somado = $valor_atual + $pontuacao;

		$sql2 = new Query ($bd);
		$txt2 = "UPDATE TREDE_CREDITOREDE SET VALCREDREDE = :valores
	            WHERE SEQUENCIACRE = :idcre";
		$sql2->AddParam(':valores',$valor_somado);
		$sql2->AddParam(':idcre',$idcred);
		$sql2->executeSQL($txt2);

	}

?>