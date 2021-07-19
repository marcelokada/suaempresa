<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
error_reporting(0);

$bd = new Database();

$id_sessao = $_SESSION['idSessao'];
$id_confer = $_GET['idSessao'];

$id_admin    	= $_SESSION['admin'];

$seg->verificaSession($id_sessao);

require_once("comum/layout.php");
$tpl->addFile("CONTEUDO", "planos.html");
$tpl->ID_SESSAO = $_GET['idSessao'];
//$tpl->ID_ADMIN 	= $_SESSION['admin'];

$nivelusua = $func->RetornaPermissoes_Admin($id_admin);

if($nivelusua == 'C'){
	$tpl->DISABLE = "style='display:none;'";
}else if($nivelusua == 'CB'){
	$tpl->DISABLE = "style='display:none;'";
}

$sql1 = new Query($bd);
$txt1 = "SELECT SEQPLANO,  
                CNOMEPLANO,
                CDESCPLANO,
                CTEMPPLANO,
                VVALTPLANO,
                MENSAPLANO,
       					AGREGPLANO,
       					DEPENPLANO,
                CCARNPLANO
			FROM TREDE_PLANOS
			ORDER BY CNOMEPLANO";
$sql1->executeQuery($txt1);

while (!$sql1->eof()) {
	$tpl->SEQPLANO = $sql1->result("SEQPLANO");
	$nnumeplano = $sql1->result("SEQPLANO");
	$tpl->CNOMEPLANO = ucwords($sql1->result("CNOMEPLANO"));
	$tpl->CDESCPLANO = $sql1->result("CDESCPLANO");
	$tpl->CTEMPPLANO = $sql1->result("CTEMPPLANO");
	$tpl->VVALTPLANO = number_format($sql1->result("VVALTPLANO"), 2, ',', '.');
	$tpl->MENSAPLANO = number_format($sql1->result("MENSAPLANO"), 2, ',', '.');
  
  $carne = $sql1->result("CCARNPLANO");
  
  if($carne == 's'){
    $tpl->VCARNE = "SIM";
  }else{
    $tpl->VCARNE = "NÃO";
  }
	
	
	if ($sql1->result("AGREGPLANO") == "") {
		$tpl->AGREGADOS = 99;
	}
	else {
		$tpl->AGREGADOS = $sql1->result("AGREGPLANO");
	}

	if ($sql1->result("DEPENPLANO") == "") {
		$tpl->DEPENDENTES = 99;
	}
	else {
		$tpl->DEPENDENTES = $sql1->result("DEPENPLANO");
	}

	$sql21 =  new Query();
	$txt21 = "SELECT VALOR, NNUMEPLAN FROM TREDE_VALOR_UNILEVEL
					WHERE NNUMEPLAN = :nnumeplan";
	$sql21->addParam(':nnumeplan',$nnumeplano);
	$sql21->executeQuery($txt21);

	$tpl->LIMITE_UNILEVEL = number_format($sql21->result("VALOR"), 2, ',', '.');


	$sql1->next();
	$tpl->block("PLANOS");
}
/////////////////ALIMENTOS E BEBIDAS/////////////////////

if (isset($_POST['inserir'])) {

	$nome = $_POST['nome'];
	$desc = $_POST['desc'];
	$tempo = $_POST['tempo'];

	$adesao = $_POST['adesao'];
	$adesao = str_replace('.', '', $adesao);
	$adesao = str_replace(',', '.', $adesao);

	$mensa = $_POST['mensa'];
	$mensa = str_replace('.', '', $mensa);
	$mensa = str_replace(',', '.', $mensa);

	$unilevel = $_POST['unilevel'];
	$unilevel = str_replace('.', '', $unilevel);
	$unilevel = str_replace(',', '.', $unilevel);

	$dependentes = $_POST['depen'];
	$agregado = $_POST['agreg'];

	$sql2 = new Query($bd);
	$txt2 = "INSERT INTO TREDE_PLANOS (CNOMEPLANO ,CDESCPLANO,CTEMPPLANO,VVALTPLANO,MENSAPLANO,AGREGPLANO,DEPENPLANO,DINCLPLANO)
                    VALUES (:nome,:descr,:tempo,:valort,:mensa,:agregado,:dependentes,'".date('Y-m-d')."')";
	$sql2->addParam(':nome', $nome);
	$sql2->addParam(':descr', $desc);
	$sql2->addParam(':tempo', $tempo);
	$sql2->addParam(':valort', $adesao);
	$sql2->addParam(':mensa', $mensa);
	$sql2->addParam(':agregado', $agregado);
	$sql2->addParam(':dependentes', $dependentes);
	$sql2->executeSQL($txt2);

	$sql22 =  new Query();
	$txt22 = "SELECT LAST_INSERT_ID(SEQPLANO) SEQPLANO FROM TREDE_PLANOS
						ORDER BY SEQPLANO DESC
						LIMIT 1";
	$sql22->executeQuery($txt22);

	$ultimo_id_plan = $sql22->result("SEQPLANO");

	$sql23 = new Query($bd);
	$txt23 = "INSERT INTO TREDE_VALOR_UNILEVEL (NNUMEPLAN,VALOR)
                    VALUES ('".$ultimo_id_plan."','".$unilevel."')";
	$sql23->executeSQL($txt23);


	//$util->redireciona("location: planos.php?idSessao=" . $id_sessao);
	echo "<script>window.location.href=window.location.href;</script>";
}

$tpl->show();
$bd->close();
?>