<?php
  
  require_once("comum/autoload.php");
  session_start();
  //error_reporting(0);
  
  $bd = new Database();
  
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
    $idrede = $sql->result("SEQUENCIACRE");
    
    $eventos[] = [
      "id"       => $sql->result("NNUMECALE"),
      "nome"     => utf8_encode($func->RetornaTipoEvento($sql->result("CTIPOCALE"))),
      "dtini"    => $sql->result("DINI_CALE"),
      "dtend"    => $sql->result("DFIM_CALE"),
      "status"   => $sql->result("CSTATCALE"),
      "idrede"   => $sql->result("SEQUENCIACRE"),
      "situacao" => $sql->result("CSITUCALE"),
      "valor"    => 'R$ '.number_format($sql->result("NVALOCALE"),2,',','.'),
      "obs"      => utf8_encode($sql->result("CNOMECALE")),
      //"color"    => $sql->result("CCOR_CALE"),
    
    ];
    $sql->next();
  }
  
  $sql1 = new Query ($bd);
  $txt1 = "SELECT   SEQUENCIACRE,
                    VNOMECREDCRE,
                    VNOMEENDECRE,
                    NNUMEENDECRE,
                    VNOMEBAIRCRE,
                    VNOMECIDAMUN,
                    CESTADOUFEST,
                    VCNPJJURICRE,
                    FACEBOOK,
                    INSTAGRAM,
                    CTIPOCREDCRE,
                    NCONSELHOCRE,
                    ESTACONSECRE,
                    VLOGEMAILCRE,
                    NNUMETELECRE,
                    NNUMECELUCRE
             FROM TREDE_CREDENCIADOS
			 WHERE SEQUENCIACRE = '".$idrede."' ";
  $sql1->executeQuery($txt1);
  
  
  if ($sql->count() > 0) {
    $eventos[] = [
      "nome_rede" => utf8_encode(ucwords($sql1->result("VNOMECREDCRE"))),
      "dado_rede" => 'CNPJ: '.$sql1->result("VCNPJJURICRE"),
      "cont_rede" => 'Email: '.$sql1->result("VLOGEMAILCRE").' | TEL/CEL: '.$sql1->result("NNUMETELECRE").'/'.$sql1->result("NNUMECELUCRE"),
      "ende_rede" => utf8_encode($sql1->result("VNOMEENDECRE")).', '.$sql1->result("NNUMEENDECRE").' | '.utf8_encode($sql1->result("VNOMEBAIRCRE")).' | '.utf8_encode($sql1->result("VNOMECIDAMUN")).' - '.$sql1->result("CESTADOUFEST"),
      "prof_rede" => 'Especialidade: '.utf8_encode($sql1->result("CTIPOCREDCRE")).' | Nº Conselho: '.$sql1->result("NCONSELHOCRE").' | UF Conselho: '.$sql1->result("ESTACONSECRE"),
    ];
  } else {
    $eventos[] = [
      "nome_rede" => 'Não Há Registros',
      "dado_rede" => 'Não Há Registros',
      "cont_rede" => 'Não Há Registros',
      "ende_rede" => 'Não Há Registros',
      "prof_rede" => 'Não Há Registros',
    ];
  }
  
  
  echo json_encode($eventos);
  
  $bd->close();
?>