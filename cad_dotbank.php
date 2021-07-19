<?php
require_once("comum/autoload.php");

$bd = new Database();

$idusua = $_POST['idusua'];

$senha2 = md5($_POST['senha2']);

$nome = $_POST['nome'];
$email = $_POST['mail'];
$cpf = $_POST['cpf'];
$rg = $_POST['rg'];
$phone = $_POST['phone'];
$ende = $_POST['ende'];
$nende = $_POST['nende'];
$cidade = $_POST['cidade'];
$estado = $_POST['estado'];
$cep = $_POST['cep'];
$comp = $_POST['comp'];
$bairro = $_POST['bairro'];
$data = $_POST['dnasc'];

$dnasc = substr($data, 6, 4) . "-" . substr($data, 3, 2) . "-" . substr($data, 0, 2);

$sql2 = new Query ($bd);
$txt2 = "UPDATE TREDE_DOTBANK_USUA SET
						NCPF_DOT = '" . $cpf . "',
						NRG__DOT = '" . $rg . "',
						SENHADOT = '" . $senha2 . "',
						ENDERDOT = '" . $ende . "',
						NENDEDOT = '" . $nende . "',
						CIDADDOT = '" . $cidade . "',
						ESTADDOT = '" . $estado . "',
						CCEP_DOT = '" . $cep . "',
						COMPLDOT = '" . $comp . "',
						BAIRRDOT = '" . $bairro . "',
						DNASCDOT = '".$dnasc."',
						CSITUDOT = 'A'
				WHERE REDE_SEQUSUA = :seq";
$sql2->addParam(':seq', $idusua);
$sql2->executeSQL($txt2);

//try{
//	echo "Sucesso";
//}catch (Exception $e){
//	echo $e;
//}


$bd->close();
?>





