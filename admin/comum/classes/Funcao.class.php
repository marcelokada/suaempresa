<?php

class Funcao {


	function gravaErro($pagina, $erro) {

		$arquivo = date('YmdHis');
		$ponteiro = fopen("../../erros/" . $arquivo . ".txt", "w");
		fwrite($ponteiro, $erro);

		fwrite($ponteiro, '============ Trace ===============' . chr(13));
		ob_start();
		debug_print_backtrace();

		fwrite($ponteiro, ob_get_clean());

		fclose($ponteiro);

		if (DEBUG) {
			echo "<script>window.open('erros/" . $arquivo . "');</script>";
		}
		else {
			echo "<script>window.location.href='comum/erro.php?codigo=" . $arquivo . "&pagina=" . $pagina . "';</script>";
		}
	}

	function RetornaValorCashBackUsuario($bd, $seq) {
		$sql = new Query ($bd);
		$txt = "SELECT  VVALUSCASH
			   FROM TREDE_CASHBACK_USU
		      WHERE NIDUSUCASH = :seq";
		$sql->addPAram(':seq', $seq);
		$sql->executeQuery($txt);

		$valorcash = $sql->result("VVALUSCASH");
		return $valorcash;
	}

	function RetornaValorPlano($seq) {

		$sql = new Query();
		$txt = "SELECT VVALTPLANO
                FROM TREDE_PLANOS
				WHERE SEQPLANO = :seq";
		$sql->addPAram(':seq', $seq);
		$sql->executeQuery($txt);

		$nnumeplan = $sql->result("VVALTPLANO");

		return $nnumeplan;
	}

	function RetornaIdPlanoUsua($seq) {

		$sql = new Query ();
		$txt = "SELECT LAST_INSERT_ID(NSEQPAGPLAN) NSEQPAGPLAN
                FROM TREDE_PAGAPLANO
				WHERE NIDUPAGPLAN = :seq
				ORDER BY 1 DESC
				LIMIT 1";
		$sql->addPAram(':seq', $seq);
		$sql->executeQuery($txt);

		$nnumeplan = $sql->result("NSEQPAGPLAN");

		return $nnumeplan;
	}

	function RetornaNomeUsuarioSeq($seq) {
		$sql = new Query ($bd);
		$txt = "SELECT REDE_NOMEUSU
			   FROM TREDE_USUADMIN
		      WHERE REDE_SEQUSUA = :seq";
		$sql->addPAram(':seq', $seq);
		$sql->executeQuery($txt);

		$valorcash = $sql->result("REDE_NOMEUSU");
		return $valorcash;
	}

	function RetornaValorVoucherUsuario($idusua) {
		$sql = new Query ();
		$txt = "SELECT NVALORVOUCH
						FROM  TREDE_VOUCHER
           WHERE NNUMEUSUA = :nnumeidplano ";
		$sql->addParam(':nnumeidplano', $idusua);
		$sql->executeQuery($txt);

		$seq = $sql->result("NVALORVOUCH");

		if($seq == ""){
			$seqidplan = 0;
		}else{
			$seqidplan = $sql->result("NVALORVOUCH");
		}

		return $seqidplan;
	}

	function RetornaValorBonusUsuario($idusua) {
		$sql = new Query ();
		$txt = "SELECT VALORTOTAL
						FROM  TREDE_ADESAO_MENSA_USU
           WHERE NIDUPAGPLAN = :nnumeidplano ";
		$sql->addParam(':nnumeidplano', $idusua);
		$sql->executeQuery($txt);

		$seq = $sql->result("VALORTOTAL");

		if($seq == ""){
			$seqidplan = 0;
		}else{
			$seqidplan = $sql->result("VALORTOTAL");
		}

		return $seqidplan;
	}


	function RetornaValorTotalExtratoUsuario($idusua) {
		$sql = new Query ();
		$txt = "SELECT SUM(CREDITO-DEBITO) TOTAL
							FROM TREDE_EXTRATO_USUA
							WHERE NPATEXTRA  = :nnumeidplano ";
		$sql->addParam(':nnumeidplano', $idusua);
		$sql->executeQuery($txt);

		$seq = $sql->result("TOTAL");

		if($seq == ""){
			$seqidplan = 0;
		}else{
			$seqidplan = $sql->result("TOTAL");
		}

		return $seqidplan;
	}

	function extensao($arquivo){
		$arquivo = strtolower($arquivo);
		$explode = explode(".", $arquivo);
		$arquivo = end($explode);

		return ($arquivo);
	}


	function RetornaPermissoes_Admin($usua) {
		$sql = new Query ();
		$txt = "SELECT NIVELADMIN FROM TREDE_ADMINS WHERE REDE_SEQUSUA = '".$usua."'";
		$sql->executeQuery($txt);

		$res_nivel = $sql->result("NIVELADMIN");

		$sql1 = new Query ();
		$txt1 = "SELECT NNIVEADMIN,CNIVEADMIN FROM TREDE_PERMISSAO_ADMIN  WHERE NNIVEADMIN = '".$res_nivel."'";
		$sql1->executeQuery($txt1);

		$resultado = $sql1->result("CNIVEADMIN");

		return $resultado;

	}

	function RetornaPermissoes($menu) {
		$sql = new Query ();
		$txt = "SELECT MENUS,SITUACAO
			  FROM TREDE_PERMISSAO
			  WHERE MENUS = '".$menu."'";
		$sql->executeQuery($txt);

		while(!$sql->eof()){
			$array[] = ["MENU" => $sql->result("MENUS"),
				"STATUS" =>  $sql->result("SITUACAO")];
			$sql->next();
		}

		return $array;
	}

	function RetornaIDPlanoPatrocinador($id) {
		$sql = new Query ();
		$txt = "SELECT LAST_INSERT_ID(NSEQPAGPLAN) NSEQPAGPLAN,
			           DDATPAGPLAN
						  FROM TREDE_PAGAPLANO
						  WHERE NIDUPAGPLAN = :id
                AND CSITUAPPLAN = 'a'
						  ORDER BY DDATPAGPLAN DESC
						  LIMIT 1";
		$sql->addPAram(':id', $id);
		$sql->executeQuery($txt);

		$idplan = $sql->result("NSEQPAGPLAN");
		if($idplan == ""){
			$seqidplan = 0.00;
		}else{
			$seqidplan = $sql->result("NSEQPAGPLAN");
		}


		return $seqidplan;
	}


	function RetornaUltimoPLanoUsuario($seq_usua){
		$sql11 = new Query();
		$txt11 = "SELECT LAST_INSERT_ID(NSEQPAGPLAN) NSEQPAGPLAN
							  FROM TREDE_PAGAPLANO
							  WHERE NIDUPAGPLAN = :usua
                  AND CSITUAPPLAN IN ('a','c')
							  ORDER BY 1 DESC
							  LIMIT 1";
		$sql11->addParam(':usua', $seq_usua);
		$sql11->executeQuery($txt11);

		$res = $sql11->result("NSEQPAGPLAN");

		if($res == ""){
			$plano = "";
		}else{
			$plano = $sql11->result("NSEQPAGPLAN");
		}
	return $plano;
	}

	function RetornaLimitePlano($nivel) {
		$sql = new Query();
		$txt = "SELECT VALOR FROM TREDE_VALOR_UNILEVEL
                    WHERE NNUMEPLAN = :logs";
		$sql->addparam(':logs', $nivel);
		$sql->executeQuery($txt);

		$res = $sql->result("VALOR");

		if($res == ""){
			$plano = "0.00";
		}else{
			$plano = $sql->result("VALOR");
		}
		return $plano;
	}


	function RetornaPorcentagemnivel($nivel) {
		$sql = new Query();
		$txt = "SELECT PORCENTAGEM FROM TREDE_NIVELPORCENT
                    WHERE NIVEL = :logs";
		$sql->addparam(':logs', $nivel);
		$sql->executeQuery($txt);

		$porc = $sql->result("PORCENTAGEM");

		return $porc;
	}

	function RetornaPorcentagemNivelAtivos($nivel) {
		$sql = new Query();
		$txt = "SELECT PORCENTAGEM FROM TREDE_NIVEIS_ATIVOS
                    WHERE SEQ = :logs";
		$sql->addparam(':logs', $nivel);
		$sql->executeQuery($txt);

		$porc = $sql->result("PORCENTAGEM");

		return $porc;
	}

	function RetornaValorUnivelUsuario($idusua) {
		$sql = new Query ();
		$txt = "SELECT VALOR
						FROM  TREDE_VALOR_UNILEVEL
           WHERE NNUMEPLAN = :nnumeidplano";
		$sql->addParam(':nnumeidplano', $idusua);
		$sql->executeQuery($txt);

		$seq = $sql->result("VALOR");
		if($seq == ""){
			$seqidplan = 0;
		}else{
			$seqidplan = $sql->result("VALOR");
		}

		return $seqidplan;
	}

	function RetonaAdminMaster($seq) {
		$sql = new Query ();
		$txt = "SELECT MASTER
			  FROM TREDE_ADMINS
			 WHERE REDE_SEQUSUA = :seq";
		$sql->addPAram(':seq', $seq);
		$sql->executeQuery($txt);

		$nomeusua = $sql->result("MASTER");

		return $nomeusua;
	}


	function RetonaNomeUsuarioPorSeq($bd, $seq) {
		$sql = new Query ($bd);
		$txt = "SELECT REDE_NOMEUSU
			  FROM TREDE_USUADMIN
			 WHERE REDE_SEQUSUA = :seq";
		$sql->addPAram(':seq', $seq);
		$sql->executeQuery($txt);

		$nomeusua = $sql->result("REDE_NOMEUSU");

		return $nomeusua;
	}

	function RetornaNivelPermissaoAdmin($usua_admin) {
		$sql = new Query();
		$txt = "SELECT NPERMRULE
				 FROM TREDE_RULES
				WHERE REDE_SEQUSUA = :usua";
		$sql->addParam(':usua',$usua_admin);
		$sql->executeQuery($txt);

		$permissao = $sql->result("NPERMRULE");

		return $permissao;

	}

	function RetornaAdminMaster($usua_admin) {
		$sql = new Query();
		$txt = "SELECT MASTER
				 FROM TREDE_ADMINS
				WHERE REDE_SEQUSUA = :usua";
		$sql->addParam(':usua',$usua_admin);
		$sql->executeQuery($txt);

		$permissao = $sql->result("MASTER");

		return $permissao;

	}

	function RetornaNomeEmpresa($bd, $seq) {
		$sql = new Query ($bd);
		$txt = "SELECT VNOMECREDCRE
			  FROM TREDE_CREDENCIADOS
			 WHERE SEQUENCIACRE = :seq ";
		$sql->addParam(':seq', $seq);
		$sql->executeQuery($txt);

		$nomecat = $sql->result("VNOMECREDCRE");

		return $nomecat;

	}

	function RetornaNomePacote($seq) {
		$sql = new Query ();
		$txt = "SELECT CNOMEPAC
			  FROM TREDE_PACOTES_REDE
			 WHERE NNUMEPAC = :seq
			   AND CSITUPAC = 'a' ";
		$sql->addPAram(':seq', $seq);
		$sql->executeQuery($txt);

		$nomecat = $sql->result("CNOMEPAC");

		return $nomecat;

	}

	function RetornaNomeCategoria($bd, $seq) {
		$sql = new Query ($bd);
		$txt = "SELECT VNOMECATECAT
			  FROM TREDE_CATEGORIAS
			 WHERE NNUMECATECAT = :seq
			   AND VSITUCATECAT = 'a' ";
		$sql->addPAram(':seq', $seq);
		$sql->executeQuery($txt);

		$nomecat = $sql->result("VNOMECATECAT");

		return $nomecat;

	}

	function assinaturaUsuario($bd, $seq) {
		$data = new Data();

		$sql = new Query ($bd);
		$txt = "SELECT LAST_INSERT_ID(SEQUPAGPLAN) SEQUPAGPLAN,
                        DDTFIMPPLAN,
                        NSEQPAGPLAN,
                        CSITPAGPLAN,
                        CSITUAPPLAN,
       									CTIPOOPPLAN
                FROM TREDE_PAGAPLANO
				WHERE NIDUPAGPLAN = :seq
				ORDER BY 1 DESC
				LIMIT 1";
		$sql->addPAram(':seq', $seq);
		$sql->executeQuery($txt);


		$nnumeplan = $sql->result("NSEQPAGPLAN");
		$situapplano = $sql->result("CSITUAPPLAN");
		$situacaopag = $sql->result("CSITPAGPLAN");
		$ddtfimpplan = $sql->result("DDTFIMPPLAN");
		$tipodeplano = $sql->result("CTIPOOPPLAN");
		$data_vence = $data->formataData1($sql->result("DDTFIMPPLAN"));
		$dataatual = date("Y-m-d");

		$nomeplano = $this->RetornaNomePlano($nnumeplan);

		if ($situacaopag == '1') {

			if (($ddtfimpplan != '') and ($tipodeplano == 'pagseguro')) {
				$datavenci = 'PLANO: Aguardando o pagseguro checar o pagamento.';
			}elseif (($ddtfimpplan == '') and ($tipodeplano == 'pagseguro')) {
				$datavenci = "PLANO: Aguardando o pagseguro checar o pagamento.";
			}
			elseif (($ddtfimpplan != '') and ($tipodeplano == 'dotbank')) {
				$datavenci = "PLANO: Aguardando o DotBank checar o pagamento.";
			}
			elseif (($ddtfimpplan == '') and ($tipodeplano == 'dotbank')) {
				$datavenci = "PLANO: Aguardando o DotBank checar o pagamento.";
			}
			elseif ($ddtfimpplan == null) {
				$datavenci = "PLANO: Não é um assinante!";
			}

		}
		elseif ($situacaopag == '2') {

			$datavenci = 'PLANO: Em análise.';

		}
		elseif ($situacaopag == '3') {

			$datavenci = "Assinante. Assinatura vence: <font color='green'>" . $data_vence . '</font>';

		}
		elseif ($situacaopag == '6') {

			$datavenci = "PLANO: Assinatura foi cancelada. Pois seu pagamento foi extornado.";

		}
		elseif ($situacaopag == '7') {

			$datavenci = "PLANO: Assinatura foi cancelada.";

		}
		elseif ($situacaopag == '9') {

			$datavenci = "PLANO: Sua assinatura foi expirada. Plano: " . $nomeplano;

		}
		elseif ($situacaopag == '') {

			$datavenci = "PLANO: Não é um assinante!";

		}

		return $datavenci;
	}

	function RetonaGrupoMembros($id){
		if ($id == 'd') {
			$tipo = "Dependente";
		}
		elseif ($id == 'a') {
			$tipo = "Agregado";
		}
		return $tipo;
	}

	function RetonaTipoUsuario($seq) {
		$tipos = [
			'm' => 'Marido',
			'e' => 'Esposa',
			'f' => 'Filho',
			'n' => 'Neta(o)',
			'p' => 'Pai',
			'a' => 'Mãe',
			'i' => 'Irmão(ã)',
			's' => 'Sogro(a)'
		];
		return isset($tipos[$seq]) ? $tipos[$seq] : null;
	}

	function Tipopagamento($seq) {

		if ($seq == '1') {
			$retorno = "Cartão Crédito";
		}
		elseif ($seq == '2') {
			$retorno = "Boleto";
		}
		elseif ($seq == '3') {
			$retorno = "Cartão Débito";
		}
		elseif ($seq == '4') {
			$retorno = "Saldo PagSeguro";
		}
		elseif ($seq == '7') {
			$retorno = "Transferência Bancária";
		}
		elseif ($seq == null) {
			$retorno = "Aguardando o PagSeguro";
		}
		elseif ($seq == 'a') {
			$retorno = "À vista";
		}
		elseif ($seq == 'c') {
			$retorno = "Cancelado";
		}

		return $retorno;
	}



	function RetornaSituaPagamento($seq) {
		$tipos = [
			'1' => 'Aguardando',
			'2' => 'Em análise',
			'3' => '<font color="green">Paga</font>',
			'4' => 'Disponível',
			'5' => 'Em disputa',
			'6' => 'Devolvida/Extornada',
			'7' => '<font color="red">Cancelada</font>',
			'9' => '<font color="green">Pago</font>'
		];
		return isset($tipos[$seq]) ? $tipos[$seq] : null;
	}

	/*function RetornaSituaPagamento($seq) {
		if ($seq == '1') {
			$pagamento = "Aguardando";
		}
		elseif ($seq == '2') {
			$pagamento = "Em análise";
		}
		elseif ($seq == '3') {
			$pagamento = "<font color='green'>Paga</font>";
		}
		elseif ($seq == '4') {
			$pagamento = "Disponível";
		}
		elseif ($seq == '5') {
			$pagamento = "Em disputa";
		}
		elseif ($seq == '6') {
			$pagamento = "Devolvida/Extornada";
		}
		elseif ($seq == '7') {
			$pagamento = "<font color='red'>Cancelada</font>";
		}
		elseif ($seq == '9') {
			$pagamento = "<font color='green'>Pago</font>";
		}
		return $pagamento;
	}*/


	function RetornaNomePlano($id) {
		$sql = new Query ();
		$txt = "SELECT CNOMEPLANO
			  FROM TREDE_PLANOS
			 WHERE SEQPLANO = :id";
		$sql->addPAram(':id',$id);
		$sql->executeQuery($txt);

		$nome = $sql->result("CNOMEPLANO");
		if($nome == ""){
			$nomeplano = "não possui";
		}else{
			$nomeplano = $sql->result("CNOMEPLANO");
		}



		return $nomeplano;
	}


	function RetornaIDUsuaPagaPlano($idusua) {
		$sql = new Query ();
		$txt = "SELECT NIDUPAGPLAN
						FROM  TREDE_PAGAPLANO
           WHERE SEQUPAGPLAN = :nnumeidplano ";
		$sql->addParam(':nnumeidplano', $idusua);
		$sql->executeQuery($txt);

		$seq = $sql->result("NIDUPAGPLAN");

		return $seq;
	}


	function RetornaNomeSubCategoria($bd, $id) {
		$sql = new Query ($bd);
		$txt = "SELECT VNOMECATESUB
			  FROM TREDE_SUBCATEGORIA
			 WHERE NNUMECATESUB = :id";
		$sql->addPAram(':id', $id);
		$sql->executeQuery($txt);

		$nomescat = $sql->result("VNOMECATESUB");

		return $nomescat;

	}

	function RetonaNomeUsuarioPorSeq1($bd, $seq) {
		$sql = new Query($bd);
		$txt = "SELECT REDE_NOMEUSU
			  FROM TREDE_USUADMIN
			 WHERE REDE_SEQUSUA = :seq";
		$sql->addPAram(':seq', $seq);
		$sql->executeQuery($txt);

		$nomeusua = $sql->result("REDE_NOMEUSU");

		return $nomeusua;
	}

	function RetonaNomeRegiao($bd, $id) {
		$sql = new Query ($bd);
		$txt = "SELECT VNOMEREGIREG
			  FROM TREDE_REGIAO
			 WHERE NNUMEREGIREG  = :id";
		$sql->addPAram(':id', $id);
		$sql->executeQuery($txt);

		$resultado = strtoupper($sql->result("VNOMEREGIREG"));

		return $resultado;
	}

	function RetonaNomeEstado($bd, $id) {
		$sql = new Query ($bd);
		$txt = "SELECT VNOMEESTAEST
			  FROM TREDE_ESTADO
			 WHERE CESTADOUFEST = :id";
		$sql->addPAram(':id', $id);
		$sql->executeQuery($txt);

		$resultado = strtoupper($sql->result("VNOMEESTAEST"));

		return $resultado;
	}

	function RetonaNomeCidade($bd, $id) {
		$sql = new Query ($bd);
		$txt = "SELECT VNOMECIDAMUN
			 FROM TREDE_MUNICIPIO
			WHERE NNUMEIBGEMUN = :id";
		$sql->addPAram(':id', $id);
		$sql->executeQuery($txt);

		$resultado = strtoupper($sql->result("VNOMECIDAMUN"));

		return $resultado;
	}

	function RetonaNomeCategoria($bd, $id) {
		$sql = new Query ($bd);
		$txt = "SELECT NNUMECATECRE
			 FROM TREDE_CREDENCIADOS
			WHERE NNUMECATECRE = :id";
		$sql->addPAram(':id', $id);
		$sql->executeQuery($txt);

		$categoria = $sql->result("NNUMECATECRE");

		if ($categoria == '1') {
			$resultado = 'ALIMENTOS E BEBIDAS';
		}
		elseif ($categoria == '2') {
			$resultado = 'LAZER';
		}
		elseif ($categoria == '3') {
			$resultado = 'BEM-ESTAR E SAÚDE';
		}
		elseif ($categoria == '4') {
			$resultado = 'EDUCAÇÃO';
		}
		elseif ($categoria == '5') {
			$resultado = 'PRODUTOS E SERVIÇOS';
		}

		return $resultado;
	}


	function RetornaImagem($bd, $seq) {
		$sql = new Query ($bd);
		$txt = "SELECT VIMAGEMCRCRE
			   FROM TREDE_CREDENCIADOS
			   WHERE SEQUENCIACRE = :seq";
		$sql->AddParam(':seq', $seq);
		$sql->executeQuery($txt);

		$file = '../temp/' . md5(uniqid(rand(), true)) . '.jpg';

		if ($sql->count() > 0) {
			$foto_usuario = $sql->result("VIMAGEMCRCRE");

			if ($foto_usuario <> '') {
				$f = fopen($file, 'wb');
				if (!$f) $this->Error('Não foi possivel criar o arquivo: ' . $file);
				fwrite($f, $foto_usuario, strlen($foto_usuario));
				fclose($f);

				return $file;
			}
			else
				return "../../comum/img/Sem-imagem.jpg";
		}
	}

	function diferenca_tempo($data1, $data2, $tipo) {
		//$data1 = "01/05/2012 08:00:01";
		//$data2 = "01/05/2013 08:00:03";
		if (!$data1 || !$data2) return false;
		for ($i = 1; $i <= 2; $i++) {
			${"dia" . $i} = substr(${"data" . $i}, 0, 2);
			${"mes" . $i} = substr(${"data" . $i}, 3, 2);
			${"ano" . $i} = substr(${"data" . $i}, 6, 4);
			${"horas" . $i} = substr(${"data" . $i}, 11, 2);
			${"minutos" . $i} = substr(${"data" . $i}, 14, 2);
			${"segundos" . $i} = substr(${"data" . $i}, 17, 2);
		}

		$segundos = @mktime($horas2, $minutos2, $segundos2, $mes2, $dia2, $ano2) - @mktime($horas1, $minutos1, $segundos1, $mes1, $dia1, $ano1);
		switch ($tipo) {
			case "H":
				$difere = @$segundos / 60 / 60;
				break;
			case "D":
				$difere = @$segundos / 86400;
				break;
			case "S":
				$difere = @$segundos;
				break;
		}
		return $difere;
	}

	function inverteData($data) {
		if (count(explode("/", $data)) > 1) {
			return implode("-", array_reverse(explode("/", $data)));
		}
		elseif (count(explode("-", $data)) > 1) {
			return implode("/", array_reverse(explode("-", $data)));
		}
	}

	function retirarPontostracosundelinebarra($valor) {
		$valor = trim($valor);
		$valor = str_replace(".", "", $valor);
		$valor = str_replace(",", "", $valor);
		$valor = str_replace("-", "", $valor);
		$valor = str_replace("/", "", $valor);
		return $valor;
	}

	function enviaEmail($para, $assunto1, $mensagem, $anexo = array()) {
		require_once("PHPMailer/src/phpmailer.php");

		// Inicia a classe PHPMailer
		$mail = new PHPMailer(true);

		// Define os dados do servidor e tipo de conexÃ£o
		// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
		$mail->IsSMTP(); // Define que a mensagem serÃ¡ SMTP

		try {
			$mail->Host = 'mail.infocell-bts.com.br'; // Endereço do servidor SMTP (Autenticação, utilize o host smtp.seudominio.com.br)
			$mail->SMTPAuth = true;  // Usar autenticacao SMTP (obrigatÃ³rio para smtp.seudominio.com.br)
			$mail->Port = 587; //  Usar 587 porta SMTP
			$mail->Username = 'Teste@infocell-bts.com.br'; // Usuario do servidor SMTP (endereço de email)
			$mail->Password = '123456'; // Senha do servidor SMTP (senha do email usado)

			//Define o remetente
			// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
			$mail->SetFrom('Teste@infocell-bts.com.br', 'Suporte Psicologa'); //Seu e-mail
			$mail->AddReplyTo('Teste@infocell-bts.com.br', 'Suporte Psicologa'); //Seu e-mail

			$mail->Subject = $assunto1;//Assunto do e-mail
			//Define os destinatario(s)
			//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
			$mail->AddAddress('marcelookada@outlook.com', 'Teste Locaweb');

			//$mail->AddAddress($para);
			//Campos abaixo são opcionais
			//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
			//$mail->AddCC('destinarario@dominio.com.br', 'Destinatario'); // Copia
			//$mail->AddBCC('destinatario_oculto@dominio.com.br', 'Destinatario2`'); // CÃ³pia Oculta
			//$mail->AddAttachment('images/phpmailer.gif');      // Adicionar um anexo
			//Define o corpo do email
			$mail->MsgHTML($mensagem);

			////Caso queira colocar o conteudo de um arquivo utilize o mÃ©todo abaixo ao invÃ©s da mensagem no corpo do e-mail.
			//$mail->MsgHTML(file_get_contents('arquivo.html'));

			$mail->Send();
			$_SESSION['email_alert'] = "Mensagem enviada com sucesso</p>\n";

			//caso apresente algum erro Ã© apresentado abaixo com essa exceÃ§Ã£o.
		} catch (phpmailerException $e) {
			$_SESSION['email_alert'] = $e->errorMessage(); //Mensagem de erro costumizada do PHPMailer
		}
	}


}

?>