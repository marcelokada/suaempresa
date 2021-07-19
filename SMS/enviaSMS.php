<?php  
 
  function somenteNumeros($variavel) {
    $indice = 0;
    $retorno = "";
    while ($indice < strlen($variavel)) {
      if (substr_count("0123456789",substr($variavel,$indice,1)) > 0)
        $retorno = $retorno.substr($variavel,$indice,1);
        
      $indice = $indice + 1;
    }
    
    return $retorno;
  }  
  
  if (isset($_POST['fornecedor'])) {
    $fornecedor = htmlentities($_POST['fornecedor']);    
                      
    if($fornecedor == 'comunika'){
      $ch = curl_init();
      $usuario       = htmlentities($_POST['conta']);
      $senha         = htmlentities($_POST['senha']);
      
      $remetente      = htmlentities($_POST['remetente']);;
      $destinatario  =  somenteNumeros(htmlentities($_POST['para']));
      $agendamento    = ''; 
      $mensagem           = urlencode(htmlentities($_POST['msg']));
      $identificador = htmlentities($_POST['id']);
      $modoTeste      = 0;        
    }    
    else if($fornecedor == 'emitfranquia'){
      //sms.emitfranquia.com.br/api/api_sms.php?codigo=77&token=7d8b8c91cc&textosms=TestedaAPI&numero=99675057
      $ch           = curl_init();
      $codigo       = htmlentities($_POST['codigo']);
      $token        = htmlentities($_POST['token']);
      
      $remetente      = htmlentities($_POST['remetente']);;
      $numero         = somenteNumeros(htmlentities($_POST['numero']));     
      $textosms       = urlencode(htmlentities($_POST['msg']));
      $modoTeste      = 0;        
    }
    else if($fornecedor == 'smsdigital'){
      $ch         = curl_init();   
      $usuario    = htmlentities($_POST['conta']);
      $senha      = htmlentities($_POST['senha']);
      $lote       = somenteNumeros(htmlentities($_POST['id']));
      $celular    = somenteNumeros(htmlentities($_POST['para']));
      $mensagem   = urlencode(htmlentities($_POST['msg']));
      $data       = ' ';
      $hora       = ' ';
      $data_exp   = ' ';
      $hora_exp   = ' ';
      $seu_numero = somenteNumeros(htmlentities($_POST['remetente']));
      $url        = 'http://webservice.smsdigital.com.br/WebService_Asp.asmx?op=SendSms';
      $soap       = '<?xml version="1.0" encoding="utf-8"?>
                    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                      xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
                      xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                      <soap:Body>
                        <SendSms xmlns="http://www.smsdigital.com.br/">
                          <Usuario>'.$usuario.'</Usuario>
                          <PassWord>'.$senha.'</PassWord>
                          <Celular>'.$celular.'</Celular>
                          <Mensagem>'.$mensagem.'</Mensagem>
                        </SendSms>
                      </soap:Body>
                    </soap:Envelope>';
      $cabecalho  = array("Content-type: text/xml",
                          "Content-length: ".strlen($soap)
                          );
      $soap_envio = curl_init();
      
      curl_setopt($soap_envio, CURLOPT_URL, $url);
      curl_setopt($soap_envio, CURLOPT_CONNECTTIMEOUT, 10);
      curl_setopt($soap_envio, CURLOPT_TIMEOUT, 10);
      curl_setopt($soap_envio, CURLOPT_RETURNTRANSFER, true );
      curl_setopt($soap_envio, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($soap_envio, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($soap_envio, CURLOPT_POST, true );
      curl_setopt($soap_envio, CURLOPT_POSTFIELDS, $soap);
      curl_setopt($soap_envio, CURLOPT_HTTPHEADER, $cabecalho);
      
      $resp = curl_exec($soap_envio);
      curl_close($soap_envio);
      
      if($resp === false) {
        $err = 'ERRO: '.curl_error($soap_envio);
        echo $err;
      } 
      else {
      
        $response1 = str_replace("<soap:Body>","",$resp);
        $response2 = str_replace("</soap:Body>","",$response1);

        // convertingc to XML
        $parser = simplexml_load_string($response2);

        $retorno = $parser->SendSmsResponse->SendSmsResult;
        
        if ($retorno == 1)
          echo utf8_decode("Usuário/Senha incorretos.");
        else if ($retorno == 2)
          echo utf8_decode("Celular com números insuficientes.");
        else if ($retorno == 3)
          echo utf8_decode("O campo mensagem esta vazio.");
        else if ($retorno == 4)
          echo utf8_decode("Erro desconhecido na inserção no banco de dados.");
        else if ($retorno == 5)
          echo utf8_decode("Créditos Insuficientes para enviar a mensagem.");
        else if ($retorno == 6)
          echo utf8_decode("Saldo Expirado");
        else
          echo "Mensagem enviada";
      }
    }
    else {
      $conta      = htmlentities($_POST['conta']);
      $senha      = htmlentities($_POST['senha']);
      $para       = somenteNumeros(htmlentities($_POST['para']));
      $msg        = urlencode(htmlentities($_POST['msg']));
      $id         = htmlentities($_POST['id']);
    }
    
    if (isset($_POST['remetente']))
      $remetente  = htmlentities($_POST['remetente']);
    else
      $remetente  = "";
    
    //echo $fornecedor." - ".$conta." - ".$senha." - ".$para." - ".$msg;  

    /* Zenvia */       
    if ($fornecedor == 'zenvia') {
      /* 
        Homologado com usuario Teste:
        conta = soluscomputacao
        senha = MEI3xcUM0s        
      */
      include_once('human/human_gateway_client_api/HumanClientMain.php');
      $sender      = new HumanSimpleSend($conta, $senha);
      
      if (substr($para,1,2) <> '55')
        $para = "55". $para;      
        
      $message     = new HumanSimpleMessage($msg, $para, "_hide");
      $response    = $sender->sendMessage($message);
      
      if ($response->getCode() == '000')
        echo "Mensagem enviada";
      else if ($response->getCode() == '013')
        echo utf8_decode("Telefone não informado e/ou invalido");
      else
        echo "Erro ao enviar mensagem - ".$response->getCode() . " - " . $response->getMessage();
      
      //echo "Mensagem enviada \n Status envio $statusEnvio \n";
      //$response = $sender->queryStatus($id);
      //echo "\nConsultando status da mensagem de id $id  \n";
      //echo $response->getCode() . " - " . $response->getMessage() . "<br />";  
    } 
    /* Torpedus */
    else if ($fornecedor == 'torpedus..') {
      echo '<script type="text/javascript">
              window.location.href="http://torpedus.com.br/sms/index.php?app=webservices&u=1723&p=fiscosaude&ta=pv&to=554333616033&msg=teste de envio de mensagem sms";
            </script>';
    }  
    else if ($fornecedor == 'webapi') {
      /*
        Homologado com usuario da União Médica: ID = NDM5NSw2MDAxOTcsZFc1cFlXOD0
      */
      $endereco = 'http://webapi.mandemensagem.com.br/api/api_fuse_connection.php?fuse=send_msg&id='.$conta.'&from='.$remetente.'&msg='.$msg.'&number='.$para;
      $homepage = file_get_contents($endereco);

      if (strpos($homepage,'FAIL_CHECK_NUMBER') > 0)
        echo "Erro ao enviar mensagem - ".$para;   
      else
        echo "Mensagem enviada";
    }
    else if ($fornecedor == 'emitfranquia') {
      /*
        Homologado com usuario da União Médica: ID = NDM5NSw2MDAxOTcsZFc1cFlXOD0
      '77&token=7d8b8c91cc&textosms=TestedaAPI&numero=99675057'*/
      $endereco = 'http://sms.emitfranquia.com.br/api/api_sms.php?codigo='.$codigo.'&token='.$token.'&textosms='.$textosms.'&numero='.$numero;
      $homepage = file_get_contents($endereco);

      if (strpos($homepage,'FAIL_CHECK_NUMBER') > 0)
        echo "Erro ao enviar mensagem - ".$para;   
      else
        echo "Mensagem enviada";
    }
    else if ($fornecedor == 'nexussms') {
    
      $url = 'http://www.smsnexus.com.br/Home/EnviarSMS/';
      $data = array('login' => $conta, 
                    'senha' => $senha,
                    'contatos' => $para,
                    'mensagem' => $msg,
                    'idGenerico' => '');
      
      $curl = curl_init();
     
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

      // execute and return string (this should be an empty string '')
      $str = curl_exec($curl);

      curl_close($curl);

      if (strpos($str,'OK') > 0)
        echo "Mensagem enviada";
      else
        echo "Erro ao enviar mensagem - ".$para;   
    }
    else if ($fornecedor == 'emitsms') {
    
      $endereco = 'http://centraldesms.com.br/app/modulo/api/index.php?action=sendsms&lgn='.$conta.'&pwd='.$senha.'&msg='.$msg.'&numbers='.$para;
      $homepage = file_get_contents($endereco);
      
      $arr = array();
      $arr = json_decode($homepage,true);
      
      if ($arr['status'] == 0)
        echo "Erro ao enviar mensagem - ".$arr['msg']." - ". $para;   
      else
        echo "Mensagem enviada";
    }
    else if($fornecedor == 'comunika'){
      ///// monta o conteúdo do parâmetro "messages" (não alterar)
      $codedMsg       = $remetente."\t".$destinatario."\t".$agendamento."\t".$mensagem."\t".$identificador;

      ///// configura parâmetros de conexão (não alterar)
      $path           = "/3.0/user_message_send.php";
      $parametros     = $path."?testmode=".$modoTeste."&linesep=0&user=".urlencode($usuario)."&pass=".urlencode($senha)."&messages=".urlencode($codedMsg);
      $url            = "https://cgi2sms.com.br".$parametros;

      ///// realiza a conexão
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

      $result = curl_exec ($ch);
      $result = ($result == ""?$result="1":$result);

      curl_close ($ch); 


      ///// verifica o resultado
      $error      = explode("\n",urldecode($result));
      $error[0]   = (int)trim($error[0]);

      if($error[0] != 0){
          ///// para o caso de um erro genérico
          $errorCode  = $error[0];
      } else {
          ///// para o caso de erro específico
          $errorPhone    = explode(" ",urldecode($error[1]));
          $errorCode  = $errorPhone[0];
      }


      ///// este código é apenas informativo, devendo ser trocado pelo tratamento desejado à resposta
      ///// os códigos de erro, exceto os códigos "1" e "404" podem ser encontrados no manual do CGI2SMS
      ///// para download no painel de controle Comunika
      switch($errorCode) {
          case 0   : $msg = "Mensagem enviada com sucesso"; break;
          case 1   : $msg = "Problemas de conexão"; break;
          case 10  : $msg = "Username e/ou Senha inválido(s)"; break;
          case 11  : $msg = "Parâmetro(s) inválido(s) ou faltando"; break;
          case 12  : $msg = "Número de telefone inválido ou não coberto pelo Comunika"; break;
          case 13  : $msg = "Operadora desativada para envio de mensagens"; break;
          case 14  : $msg = "Usuário não pode enviar mensagens para esta operadora"; break;
          case 15  : $msg = "Créditos insuficientes";    break;
          case 16  : $msg = "Tempo mínimo entre duas requisições em andamento"; break;
          case 17  : $msg = "Permissão negada para a utilização do CGI/Produtos Comunika"; break;
          case 18  : $msg = "Operadora Offline"; break;
          case 19  : $msg = "IP de origem negado"; break;
          case 404 : $msg = "Página não encontrada"; break;
      }

      echo($errorCode." : ".$msg);    
    }
    else if ($fornecedor == 'tww')  {
      $mensagem = substr($mensagem, 0, 160);
      
      $posting = "NumUsu=" . $conta . "&Senha=" . $senha . "&SeuNum=" . $id . "&Celular=55" . $para . "&Mensagem=".urlencode($mensagem);
      $postlength = strlen($posting);
      
      $ktstring = "POST /reluzcap/wsreluzcap.asmx/EnviaSMS HTTP/1.1\r\n";
      $ktstring .= "Host: webservices.twwwireless.com.br\r\n";
      $ktstring .= "Content-Length: $postlength\r\n";
      $ktstring .= "Content-Type: application/x-www-form-urlencoded\r\n";
      $ktstring .= "Connection: Close\r\n\r\n";
      
      
      $fp = fsockopen ("ssl://webservices.twwwireless.com.br", 443, $errno, $errstr, 30);
      if (!$fp)
        echo "$errstr ($errno)<br>\n";
      else {
        fputs ($fp, $ktstring);
        fputs ($fp, $posting );
        $buffer = "";
        
        while (!feof($fp)){ 
          $buffer .= fgets ($fp,1024);
        }
        fclose ($fp);
        echo "SMS Aceito. Resultado: ".$buffer;
      }
    }
  }  
?>  