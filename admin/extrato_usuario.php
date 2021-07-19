<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();
$func = new Funcao();

$id_sessao = $_SESSION['idSessao'];
$id_confer = $_GET['idSessao'];

$seq = $_SESSION['idUsuario'];

$seg->verificaSession($id_sessao);

require_once("comum/layout.php");
$tpl->addFile("CONTEUDO", "extrato_usuario.html");
$tpl->ID_SESSAO = $_SESSION['idSessao'];
$tpl->IDUSUA = $_SESSION['idUsuario'];
$seq = $_GET['idUsuario'];

//SELECT PARA VERIFICAR O USUARIO
$tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $seq)));
//SELECT PARA VERIFICAR O USUARIO


//CASHBACK USUARIO
$valortotal_cash = $func->RetornaValorCashBackUsuario($bd, $seq);
$tpl->MEUCASH = $formata->formataNumero($valortotal_cash);

$valortotal_bonus = $func->RetornaValorBonusUsuario($seq);
$tpl->MEUBONUS = $formata->formataNumero($valortotal_bonus);

$valortotal_voucher = $func->RetornaValorVoucherUsuario($seq);
$tpl->MEUVOUCHER = $formata->formataNumero($valortotal_voucher);

$tpl->MEUPLANO = $func->assinaturaUsuario($bd, $seq);
$tpl->NOMEPLANO = $func->RetornaNomePlano($func->RetornaUltimoPLanoUsuario($seq));
//CASHBACK USUARIO



$data_incio = mktime(0, 0, 0, date('m'), 1, date('Y'));
$data_fim = mktime(23, 59, 59, date('m'), date("t"), date('Y'));

$sqlT = new Query ($bd);
$txtT = "
SELECT NNUMEXTRA,
    NNUMEUSUA,
    DEBITO,
    CREDITO,
    DTRAEXTRA,
    NPATEXTRA,
    CTIPEXTRA,
    CTPOEXTRA,
		NNUMEUSUA1
FROM
     TREDE_EXTRATO_USUA
WHERE NPATEXTRA = :usua
ORDER BY 4 DESC ";
$sqlT->addParam(':usua', $seq);
$sqlT->executeQuery($txtT);

$valor_total_filtro = 0;
$valortotal_sub = 0;

while (!$sqlT->eof()) {
	$idusuario = $sqlT->result("NNUMEUSUA");
	$idusuario1 = $sqlT->result("NNUMEUSUA1");
	$nnumeextra = $sqlT->result("NNUMEXTRA");
	$tipobonus = $sqlT->result("CTIPEXTRA");
	$datainsercao = $sqlT->result("DTRAEXTRA");
	$horainsercao = SUBSTR($sqlT->result("DTRAEXTRA"),11,10);
	$deb_cred = $sqlT->result("CTPOEXTRA");

	$nome_usua = $func->RetonaNomeUsuarioPorSeq1($bd,$idusuario1);

	$tpl->IDUSUAT = $nnumeextra;

	if ($tipobonus == 'a') {
		$tpl->DESC_BONUS = "Bonificação por Adesão do Plano - " . $nome_usua;
	}
	elseif ($tipobonus == 'm') {
		$tpl->DESC_BONUS = "Bonificação por Ativação Mensal - " . $nome_usua;
	}
	elseif ($tipobonus == 'c') {
		$tpl->DESC_BONUS = "Bonificação por CashBack - " . $nome_usua;
	}elseif ($tipobonus == 's') {
		$tpl->DESC_BONUS = "Solicitação de Saque - " . $nome_usua;
	}


	$tpl->DATAT = $data->formataData1($datainsercao).' - '.$horainsercao;

	if ($deb_cred == 'C') {
		$tpl->TIPOT = "<font color='green'>Credito</font>";
		$valortotal = $sqlT->result("CREDITO");
		$valor_total_filtro += $valortotal;
		$tpl->VALORT = number_format($valortotal, 2, ',', '.');
	}
	elseif ($deb_cred == 'D') {
		$tpl->TIPOT = "<font color='red'>Débito</font>";
		$valortotal = $sqlT->result("DEBITO");
		$hifen = '-';
		$valortotal_sub = $valortotal;
		$tpl->VALORT = $hifen.number_format($valortotal, 2, ',', '.');
	}

	$qtde = $sqlT->count("NNUMEUSUA");


	$tpl->TOTAL_TOTAL = number_format($valor_total_filtro, 2, ',', '.');


	$tpl->block("LISTAR1");
	$sqlT->next();
}




$tpl->show();
$bd->close();
?>