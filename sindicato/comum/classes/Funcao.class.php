<?php

class Funcao {

	function __call($func, $arq) {
		if (!method_exists(get_class($this), $func)) {
			throw new Exception(" O metodo \"$func\" nao existe");
		}
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

	function RetornaCidadeUsuario($seq) {

		$sql = new Query();
		$txt = "SELECT REDE_CIDADE
                FROM TREDE_USUADMIN
				WHERE REDE_SEQUSUA = :seq";
		$sql->addPAram(':seq', $seq);
		$sql->executeQuery($txt);

		$nnumeplan = $sql->result("REDE_CIDADE");

		return $nnumeplan;
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

	function RetornaNomeProduto($id) {
		$sql = new Query();
		$txt = "SELECT VNOMEPRODU
            FROM TREDE_PRODUTOS 
            WHERE NSEQUPRODU = :idprod";
		$sql->addParam(':idprod', $id);
		$sql->executeQuery($txt);

		$porc = $sql->result("VNOMEPRODU");

		return $porc;
	}

	function RetornaNomeUsuario($id) {
		$sql = new Query();
		$txt = "SELECT REDE_NOMEUSU
            FROM TREDE_USUADMIN 
            WHERE REDE_SEQUSUA = :idprod";
		$sql->addParam(':idprod', $id);
		$sql->executeQuery($txt);

		$porc = $sql->result("REDE_NOMEUSU");

		return $porc;
	}

	function RetornaEmailUsuario($id) {
		$sql = new Query();
		$txt = "SELECT REDE_EMAILUS
            FROM TREDE_USUADMIN 
            WHERE REDE_SEQUSUA = :idprod";
		$sql->addParam(':idprod', $id);
		$sql->executeQuery($txt);

		$porc = $sql->result("REDE_EMAILUS");

		return $porc;
	}

	function AtivoPagSeguro() {
		$sql = new Query();
		$txt = "SELECT ATIVO
            FROM TREDE_CONFIG_BASICS 
            WHERE TIPOCONFIG = 'pagseguro'";
		$sql->executeQuery($txt);

		$porc = $sql->result("ATIVO");

		return $porc;
	}


	function AtivoDotBank() {
		$sql = new Query();
		$txt = "SELECT ATIVO
            FROM TREDE_CONFIG_BASICS 
            WHERE TIPOCONFIG = 'dotbank'";
		$sql->executeQuery($txt);

		$porc = $sql->result("ATIVO");

		return $porc;
	}

	function RetornaTipoCartao($seq) {
		if ($seq == "101") {
			$retorno = "Cart??o de cr??dito Visa.";
		}
		elseif ($seq == "102") {
			$retorno = "Cart??o de cr??dito MasterCard.";
		}
		elseif ($seq == "103") {
			$retorno = "Cart??o de cr??dito American Express.";
		}
		elseif ($seq == "104") {
			$retorno = "Cart??o de cr??dito Diners.";
		}
		elseif ($seq == "105") {
			$retorno = "Cart??o de cr??dito Hipercard.";
		}
		elseif ($seq == "106") {
			$retorno = "Cart??o de cr??dito Aura.";
		}
		elseif ($seq == "107") {
			$retorno = "Cart??o de cr??dito Elo.";
		}
		elseif ($seq == "108") {
			$retorno = "Cart??o de cr??dito PLENOCard.";
		}
		elseif ($seq == "109") {
			$retorno = "Cart??o de cr??dito PersonalCard.";
		}
		elseif ($seq == "110") {
			$retorno = "Cart??o de cr??dito JCB.";
		}
		elseif ($seq == "111") {
			$retorno = "Cart??o de cr??dito Discover.";
		}
		elseif ($seq == "112") {
			$retorno = "Cart??o de cr??dito BrasilCard.";
		}
		elseif ($seq == "113") {
			$retorno = "Cart??o de cr??dito FORTBRASIL.";
		}
		elseif ($seq == "114") {
			$retorno = "Cart??o de cr??dito CARDBAN.";
		}
		elseif ($seq == "115") {
			$retorno = "Cart??o de cr??dito VALECARD.";
		}
		elseif ($seq == "116") {
			$retorno = "Cart??o de cr??dito Cabal.";
		}
		elseif ($seq == "117") {
			$retorno = "Cart??o de cr??dito Mais!.";
		}
		elseif ($seq == "118") {
			$retorno = "Cart??o de cr??dito Avista.";
		}
		elseif ($seq == "119") {
			$retorno = "Cart??o de cr??dito GRANDCARD.";
		}
		elseif ($seq == "120") {
			$retorno = "Cart??o de cr??dito Sorocred.";
		}
		elseif ($seq == "122") {
			$retorno = "Cart??o de cr??dito Up Policard.";
		}
		elseif ($seq == "123") {
			$retorno = "Cart??o de cr??dito Banese Card.";
		}
		elseif ($seq == "201") {
			$retorno = "Boleto Bradesco.";
		}
		elseif ($seq == "202") {
			$retorno = "Boleto Santander.";
		}
		elseif ($seq == "301") {
			$retorno = "D??bito online Bradesco.";
		}
		elseif ($seq == "302") {
			$retorno = "D??bito online Ita??.";
		}
		elseif ($seq == "303") {
			$retorno = "D??bito online Unibanco.";
		}
		elseif ($seq == "304") {
			$retorno = "D??bito online Banco do Brasil.";
		}
		elseif ($seq == "305") {
			$retorno = "D??bito online Banco Real.";
		}
		elseif ($seq == "306") {
			$retorno = "D??bito online Banrisul.";
		}
		elseif ($seq == "307") {
			$retorno = "D??bito online HSBC.";
		}
		elseif ($seq == "401") {
			$retorno = "Saldo PagSeguro.";
		}
		elseif ($seq == "501") {
			$retorno = "Oi Paggo.";
		}
		elseif ($seq == "701") {
			$retorno = "Dep??sito em conta - Banco do Brasil";
		}

		return $retorno;

	}

	function RetornaNomePlano($id) {
		$sql = new Query ();
		$txt = "SELECT CNOMEPLANO 
			  FROM TREDE_PLANOS
			 WHERE SEQPLANO = :id";
		$sql->addPAram(':id', $id);
		$sql->executeQuery($txt);

		$nomeplano = $sql->result("CNOMEPLANO");

		return $nomeplano;
	}


	function assinaturaUsuario($seq) {
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
			}
			elseif (($ddtfimpplan == '') and ($tipodeplano == 'pagseguro')) {
				$datavenci = "PLANO: Aguardando o pagseguro checar o pagamento.";
			}
			elseif (($ddtfimpplan != '') and ($tipodeplano == 'dotbank')) {
				$datavenci = "PLANO: Aguardando o DotBank checar o pagamento.";
			}
			elseif (($ddtfimpplan == '') and ($tipodeplano == 'dotbank')) {
				$datavenci = "PLANO: Aguardando o DotBank checar o pagamento.";
			}
			elseif ($ddtfimpplan == null) {
				$datavenci = "PLANO: N??o ?? um assinante!";
			}

		}
		elseif ($situacaopag == '2') {

			$datavenci = 'PLANO: Em an??lise.';

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

			$datavenci = "PLANO: N??o ?? um assinante!";

		}

		return $datavenci;
	}

	function RetornaImagemProdutos($bd, $seq) {
		$sql = new Query ($bd);
		$txt = "SELECT IMAGEM 
			   FROM TREDE_PRODUTOS
			   WHERE NSEQUPRODU = :seq";
		$sql->AddParam(':seq', $seq);
		$sql->executeQuery($txt);

		$file = '../temp/' . md5(uniqid(rand(), true)) . '.jpg';

		if ($sql->count() > 0) {
			$foto_usuario = $sql->result("IMAGEM");

			if ($foto_usuario <> '') {
				$f = fopen($file, 'wb');
				if (!$f) $this->Error('N??o foi possivel criar o arquivo: ' . $file);
				fwrite($f, $foto_usuario, strlen($foto_usuario));
				fclose($f);

				return $file;
			}
			else
				return "../comum/img/Sem-imagem.jpg";
		}
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
			$resultado = 'BEM-ESTAR E SA??DE';
		}
		elseif ($categoria == '4') {
			$resultado = 'EDUCA????O';
		}
		elseif ($categoria == '5') {
			$resultado = 'PRODUTOS E SERVI??OS';
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
				if (!$f) $this->Error('N??o foi possivel criar o arquivo: ' . $file);
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

		// Define os dados do servidor e tipo de conex????o
		// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
		$mail->IsSMTP(); // Define que a mensagem ser???? SMTP

		try {
			$mail->Host = 'mail.infocell-bts.com.br'; // Endere??o do servidor SMTP (Autentica????o, utilize o host smtp.seudominio.com.br)
			$mail->SMTPAuth = true;  // Usar autenticacao SMTP (obrigat????rio para smtp.seudominio.com.br)
			$mail->Port = 587; //  Usar 587 porta SMTP
			$mail->Username = 'Teste@infocell-bts.com.br'; // Usuario do servidor SMTP (endere??o de email)
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
			//Campos abaixo s??o opcionais
			//=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
			//$mail->AddCC('destinarario@dominio.com.br', 'Destinatario'); // Copia
			//$mail->AddBCC('destinatario_oculto@dominio.com.br', 'Destinatario2`'); // C????pia Oculta
			//$mail->AddAttachment('images/phpmailer.gif');      // Adicionar um anexo
			//Define o corpo do email
			$mail->MsgHTML($mensagem);

			////Caso queira colocar o conteudo de um arquivo utilize o m????todo abaixo ao inv????s da mensagem no corpo do e-mail.
			//$mail->MsgHTML(file_get_contents('arquivo.html'));

			$mail->Send();
			$_SESSION['email_alert'] = "Mensagem enviada com sucesso</p>\n";

			//caso apresente algum erro ???? apresentado abaixo com essa exce????????o.
		} catch (phpmailerException $e) {
			$_SESSION['email_alert'] = $e->errorMessage(); //Mensagem de erro costumizada do PHPMailer
		}
	}


}

?>