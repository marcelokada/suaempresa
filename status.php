<?php
header("access-control-allow-origin: https://sandbox.pagseguro.uol.com.br");
require_once("comum/autoload.php");

$bd = new Database();
$func = new Funcao();

$notificationCode = preg_replace('/[^[:alnum:]-]/', '', $_POST["notificationCode"]);
//$notificationCode = '3CB5FD9A9638963868E994A3DFB67F03C98C';
//$notificationType = $_POST["notificationType"];

$token = 'be5d1b2b-e804-46d1-afb7-04951c8f95707f0980b24c57b7392dbfc670fcd40800493b-54c6-4bd3-a90e-2a6047a6354c';
$email = 'doutorultraconvenio@gmail.com';

//$token = 'C842E37AAD194A8DA458FBCE2D101FB2';
//$email = 'marcelookada@outlook.com';

$url = 'https://ws.pagseguro.uol.com.br/v3/transactions/notifications/' . $notificationCode . '?email=' . $email . '&token=' . $token; //Teste s� acrescentar o .sandbox
//$url = 'https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/'.$notificationCode.'?email='.$email.'&token='.$token; //Teste s� acrescentar o .sandbox

//$url = 'https://ws.pagseguro.uol.com.br/v3/transactions/notifications/{'.$notificationCode.'}?'.$data;

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$xml = curl_exec($curl);
curl_close($curl);

$res = simplexml_load_string($xml);

echo $res;

$reference = $res->reference;
$code = $res->code;
$status = $res->status;
$tipopagmto = $res->paymentMethod->type;
$tippcartao = $res->paymentMethod->code;
$cpf = $res->sender->documents->document->value;

$idref = substr($reference, 0, 1);

if ($idref == 'c') {

	$com = explode('c', $reference);

	$pc = $com[1];
	$cr = $com[2];

	$sql1a = new Query ($bd);
	$txt1a = "SELECT  SEQPAGCOMPRA,
       								  SEQUENCIACRE,
       									VIDCARCARR,
       									NVALORCPAG,
       							 	  NIDUSPAGCOMPRA,
       									NIDCSPAGCOMPRA
       					 FROM TREDE_PAGACOMPRA
            		WHERE SEQPAGCOMPRA = :seqcre";
	$sql1a->addParam(':seqcre', $pc);
	$sql1a->executeQuery($txt1a);

	$seq_comp = $sql1a->result("SEQPAGCOMPRA");
	$seq_cred = $sql1a->result("SEQUENCIACRE");
	$seq_carr = $sql1a->result("VIDCARCARR");
	$seq_usua = $sql1a->result("NIDUSPAGCOMPRA");
	$seq_cash = $sql1a->result("NIDCSPAGCOMPRA");
	$valor_compra = $sql1a->result("NVALORCPAG");

	$sql1b = new Query ($bd);
	$txt1b = "SELECT NVAGECSPAG,
       									CSITUPGPAG       									
       FROM TREDE_PAGAGERACASH
            WHERE NSEQUENPAG = :seqcre";
	$sql1b->addParam(':seqcre', $seq_cash);
	$sql1b->executeQuery($txt1b);

	$cash = $sql1b->result("NVAGECSPAG");

	$valortotal_cash = $func->RetornaValorCashBackUsuario($bd, $seq_usua);

	$valor_atual = $valortotal_cash + $cash;

	$sql = new Query ($bd);
	$txt = "UPDATE TREDE_PAGACOMPRA SET 
							CSITUPAGCOMPRA = :pagm, 
              DDATAEFETIVCOMP = '".date('Y-m-d')."',
							CTIPOPAGCOMPRA = :tppag, 
							CIDTRPAGCOMPRA = :code,
							CCPFUSUAPAGCOMP = :cpf
			 WHERE IDPAGSEGCOMPRA = :refe ";
	$sql->addParam(':tppag', $tipopagmto);
	$sql->addParam(':pagm', $status);
	$sql->addParam(':refe', $reference);
	$sql->addParam(':code', $code);
	$sql->addParam(':cpf', $cpf);
	$sql->executeSQL($txt);

	$sql1 = new Query ($bd);
	$txt1 = "UPDATE TREDE_CASHBACK_USU SET VVALUSCASH = :valor_atual
			 WHERE NIDUSUCASH = :idusua";
	$sql1->addParam(':valor_atual', $valor_atual);
	$sql1->addParam(':idusua', $seq_usua);
	$sql1->executeSQL($txt1);

	$sql2 = new Query ($bd);
	$txt2 = "UPDATE TREDE_PAGAGERACASH SET CSITUPGPAG = '3'
       					WHERE NSEQUENPAG = :seqcre";
	$sql2->addParam(':seqcre', $seq_cash);
	$sql2->executeSQL($txt2);

	$sql1b = new Query ($bd);
	$txt1b = "SELECT VALCREDREDE FROM TREDE_CREDITOREDE 
			 WHERE SEQUENCIACRE = :seqcre";
	$sql1b->addParam(':seqcre', $seq_cred);
	$sql1b->executeQuery($txt1b);

	$cash_rede = $sql1b->result("VALCREDREDE");

	$menu_porc_rede = $func->RetornaPermissoes('MENU_PORCENTAGEM_REDE');

	if($menu_porc_rede[0]['STATUS'] == '0'){
		$valor_total_pontoecash = $cash_rede - $cash;
		$ctipoclass = 'i';
		sdebug('Desconta o valor do cash: '.$cash);
	}else{
		$valor_total_pontoecash = $cash_rede - $valor_compra;
		$ctipoclass = 'p';
		sdebug('Desconta o valor da compra total: '.$valor_compra);
	}

	$sqlc = new Query ($bd);
	$txtc = "UPDATE TREDE_PAGACOMPRA SET CTIPINTECOMPRA = '".$ctipoclass."'  WHERE IDPAGSEGCOMPRA = :refe ";
	$sqlc->addParam(':refe', $reference);
	$sqlc->executeSQL($txtc);

	/*			sdebug('Status: '.$status);
				sdebug('Seq_pagcompra: '.$pc);
				sdebug('seq_carrinho: '.$cr);
				sdebug('Cashbck Compra: '.$cash);
				sdebug('valor da Compra: '.$valor_compra);
				sdebug('Cash Atual: '.$valortotal_cash);
				sdebug('Cash atual + compra: '.$valor_atual);
				sdebug('pontos rede: '.$cash_rede);
				sdebug('pontos rede - cash: '.$valor_total_pontoecash);*/

	$sql1c = new Query ($bd);
	$txt1c = "UPDATE TREDE_CREDITOREDE SET VALCREDREDE = :valor_atual_subtraido
			 WHERE SEQUENCIACRE = :seqcre";
	$sql1c->addParam(':valor_atual_subtraido', $valor_total_pontoecash);
	$sql1c->addParam(':seqcre', $seq_cred);
	$sql1c->executeSQL($txt1c);

}
elseif ($idref == 'p') {

	$res = explode('p', $reference);


	if ($res[0] == '') {
		$id_plano = $res[1];
		$id_seqpl = $res[2];
	}
	else {
		$id_plano = $res[0];
		$id_seqpl = $res[1];
	}

	$sql1 = new Query ($bd);
	$txt1 = "SELECT CTEMPPLANO FROM TREDE_PLANOS 
				WHERE SEQPLANO = :seqplan";
	$sql1->addParam(':seqplan', $id_plano);
	$sql1->executeQuery($txt1);

	$res_temp_mes = $sql1->result("CTEMPPLANO");

	if ($status == '3') {

		$data_ano_atual = date('Y');
		$data_mes_atual = date('m');
		$data_dia_atual = date('d');

		$hora_atual = date('H:i:s');

		$data_mes_add = $data_mes_atual + $res_temp_mes;

		$data_efeticado = $data_ano_atual . '-' . $data_mes_add . '-' . $data_dia_atual . ' ' . $hora_atual;

		$dtini = date('Y-m-d H:i:s');
		$dtfim = $data_efeticado;
	}
	else {
		$dtini = '(NULL)';
		$dtfim = '(NULL)';
	}

	$sql11 = new Query ($bd);
	$txt11 = "SELECT CTIPOTRPLAN FROM TREDE_PAGAPLANO 
			 WHERE IDPGSEGPLAN = :refe ";
	$sql11->addParam(':refe', $reference);
	$sql11->executeQuery($txt11);

	$res_mensalidade = $sql11->result("CTIPOTRPLAN");

	if ($res_mensalidade == 'm') {
		$data = date('Y-m-d');
		$cond1 = "DDTINIPPLAN = '" . date('Y-m-d') . "', ";
		$cond2 = "DDTFIMPPLAN = '" . date('Y-m-d', strtotime("+30 days", strtotime($data))) . "', ";
		$status_plan = 'a';
	}
	else {

		$cond1 = "";
		$cond2 = "";

		$data = date('Y-m-d');
		$cond1 = "DDTINIPPLAN = '" . date('Y-m-d') . "', ";
		$cond2 = "DDTFIMPPLAN = '" . date('Y-m-d', strtotime("+30 days", strtotime($data))) . "', ";

		$status_plan = 'a';
	}


	$sql = new Query ($bd);
	$txt = "UPDATE TREDE_PAGAPLANO SET 
							CSITPAGPLAN = :pagm, 
							CTIPOPGPLAN = :tppag, 
                           " . $cond1 . "
                           " . $cond2 . "
							NIDCODIPLAN = :code,
							CSITUAPPLAN = :sta
			 WHERE IDPGSEGPLAN = :refe ";
	$sql->addParam(':sta', $status_plan);
	$sql->addParam(':tppag', $tipopagmto);
	$sql->addParam(':pagm', $status);
	$sql->addParam(':refe', $reference);
	$sql->addParam(':code', $code);
	$sql->executeSQL($txt);


	$sqla = new Query ($bd);
	$txta = "SELECT NIDUPAGPLAN,ADESAOPLANO,MENSAPLANO,CTIPOTRPLAN FROM TREDE_PAGAPLANO
			WHERE IDPGSEGPLAN = :refe";
	$sqla->addParam(':refe', $reference);
	$sqla->executeQuery($txta);

	$idusu = $sqla->result("NIDUPAGPLAN");
	$tipo_trans = $sqla->result("CTIPOTRPLAN");

	if ($tipo_trans == 'a') {
		$valor_total = $sqla->result("ADESAOPLANO");
	}
	elseif ($tipo_trans == 'm') {
		$valor_total = $sqla->result("MENSAPLANO");
	}


	/************* BONIFICAÇÃOO **************/
	/************* BONIFICAÇÃOO **************/
	/************* BONIFICAÇÃOO **************/

	//	sdebug('id usuario comprador: '.$idusu);

	$sqln1 = new Query($bd);
	$txtn1 = "SELECT NNUMEFILI,NNUMENIVE,NPORCNIVE FROM TREDE_NIVEL WHERE NIDUSNIVE = :sequ 
            AND NNUMEFILI NOT IN(SELECT REDE_SEQUSUA FROM TREDE_USUADMIN WHERE REDE_ADMINUS = 's')";
	$sqln1->addParam(':sequ', $idusu);
	$sqln1->executeQuery($txtn1);

	while(!$sqln1->eof()){
		$seq_patrocinador1 = $sqln1->result("NNUMEFILI");
		$nivel_comprador_1 = 1;
		$porcetagem_nivel1 = $sqln1->result("NPORCNIVE");

		$sqln2 = new Query($bd);
		$txtn2 = "SELECT NNUMEFILI,NNUMENIVE,NPORCNIVE FROM TREDE_NIVEL WHERE NIDUSNIVE = :sequ
							AND NNUMEFILI NOT IN(SELECT REDE_SEQUSUA FROM trede_usuadmin WHERE REDE_ADMINUS = 's')";
		$sqln2->addParam(':sequ', $seq_patrocinador1);
		$sqln2->executeQuery($txtn2);

		while(!$sqln2->eof()){
			$seq_patrocinador2 = $sqln2->result("NNUMEFILI");
			$nivel_comprador_2 = 2;
			$porcetagem_nivel2 = $sqln2->result("NPORCNIVE");

			$sqln3 = new Query($bd);
			$txtn3 = "SELECT NNUMEFILI,NNUMENIVE,NPORCNIVE FROM TREDE_NIVEL WHERE NIDUSNIVE = :sequ
							  AND NNUMEFILI NOT IN(SELECT REDE_SEQUSUA FROM trede_usuadmin WHERE REDE_ADMINUS = 's')";
			$sqln3->addParam(':sequ', $seq_patrocinador2);
			$sqln3->executeQuery($txtn3);

			while(!$sqln3->eof()){
				$seq_patrocinador3 = $sqln3->result("NNUMEFILI");
				$nivel_comprador_3 = 3;
				$porcetagem_nivel3 = $sqln3->result("NPORCNIVE");

				$sqln4 = new Query($bd);
				$txtn4 = "SELECT NNUMEFILI,NNUMENIVE,NPORCNIVE FROM TREDE_NIVEL WHERE NIDUSNIVE = :sequ
  								AND NNUMEFILI NOT IN(SELECT REDE_SEQUSUA FROM trede_usuadmin WHERE REDE_ADMINUS = 's')";
				$sqln4->addParam(':sequ', $seq_patrocinador3);
				$sqln4->executeQuery($txtn4);

				while(!$sqln4->eof()){
					$seq_patrocinador4 = $sqln4->result("NNUMEFILI");
					$nivel_comprador_4 = 4;
					$porcetagem_nivel4 = $sqln4->result("NPORCNIVE");

					$nnumeidplano4 = $func->RetornaIDPlanoPatrocinador($seq_patrocinador4);

					if ($tipo_trans == 'a') {
						$porc_nivel4 = $func->RetornaPorcentagemnivel($nivel_comprador_4);
					}
					elseif ($tipo_trans == 'm') {
						$porc_nivel4 = $func->RetornaPorcentagemNivelAtivos($nivel_comprador_4);
					}

					$valor_total_pat4 = $valor_total * $porc_nivel4 / 100;
					$valor_do_limite4 = $func->RetornaValorUnivelUsuario($nnumeidplano4);

					if ($valor_do_limite4 == 0) {

					} else {

						$sql644 = new Query ($bd);
						$txt644 = "SELECT VALORTOTAL FROM TREDE_ADESAO_MENSA_USU 
            WHERE NIDUPAGPLAN = :idusuas";
						$sql644->addParam('idusuas', $seq_patrocinador4);
						$sql644->executeQuery($txt644);

						$res_valorT4 = floatval($sql644->result("VALORTOTAL"));
						$valor_ade_mensal4 = floatval($res_valorT4 + $valor_total_pat4);

						if ($valor_ade_mensal4 > $valor_do_limite4) {

							$sql6n4 = new Query ($bd);
							$txt6n4 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '" . $valor_do_limite4 . "' WHERE NIDUPAGPLAN = :idusuas1";
							$sql6n4->addParam(':idusuas1', $seq_patrocinador4);
							$sql6n4->executeSQL($txt6n4);
						}
						else {

							$sql7n4 = new Query ($bd);
							$txt7n4 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '" . $valor_ade_mensal4 . "' WHERE NIDUPAGPLAN = :idusuas1";
							$sql7n4->addParam(':idusuas1', $seq_patrocinador4);
							$sql7n4->executeSQL($txt7n4);

							$usuario_vem_conus4 = $func->RetornaIDUsuaPagaPlano($reference);

							$sql2n44 = new Query ($bd);
							$txt2n44 = "INSERT INTO TREDE_EXTRATO_USUA (NNUMEUSUA,DEBITO,CREDITO,DTRAEXTRA,NPATEXTRA,CTIPEXTRA,CTPOEXTRA,NNUMEUSUA1)
														VALUES
									('".$seq_patrocinador3."',0,'".$valor_total_pat4."','".date('Y-m-d H:i:s')."',
									'".$seq_patrocinador4."','".$tipo_trans."','C','".$usuario_vem_conus4."')";
							$sql2n44->executeSQL($txt2n44);
						}
					}

					/*					sdebug('seq Patrocinador: '.$seq_patrocinador4.
											' - Nivel Patrocinador: '.$nivel_comprador_4.
											' - Plano: '.$nnumeidplano4.
											' - porcentagem que PAT recebe:'.$porc_nivel4.
											' - valor que recebe: '.$valor_total_pat4.
											' - valor limite do plano: '.$valor_do_limite4);*/


					$sqln4->next();
				}

				$nnumeidplano3 = $func->RetornaIDPlanoPatrocinador($seq_patrocinador3);

				if ($tipo_trans == 'a') {
					$porc_nivel3 = $func->RetornaPorcentagemnivel($nivel_comprador_3);
				}
				elseif ($tipo_trans == 'm') {
					$porc_nivel3 = $func->RetornaPorcentagemNivelAtivos($nivel_comprador_3);
				}

				$valor_total_pat3 = $valor_total * $porc_nivel3 / 100;
				$valor_do_limite3 = $func->RetornaValorUnivelUsuario($nnumeidplano3);

				if ($valor_do_limite3 == 0) {

				} else {

					$sql633 = new Query ($bd);
					$txt633 = "SELECT VALORTOTAL FROM TREDE_ADESAO_MENSA_USU 
            WHERE NIDUPAGPLAN = :idusuas";
					$sql633->addParam('idusuas', $seq_patrocinador3);
					$sql633->executeQuery($txt633);

					$res_valorT3 = floatval($sql633->result("VALORTOTAL"));
					$valor_ade_mensal3 = floatval($res_valorT3 + $valor_total_pat3);

					if ($valor_ade_mensal3 > $valor_do_limite3) {

						$sql6n3 = new Query ($bd);
						$txt6n3 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '" . $valor_do_limite3 . "' WHERE NIDUPAGPLAN = :idusuas1";
						$sql6n3->addParam(':idusuas1', $seq_patrocinador3);
						$sql6n3->executeSQL($txt6n3);
					}
					else {

						$sql7n3 = new Query ($bd);
						$txt7n3 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '" . $valor_ade_mensal3 . "' WHERE NIDUPAGPLAN = :idusuas1";
						$sql7n3->addParam(':idusuas1', $seq_patrocinador3);
						$sql7n3->executeSQL($txt7n3);

						$usuario_vem_conus3 = $func->RetornaIDUsuaPagaPlano($reference);

						$sql2n33 = new Query ($bd);
						$txt2n33 = "INSERT INTO TREDE_EXTRATO_USUA (NNUMEUSUA,DEBITO,CREDITO,DTRAEXTRA,NPATEXTRA,CTIPEXTRA,CTPOEXTRA,NNUMEUSUA1)
														VALUES
						('".$seq_patrocinador2."',0,'".$valor_total_pat3."','".date('Y-m-d H:i:s')."',
						'".$seq_patrocinador3."','".$tipo_trans."','C','".$usuario_vem_conus3."')";
						$sql2n33->executeSQL($txt2n33);
					}
				}


				/*				sdebug('seq Patrocinador: '.$seq_patrocinador3.
									' - Nivel Patrocinador: '.$nivel_comprador_3.
									' - Plano: '.$nnumeidplano3.
									' - porcentagem que PAT recebe:'.$porc_nivel3.
									' - valor que recebe: '.$valor_total_pat3.
									' - valor limite do plano: '.$valor_do_limite3);*/

				$sqln3->next();
			}

			$nnumeidplano2 = $func->RetornaIDPlanoPatrocinador($seq_patrocinador2);

			if ($tipo_trans == 'a') {
				$porc_nivel2 = $func->RetornaPorcentagemnivel($nivel_comprador_2);
			}
			elseif ($tipo_trans == 'm') {
				$porc_nivel2 = $func->RetornaPorcentagemNivelAtivos($nivel_comprador_2);
			}

			$valor_total_pat2 = $valor_total * $porc_nivel2 / 100;
			$valor_do_limite2 = $func->RetornaValorUnivelUsuario($nnumeidplano2);

			if ($valor_do_limite2 == 0) {

			} else {

				$sql622 = new Query ($bd);
				$txt622 = "SELECT VALORTOTAL FROM TREDE_ADESAO_MENSA_USU 
            WHERE NIDUPAGPLAN = :idusuas";
				$sql622->addParam('idusuas', $seq_patrocinador2);
				$sql622->executeQuery($txt622);

				$res_valorT2 = floatval($sql622->result("VALORTOTAL"));
				$valor_ade_mensal2 = floatval($res_valorT2 + $valor_total_pat2);

				if ($valor_ade_mensal2 > $valor_do_limite2) {

					$sql6n2 = new Query ($bd);
					$txt6n2 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '" . $valor_do_limite2 . "'
            WHERE NIDUPAGPLAN = :idusuas1";
					$sql6n2->addParam(':idusuas1', $seq_patrocinador2);
					$sql6n2->executeSQL($txt6n2);
				}
				else {

					$sql7n2 = new Query ($bd);
					$txt7n2 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '" . $valor_ade_mensal2 . "'
            WHERE NIDUPAGPLAN = :idusuas1";
					$sql7n2->addParam(':idusuas1', $seq_patrocinador2);
					$sql7n2->executeSQL($txt7n2);

					$usuario_vem_conus2 = $func->RetornaIDUsuaPagaPlano($reference);

					$sql2n22 = new Query ($bd);
					$txt2n22 = "INSERT INTO TREDE_EXTRATO_USUA (NNUMEUSUA,DEBITO,CREDITO,DTRAEXTRA,NPATEXTRA,CTIPEXTRA,CTPOEXTRA,NNUMEUSUA1)
														VALUES
						('".$seq_patrocinador1."',0,'".$valor_total_pat2."','".date('Y-m-d H:i:s')."',
						'".$seq_patrocinador2."','".$tipo_trans."','C','".$usuario_vem_conus2."')";
					$sql2n22->executeSQL($txt2n22);
				}
			}

			/*			sdebug('seq Patrocinador: '.$seq_patrocinador2.
							' - Nivel Patrocinador: '.$nivel_comprador_2.
							' - Plano: '.$nnumeidplano2.
							' - porcentagem que PAT recebe:'.$porc_nivel2.
							' - valor que recebe: '.$valor_total_pat2.
							' - valor limite do plano: '.$valor_do_limite2);*/

			$sqln2->next();
		}

		$nnumeidplano1 = $func->RetornaIDPlanoPatrocinador($seq_patrocinador1);

		if ($tipo_trans == 'a') {
			$porc_nivel1 = $func->RetornaPorcentagemnivel($nivel_comprador_1);
		}
		elseif ($tipo_trans == 'm') {
			$porc_nivel1 = $func->RetornaPorcentagemNivelAtivos($nivel_comprador_1);
		}

		$valor_total_pat1 = $valor_total * $porc_nivel1 / 100;
		$valor_do_limite1 = $func->RetornaValorUnivelUsuario($nnumeidplano1);

		if ($valor_do_limite1 == 0) {

		} else {

			$sql6 = new Query ($bd);
			$txt6 = "SELECT VALORTOTAL FROM TREDE_ADESAO_MENSA_USU 
            WHERE NIDUPAGPLAN = :idusuas";
			$sql6->addParam('idusuas', $seq_patrocinador1);
			$sql6->executeQuery($txt6);

			$res_valorT = floatval($sql6->result("VALORTOTAL"));
			$valor_ade_mensal1 = floatval($res_valorT + $valor_total_pat1);

			if ($valor_ade_mensal1 > $valor_do_limite1) {

				$sql62 = new Query ($bd);
				$txt62 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '" . $valor_do_limite1 . "'
            WHERE NIDUPAGPLAN = :idusuas1";
				$sql62->addParam(':idusuas1', $seq_patrocinador1);
				$sql62->executeSQL($txt62);
			}
			else {

				$sql61 = new Query ($bd);
				$txt61 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '" . $valor_ade_mensal1 . "'
            WHERE NIDUPAGPLAN = :idusuas1";
				$sql61->addParam(':idusuas1', $seq_patrocinador1);
				$sql61->executeSQL($txt61);

				$usuario_vem_conus1 = $func->RetornaIDUsuaPagaPlano($reference);

				$sql2n11 = new Query ($bd);
				$txt2n11 = "INSERT INTO TREDE_EXTRATO_USUA (NNUMEUSUA,DEBITO,CREDITO,DTRAEXTRA,NPATEXTRA,CTIPEXTRA,CTPOEXTRA,NNUMEUSUA1)
														VALUES
						('".$idusu."',0,'".$valor_total_pat1."','".date('Y-m-d H:i:s')."','".$seq_patrocinador1."',
						'".$tipo_trans."','C','".$usuario_vem_conus1."')";
				$sql2n11->executeSQL($txt2n11);
			}

		}

		/*		sdebug('seq Patrocinador: '.$seq_patrocinador1.
					' - Nivel Patrocinador: '.$nivel_comprador_1.
					' - Plano: '.$nnumeidplano1.
					' - porcentagem que PAT recebe:'.$porc_nivel1.
					' - valor que recebe: '.$valor_total_pat1.
					' - valor limite do plano: '.$valor_do_limite1);*/

		$sqln1->next();
	}


	/************* BONIFICAÇÃOO **************/
	/************* BONIFICAÇÃOO **************/
	/************* BONIFICAÇÃOO **************/

}elseif ($idref == 'r'){


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

	$sql21 = new Query ($bd);
	$txt21 = "UPDATE TREDE_PAGAPACOTE SET SITPAGPAC = '3',
                            							CSITUAPAC = 'a',
                            							DDATAPPAC = '".date('y-m-d H:i:s')."'

		            WHERE NNUMEPPAC = :idcre";
	$sql21->addParam(':idcre',$nnumepacote);
	$sql21->executeSQL($txt21);


}

?>