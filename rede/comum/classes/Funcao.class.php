<?php

class Funcao {

	function __call($func, $arq) {
		if (!method_exists(get_class($this), $func)) {
			throw new Exception(" O metodo \"$func\" nao existe");
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
  
  function RetornaTipoEvento($id) {
    
    $sql = new Query ();
    $txt = "SELECT CNOMEEVENT
						FROM TREDE_TIPO_EVENTO
           WHERE NNUMEEVENT = :id ";
    $sql->addParam(':id',$id);
    $sql->executeQuery($txt);
    
    $resultado = $sql->result("CNOMEEVENT");
    
    return $resultado;
  }

	function RetornaNomePacote($seq) {
		$sql = new Query ();
		$txt = "SELECT CNOMEPAC
						 FROM TREDE_PACOTES_REDE
						  WHERE NNUMEPAC= :seq";
		$sql->addPAram(':seq', $seq);
		$sql->executeQuery($txt);

		$valorcash = $sql->result("CNOMEPAC");
		return $valorcash;
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

	function AtivoTransf() {
		$sql = new Query();
		$txt = "SELECT ATIVO
            FROM TREDE_CONFIG_BASICS 
            WHERE TIPOCONFIG = 'transferencia'";
		$sql->executeQuery($txt);

		$porc = $sql->result("ATIVO");

		return $porc;
	}

	function RetornaTipoCartao($seq) {
		if ($seq == "101") {
			$retorno = "Cartão de crédito Visa.";
		}
		elseif ($seq == "102") {
			$retorno = "Cartão de crédito MasterCard.";
		}
		elseif ($seq == "103") {
			$retorno = "Cartão de crédito American Express.";
		}
		elseif ($seq == "104") {
			$retorno = "Cartão de crédito Diners.";
		}
		elseif ($seq == "105") {
			$retorno = "Cartão de crédito Hipercard.";
		}
		elseif ($seq == "106") {
			$retorno = "Cartão de crédito Aura.";
		}
		elseif ($seq == "107") {
			$retorno = "Cartão de crédito Elo.";
		}
		elseif ($seq == "108") {
			$retorno = "Cartão de crédito PLENOCard.";
		}
		elseif ($seq == "109") {
			$retorno = "Cartão de crédito PersonalCard.";
		}
		elseif ($seq == "110") {
			$retorno = "Cartão de crédito JCB.";
		}
		elseif ($seq == "111") {
			$retorno = "Cartão de crédito Discover.";
		}
		elseif ($seq == "112") {
			$retorno = "Cartão de crédito BrasilCard.";
		}
		elseif ($seq == "113") {
			$retorno = "Cartão de crédito FORTBRASIL.";
		}
		elseif ($seq == "114") {
			$retorno = "Cartão de crédito CARDBAN.";
		}
		elseif ($seq == "115") {
			$retorno = "Cartão de crédito VALECARD.";
		}
		elseif ($seq == "116") {
			$retorno = "Cartão de crédito Cabal.";
		}
		elseif ($seq == "117") {
			$retorno = "Cartão de crédito Mais!.";
		}
		elseif ($seq == "118") {
			$retorno = "Cartão de crédito Avista.";
		}
		elseif ($seq == "119") {
			$retorno = "Cartão de crédito GRANDCARD.";
		}
		elseif ($seq == "120") {
			$retorno = "Cartão de crédito Sorocred.";
		}
		elseif ($seq == "122") {
			$retorno = "Cartão de crédito Up Policard.";
		}
		elseif ($seq == "123") {
			$retorno = "Cartão de crédito Banese Card.";
		}
		elseif ($seq == "201") {
			$retorno = "Boleto Bradesco.";
		}
		elseif ($seq == "202") {
			$retorno = "Boleto Santander.";
		}
		elseif ($seq == "301") {
			$retorno = "Débito online Bradesco.";
		}
		elseif ($seq == "302") {
			$retorno = "Débito online Itaú.";
		}
		elseif ($seq == "303") {
			$retorno = "Débito online Unibanco.";
		}
		elseif ($seq == "304") {
			$retorno = "Débito online Banco do Brasil.";
		}
		elseif ($seq == "305") {
			$retorno = "Débito online Banco Real.";
		}
		elseif ($seq == "306") {
			$retorno = "Débito online Banrisul.";
		}
		elseif ($seq == "307") {
			$retorno = "Débito online HSBC.";
		}
		elseif ($seq == "401") {
			$retorno = "Saldo PagSeguro.";
		}
		elseif ($seq == "501") {
			$retorno = "Oi Paggo.";
		}
		elseif ($seq == "701") {
			$retorno = "Depósito em conta - Banco do Brasil";
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
				if (!$f) $this->Error('Não foi possivel criar o arquivo: ' . $file);
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