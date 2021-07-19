<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$id_sessao 	= $_SESSION['idSessao'];
$id_confer 	= $_GET['idSessao'];
$seq 		= $_SESSION['idUsuario'];

if ($id_confer != $id_sessao) {
	require_once("comum/restrito.html");
	session_destroy();
} else {
	require_once("comum/layout_teste.php");
	$tpl->addFile("CONTEUDO", "config_usuario_teste.html");
	$tpl->ID_SESSAO = $_SESSION['idSessao'];

	//SELECT PARA VERIFICAR O USUARIO
	$tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $seq)));
	//SELECT PARA VERIFICAR O USUARIO

	$sql = new Query($bd);
	$txt = "SELECT 	REDE_SEQUSUA,
					REDE_NOMEUSU,
					REDE_CPFUSUA,
					REDE_ADMINUS,
					REDE_SENHAUS,
					REDE_TIPOUSU,
					REDE_USUBLOC,
					REDE_EMAILUS,
					REDE_SERECUS,
					REDE_DRECUSU,
					REDE_HRECUSU,
					REDE_DNASCUS,
					REDE_CELULAR
					FROM TREDE_USUADMIN
					WHERE REDE_SEQUSUA = :seq";
	$sql->addParam(':seq', $seq);
	$sql->executeQuery($txt);

	$tpl->SEQ 	= $sql->result("REDE_SEQUSUA");
	$tpl->EMAI 	= $sql->result("REDE_EMAILUS");
	$tpl->CCPF 	= $sql->result("REDE_CPFUSUA");
	$tpl->CELU 	= $sql->result("REDE_CELULAR");
	$tpl->NASC 	= $data->formataData1($sql->result("REDE_DNASCUS"));


	if (isset($_POST['alterar'])) {
		$senha = md5($seg->antiInjection($_POST['senha_atual']));

		$nome 		= $seg->antiInjection($_POST['nome']);
		$email 		= $seg->antiInjection($_POST['mail']);
		$cpf 		= $seg->antiInjection($_POST['cpf']);
		$phone 		= $seg->antiInjection($_POST['phone']);
		$dtnasc 	= $data->dataInvertida($seg->antiInjection($_POST['dtnasc']));
		$cc 		= $_POST['se'];

		$tpl->SEQ 	= $seg->antiInjection($_POST['nome']);
		$tpl->EMAI 	= $seg->antiInjection($_POST['mail']);
		$tpl->CCPF 	= $seg->antiInjection($_POST['cpf']);
		$tpl->CELU 	= $seg->antiInjection($_POST['phone']);
		$tpl->NASC 	= $seg->antiInjection($_POST['dtnasc']);


		$sql1 = new Query($bd);
		$txt1 = "SELECT REDE_SENHAUS					
					FROM TREDE_USUADMIN
					WHERE REDE_SEQUSUA = :seq";
		$sql1->addParam(':seq', $seq);
		$sql1->executeQuery($txt1);

		$senha_res = $sql1->result("REDE_SENHAUS");

		if ($senha != $senha_res) {
			$tpl->MSG = '<center><font color="RED">Sua senha não confere.</font></center>';
			$tpl->block("ERRO");
		} else {

			if ($cc == 'on') {
				$nova_senha = md5($seg->antiInjection($_POST['nova_senha1']));

				$sql3 = new Query($bd);
				$txt3 = "UPDATE TREDE_USUADMIN SET
						REDE_SENHAUS = '" . $nova_senha . "'
				WHERE REDE_SEQUSUA = :seq";
				$sql3->addParam(':seq', $seq);
				$sql3->executeSQL($txt3);
			}


			$sql2 = new Query($bd);
			$txt2 = "UPDATE TREDE_USUADMIN SET
						REDE_NOMEUSU = :nome,
						REDE_CPFUSUA = :cpf,
						REDE_EMAILUS = :email,
						REDE_DNASCUS = :dnasc,
						REDE_CELULAR = :celu
				WHERE REDE_SEQUSUA = :seq";
			$sql2->addParam(':seq', $seq);
			$sql2->addParam(':nome', $nome);
			$sql2->addParam(':email', $email);
			$sql2->addParam(':cpf', $cpf);
			$sql2->addParam(':dnasc', $dtnasc);
			$sql2->addParam(':celu', $phone);
			$sql2->executeSQL($txt2);

			$tpl->MSG = '<center><font color="green">Alterações realizadas com sucesso.</font></center>';
			$tpl->block("SUCESSO");
		}
	}
}
$tpl->show();
$bd->close();
?>