<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$id_sessao = $_SESSION['idSessao'];
$id_confer = $_GET['idSessao'];

///$id_admin    	= $_SESSION['admin'];

$seg->verificaSession($id_sessao);

require_once("comum/layout.php");
$tpl->addFile("CONTEUDO", "editapacote.html");
$tpl->ID_SESSAO = $_GET['idSessao'];
//$tpl->ID_ADMIN 	= $_SESSION['admin'];

$idplan = $_GET['idplano'];
$tpl->IDPLANO = $_GET['idplano'];

$sql1 = new Query($bd);
$txt1 = "SELECT NNUMEPAC,
								CNOMEPAC,
								CDESCPAC,
								NPONTPAC,
								CSITUPAC,
								NVALOPAC
			FROM TREDE_PACOTES_REDE
			WHERE NNUMEPAC = :idplan";
$sql1->addParam(':idplan', $idplan);
$sql1->executeQuery($txt1);


while (!$sql1->eof()) {
	$tpl->SEQPLANO = $sql1->result("NNUMEPAC");
	$nnumeplano = $sql1->result("NNUMEPAC");
	$tpl->CNOMEPLANO = ucwords(utf8_encode($sql1->result("CNOMEPAC")));
	$tpl->CDESCPLANO = utf8_encode($sql1->result("CDESCPAC"));
	$tpl->VVALTPLANO = number_format($sql1->result("NPONTPAC"), 2, ',', '.');
	$tpl->MENSAPLANO = number_format($sql1->result("NVALOPAC"), 2, ',', '.');

	$sql1->next();
}
/////////////////ALIMENTOS E BEBIDAS/////////////////////

if (isset($_POST['alterar'])) {

	$nome = utf8_decode($_POST['nome']);
	$desc = utf8_decode($_POST['desc']);

	$adesao = $_POST['pontuacao'];
	$adesao = str_replace('.', '', $adesao);
	$adesao = str_replace(',', '.', $adesao);

	$mensa = $_POST['valor'];
	$mensa = str_replace('.', '', $mensa);
	$mensa = str_replace(',', '.', $mensa);

	$sql2 = new Query($bd);
	$txt2 = "UPDATE TREDE_PACOTES_REDE SET CNOMEPAC  = :nome,
                                         CDESCPAC = :desc,
                                         NPONTPAC = :valort,
                                         NVALOPAC = :mensa
				WHERE NNUMEPAC = :id";
	$sql2->addParam(':nome', $nome);
	$sql2->addParam(':desc', $desc);
	$sql2->addParam(':valort', $adesao);
	$sql2->addParam(':mensa', $mensa);
	$sql2->addParam(':id', $idplan);
	$sql2->executeSQL($txt2);

	echo "<script>alert('Alteração Realizado com Sucesso!!'); window.location.href = window.location.href</script>";
	$tpl->MSG = "Alteração Realizado com Sucesso!";
	$tpl->block("SUCESSO");
}


$tpl->show();
$bd->close();
?><?php
