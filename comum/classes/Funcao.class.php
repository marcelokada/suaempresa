<?php
  
  
  $bd = new Database();
  
  class Funcao {
    
    function __call($func,$arq) {
      if (!method_exists(get_class($this),$func)) {
        throw new Exception(" O metodo \"$func\" nao existe");
      }
    }
  
    function enviaEmail1($para,$assunto,$mensagem,$anexo='') {
      require_once ("../../SMS/PHPMailer/PHPMailerAutoload.php");
    
      $host      = $_SESSION['email_smtp'];
      $port      = '587';
      $autentica = 1;
    
      $mail = new PHPMailer;
      $mail->Timeout    =   5;
      $mail->isSMTP();
      // $mail->SMTPDebug = 2;
      $mail->Host       = $host;
    
      if (($autentica == '1') or ($autentica == '2'))
        $mail->SMTPAuth   = true;         // Enable SMTP authentication
      else
        $mail->SMTPAuth   = true;        // Enable SMTP authentication
    
      if ($autentica == '1')
        $tipoAutenticacao = 'tls';
      else if ($autentica == '2')
        $tipoAutenticacao = 'ssl';
      else
        $tipoAutenticacao = false;
    
      $mail->Username   = $email_admin;
      $mail->Password   = $senha;
      $mail->SMTPSecure = $tipoAutenticacao;
      $mail->Port       = $port;
    
      $mail->setFrom($email_admin,'');
    
      $destinatarios = explode(';',$para);
    
      foreach ($destinatarios as $d) {
        $mail->addAddress($d);
      }
    
      $mail->isHTML(true);
      $mail->Subject = $assunto;
      $mail->Body    = $mensagem;
      $mail->AltBody = 'Este email só será visualizado em padrão HTML';
    
      if(!$mail->send()) {
        return '<br>'. utf8_encode(print_r($mail->ErrorInfo,true));
      }
      else {
        return '';
      }
    }
    
    
    function GerarChaveAleatoria($minimo,$maximo){
      $aleatorio = mt_rand($minimo,$maximo); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
      $valor = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$aleatorio);
      
      return $valor;
    }
    
    function redimencionarImagem($tipo,$imagem,$extensao) {
      
      $altura = "1200";
      $largura = "800";
      
      switch ($tipo):
        case 'image/jpeg';
        case 'image/pjpeg';
          $imagem_temporaria = imagecreatefromjpeg($imagem);
          
          $largura_original = imagesx($imagem_temporaria);
          
          $altura_original = imagesy($imagem_temporaria);
          
          
          $nova_largura = $largura ? $largura : floor(($largura_original / $altura_original) * $altura);
          
          $nova_altura = $altura ? $altura : floor(($altura_original / $largura_original) * $largura);
          
          $imagem_redimensionada = imagecreatetruecolor($nova_largura,$nova_altura);
          
          return $imagem_redimensionada;
      
      endswitch;
      
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
    
    function RetornaNomeProfissao($id) {
    
      $sql = new Query ();
      $txt = "SELECT CNOMEPROF
						FROM TREDE_PROFISSAO
           WHERE NNUMEPROF = :id ";
      $sql->addParam(':id',$id);
      $sql->executeQuery($txt);
    
      $resultado = $sql->result("CNOMEPROF");
    
      return $resultado;
    }
  
    function RetornaNomeEmpresa_NOVO($id) {
    
      $sql = new Query ();
      $txt = "SELECT TEXTO
						FROM  TREDE_CONFIG_BASICS
           WHERE TIPOCONFIG = 'nome_empresa'
             AND EMPRESA = :id ";
      $sql->addParam(':id',$id);
      $sql->executeQuery($txt);
    
      $resultado = utf8_encode($sql->result("TEXTO"));
    
      return $resultado;
    }
  
    function RetornaNomeEmpresaPG($id) {
    
      $sql = new Query ();
      $txt = "SELECT EMPRESA
						FROM  TREDE_CONFIG_BASICS
           WHERE TIPOCONFIG = 'nome_empresa'
             AND TEXTO = :id ";
      $sql->addParam(':id',$id);
      $sql->executeQuery($txt);
    
      $resultado = utf8_encode($sql->result("EMPRESA"));
    
      return $resultado;
    }
    
    function RetornaNomeEmpresa_MIMO($id) {
  
      $empresa = explode('/',$id);
      $emp = $empresa[1];
      
      $sql = new Query ();
      $txt = "SELECT NOMEEMPRESA,LINKEMPRESA
						FROM  TREDE_EMPRESAS_LAYOUT
           WHERE LINKEMPRESA = :id ";
      $sql->addParam(':id',$emp);
      $sql->executeQuery($txt);

      $resultado = $sql->result("NOMEEMPRESA");
      
      return $resultado;
    }
    
    
    
    
    function RetornaValorVoucherUsuario($idusua) {
      $sql = new Query ();
      $txt = "SELECT NVALORVOUCH
						FROM  TREDE_VOUCHER
           WHERE NNUMEUSUA = :nnumeidplano ";
      $sql->addParam(':nnumeidplano',$idusua);
      $sql->executeQuery($txt);
      
      $seq = $sql->result("NVALORVOUCH");
      
      if ($seq == "") {
        $seqidplan = 0;
      } else {
        $seqidplan = $sql->result("NVALORVOUCH");
      }
      
      return $seqidplan;
    }
    
    function RetornaPemissao($nome_pemissao) {
      $sql = new Query ();
      $txt = "SELECT LAST_INSERT_ID(NSEQPAGPLAN) NSEQPAGPLAN,
			           DDATPAGPLAN
						  FROM TREDE_PAGAPLANO
						  WHERE NIDUPAGPLAN = :id
						  ORDER BY DDATPAGPLAN DESC
						  LIMIT 1";
      $sql->addPAram(':nome_pemissao',$nome_pemissao);
      $sql->executeQuery($txt);
      
      $seqidplan = $sql->result("NSEQPAGPLAN");
      
      return $seqidplan;
    }
    
    
    function RetornaPermissoes($menu) {
      $sql = new Query ();
      $txt = "SELECT MENUS,SITUACAO
			  FROM TREDE_PERMISSAO
			  WHERE MENUS = '".$menu."'";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $array[] = [
          "MENU"   => $sql->result("MENUS"),
          "STATUS" => $sql->result("SITUACAO"),
        ];
        $sql->next();
      }
      
      return $array;
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
        '9' => '<font color="green">Pago</font>',
      ];
      return isset($tipos[$seq]) ? $tipos[$seq] : NULL;
    }
    
    
    function RetornaValorUnivelUsuario($idusua) {
      $sql = new Query ();
      $txt = "SELECT VALOR
						FROM  TREDE_VALOR_UNILEVEL
           WHERE NNUMEPLAN = :nnumeidplano";
      $sql->addParam(':nnumeidplano',$idusua);
      $sql->executeQuery($txt);
      
      $seq = $sql->result("VALOR");
      if ($seq == "") {
        $seqidplan = 0;
      } else {
        $seqidplan = $sql->result("VALOR");
      }
      
      
      return $seqidplan;
    }
    
    function RetornaValorTotalExtratoUsuario($idusua) {
      $sql = new Query ();
      $txt = "SELECT SUM(CREDITO-DEBITO) TOTAL
							FROM TREDE_EXTRATO_USUA
							WHERE NPATEXTRA  = :nnumeidplano ";
      $sql->addParam(':nnumeidplano',$idusua);
      $sql->executeQuery($txt);
      
      $seq = $sql->result("TOTAL");
      
      if ($seq == "") {
        $seqidplan = 0;
      } else {
        $seqidplan = $sql->result("TOTAL");
      }
      
      return $seqidplan;
    }
    
    function RetornaIDUsuaPagaPlano($idusua) {
      $sql = new Query ();
      $txt = "SELECT NIDUPAGPLAN
						FROM  TREDE_PAGAPLANO
           WHERE IDPGSEGPLAN = :nnumeidplano ";
      $sql->addParam(':nnumeidplano',$idusua);
      $sql->executeQuery($txt);
      
      $seq = $sql->result("NIDUPAGPLAN");
      
      return $seq;
    }
    
    function RetornaValorBonusUsuario($idusua) {
      $sql = new Query ();
      $txt = "SELECT VALORTOTAL
						FROM  TREDE_ADESAO_MENSA_USU
           WHERE NIDUPAGPLAN = :nnumeidplano ";
      $sql->addParam(':nnumeidplano',$idusua);
      $sql->executeQuery($txt);
      
      $seq = $sql->result("VALORTOTAL");
      
      if ($seq == "") {
        $seqidplan = 0;
      } else {
        $seqidplan = $sql->result("VALORTOTAL");
      }
      
      return $seqidplan;
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
      $sql->addPAram(':id',$id);
      $sql->executeQuery($txt);
      
      $idplan = $sql->result("NSEQPAGPLAN");
      if ($idplan == "") {
        $seqidplan = 0.00;
      } else {
        $seqidplan = $sql->result("NSEQPAGPLAN");
      }
      
      
      return $seqidplan;
    }
    
    function Tipopagamento($seq) {
      
      if ($seq == '1') {
        $retorno = "Cartão Crédito";
      } else if ($seq == '2') {
        $retorno = "Boleto";
      } else if ($seq == '3') {
        $retorno = "Cartão Débito";
      } else if ($seq == '4') {
        $retorno = "Saldo PagSeguro";
      } else if ($seq == '7') {
        $retorno = "Transferência Bancária";
      } else if ($seq == NULL) {
        $retorno = "Aguardando o PagSeguro";
      } else if ($seq == 'a') {
        $retorno = "À vista";
      } else if ($seq == 'c') {
        $retorno = "Cancelado";
      }
      
      return $retorno;
    }
    
    function TipoUsuarioMembros($seq) {
      
      if ($seq == 'd') {
        $retorno1 = 'Dependente';
      } else if ($seq == 'D') {
        $retorno1 = 'Dependente';
      } else if ($seq == 'a') {
        $retorno1 = 'Agregado';
      } else if ($seq == 'A') {
        $retorno1 = 'Agregado';
      } else {
        $retorno1 = '--';
      }
      
      return $retorno1;
    }
    
    function RetornaStatusIDBoletoDotbank($id) {
      
      if ($id == '24075675-98ae-354e-89ca-0126a9ad36e3') {
        $val = '1';
      } else if ($id == '115ff52f-d605-3b4b-98fe-c0ea57f4930c') {
        $val = '11';
      } else if ($id == 'ed0221e8-ac7d-393b-821d-25183567885b') {
        $val = '12';
      } else if ($id == '508ef333-85a6-314c-bcf3-17ddc32b2216') {
        $val = '13';
      } else if ($id == '2c269ea4-dbfd-32dd-9bd7-a5c22677d18b') {
        $val = '14';
      } else if ($id == 'a4715ee0-524a-37cc-beb2-a0b5030757b7') {
        $val = '15';
      } else if ($id == '44eb0948-118f-3f28-87e4-f61c8f889aba') {
        $val = '3';
      } else if ($id == 'fc72beeb-f790-36ee-a73d-33888c9d8880') {
        $val = '7';
      } else if ($id == '1e46afa2-6176-3cd3-9750-3015846723df') {
        $val = '16';
      } else if ($id == 'd1c72756-aaec-3470-a2f2-97415f44d72f') {
        $val = '17';
      }
      
      return $val;
      
      
      /*
    
    
    
      '24075675-98ae-354e-89ca-0126a9ad36e3' => 'Processando',
    
        '115ff52f-d605-3b4b-98fe-c0ea57f4930c' => 'Criado (Cobrança foi gerada no nosso sistema)', // Created
    
        'ed0221e8-ac7d-393b-821d-25183567885b' => 'Pode ser registrado ('Pronto para envio ao banco')', // Accept Registration
    
        '508ef333-85a6-314c-bcf3-17ddc32b2216' => 'Registrando ('Enviando para o banco')', // Registering
    
        '2c269ea4-dbfd-32dd-9bd7-a5c22677d18b' => 'Registrado', // Registered
    
        'a4715ee0-524a-37cc-beb2-a0b5030757b7' => 'Rejeitado (Rejeitado pelo banco por algum motivo)', // Rejected
    
        '44eb0948-118f-3f28-87e4-f61c8f889aba' => 'Liquidado (Pago)', // Paid
    
        'fc72beeb-f790-36ee-a73d-33888c9d8880' => 'Cancelado', // Cancel
    
        '1e46afa2-6176-3cd3-9750-3015846723df' => 'Erro Interno (Consulte o suporte)', // Error Internal
    
        'd1c72756-aaec-3470-a2f2-97415f44d72f' => 'Erro de Saldo', // Error Balance
    */
    }
    
    function retornaStatuspagamento($seq) {
      if ($seq == '1') {
        $tipopg = "Aguardando";
      } else if ($seq == '2') {
        $tipopg = "Em análise";
      } else if ($seq == '3') {
        $tipopg = "Paga";
      } else if ($seq == '4') {
        $tipopg = "Disponível";
      } else if ($seq == '5') {
        $tipopg = "Em disputa";
      } else if ($seq == '6') {
        $tipopg = "Devolvida/Extornada";
      } else if ($seq == '7') {
        $tipopg = "Cancelada";
      } else if ($seq == '9') {
        $tipopg = "Expirada";
      } else if ($seq == '11') {
        $tipopg = "Criado (Cobrança foi gerada no nosso sistema)";
      } else if ($seq == '12') {
        $tipopg = "Pode ser registrado ('Pronto para envio ao banco')";
      } else if ($seq == '13') {
        $tipopg = "Registrando ('Enviando para o banco')";
      } else if ($seq == '14') {
        $tipopg = "Registrado";
      } else if ($seq == '15') {
        $tipopg = "Rejeitado (Rejeitado pelo banco por algum motivo)'";
      } else if ($seq == '16') {
        $tipopg = "Erro Interno (Consulte o suporte)";
      } else if ($seq == '17') {
        $tipopg = "Erro de Saldo";
      }
    }
    
    function RetonaNomeUsuarioPorSeq1($bd,$seq) {
      $sql = new Query($bd);
      $txt = "SELECT REDE_NOMEUSU
			  FROM TREDE_USUADMIN 
			 WHERE REDE_SEQUSUA = :seq";
      $sql->addPAram(':seq',$seq);
      $sql->executeQuery($txt);
      
      $nomeusua = $sql->result("REDE_NOMEUSU");
      
      return $nomeusua;
    }
    
    function RetornaNomePlano($id) {
      $sql = new Query ();
      $txt = "SELECT CNOMEPLANO
			  FROM TREDE_PLANOS
			 WHERE SEQPLANO = :id";
      $sql->addPAram(':id',$id);
      $sql->executeQuery($txt);
      
      $nomeplano = $sql->result("CNOMEPLANO");
      
      return $nomeplano;
    }
    
    
    function AtualizaStatusUsuario($seq) {
      
      $sql_v = new Query ();
      $txt_v = "SELECT LAST_INSERT_ID(SEQUPAGPLAN) SEQUPAGPLAN, DDTFIMPPLAN,CSITPAGPLAN FROM TREDE_PAGAPLANO
				WHERE NIDUPAGPLAN = :idusua
				  AND CSITUAPPLAN = 'a' 
				  ORDER BY 1 DESC
				  LIMIT 1";
      $sql_v->addParam(':idusua',$seq);
      $sql_v->executeQuery($txt_v);
      
      $seqplano = $sql_v->result("SEQUPAGPLAN");
      $datafim = $sql_v->result("DDTFIMPPLAN");
      $dataatual = date('Y-m-d');
      $tipopg = $sql_v->result("CSITPAGPLAN");
      
      if ($datafim == '') {
        $sql_u1 = new Query ();
        $txt_u1 = "UPDATE TREDE_PAGAPLANO SET CSITUAPPLAN = 'p', CSITPAGPLAN = '1'
				WHERE SEQUPAGPLAN = :seqplano";
        $sql_u1->addParam(':seqplano',$seqplano);
        $sql_u1->executeSQL($txt_u1);
      } else if ($datafim < $dataatual) {
        $sql_u = new Query ();
        $txt_u = "UPDATE TREDE_USUADMIN SET REDE_PLANUSU = 'c'
				WHERE REDE_SEQUSUA = :idusua";
        $sql_u->addParam(':idusua',$seq);
        $sql_u->executeSQL($txt_u);
        
        $sql_u1 = new Query ();
        $txt_u1 = "UPDATE TREDE_PAGAPLANO SET CSITUAPPLAN = 'c', CSITPAGPLAN = '9'
				WHERE SEQUPAGPLAN = :seqplano";
        $sql_u1->addParam(':seqplano',$seqplano);
        $sql_u1->executeSQL($txt_u1);
        
      } else if ($datafim >= $dataatual) {
        $sql_u = new Query ();
        $txt_u = "UPDATE TREDE_USUADMIN SET REDE_PLANUSU = 'a'
				WHERE REDE_SEQUSUA = :idusua";
        $sql_u->addParam(':idusua',$seq);
        $sql_u->executeSQL($txt_u);
        
        $sql_u1 = new Query ();
        $txt_u1 = "UPDATE TREDE_PAGAPLANO SET CSITUAPPLAN = 'a',  CSITPAGPLAN = '3'
				WHERE SEQUPAGPLAN = :seqplano";
        $sql_u1->addParam(':seqplano',$seqplano);
        $sql_u1->executeSQL($txt_u1);
        
      }
    }
    
    
    function RetornaPorcentagemnivel($nivel) {
      $sql = new Query();
      $txt = "SELECT PORCENTAGEM FROM TREDE_NIVELPORCENT
                    WHERE NIVEL = :logs";
      $sql->addparam(':logs',$nivel);
      $sql->executeQuery($txt);
      
      $porc = $sql->result("PORCENTAGEM");
      
      return $porc;
    }
    
    function RetornaPorcentagemNivelAtivos($nivel) {
      $sql = new Query();
      $txt = "SELECT PORCENTAGEM FROM TREDE_NIVEIS_ATIVOS
                    WHERE SEQ = :logs";
      $sql->addparam(':logs',$nivel);
      $sql->executeQuery($txt);
      
      $porc = $sql->result("PORCENTAGEM");
      
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
    
    function extensao($arquivo) {
      $arquivo = strtolower($arquivo);
      $explode = explode(".",$arquivo);
      $arquivo = end($explode);
      
      return ($arquivo);
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
    
    function AtivoDotBank() {
      $sql = new Query();
      $txt = "SELECT ATIVO
            FROM TREDE_CONFIG_BASICS 
            WHERE TIPOCONFIG = 'dotbank'";
      $sql->executeQuery($txt);
      
      $porc = $sql->result("ATIVO");
      
      return $porc;
    }
    
    function EnviarEmail($para,$assunto,$texto) {
      // Inclui o arquivo class.phpmailer.php localizado na mesma pasta do arquivo php
      require_once("PHPMailer-master/PHPMailerAutoload.php");
      
      // Inicia a classe PHPMailer
      $mail = new PHPMailer();
      $mail->isSMTP();
      $mail->Host = "mail.drultraconvenio.com.br";
      
      //$mail->Port = 587;
      $mail->Port = 587;
      
      $mail->SMTPAuth = TRUE;
      
      $mail->Username = 'no-reply@cb.acesso.in';
      
      $mail->Password = 'QUtq5G66u0ny9go';
      
      // Configurações de compatibilidade para autenticação em TLS
      $autentica = '1';
      
      if ($autentica == '1')
        $tipoAutenticacao = 'tls'; else if ($autentica == '2')
        $tipoAutenticacao = 'ssl';
      else
        $tipoAutenticacao = FALSE;
      
      
      $mail->SMTPSecure = $tipoAutenticacao;
      // Você pode habilitar esta opção caso tenha problemas. Assim pode identificar mensagens de erro.
      // $mail->SMTPDebug = 2;
      
      // Define o remetente
      // Seu e-mail
      $mail->From = "contato@drultraconvenio.com.br";
      
      // Seu nome
      $mail->FromName = "Doutor Ultra Convênio";
      
      // Define o(s) destinatário(s)
      $mail->AddAddress($para);
      
      // Opcional: mais de um destinatário
      // $mail->AddAddress('fernando@email.com');
      
      // Opcionais: CC e BCC
      // $mail->AddCC('joana@provedor.com', 'Joana');
      // $mail->AddBCC('roberto@gmail.com', 'Roberto');
      
      // Definir se o e-mail é em formato HTML ou texto plano
      // Formato HTML . Use "false" para enviar em formato texto simples ou "true" para HTML.
      
      $mail->isHTML(TRUE);
      
      // Charset (opcional)
      $mail->CharSet = 'UTF-8';
      
      // Assunto da mensagem
      $mail->Subject = $assunto;
      
      // Corpo do email
      $mail->Body = $texto;
      
      // Opcional: Anexos
      // $mail->AddAttachment("/home/usuario/public_html/documento.pdf", "documento.pdf");
      
      // Envia o e-mail
      $enviado = $mail->Send();
      
      // Exibe uma mensagem de resultado
      if ($enviado) {
        echo "Seu email foi enviado com sucesso!";
      } else {
        echo "Houve um erro enviando o email: ".$mail->ErrorInfo;
      }
    }
    
    
    function RetornaNomeProduto($id) {
      $sql = new Query();
      $txt = "SELECT VNOMEPRODU
            FROM TREDE_PRODUTOS 
            WHERE NSEQUPRODU = :idprod";
      $sql->addParam(':idprod',$id);
      $sql->executeQuery($txt);
      
      $porc = $sql->result("VNOMEPRODU");
      
      return $porc;
    }
    
    function IdNivelUm($seq1) {
      
      $sql = new Query();
      $txt = "SELECT NNUMEFILI,NNUMENIVE FROM TREDE_NIVEL
		WHERE NIDUSNIVE  = :sequsu";
      $sql->addParam(':sequsu',$seq1);
      $sql->executeQuery($txt);
      
      $a78 = $sql->result("NNUMEFILI");
      
      $sql1 = new Query();
      $txt1 = "SELECT NNUMEFILI,NNUMENIVE FROM TREDE_NIVEL
		        WHERE NIDUSNIVE  = :sequsu";
      $sql1->addParam(':sequsu',$a78);
      $sql1->executeQuery($txt1);
      
      $a77 = $sql1->result("NNUMEFILI");
      $n77 = $sql1->result("NNUMENIVE");
      
      if ($n77 == 1) {
        return $a78;
      } else if ($n77 == 2) {
        
        return $a77;
        
      } else if ($n77 == 3) {
        $sql2 = new Query();
        $txt2 = "SELECT NNUMEFILI,NNUMENIVE FROM TREDE_NIVEL
		                WHERE NIDUSNIVE  = :sequsu";
        $sql2->addParam(':sequsu',$a77);
        $sql2->executeQuery($txt2);
        
        $a76 = $sql2->result("NNUMEFILI");
        
        $sql3 = new Query();
        $txt3 = "SELECT NIDUSNIVE FROM TREDE_NIVEL
		                WHERE NIDUSNIVE  = :sequsu";
        $sql3->addParam(':sequsu',$a76);
        $sql3->executeQuery($txt3);
        
        
        return $sql3->result("NIDUSNIVE");
        
      } else if ($n77 == 4) {
        
        $sql2 = new Query();
        $txt2 = "SELECT NNUMEFILI,NNUMENIVE FROM TREDE_NIVEL
		                  WHERE NIDUSNIVE  = :sequsu";
        $sql2->addParam(':sequsu',$a77);
        $sql2->executeQuery($txt2);
        
        $a76 = $sql2->result("NNUMEFILI");
        
        $sql3 = new Query();
        $txt3 = "SELECT NNUMEFILI FROM TREDE_NIVEL
		                WHERE NIDUSNIVE  = :sequsu";
        $sql3->addParam(':sequsu',$a76);
        $sql3->executeQuery($txt3);
        
        $a75 = $sql3->result("NNUMEFILI");
        
        $sql4 = new Query();
        $txt4 = "SELECT NIDUSNIVE FROM TREDE_NIVEL
		                WHERE NIDUSNIVE  = :sequsu";
        $sql4->addParam(':sequsu',$a75);
        $sql4->executeQuery($txt4);
        
        $idfinal = $sql4->result("NIDUSNIVE");
        
        return $idfinal;
      }
      
      
    }
    
    
    function RetornaNivelMMN($login) {
      
      $sql = new Query();
      $txt = "SELECT REDE_SEQUSUA,REDE_ADMINUS FROM TREDE_USUADMIN
                    WHERE REDE_LOGUSUA = :logs";
      $sql->addparam(':logs',$login);
      $sql->executeQuery($txt);
      
      //$res = $sql->result("REDE_SEQUSUA");
      $res = $sql->result("REDE_ADMINUS");
      $resusua = $sql->result("REDE_SEQUSUA");
      
      if ($res == "s") {
        $nivel = 1;
      } else {
        $sql1 = new Query();
        $txt1 = "SELECT NNUMENIVE FROM TREDE_NIVEL
                    WHERE NIDUSNIVE = :seq";
        $sql1->addparam(':seq',$resusua);
        $sql1->executeQuery($txt1);
        
        $resn = $sql1->result("NNUMENIVE");
        
        if (($resn == NULL) or ($resn == "") or ($resn == 0)) {
          $nivel = 1;
        } else if ($resn == 1) {
          $nivel = 2;
        } else if ($resn == 2) {
          $nivel = 3;
        } else if ($resn == 3) {
          $nivel = 4;
        } else if ($resn == 4) {
          $nivel = 4;
        }
        
      }
      
      return $nivel;
    }
    
    function RetornaNivelMMNid($id) {
      
      $sql = new Query();
      $txt = "SELECT REDE_SEQUSUA,REDE_ADMINUS FROM TREDE_USUADMIN
                    WHERE REDE_SEQUSUA = :logs";
      $sql->addparam(':logs',$id);
      $sql->executeQuery($txt);
      
      //$res = $sql->result("REDE_SEQUSUA");
      $res = $sql->result("REDE_ADMINUS");
      $resusua = $sql->result("REDE_SEQUSUA");
      
      if ($res == "s") {
        $nivel = 1;
      } else {
        $sql1 = new Query();
        $txt1 = "SELECT NNUMENIVE FROM TREDE_NIVEL
                    WHERE NIDUSNIVE = :seq";
        $sql1->addparam(':seq',$resusua);
        $sql1->executeQuery($txt1);
        
        $resn = $sql1->result("NNUMENIVE");
        
        if (($resn == NULL) or ($resn == "") or ($resn == 0)) {
          $nivel = 1;
        } else if ($resn == 1) {
          $nivel = 2;
        } else if ($resn == 2) {
          $nivel = 3;
        } else if ($resn == 3) {
          $nivel = 4;
        } else if ($resn == 4) {
          $nivel = 4;
        }
        
      }
      
      return $nivel;
    }
    
    
    function RetornaTipoCartao($seq) {
      if ($seq == "101") {
        $retorno = "Cartão de crédito Visa.";
      } else if ($seq == "102") {
        $retorno = "Cartão de crédito MasterCard.";
      } else if ($seq == "103") {
        $retorno = "Cartão de crédito American Express.";
      } else if ($seq == "104") {
        $retorno = "Cartão de crédito Diners.";
      } else if ($seq == "105") {
        $retorno = "Cartão de crédito Hipercard.";
      } else if ($seq == "106") {
        $retorno = "Cartão de crédito Aura.";
      } else if ($seq == "107") {
        $retorno = "Cartão de crédito Elo.";
      } else if ($seq == "108") {
        $retorno = "Cartão de crédito PLENOCard.";
      } else if ($seq == "109") {
        $retorno = "Cartão de crédito PersonalCard.";
      } else if ($seq == "110") {
        $retorno = "Cartão de crédito JCB.";
      } else if ($seq == "111") {
        $retorno = "Cartão de crédito Discover.";
      } else if ($seq == "112") {
        $retorno = "Cartão de crédito BrasilCard.";
      } else if ($seq == "113") {
        $retorno = "Cartão de crédito FORTBRASIL.";
      } else if ($seq == "114") {
        $retorno = "Cartão de crédito CARDBAN.";
      } else if ($seq == "115") {
        $retorno = "Cartão de crédito VALECARD.";
      } else if ($seq == "116") {
        $retorno = "Cartão de crédito Cabal.";
      } else if ($seq == "117") {
        $retorno = "Cartão de crédito Mais!.";
      } else if ($seq == "118") {
        $retorno = "Cartão de crédito Avista.";
      } else if ($seq == "119") {
        $retorno = "Cartão de crédito GRANDCARD.";
      } else if ($seq == "120") {
        $retorno = "Cartão de crédito Sorocred.";
      } else if ($seq == "122") {
        $retorno = "Cartão de crédito Up Policard.";
      } else if ($seq == "123") {
        $retorno = "Cartão de crédito Banese Card.";
      } else if ($seq == "201") {
        $retorno = "Boleto Bradesco.";
      } else if ($seq == "202") {
        $retorno = "Boleto Santander.";
      } else if ($seq == "301") {
        $retorno = "Débito online Bradesco.";
      } else if ($seq == "302") {
        $retorno = "Débito online Itaú.";
      } else if ($seq == "303") {
        $retorno = "Débito online Unibanco.";
      } else if ($seq == "304") {
        $retorno = "Débito online Banco do Brasil.";
      } else if ($seq == "305") {
        $retorno = "Débito online Banco Real.";
      } else if ($seq == "306") {
        $retorno = "Débito online Banrisul.";
      } else if ($seq == "307") {
        $retorno = "Débito online HSBC.";
      } else if ($seq == "401") {
        $retorno = "Saldo PagSeguro.";
      } else if ($seq == "501") {
        $retorno = "Oi Paggo.";
      } else if ($seq == "701") {
        $retorno = "Depósito em conta - Banco do Brasil";
      }
      
      return $retorno;
      
    }
    
    
    function assinaturaUsuarioID($bd,$seq) {
      
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
      $sql->addPAram(':seq',$seq);
      $sql->executeQuery($txt);
      
      
      $nnumeplan = $sql->result("NSEQPAGPLAN");
      $situapplano = $sql->result("CSITUAPPLAN");
      $situacaopag = $sql->result("CSITPAGPLAN");
      $ddtfimpplan = $sql->result("DDTFIMPPLAN");
      $tipodeplano = $sql->result("CTIPOOPPLAN");
      
      return $situacaopag;
    }
    
    function RetornaIdUsuaPorReferencia($id) {
      
      $sql = new Query ();
      $txt = "SELECT LAST_INSERT_ID(NIDUPAGPLAN) NIDUPAGPLAN FROM TREDE_PAGAPLANO
           WHERE IDPGSEGPLAN = :seq";
      $sql->addPAram(':seq',$id);
      $sql->executeQuery($txt);
      
      $idusua = $sql->result("NIDUPAGPLAN");
      
      return $idusua;
    }
    
    function RetornaUltimaReferencia($id) {
      
      $sql = new Query ();
      $txt = "SELECT LAST_INSERT_ID(SEQUPAGPLAN), IDPGSEGPLAN,DDTFIMPPLAN
              FROM TREDE_PAGAPLANO
             WHERE NIDUPAGPLAN = :seq
             AND CSITPAGPLAN = 3
             ORDER BY 1 DESC
             LIMIT 1";
      $sql->addPAram(':seq',$id);
      $sql->executeQuery($txt);
      
      $idusua = $sql->result("DDTFIMPPLAN");
      
      return $idusua;
    }
    
    function RetornaIdPlanoUsua($seq) {
      
      $sql = new Query ();
      $txt = "SELECT LAST_INSERT_ID(NSEQPAGPLAN) NSEQPAGPLAN
                FROM TREDE_PAGAPLANO
				WHERE NIDUPAGPLAN = :seq
				ORDER BY 1 DESC				
				LIMIT 1";
      $sql->addPAram(':seq',$seq);
      $sql->executeQuery($txt);
      
      $nnumeplan = $sql->result("NSEQPAGPLAN");
      
      return $nnumeplan;
    }
    
    
    function RetornaValorPlano($seq) {
      
      $sql = new Query();
      $txt = "SELECT VVALPPLANO
                FROM TREDE_PLANOS
				WHERE SEQPLANO = :seq";
      $sql->addPAram(':seq',$seq);
      $sql->executeQuery($txt);
      
      $nnumeplan = $sql->result("VVALPPLANO");
      
      return $nnumeplan;
    }
    
    function RetornaQtdeAgregados($idplano) {
      $sqld = new Query();
      $txtd = "SELECT AGREGPLANO
					 FROM TREDE_PLANOS
					 WHERE SEQPLANO = :seqplano";
      $sqld->addParam(':seqplano',$idplano);
      $sqld->executeQuery($txtd);
      
      $agregados = $sqld->result("AGREGPLANO");
      
      return $agregados;
      
    }
    
    function RetornaQtdeDependentes($idplano) {
      $sqld = new Query();
      $txtd = "SELECT DEPENPLANO
					 FROM TREDE_PLANOS
					 WHERE SEQPLANO = :seqplano";
      $sqld->addParam(':seqplano',$idplano);
      $sqld->executeQuery($txtd);
      
      $dependentes = $sqld->result("DEPENPLANO");
      
      return $dependentes;
      
    }
    
    function RetornaData_INI_FIM_DoPlanoUsuario($seq,$tipo) {
      
      $sql = new Query ();
      $txt = "SELECT LAST_INSERT_ID(SEQUPAGPLAN) SEQUPAGPLAN,
                        DDTINIPPLAN,
                        DDTFIMPPLAN
                FROM TREDE_PAGAPLANO
				WHERE NIDUPAGPLAN = :seq
				ORDER BY 1 DESC
				LIMIT 1";
      $sql->addPAram(':seq',$seq);
      $sql->executeQuery($txt);
      
      if (($tipo == 'I') or ($tipo == 'i')) {
        $data = $sql->result("DDTINIPPLAN");
      } else {
        $data = $sql->result("DDTFIMPPLAN");
      }
      
      return $data;
    }
    
    
    function RetornaStatusDoPlanoUsuario($seq) {
      $data = new Data();
      
      $sql = new Query ();
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
      $sql->addPAram(':seq',$seq);
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
          $datavenci = 'Aguardando o pagseguro checar o pagamento.';
        } else if (($ddtfimpplan == '') and ($tipodeplano == 'pagseguro')) {
          $datavenci = "Aguardando o pagseguro checar o pagamento.";
        } else if (($ddtfimpplan != '') and ($tipodeplano == 'dotbank')) {
          $datavenci = "Aguardando o DotBank checar o pagamento.";
        } else if (($ddtfimpplan == '') and ($tipodeplano == 'dotbank')) {
          $datavenci = "Aguardando o DotBank checar o pagamento.";
        } else if ($ddtfimpplan == NULL) {
          $datavenci = "Você ainda não é um assinante!";
        }
        
      } else if ($situacaopag == '2') {
        
        $datavenci = 'Em análise.';
        
      } else if ($situacaopag == '3') {
        
        $datavenci = "Sua assinatura vence: <font color='green'>".$data_vence.' </font> <br> <h4><b>Seu Plano:</b> '.$nomeplano.'</h4>';
        
      } else if ($situacaopag == '6') {
        
        $datavenci = "Sua assinatura foi cancelada. Pois seu pagamento foi extornado.";
        
      } else if ($situacaopag == '7') {
        
        $datavenci = "Sua assinatura foi cancelada, ou expirou o perido de ativação.";
        
      } else if ($situacaopag == '9') {
        
        $datavenci = "Sua assinatura foi expirada. <a href='ativacaomensal.php?idSessao={ID_SESSAO}'>Clique aqui para reativa-la.</a><br> <h4><b>Seu Plano:</b> ".$nomeplano.'</h4>';
        
      } else if ($situacaopag == '') {
        
        $datavenci = "Você ainda não é um assinante!";
        
      }
      
      return $datavenci;
    }
    
    function RetornaStatusAssinaturaUsuarioAdesao($seq) {
      
      $sql = new Query ($bd);
      $txt = "SELECT LAST_INSERT_ID(SEQUPAGPLAN) SEQUPAGPLAN,
                        DDTFIMPPLAN,
                        NSEQPAGPLAN,
                        CSITPAGPLAN,
                        CSITUAPPLAN,
       									CTIPOOPPLAN
                FROM TREDE_PAGAPLANO
				WHERE NIDUPAGPLAN = :seq
          AND CTIPOTRPLAN = 'a'
				ORDER BY 1 DESC
				LIMIT 1";
      $sql->addPAram(':seq',$seq);
      $sql->executeQuery($txt);
      
      $res = $sql->result("CSITPAGPLAN");
      
      return $res;
    }
    
    
    function RetornaStatusAssinaturaUsuario($seq) {
      
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
      $sql->addPAram(':seq',$seq);
      $sql->executeQuery($txt);
      
      $res = $sql->result("CSITPAGPLAN");
      
      return $res;
    }
    
    function assinaturaUsuarioMimo($bd,$seq) {
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
          AND CSITPAGPLAN >= 3
				ORDER BY 1 DESC				
				LIMIT 1";
      $sql->addPAram(':seq',$seq);
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
          $datavenci = 'MEU PLANO: Aguardando o pagseguro checar o pagamento.';
        } else if (($ddtfimpplan == '') and ($tipodeplano == 'pagseguro')) {
          $datavenci = "MEU PLANO: Aguardando o pagseguro checar o pagamento.";
        } else if (($ddtfimpplan != '') and ($tipodeplano == 'dotbank')) {
          $datavenci = "MEU PLANO: Aguardando o DotBank checar o pagamento.";
        } else if (($ddtfimpplan == '') and ($tipodeplano == 'dotbank')) {
          $datavenci = "MEU PLANO: Aguardando o DotBank checar o pagamento.";
        } else if ($ddtfimpplan == NULL) {
          $datavenci = "MEU PLANO: Você ainda não é um assinante!";
        }
        
      } else if ($situacaopag == '2') {
        
        $datavenci = 'MEU PLANO: Em análise.';
        
      } else if ($situacaopag == '3') {
        
        $datavenci = "Sua assinatura vence: <a href='javascript:void(0);'>".$data_vence.'</a><br> <b>Seu Plano:</b> '.$nomeplano;
        
      } else if ($situacaopag == '6') {
        
        $datavenci = "MEU PLANO: Sua assinatura foi cancelada. Pois seu pagamento foi extornado.";
        
      } else if ($situacaopag == '7') {
        
        $datavenci = "MEU PLANO: Sua assinatura foi cancelada, ou expirou o perido de ativação.";
        
      } else if ($situacaopag == '9') {
        
        $datavenci = "MEU PLANO: Sua assinatura foi expirada. <a href='ativacaomensal.php?idSessao={ID_SESSAO}'>Clique aqui para reativa-la.</a><br><b>Seu Plano:</b> ".$nomeplano;
        
      } else if ($situacaopag == '') {
        
        $datavenci = "MEU PLANO: Você ainda não é um assinante!";
        
      }
      
      
      return $datavenci;
    }
    
    function assinaturaUsuarioTAG($bd,$seq) {
      $sql = new Query ($bd);
      $txt = "SELECT LAST_INSERT_ID(CSITPAGPLAN) CSITPAGPLAN FROM TREDE_PAGAPLANO
				WHERE NIDUPAGPLAN = :seq		
				ORDER BY 1 DESC				
				LIMIT 1";
      $sql->addPAram(':seq',$seq);
      $sql->executeQuery($txt);
      
      $csitpagplan = $sql->result("CSITPAGPLAN");
      
      if ($csitpagplan != '') {
        $datavenci = "S";
      } else {
        $datavenci = "N";
      }
      return $datavenci;
    }
  
    function RetonaCarneBol($seq) {
      $sql = new Query ();
      $txt = "SELECT CCARNPLANO
			   FROM TREDE_PLANOS
		      WHERE NIDUSUCASH = :seq";
      $sql->addPAram(':seq',$seq);
      $sql->executeQuery($txt);
    
      $valorcash = $sql->result("CCARNPLANO");
      
      return $valorcash;
    }
    
    function RetornaValorCashBackUsuario($bd,$seq) {
      $sql = new Query ($bd);
      $txt = "SELECT  VVALUSCASH
			   FROM TREDE_CASHBACK_USU
		      WHERE NIDUSUCASH = :seq";
      $sql->addPAram(':seq',$seq);
      $sql->executeQuery($txt);
      
      $valorcash = $sql->result("VVALUSCASH");
      return $valorcash;
    }
    
    function RetornaNomeEmpresa($bd,$seq) {
      $sql = new Query ($bd);
      $txt = "SELECT VNOMECREDCRE
			  FROM TREDE_CREDENCIADOS
			 WHERE SEQUENCIACRE = :seq ";
      $sql->addParam(':seq',$seq);
      $sql->executeQuery($txt);
      
      $nomecat = $sql->result("VNOMECREDCRE");
      
      return $nomecat;
      
    }
    
    
    function RetornaNomeRede($bd,$seq) {
      $sql = new Query ($bd);
      $txt = "SELECT  VNOMECREDCRE
			   FROM TREDE_CREDENCIADOS
		      WHERE SEQUENCIACRE = :seq";
      $sql->addPAram(':seq',$seq);
      $sql->executeQuery($txt);
      
      $nomerede = $sql->result("VNOMECREDCRE");
      
      return $nomerede;
      
    }
    
    
    function RetornaNomeCategoria($bd,$seq) {
      $sql1 = new Query ($bd);
      $txt1 = "SELECT VNOMECATECAT
			  FROM TREDE_CATEGORIAS 
			 WHERE NNUMECATECAT = :seq
			   AND VSITUCATECAT = 'a' ";
      $sql1->addPAram(':seq',$seq);
      $sql1->executeQuery($txt1);
      
      $nomecat = $sql1->result("VNOMECATECAT");
      
      return $nomecat;
      
    }
    
    
    function RetornaNomeSubCategoria($bd,$id) {
      $sql = new Query ($bd);
      $txt = "SELECT VNOMECATESUB
			  FROM TREDE_SUBCATEGORIA 
			 WHERE NNUMECATESUB = :id";
      $sql->addPAram(':id',$id);
      $sql->executeQuery($txt);
      
      $nomescat = $sql->result("VNOMECATESUB");
      
      return $nomescat;
      
    }
    
    function RetonaTipoUsuario($seq) {
      
      if ($seq == 'm') {
        $tipo = "Marido";
      } else if ($seq == 'e') {
        $tipo = "Esposa";
      } else if ($seq == 'f') {
        $tipo = "Filha(o)";
      } else if ($seq == 'n') {
        $tipo = "Neta(o)";
      } else if ($seq == 'p') {
        $tipo = "Pai";
      } else if ($seq == 'a') {
        $tipo = "Mãe";
      } else if ($seq == 'i') {
        $tipo = "Irmão(ã)";
      } else if ($seq == 's') {
        $tipo = "Sogro(a)";
      }
      return $tipo;
    }
    
    
    function RetonaNomeUsuarioPorSeq($bd,$seq) {
      $sql = new Query ($bd);
      $txt = "SELECT REDE_NOMEUSU
			  FROM TREDE_USUADMIN 
			 WHERE REDE_SEQUSUA = :seq";
      $sql->addPAram(':seq',$seq);
      $sql->executeQuery($txt);
      
      $nomeusua = $sql->result("REDE_NOMEUSU");
      
      return $nomeusua;
    }
    
    function RetonaLoginPorSeq($bd,$seq) {
      $sql = new Query ($bd);
      $txt = "SELECT REDE_LOGUSUA
			  FROM TREDE_USUADMIN 
			 WHERE REDE_SEQUSUA = :seq";
      $sql->addPAram(':seq',$seq);
      $sql->executeQuery($txt);
      
      $nomeusua = $sql->result("REDE_LOGUSUA");
      
      return $nomeusua;
    }
    
    function RetonaCpfUsuario($bd,$seq) {
      $sql = new Query ($bd);
      $txt = "SELECT REDE_CPFUSUA
			  FROM TREDE_USUADMIN 
			 WHERE REDE_SEQUSUA = :seq";
      $sql->addPAram(':seq',$seq);
      $sql->executeQuery($txt);
      
      $cpf = $sql->result("REDE_CPFUSUA");
      
      return $cpf;
    }
    
    
    function RetonaNomeRegiao($bd,$id) {
      $sql = new Query ($bd);
      $txt = "SELECT VNOMEREGIREG
			  FROM TREDE_REGIAO
			 WHERE NNUMEREGIREG  = :id";
      $sql->addPAram(':id',$id);
      $sql->executeQuery($txt);
      
      $resultado = strtoupper($sql->result("VNOMEREGIREG"));
      
      return $resultado;
    }
    
    function RetonaNomeEstado($bd,$id) {
      $sql = new Query ($bd);
      $txt = "SELECT VNOMEESTAEST
			  FROM TREDE_ESTADO
			 WHERE CESTADOUFEST = :id";
      $sql->addPAram(':id',$id);
      $sql->executeQuery($txt);
      
      $resultado = strtoupper($sql->result("VNOMEESTAEST"));
      
      return $resultado;
    }
    
    function RetonaNomeCidade($bd,$id) {
      $sql = new Query ($bd);
      $txt = "SELECT VNOMECIDAMUN
			 FROM TREDE_MUNICIPIO
			WHERE NNUMEIBGEMUN = :id";
      $sql->addPAram(':id',$id);
      $sql->executeQuery($txt);
      
      $resultado = strtoupper($sql->result("VNOMECIDAMUN"));
      
      return $resultado;
    }
    
    function RetonaNomeCategoria($bd,$id) {
      $sql = new Query ($bd);
      $txt = "SELECT NNUMECATECRE
			 FROM TREDE_CREDENCIADOS
			WHERE NNUMECATECRE = :id";
      $sql->addPAram(':id',$id);
      $sql->executeQuery($txt);
      
      $categoria = $sql->result("NNUMECATECRE");
      
      if ($categoria == '1') {
        $resultado = 'ALIMENTOS E BEBIDAS';
      } else if ($categoria == '2') {
        $resultado = 'LAZER';
      } else if ($categoria == '3') {
        $resultado = 'BEM-ESTAR E SAÚDE';
      } else if ($categoria == '4') {
        $resultado = 'EDUCAÇÃO';
      } else if ($categoria == '5') {
        $resultado = 'PRODUTOS E SERVIÇOS';
      }
      
      return $resultado;
    }
    
    function RetornaImagemGeral($bd,$tabela,$condi,$seq) {
      $sql = new Query ($bd);
      $txt = "SELECT IMAGEM
			   FROM ".$tabela."
			   WHERE ".$condi." = :seq";
      $sql->AddParam(':seq',$seq);
      $sql->executeQuery($txt);
      
      $file = '../temp/'.md5(uniqid(rand(),TRUE)).'.jpg';
      
      if ($sql->count() > 0) {
        $foto_usuario = $sql->result("IMAGEM");
        
        if ($foto_usuario <> '') {
          $f = fopen($file,'wb');
          if (!$f)
            $this->Error('Não foi possivel criar o arquivo: '.$file);
          fwrite($f,$foto_usuario,strlen($foto_usuario));
          fclose($f);
          
          return $file;
        } else
          return "../../comum/img/Sem-imagem.jpg";
      }
    }
    
    function RetornaImagemProdutos($bd,$seq) {
      $sql = new Query ($bd);
      $txt = "SELECT IMAGEM
			   FROM TREDE_PRODUTOS
			   WHERE NSEQUPRODU = :seq";
      $sql->AddParam(':seq',$seq);
      $sql->executeQuery($txt);
      
      $file = 'temp/'.md5(uniqid(rand(),TRUE)).'.jpg';
      
      if ($sql->count() > 0) {
        $foto_usuario = $sql->result("IMAGEM");
        
        if ($foto_usuario <> '') {
          $f = fopen($file,'wb');
          if (!$f)
            $this->Error('Não foi possivel criar o arquivo: '.$file);
          fwrite($f,$foto_usuario,strlen($foto_usuario));
          fclose($f);
          
          return $file;
        } else
          return "../comum/img/Sem-imagem.jpg";
      }
    }
    
    function RetornaImagem($bd,$seq) {
      $sql = new Query ($bd);
      $txt = "SELECT VIMAGEMCRCRE
			   FROM TREDE_CREDENCIADOS
			   WHERE SEQUENCIACRE = :seq";
      $sql->AddParam(':seq',$seq);
      $sql->executeQuery($txt);
      
      $file = 'temp/'.md5(uniqid(rand(),TRUE)).'.jpg';
      
      if ($sql->count() > 0) {
        $foto_usuario = $sql->result("VIMAGEMCRCRE");
        
        if ($foto_usuario <> '') {
          $f = fopen($file,'wb');
          if (!$f)
            $this->Error('Não foi possivel criar o arquivo: '.$file);
          fwrite($f,$foto_usuario,strlen($foto_usuario));
          fclose($f);
          
          return $file;
        } else
          return "../comum/img/Sem-imagem.jpg";
      }
    }
    
    
    function inverteData($data) {
      if (count(explode("/",$data)) > 1) {
        return implode("-",array_reverse(explode("/",$data)));
      } else if (count(explode("-",$data)) > 1) {
        return implode("/",array_reverse(explode("-",$data)));
      }
    }
    
    function retirarPontostracosundelinebarra($valor) {
      $valor = trim($valor);
      $valor = str_replace(".","",$valor);
      $valor = str_replace(",","",$valor);
      $valor = str_replace("-","",$valor);
      $valor = str_replace("/","",$valor);
      return $valor;
    }
    
    
    function enviaEmail($para,$para_nome,$assunto,$mensagem) {
      include("MultiSYS_Club_DoutorUltra/SMS/PHPMailer/PHPMailerAutoload.php");
      
      $mail = new PHPMailer();
      $mail->isSMTP();
      $mail->From = 'doutorultra@cb.acesso.in';
      $mail->FromName = 'Club de Benefícios Doutor Ultra';
      
      $mail->Host = 'mail.cb.acesso.in';
      $mail->Port = '587';
      $mail->SMTPAuth = TRUE;
      $mail->Username = 'doutorultra@cb.acesso.in';
      $mail->Password = 'yDX^MXQbbhGm';
      $mail->SMTPSecure = TRUE;
      
      $destinatarios = explode(';',$para);
      
      foreach ($destinatarios as $d) {
        $mail->addAddress($d);
      }
      
      $mail->MsgHtml($mensagem);
      $mail->Subject = $assunto;
      $mail->AltBody = 'Este email s� ser� visualizado em padr�o HTML';
      
      if (!$mail->send()) {
        return '<br>'.utf8_encode(print_r($mail->ErrorInfo,TRUE));
      } else {
        return '1';
      }
    }
    
    /************************************************************************************************************/
    /************************************************************************************************************/
    /************************************************************************************************************/
    /************************************************************************************************************/
    /************************************************************************************************************/
    /************************************************************************************************************/
  
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
  


  
    
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  }

?>