<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$id_sessao = $_SESSION['idSessao'];
$id_confer = $_GET['idSessao'];
$seq = $_SESSION['idUsuario'];

$seg->verificaSession($id_sessao);

$pagamento = $_GET['pag'];

require_once("comum/layout.php");
$tpl->addFile("CONTEUDO", "pagamento_transf.html");
$tpl->ID_SESSAO = $_SESSION['idSessao'];
$tpl->IDUSUA = $_SESSION['idUsuario'];

$idcart = $_GET['idcart'];
$tpl->IDCART = $_GET['idcart'];
$tpl->ID_CRED = $_GET['idLoja'];
$id_loja = $_GET['idLoja'];

$tpl->ID_USUA_CR = $_SESSION['idUsuario'];

//SELECT PARA VERIFICAR O USUARIO
$tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $seq)));
//SELECT PARA VERIFICAR O USUARIO

//CASHBACK USUARIO
$valortotal_cash = $func->RetornaValorCashBackUsuario($bd, $seq);
$tpl->MEUCASH = $formata->formataNumero($valortotal_cash);
//CASHBACK USUARIO

// select nome da rede //
$sql = new Query ($bd);
$txt = "SELECT SEQUENCIACRE, VNOMECREDCRE, NNUMECATECRE
			  FROM TREDE_CREDENCIADOS
			 WHERE SEQUENCIACRE = :seq";
$sql->addparam(':seq', $id_loja);
$sql->executeQuery($txt);

$tpl->NOMELOJA = ucwords(utf8_encode($sql->result("VNOMECREDCRE")));
$tpl->CATEGORIALOJA = ucwords(utf8_encode($func->RetornaNomeCategoria($bd, $sql->result("NNUMECATECRE"))));
$id = $sql->result("SEQUENCIACRE");
// select nome da rede //


$sql3 = new Query ($bd);
$txt3 = "SELECT  NSEQUECARR,
					 NSEQUPRODU,
					 SEQUENCIACRE,
					 VIDCARCARR,
					 NVALORCARR,
					 NQUATICARR,
					 VNOMEPCARR,
					 NVVALOCARR
			   FROM TREDE_CARRINHO
		      WHERE SEQUENCIACRE = :idloja 
				AND VIDCARCARR = :idcart
		ORDER BY NSEQUECARR DESC";
$sql3->addParam(':idloja', $id_loja);
$sql3->addParam(':idcart', $idcart);
$sql3->executeQuery($txt3);

$quantidade = $sql3->count();

$valortotalcash = '0';

while (!$sql3->eof()) {

	$idcarrinho = $sql3->result("VIDCARCARR");

	$sql3A = new Query ($bd);
	$txt3A = "SELECT IDPAGSEGCOMPRA FROM TREDE_PAGACOMPRA
					WHERE VIDCARCARR = :idcart
					AND SEQUENCIACRE = :idloja";
	$sql3A->addParam(':idloja', $id_loja);
	$sql3A->addParam(':idcart', $idcarrinho);
	$sql3A->executeQuery($txt3A);

	sdebug($id_loja);
	sdebug($idcarrinho);

	$tpl->IDPEDIDO = $sql3A->result("IDPAGSEGCOMPRA");

	$tpl->ID = $sql3->result("NSEQUPRODU");
	$tpl->DESC = ucwords(utf8_encode($sql3->result("VNOMEPCARR")));
	$qtdes = $sql3->result("NQUATICARR");

	$tpl->QTDE = $sql3->result("NQUATICARR");

	$valor_unit = $sql3->result("NVALORCARR");
	$tpl->VALOR = $formata->formataNumero($sql3->result("NVALORCARR"));

	$seqprod = $sql3->result("NSEQUPRODU");
	$seqloja = $sql3->result("SEQUENCIACRE");


	$sql32 = new Query ($bd);
	$txt32 = "SELECT VCASHPRODU,NSEQUPRODU,CIMAGPRODU
			   FROM TREDE_PRODUTOS
		      WHERE SEQUENCIACRE = :seqloja
				AND NSEQUPRODU = :seqprod";
	$sql32->addParam(':seqloja', $seqloja);
	$sql32->addParam(':seqprod', $seqprod);
	$sql32->executeQuery($txt32);

	$cashback_lista = $sql32->result("VCASHPRODU");
	$valor_totalcar = $sql3->result("NVVALOCARR");


	$valor_total = $valor_unit * $qtdes;
	$tpl->VALOR_TOTAL_PRODU = $formata->formataNumero($valor_total);

	$idl = $sql32->result("NSEQUPRODU");

	$imagem1 = $sql32->result("CIMAGPRODU");

	if (($imagem1 == null) or (substr($imagem1, 0, 7) != 'uploads')) {
		$tpl->IMAGEM = 'comum/img/Sem-imagem.jpg';
	}
	else {
		$tpl->IMAGEM = 'rede/' . $imagem1;
	}
	// $tpl->IMAGEM = $func->RetornaImagemProdutos($bd, $idl);

	$tpl->block("CARRINHO");
	$sql3->next();

}

$sql31 = new Query ($bd);
$txt31 = "SELECT SUM(NVVALOCARR) SOMA
			   FROM TREDE_CARRINHO
		      WHERE SEQUENCIACRE = :idloja 
				AND VIDCARCARR = :idcart";
$sql31->addParam(':idloja', $id_loja);
$sql31->addParam(':idcart', $idcart);
$sql31->executeQuery($txt31);

$valortotal = $sql31->result("SOMA");

$tpl->TOTAL = $formata->formataNumero($valortotal);
$tpl->VALOR_TOTAL = $valortotal;

$sql32 = new Query ($bd);
$txt32 = "SELECT SUM(VVACASCARR) SOMA_CASH
			    FROM TREDE_CARRINHO
				WHERE VIDCARCARR = :idcart
				  AND SEQUENCIACRE = :idloja";
$sql32->addParam(':idloja', $id_loja);
$sql32->addParam(':idcart', $idcart);
$sql32->executeQuery($txt32);

$valortotal = $sql32->result("SOMA_CASH");


if ($pagamento == 'eccbc87e4b5ce2fe28308fd9f2a7baf3') {
	$tpl->PAGAMENTO = "TRANSFERÊNCIA SIMPLES (TED,DOC)";
	$tpl->INFO_COMPRA = "<h3>Seu CashBack foi gerado no valor <font color='green'>R$ " . $formata->formataNumero($valortotal) . "</font></h3>";
	$tpl->block('CERTO');
	$tpl->block('CERTO1');
}
else {
	$tpl->PAGAMENTO = "ALGO DEU ERRADO, REFAÇA SEU PAGAMENTO.";
	$tpl->INFO_COMPRA = "<h3><font color='red'>Algo deu errado!!</font></h3>";
	$tpl->block('ERRADO');
}

if (isset($_POST['novacompra'])) {
	$aleatorio = mt_rand(20, 20); // 5 ? 10 CARACTERES - CRIAR SESS?O E ENVIAR PARA OUTRA PG
	$valor = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"), 0, $aleatorio);

	$novoidcart = $valor;

	$util->redireciona('lojarede.php?idSessao=' . $_SESSION['idSessao'] . '&cat=0');

}


$tpl->show();
$bd->close();
?>