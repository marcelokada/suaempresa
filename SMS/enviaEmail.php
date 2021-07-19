<?php
  require_once ("../comum/classes/Oracle.class.php");
  require_once ("../comum/classes/Seguranca.class.php");
  require_once ("../comum/classes/Query.class.php");
  require_once ("../comum/classes/Util.class.php");
  require_once 'PHPMailer/PHPMailerAutoload.php';
/*
    $_POST['host'] = 'smtp.solus.inf.br';
    $_POST['usuariosmtp'] = 'angelo@solus.inf.br';
    $_POST['senhasmtp'] = 'solus@123';
    $_POST['porta'] = '587';
    $_POST['de'] = 'angelo@solus.inf.br';
    $_POST['para'] = 'angelomm@gmail.com';
    $_POST['cc'] = ''; 
    $_POST['cco'] = '';
    $_POST['assunto'] = 'Teste';
    $_POST['texto'] = 'Email com anexo';
    $_POST['autenticacao'] = 'tls';
    $_POST['idSaida'] = 6;
    */
  $host          = htmlentities($_POST['host']);
  $usuarioSMTP   = htmlentities($_POST['usuariosmtp']);
  $senhaSMTP     = htmlentities($_POST['senhasmtp']);
  $porta         = htmlentities($_POST['porta']);
  $de            = htmlentities($_POST['de']);
  $nomeRemetente = htmlentities($_POST['nomeRemetente']);
  $para          = htmlentities($_POST['para']);
  $cc            = htmlentities($_POST['cc']);
  $cco           = htmlentities($_POST['cco']);
  $assunto       = htmlentities($_POST['assunto']);
  $texto         = $_POST['texto'];
  $auth          = htmlentities(strToLower($_POST['autenticacao']));
  $idSaida       = htmlentities($_POST['idSaida']);
  $confirmacao   = htmlentities($_POST['confirmacao']);
  $prioridade    = htmlentities($_POST['prioridade']);
     
 $bd = new Oracle(); 
 $mail = new PHPMailer;

  $mail->SMTPDebug = 2;             // Enable verbose debug output
  $mail->Timeout    =   5;

  $mail->isSMTP();                    // Set mailer to use SMTP
  $mail->Host       = $host;          // Specify main and backup SMTP servers
  
  if (($auth == 'tls') or ($auth == 'ssl'))
    $mail->SMTPAuth   = true;         // Enable SMTP authentication
  else    
    $mail->SMTPAuth   = false;        // Enable SMTP authentication
  
  $mail->Username   = $usuarioSMTP;   // SMTP username
  $mail->Password   = $senhaSMTP;     // SMTP password
  $mail->SMTPSecure = false;          // Enable TLS encryption, `ssl` also accepted
  $mail->Port       = $porta;         // TCP port to connect to

  $mail->setFrom($de,$nomeRemetente);
    
  if ($confirmacao == 'S')
    $mail->ConfirmReadingTo = $de;
  
  $mail->Priority = $prioridade;
  
  $destinatarios = explode(';',$para);
   
  foreach ($destinatarios as $d) {
    $mail->addAddress($d);            // Add a recipient   
  }
  
  $destinatariosCC = explode(';',$cc);
   
  foreach ($destinatariosCC as $d) {
    $mail->addCC($d);                 // Add a recipient   
  }

  $destinatariosCCO = explode(';',$cco);
   
  foreach ($destinatariosCCO as $d) {
    $mail->addBCC($d);                // Add a recipient   
  }
  
  $arquivos = array();
  $util = new Util();
  
  if ($idSaida > 0) {
    $sql_anexos = new Query($bd);
    $txt = "SELECT HSSIMAGEM.CIMAGIMAGEM,HSSARQUI.CNOMEARQUI,HSSARQUI.CEXT_ARQUI
              FROM HSSASAID,HSSARQUI,HSSIMAGEM
             WHERE HSSASAID.NNUMESAIDA = :idSaida
               AND HSSASAID.NNUMEARQUI = HSSARQUI.NNUMEARQUI
               AND HSSARQUI.NNUMEARQUI = HSSIMAGEM.NNUMEARQUI
               AND HSSARQUI.CLOCAARQUI = 'HSSASAID'";
    $sql_anexos->addParam(":idSaida",$idSaida);
    $sql_anexos->executeQuery($txt);
    $nomeArquivo = '';

    while (!$sql_anexos->eof()) {
      $arquivo = $sql_anexos->result('CNOMEARQUI').".".$sql_anexos->result('CEXT_ARQUI');
			$nomeArquivo = strtr($arquivo, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ","aaaaeeiooouucAAAAEEIOOOUUC_");
      $arquivo = '../temp/'.$nomeArquivo;
      $arquivos[] = $arquivo;
      
      $ponteiro = fopen ($arquivo, "wb"); 
      fwrite($ponteiro,$sql_anexos->result('CIMAGIMAGEM')); 
      fclose ($ponteiro); 
    
      $mail->AddAttachment($arquivo);
            
      $sql_anexos->next();
    }   
  }

  $mail->isHTML(true);                                  // Set email format to HTML

  $mail->Subject = $assunto;
  $mail->Body    = $texto;
  $mail->AltBody = 'Este email só será visualizado em padrão HTML';

  /*
    Não alterar o primeiro caracter, o mesmo é usado para indicar se a mensagem foi ou não enviada.
    Retorno separado por pipe 
    
    Campo 1: Código de retorno
    Campo 2: Mensagem de erro
    
    Códigos de retorno
    1 = Enviado
    2 = Não enviado
  */
  
  if(!$mail->send()) {
    echo '2|'.print_r($mail->ErrorInfo,true);
  } 
  else {
    echo '1|';
  }
  
  //Apaga os arquivos temporarios de anexo
  for ($i=0;$i<sizeOf($arquivos);$i++) {
    unlink($arquivos[$i]);    
  }
  
  $bd->close();
?>