<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  error_reporting(0);
  
  $bd = new Database();
  
  $idPedido = $seg->antiInjection($_POST['idPedido']);
  //$idPedido = 'p6p700';
  
  $sql = new Query ($bd);
  $txt = "SELECT ADESAOPLANO,MENSAPLANO,CTIPOTRPLAN,DVENCBOPLAN
			    FROM TREDE_PAGAPLANO
				WHERE IDPGSEGPLAN = :id";
  $sql->AddParam(':id',$idPedido);
  $sql->executeQuery($txt);
  
  $tipo_plan = $sql->result("CTIPOTRPLAN");
  $venci_bol = $sql->result("DVENCBOPLAN");
  
  
  if ($tipo_plan == 'a') {
    $valor = $sql->result("ADESAOPLANO");
  } else if ($tipo_plan == 'm') {
    $valor = $sql->result("MENSAPLANO");
  }
  
  //$data_vencimento = date('Y-m-d', strtotime('+1 days', strtotime($venci_bol)));
  
  $data_ver = strtotime($data_vencimento);
  $data_atu = strtotime(date('Y-m-d'));
  
/*  if($data_atu > $data_ver){
    $data_vencimento = date('Y-m-d');
  }else{
    $data_vencimento = date('Y-m-d', strtotime('+1 days', strtotime($venci_bol)));
  }*/
  
  $eventos['data_venc'] = date('Y-m-d');
  
/*  $diferenca = strtotime(date('Y-m-d')) - strtotime($venci_bol);
  
  $dias = floor($diferenca / (60 * 60 * 24));
  
  $valor_juros_venc = $valor * $juros_venc / 100;
  
  $juros_por_dia = 0;
  
  for ($i = 0; $i < $dias; $i++) {
    $juros_por_dia += $valor * $juros_dia / 100;
  }
  
  $valor_total_mais_juros = $juros_por_dia + $valor_juros_venc;
  
  $eventos['valor_juros'] = $valor_total_mais_juros;*/
  
  $sql6 = new Query($bd);
  $txt6 = "SELECT TOKEN FROM TREDE_DOTBANK";
  $sql6->executeQuery($txt6);
  
  $eventos['token_dotbank'] = $sql6->result("TOKEN");
  
  echo json_encode($eventos);
  
  $bd->close();
?>