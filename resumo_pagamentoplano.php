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

require_once("comum/layout.php");
$tpl->addFile("CONTEUDO", "resumo_pagamentoplano.html");
$tpl->ID_SESSAO = $_SESSION['idSessao'];
$tpl->IDUSUA = $_SESSION['idUsuario'];

$idpagplano = $_GET['pagplano'];
$tpl->IDPAGPLAN = $_GET['pagplano'];

//SELECT PARA VERIFICAR O USUARIO
$tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $seq)));
//SELECT PARA VERIFICAR O USUARIO

//CASHBACK USUARIO
$valortotal_cash = $func->RetornaValorCashBackUsuario($bd, $seq);
$tpl->MEUCASH = $formata->formataNumero($valortotal_cash);
//CASHBACK USUARIO

$sqlv = new Query();
$txtv = "SELECT SEQUPAGPLAN,CTIPOTRPLAN FROM TREDE_PAGAPLANO
				WHERE IDPGSEGPLAN = '" . $idpagplano . "'";
$sqlv->executeQuery($txtv);

$id = $sqlv->result("SEQUPAGPLAN");
$tipotrans = $sqlv->result("CTIPOTRPLAN");

if (($id == "") or ($id == null)) {
	$tpl->block("ERRADO");
}
else {

	if($tipotrans == 'a'){
		$tpl->TIPOTRANS = "Adesão de Plano";
	}else if ($tipotrans == 'm'){
		$tpl->TIPOTRANS = "Ativação Mensal";
	}

	//SELECT PARA VERIFICAR O USUARIO
	$tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $seq)));
	//SELECT PARA VERIFICAR O USUARIO

	$func->AtualizaStatusUsuario($seq);

	//CASHBACK USUARIO
	$valortotal_cash = $func->RetornaValorCashBackUsuario($bd, $seq);
	$tpl->MEUCASH = $formata->formataNumero($valortotal_cash);

	$valortotal_bonus = $func->RetornaValorBonusUsuario($seq);
	$tpl->MEUBONUS = $formata->formataNumero($valortotal_bonus);

	$valortotal_voucher = $func->RetornaValorVoucherUsuario($seq);
	$tpl->MEUVOUCHER = $formata->formataNumero($valortotal_voucher);
	//CASHBACK USUARIO

	$sql = new Query ($bd);
	$txt = "SELECT SEQUPAGPLAN,
       NSEQPAGPLAN,
       CSITPAGPLAN,
       CTIPOPGPLAN,
       DDTINIPPLAN,
       DDTFIMPPLAN,
       LINKBOLETO,
       ADESAOPLANO,
       MENSAPLANO,
       CTIPOTRPLAN,
       IDPGSEGPLAN,
       CCOMPRPPLAN,
       LINKBOLETO
			 FROM TREDE_PAGAPLANO
			WHERE IDPGSEGPLAN = :idusua
			ORDER BY DDATPAGPLAN DESC";
	$sql->addParam(':idusua', $idpagplano);
	$sql->executeQuery($txt);

	$res_data_inicio = $sql->result("DDTINIPPLAN");
	$res_data_final = $sql->result("DDTFIMPPLAN");

	$sql21 = new Query ($bd);
	$txt21 = "SELECT REDE_PLANUSU
			   FROM TREDE_USUADMIN
			  WHERE REDE_SEQUSUA = :usua";
	$sql21->AddParam(':usua', $seq);
	$sql21->executeQuery($txt21);

	$res_assi = $sql21->result("REDE_PLANUSU");

	$tpl->MEUPLANO = $func->assinaturaUsuario($bd, $seq);
	$tpl->block("MEUPLANO");

	while (!$sql->eof()) {

		$tipos_plano = $sql->result("CTIPOTRPLAN");
		$idpagplan = $sql->result("NSEQPAGPLAN");
		$tpl->ID = $sql->result("SEQUPAGPLAN");

		$tpl->IDPAGA = $sql->result("IDPGSEGPLAN");

		$comprovante = $sql->result("CCOMPRPPLAN");

		if($tipotrans == 'a'){
			$tpl->VALOR = $formata->formataNumero($sql->result("ADESAOPLANO"));
		}else if ($tipotrans == 'm'){
			$tpl->VALOR = $formata->formataNumero($sql->result("MENSAPLANO"));
		}



		$data_inicio = $sql->result("DDTINIPPLAN");
		$data_final = $sql->result("DDTFIMPPLAN");
		$data_atual = date('Y-m-d');

		if ($data_inicio == null or $data_final == null) {
			$vencida = "";
			$tpl->VALIDADE = "Em analise";
		}
		elseif ($data_final < $data_atual) {
			$vencida = "<font color='red'>(Vencida)</font>";
			$tpl->VALIDADE = $data->formataData1($data_inicio) . ' - ' . $data->formataData1($data_final) . ' ' . $vencida;
		}
		else {
			$vencida = "";
			$tpl->VALIDADE = $data->formataData1($data_inicio) . ' - ' . $data->formataData1($data_final) . ' ' . $vencida;
		}

		$situa_pag = $sql->result("CSITPAGPLAN");
		$tipo_pag = $sql->result("CTIPOPGPLAN");

		if ($situa_pag == '1') {
			$tpl->SITUA = "Aguardando";
			$tpl->COR = "#FAFBFA";
			$tpl->LINKBOLETO = $sql->result("LINKBOLETO");
			if ($tipo_pag == '7') {
				if($comprovante == ''){
					$tpl->block("DADOSTRANSF");
					$tpl->block("DADOSTRANSF1");
				}else{
					$tpl->block("DADOSTRANSFCC");
					$tpl->block("DADOSTRANSF1CC");
				}
			}
			else {
				$tpl->block("SEGBOL");
				$tpl->block("SEGBOL1");
			}
			//$tpl->block("BTN");
		}
		elseif ($situa_pag == '2') {
			$tpl->SITUA = "Em análise";
			$tpl->COR = "#E0EBE1";
			$tpl->block("BTN");
			$tpl->LINKBOLETO = $sql->result("LINKBOLETO");
			if ($tipo_pag == '7') {
				if($comprovante == ''){
					$tpl->block("DADOSTRANSF");
					$tpl->block("DADOSTRANSF1");
				}else{
					$tpl->block("DADOSTRANSFCC");
					$tpl->block("DADOSTRANSF1CC");
				}
			}
			else {
				$tpl->block("SEGBOL");
				$tpl->block("SEGBOL1");
			}
		}
		elseif ($situa_pag == '3') {
			$tpl->SITUA = "Paga";
			$tpl->COR = "#8FE89C";
		}
		elseif ($situa_pag == '4') {
			$tpl->SITUA = "Disponível";
			$tpl->COR = "#E0EBE1";
		}
		elseif ($situa_pag == '5') {
			$tpl->SITUA = "Em disputa";
		}
		elseif ($situa_pag == '6') {
			$tpl->COR = "#C49AEB";
			$tpl->SITUA = "Devolvida/Extornada";
			$tpl->COR = "#F1BC96";
		}
		elseif ($situa_pag == '7') {
			$tpl->SITUA = "Cancelada";
			$tpl->COR = "#F9A3A3";
		}
		elseif ($situa_pag == '9') {
			$tpl->SITUA = "Expirada - Renovar Plano";
			$tpl->COR = "#F9A3A3";
		}

		$sqlB = new Query ($bd);
		$txtB = "SELECT CNOMEBANCO,
										NAGENBANCO,
										CONTABANCO,
										NCPCJBANCO,
										CNOMFBANCO
			 FROM TREDE_DADOS_BANCARIOS";
		$sqlB->executeQuery($txtB);

		$tpl->BANCO     = ucwords(utf8_encode($sqlB->result("CNOMEBANCO")));
		$tpl->AGENCIA   = $sqlB->result("NAGENBANCO");
		$tpl->CONTA     = $sqlB->result("CONTABANCO");
		$tpl->CPFCNPJ   = $sqlB->result("NCPCJBANCO");
		$tpl->NOMEFAVO  = ucwords(utf8_encode($sqlB->result("CNOMFBANCO")));

		$sql1 = new Query ($bd);
		$txt1 = "SELECT SEQPLANO,
					CNOMEPLANO,CDESCPLANO,
					CPRIMPLANO,
					CSEGUPLANO,
					CTERCPLANO,
					CQUARPLANO,
					VVALPPLANO,
					VVALSPLANO,
					VVALTPLANO,
					CTEMPPLANO
			 FROM TREDE_PLANOS
			WHERE SEQPLANO = :idplano";
		$sql1->addParam(':idplano', $idpagplan);
		$sql1->executeQuery($txt1);


		$tpl->NOMEPLANO = ucwords($sql1->result("CNOMEPLANO"));
		$tpl->DESC = ucwords($sql1->result("CDESCPLANO"));
		//$tpl->DESC = "Descrição do Plano";

		$tpl->TEMPO = $sql1->result("CTEMPPLANO");

		//tipo de pagamento//

		if ($tipo_pag == '1') {
			$tpl->TIPOP = "Cartão Crédito";
		}
		elseif ($tipo_pag == '2') {
			$tpl->TIPOP = "Boleto";
		}
		elseif ($tipo_pag == '3') {
			$tpl->TIPOP = "Cartão Débito";
		}
		elseif ($tipo_pag == '4') {
			$tpl->TIPOP = "Saldo PagSeguro";
		}
		elseif ($tipo_pag == '7') {
			$tpl->TIPOP = "Transferência Bancária";
		}
		elseif ($tipo_pag == null) {
			$tpl->TIPOP = "Aguardando o PagSeguro";
		}
		elseif ($tipo_pag == 'a') {
			$tpl->TIPOP = "À vista";
		}
		elseif ($tipo_pag == 'c') {
			$tpl->TIPOP = "Cancelado";
		}
		//tipo de pagamento//


		$tpl->block("PLANOS");
		$sql->next();
	}

	$tpl->block("CERTO");
}


if (isset($_POST['enviou']) && $_POST['enviou'] == 1) {

	// arquivo
	$arquivo = $_FILES['arquivo'];
	$idpagplano = $_POST['idpagplano'];


	// Tamanho máximo do arquivo (em Bytes)
	$tamanhoPermitido = 1024 * 1024 * 10; // 2Mb

	//Define o diretorio para onde enviaremos o arquivo
	$diretorio = "comprovantes/adesao/";

	// verifica se arquivo foi enviado e sem erros
	if ($arquivo['error'] == UPLOAD_ERR_OK) {

		// pego a extensão do arquivo
		$extensao = $func->extensao($arquivo['name']);

		// valida a extensão
		if (in_array($extensao, array(
			"pdf",
			"jpg",
			"jpeg",
			"png",
			"gif"
		))) {

			// verifica tamanho do arquivo
			if ($arquivo['size'] > $tamanhoPermitido) {

				$tpl->MSG = "<strong>Aviso!</strong> O arquivo enviado é muito grande, envie arquivos de até " . $tamanhoPermitido / MB . " MB.";
				$tpl->block("ERRO");

			}
			else {
				$altura = "1000";
				$largura = "800";

				if ($extensao != 'pdf') {

					switch ($_FILES['arquivo']['type']):
						case 'image/jpeg';
						case 'image/pjpeg';
							$imagem_temporaria = imagecreatefromjpeg($_FILES['arquivo']['tmp_name']);

							$largura_original = imagesx($imagem_temporaria);

							$altura_original = imagesy($imagem_temporaria);

							$nova_largura = $largura ? $largura : floor(($largura_original / $altura_original) * $altura);

							$nova_altura = $altura ? $altura : floor(($altura_original / $largura_original) * $largura);

							$imagem_redimensionada = imagecreatetruecolor($nova_largura, $nova_altura);
							imagecopyresampled($imagem_redimensionada, $imagem_temporaria, 0, 0, 0, 0, $nova_largura, $nova_altura, $largura_original, $altura_original);

							$novo_nome = md5(date('YmdHis')) . "." . $extensao;

							imagejpeg($imagem_redimensionada, $diretorio . $novo_nome);

							//move_uploaded_file($_FILES['arquivo']['name'],$diretorio);
							//echo "<img src='".$diretorio.$_FILES['arquivo']['name']. "'>";
							$sql = new Query($bd);
							$sql->clear();
							$txt = "UPDATE TREDE_PAGAPLANO SET CCOMPRPPLAN = '" . $diretorio . $novo_nome . "',
																	CNOMCOPPLAN = '" . $novo_nome . "',
																	DCOMPRPPLAN = '" . date('Y-m-d H:i:s') . "'
																	WHERE IDPGSEGPLAN = '" . $idpagplano . "' ";
							$sql->executeSQL($txt);

							echo "<script>alert('Enviado com Sucesso.');  window.location.href = window.location.href </script>";

							break;



						//Caso a imagem seja extensão PNG cai nesse CASE
						case 'image/png':
						case 'image/x-png';
							$imagem_temporaria = imagecreatefrompng($_FILES['arquivo']['tmp_name']);

							$largura_original = imagesx($imagem_temporaria);
							$altura_original = imagesy($imagem_temporaria);

							/* Configura a nova largura */
							$nova_largura = $largura ? $largura : floor(($largura_original / $altura_original) * $altura);

							/* Configura a nova altura */
							$nova_altura = $altura ? $altura : floor(($altura_original / $largura_original) * $largura);

							/* Retorna a nova imagem criada */
							$imagem_redimensionada = imagecreatetruecolor($nova_largura, $nova_altura);

							/* Copia a nova imagem da imagem antiga com o tamanho correto */
							//imagealphablending($imagem_redimensionada, false);
							//imagesavealpha($imagem_redimensionada, true);

							imagecopyresampled($imagem_redimensionada, $imagem_temporaria, 0, 0, 0, 0, $nova_largura, $nova_altura, $largura_original, $altura_original);

							$novo_nome = md5(date('YmdHis')) . "." . $extensao;

							imagejpeg($imagem_redimensionada, $diretorio . $novo_nome);

							$sql = new Query($bd);
							$sql->clear();
							$txt = "UPDATE TREDE_PAGAPLANO SET CCOMPRPPLAN = '".$diretorio.$novo_nome."',
																CNOMCOPPLAN = '".$novo_nome."',
																DCOMPRPPLAN = '".date('Y-m-d H:i:s')."'
																WHERE IDPGSEGPLAN = '".$idpagplano."' ";
							$sql->executeSQL($txt);

							echo "<script>alert('Enviado com Sucesso.');  window.location.href = window.location.href </script>";

							break;

					endswitch;
				}else if($extensao == 'pdf'){

					$novo_nome  = md5(date('YmdHis')).".".$extensao;
					$enviou = move_uploaded_file($_FILES['arquivo']['tmp_name'], $diretorio.$novo_nome);

					$sql = new Query($bd);
					$sql->clear();
					$txt = "UPDATE TREDE_PAGAPLANO SET CCOMPRPPLAN = '".$diretorio.$novo_nome."',
															CNOMCOPPLAN = '".$novo_nome."',
															DCOMPRPPLAN = '".date('Y-m-d H:i:s')."'
															WHERE IDPGSEGPLAN = '".$idpagplano."' ";
					$sql->executeSQL($txt);

				}
			}
		}
		else {
			$tpl->MSG = "<strong>Erro!</strong> Somente arquivos de Imagens são permitidos.";
			$tpl->block("ERRO");
		}

	}
	else {
		$tpl->MSG = "<strong>Atenção!</strong> Você deve enviar um arquivo.";
		$tpl->block("ERRO");
	}
}

$tpl->show();
$bd->close();
?>