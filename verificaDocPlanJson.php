<?php
  require_once("comum/autoload.php");
  session_start();
  error_reporting(0);
  
  $idPedido = $_POST['idPedido'];
  //$idPedido = 'p6p700';
  
  $arquivo = file_get_contents("boletos_adesao_mensa/".$idPedido.'.txt');
  
  $json1 = json_decode($arquivo);
  
  $eventos['customer_name']      = $json1->customer_name;
  $eventos['customer_document']  = $json1->customer_document;
  $eventos['customer_mail']      = $json1->customer_mail;
  $eventos['customer_phone']     = $json1->customer_phone;
  $eventos['address_line1']      = $json1->address_line1;
  $eventos['address_line2']      = "";
  $eventos['neighborhood']       = $json1->neighborhood;
  $eventos['city']               = $json1->city;
  $eventos['state']              = $json1->state;
  $eventos['zip_code']           = $json1->zip_code;
  $eventos['external_number']    = $json1->external_number;
  $eventos['mail_to_send']       = $json1->mail_to_send;
  $eventos['phone_to_send']      = $json1->phone_to_send;
  $eventos['due_date']           = $json1->due_date;
  $eventos['total_value']        = $json1->total_value;
  $eventos['fine_date']          = $json1->fine_date;
  $eventos['fine_percent']       = $json1->fine_percent;
  $eventos['interest_percent']   = $json1->interest_percent;
  
  echo json_encode($eventos);
  
?>