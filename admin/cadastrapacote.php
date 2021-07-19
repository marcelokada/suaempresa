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
$tpl->addFile("CONTEUDO", "cadastrapacote.html");
$tpl->ID_SESSAO = $_GET['idSessao'];
//$tpl->ID_ADMIN 	= $_SESSION['admin'];

$nivelusua = $func->RetornaPermissoes_Admin($id_admin);

if($nivelusua == 'C'){
	$tpl->DISABLE = "style='display:none;'";
}else if($nivelusua == 'CB'){
	$tpl->DISABLE = "style='display:none;'";
}

$sql1 = new Query($bd);
$txt1 = "SELECT NNUMEPAC,
       					CNOMEPAC,
							  CDESCPAC,
							  NPONTPAC,
							  CSITUPAC,
							  NVALOPAC
			FROM TREDE_PACOTES_REDE
			ORDER BY CNOMEPAC";
$sql1->executeQuery($txt1);

while (!$sql1->eof()) {
	$tpl->SEQ = $sql1->result("NNUMEPAC");
	$nnumeplano = $sql1->result("NNUMEPAC");
	$tpl->NOME = ucwords($sql1->result("CNOMEPAC"));
	$tpl->DESC = ucwords($sql1->result("CDESCPAC"));
	$tpl->PONTOS = number_format($sql1->result("NPONTPAC"), 2, ',', '.');
	$tpl->VALOR = number_format($sql1->result("NVALOPAC"), 2, ',', '.');

	$situacao = $sql1->result("CSITUPAC");

	if($situacao == 'c'){
		$tpl->SITUA = "Cancelado";
		$tpl->TIPO = "c";

	}else{
		$tpl->SITUA = "Ativo";
		$tpl->TIPO = "a";
	}


	$sql1->next();
	$tpl->block("PLANOS");
}
/////////////////ALIMENTOS E BEBIDAS/////////////////////

if (isset($_POST['inserir'])) {

	$nome = $_POST['nome'];
	$desc = $_POST['desc'];

	$pontos = $_POST['pontuacao'];
	$pontos = str_replace('.', '', $pontos);
	$pontos = str_replace(',', '.', $pontos);

	$valor = $_POST['valor'];
	$valor = str_replace('.', '', $valor);
	$valor = str_replace(',', '.', $valor);

	$sql2 = new Query($bd);
	$txt2 = "INSERT INTO TREDE_PACOTES_REDE (CNOMEPAC,
										CDESCPAC,
										NPONTPAC,
										CSITUPAC,
										NVALOPAC)
                    VALUES (:nome,:descr,:pontos,'a',:valort)";
	$sql2->addParam(':nome', $nome);
	$sql2->addParam(':descr', $desc);
	$sql2->addParam(':pontos', $pontos);
	$sql2->addParam(':valort', $valor);
	$sql2->executeSQL($txt2);

	$sql22 =  new Query();
	$txt22 = "SELECT LAST_INSERT_ID(SEQPLANO) SEQPLANO FROM TREDE_PLANOS
						ORDER BY SEQPLANO DESC
						LIMIT 1";
	$sql22->executeQuery($txt22);

	//$util->redireciona("location: planos.php?idSessao=" . $id_sessao);
	echo "<script>window.location.href=window.location.href;</script>";
}

$tpl->show();
$bd->close();
?>