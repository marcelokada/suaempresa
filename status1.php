<?php
require_once("comum/autoload.php");
error_reporting(0);

$bd = new Database();

require_once("comum/layout.php");
$tpl->addFile("CONTEUDO", "status1.html");

$reference = 'p9p299';

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

$tpl->USUARIO = $idusu;

///////////////////////////////////////////////////////////

$sql4 = new Query($bd);
$txt4 = "SELECT NNUMEFILI,NPORCNIVE FROM TREDE_NIVEL 
					WHERE NIDUSNIVE = :sequsu
		        ORDER BY 1";
$sql4->addParam(':sequsu', $idusu);
$sql4->executeQuery($txt4);

while (!$sql4->eof()) {
	$id4 = $sql4->result("NNUMEFILI");
	//$tpl->ID4 = $sql4->result("NNUMEFILI");
	$nome4 = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $id4)));

	$tpl->block("NIVEIS4");
	$sql4->next();
}


$sql3 = new Query($bd);
$txt3 = "SELECT NNUMEFILI,NNUMENIVE FROM TREDE_NIVEL
		WHERE NIDUSNIVE  = :sequsu
		ORDER BY 1";
$sql3->addParam(':sequsu', $id4);
$sql3->executeQuery($txt3);

while (!$sql3->eof()) {
	$id3 = $sql3->result("NNUMEFILI");
	//$tpl->ID3 = $sql3->result("NNUMEFILI");
	$nome3 = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $id3)));

	$tpl->block("NIVEIS3");
	$sql3->next();
}


$sql2 = new Query($bd);
$txt2 = "SELECT NNUMEFILI,NNUMENIVE FROM TREDE_NIVEL
		WHERE NIDUSNIVE  = :sequsu
		ORDER BY 1";
$sql2->addParam(':sequsu', $id3);
$sql2->executeQuery($txt2);


while (!$sql2->eof()) {
	$id2 = $sql2->result("NNUMEFILI");
	//$tpl->ID2 = $sql2->result("NNUMEFILI");
	$nome2 = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $id2)));


	$tpl->block("NIVEIS2");
	$sql2->next();
}

$sql1 = new Query($bd);
$txt1 = "SELECT NNUMEFILI,NNUMENIVE FROM TREDE_NIVEL
		WHERE NIDUSNIVE  = :sequsu
		ORDER BY 1";
$sql1->addParam(':sequsu', $id2);
$sql1->executeQuery($txt1);


while (!$sql1->eof()) {
	$id1 = $sql1->result("NNUMEFILI");
	//$tpl->ID1 = $sql1->result("NNUMEFILI");
	$nome = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $id1)));

	$tpl->block("NIVEIS1");
	$sql1->next();
}

$sql1a = new Query($bd);
$txt1a = "SELECT NNUMEFILI,NPORCNIVE FROM TREDE_NIVEL 
						WHERE NIDUSNIVE = '" . $id2 . "'
						
						UNION ALL
						
						SELECT NNUMEFILI,NPORCNIVE FROM TREDE_NIVEL 
						WHERE NIDUSNIVE =  '" . $id3 . "'
						
						UNION ALL  
						            
						SELECT NNUMEFILI,NPORCNIVE FROM TREDE_NIVEL 
						WHERE NIDUSNIVE =  '" . $id4 . "'
						UNION ALL  
						      
						SELECT NNUMEFILI,NPORCNIVE FROM TREDE_NIVEL 
						WHERE NIDUSNIVE =  '" . $idusu . "'";
$sql1a->executeQuery($txt1a);

$id1aa[0] = $sql1a->result("NNUMEFILI");

$sql1ab = new Query($bd);
$txt1ab = "SELECT REDE_ADMINUS 
						FROM TREDE_USUADMIN
					WHERE REDE_SEQUSUA = '" . $id1aa[0] . "'";
$sql1ab->executeQuery($txt1ab);

$admin = $sql1ab->result("REDE_ADMINUS");

if ($admin == 's') {
	$i = 0;
}
else {
	$i = 1;
}

while (!$sql1a->eof()) {
	$id1a = $sql1a->result("NNUMEFILI");


	$nome = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $id1a)));
//
//	$tpl->ID = $id1a;
//	$tpl->NOME = $nome;
//	$tpl->NIV = $i;
//	$tpl->VALOR = $valor_total;

	$nnumeidplano = $func->RetornaIDPlanoPatrocinador($id1a);

	$porc_nivel = $func->RetornaPorcentagemnivel($i);
	$valor_total_pat = $valor_total * $porc_nivel / 100;


	//$tpl->PORC = $porc_nivel.'% - id plan: '.$nnumeidplano;

	//$tpl->VALORT = $valor_total_pat;



	//update no usuario com o bonus
	$sql611 = new Query ($bd);
	$txt611 = "SELECT VALOR
						FROM  TREDE_VALOR_UNILEVEL
           WHERE NNUMEPLAN = :nnumeidplano";
	$sql611->addParam(':nnumeidplano', $nnumeidplano);
	$sql611->executeQuery($txt611);

	$valor_do_limite = $sql611->result("VALOR");

	$sql6 = new Query ($bd);
	$txt6 = "SELECT VALORTOTAL FROM TREDE_ADESAO_MENSA_USU 
            WHERE NIDUPAGPLAN = :idusuas";
	$sql6->addParam('idusuas', $id1a);
	$sql6->executeQuery($txt6);

	$res_valorT = $sql6->result("VALORTOTAL");

	$valor_ade_mensal = $res_valorT + $valor_total_pat;
	//$valor_ade_mensal = '750';

	//$tpl->LIMITE = $valor_ade_mensal.' - '.$valor_do_limite;
	//$tpl->LIMITE = $res_valorT;

	//$tpl->VALOUSUA = $res_valorT;


	if ($valor_ade_mensal > $valor_do_limite) {
		//$tpl->VAL = "passou";

		$sql62 = new Query ($bd);
		$txt62 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '" . $valor_do_limite . "'
            WHERE NIDUPAGPLAN = :idusuas1";
		$sql62->addParam(':idusuas1', $id1a);
		$sql62->executeSQL($txt62);
	}
	else {
		//$tpl->VAL = "não passou";

		$sql61 = new Query ($bd);
		$txt61 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '" . $valor_ade_mensal . "'
            WHERE NIDUPAGPLAN = :idusuas1";
		$sql61->addParam(':idusuas1', $id1a);
		$sql61->executeSQL($txt61);
	}
	//update no usuario com o bonus

	$tpl->block("NIVEIS");
	$sql1a->next();
	$i++;
}


$tpl->show();
$bd->close();
?>