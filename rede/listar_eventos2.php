<?php
  
  require_once("comum/autoload.php");
  session_start();
  //error_reporting(0);
  
  $bd   = new Database();
  $func = new Funcao();
  $data = new Data();
  
  $idloja   = $_POST['idloja'];
  $idevento = $_POST['idevento'];
  
  $sql = new Query ($bd);
  $txt = "SELECT NNUMECALE,
                 CNOMECALE,
                 DINI_CALE,
                 DFIM_CALE,
                 SEQUENCIACRE,
                 REDE_SEQUSUA,
                 CSTATCALE,
                 CCOR_CALE,
                 CSITUCALE,
                 CTIPOCALE,
                 NVALOCALE
            FROM TREDE_CALENDARIO_CRED
			 WHERE SEQUENCIACRE = '".$idloja."'
			  AND NNUMECALE = '".$idevento."' ";
  $sql->executeQuery($txt);
  
  while (!$sql->eof()) {
    $idusua    = $sql->result("REDE_SEQUSUA");
    $nnumecale = $sql->result("NNUMECALE");
    
    $sqla = new Query ($bd);
    $txta = "SELECT NNUMEPCAL,
                   NNUMEUSUA,
                   SEQUENCIACRE,
                   NNUMECALE,
                   DRESEPCAL,
                   STAPGPCAL,
                   CTIPPGCAL,
                   PAGLOPCAL,
                   DCRIAPCAL
            FROM TREDE_PAGACALENDARIO
			 WHERE SEQUENCIACRE = '".$idloja."'
			  AND NNUMECALE = '".$idevento."' ";
    $sqla->executeQuery($txta);
    
    $nnumecale = $sql->result("NNUMECALE");
    $tppago    = $sqla->result("CTIPPGCAL");
    $local     = $sqla->result("PAGLOPCAL");
    
    if ($tppago == 'c') {
      $tipopago = "Cartão";
    } else if ($tppago == 'x') {
      $tipopago = "Transf. ou PIX";
    } else if ($tppago == 'd') {
      $tipopago = "Dinheiro";
    } else if ($tppago == 'p') {
      $tipopago = "PIX";
    }
    
    $eventos[] = [
      "id"        => $sql->result("NNUMECALE"),
      "nome"      => utf8_encode($func->RetornaTipoEvento($sql->result("CTIPOCALE"))),
      "dtini"     => $sql->result("DINI_CALE"),
      "dtend"     => $sql->result("DFIM_CALE"),
      "status"    => $sql->result("CSTATCALE"),
      "idrede"    => $sql->result("SEQUENCIACRE"),
      "situacao"  => $sql->result("CSITUCALE"),
      "obs"       => utf8_encode($sql->result("CNOMECALE")),
      "valor"     => 'R$ '.number_format($sql->result("NVALOCALE"),2,',','.'),
      "pagamento" => $sqla->result("PAGLOPCAL"),
      "tipopgmto" => $tipopago,
      //"color"    => $sql->result("CCOR_CALE"),
    ];
    $sql->next();
  }
  
  
  $sql1 = new Query ($bd);
  $txt1 = "SELECT REDE_CELULAR,
                  REDE_ENDE,
                  REDE_NUM,
                  REDE_EMAILUS,
                  REDE_SEQUSUA,
                  REDE_CEP,
                  REDE_CIDADE,
                  REDE_BAIRRO,
                  REDE_NOMEUSU,
                  FACEBOOK,
                  REDE_ESTADO,
                  INSTAGRAM,
                  REDE_DNASCUS
             FROM TREDE_USUADMIN
			 WHERE REDE_SEQUSUA = '".$idusua."' ";
  $sql1->executeQuery($txt1);
  
  
  if ($sql->count() > 0) {
    $eventos[] = [
      "nomeusua" => utf8_encode(ucwords($sql1->result("REDE_NOMEUSU"))),
      "endeusua" => utf8_encode($sql1->result("REDE_ENDE")).', '.$sql1->result("REDE_NUM").' | '.utf8_encode($sql1->result("REDE_BAIRRO")).' | '.utf8_encode($sql1->result("REDE_CIDADE")).' - '.$sql1->result("REDE_ESTADO"),
      "dnasusua" => $data->formataData1($sql1->result("REDE_DNASCUS")),
      "celuusua" => $sql1->result("REDE_CELULAR"),
      "faceusua" => utf8_encode($sql1->result("FACEBOOK")),
      "instusua" => utf8_encode($sql1->result("INSTAGRAM")),
    ];
  } else {
    $eventos[] = [
      "nomeusua" => 'Não Há Registros',
      "endeusua" => 'Não Há Registros',
      "dnasusua" => 'Não Há Registros',
      "celuusua" => 'Não Há Registros',
      "faceusua" => 'Não Há Registros',
      "instusua" => 'Não Há Registros',
    ];
  }
  
  
  echo json_encode($eventos);
  
  $bd->close();
?>