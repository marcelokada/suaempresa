<?php
  session_start();
  //error_reporting(0);
  
  $id_pedido = $_POST['idPedido'];
  
  $token = $_POST['token'];
  //$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9
  //.eyJpc3MiOiJodHRwczpcL1wvc3RhZ2Uud2ViLmRvdGJhbmsuY29tLmJyIiwiYXVkIjoiaHR0cHM6XC9cL3N0YWdlLndlYi5kb3RiYW5rLmNvbS5iciIsImlhdCI6MTYxOTE4MTIxOCwiZXhwIjoxNjgyMjUzMjE4LCJzdWIiOiI4OTNmZDkxMC1kZTlhLTRlNzYtYjkwNS03ODJjOTlkYzMwYTYiLCJwaWQiOiJhZDQ2NzRiOS04MzE2LTQ2OTUtODNmMC0wMmEyYTc0MzdiNGIiLCJhY2NvdW50IjoiNGQxM2M5NWEtNjcwYi00MWMwLWIxNTYtOGVkN2I2YzczMDE1In0.P8V_7fKeCHv5_tp1FvOHWU5LXL3fcSGoYlF_0Wnr1KM";
  
  //$id_pedido = '147-6-0beb65eed13e69800912d15357e4bead';
  $arquivo = file_get_contents("boletos_adesao_mensa/".$id_pedido.'.txt');
  
  //$arquivo = '';
  
  $json1 = json_decode($arquivo);
  
  $eventos['customer_name']         = $json1->customer_name;
  $eventos['customer_document']     = $json1->customer_document;
  $eventos['customer_mail']         = $json1->customer_mail;
  $eventos['customer_phone']        = $json1->customer_phone;
  $eventos['address_line1']         = $json1->address_line1;
  $eventos['address_line2']         = "";
  $eventos['neighborhood']          = $json1->neighborhood;
  $eventos['city']                  = $json1->city;
  $eventos['state']                 = $json1->state;
  $eventos['zip_code']              = $json1->zip_code;
  $eventos['external_number']       = $json1->external_number;
  $eventos['mail_to_send']          = $json1->mail_to_send;
  $eventos['phone_to_send']         = $json1->phone_to_send;
  $eventos['due_date']              = $json1->due_date;
  $eventos['total_value']           = $json1->total_value;
  $eventos['fine_date']             = $json1->fine_date;
  $eventos['fine_value']            = $json1->fine_value;
  $eventos['interest_value']        = $json1->interest_value;
  $eventos['installments_number']   = $json1->installments_number;
  $eventos['carnet_cod']            = $json1->carnet_cod;
  $eventos['boletos']               = $json1->boletos;
  
  $url = "https://stage.api.dotbank.com.br/v1/boleto/carnet";
  //$url = "https://api.dotbank.com.br/v1/boleto/carnet";
  //$url = "https://stage.api.dotbank.com.br/v1/boleto";
  //$url = "https://api.dotbank.com.br/v1/boleto";
  
  $json = json_encode($eventos);
  
  $curl = curl_init($url);
  curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE);
  curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
  curl_setopt($curl,CURLOPT_POST,TRUE);
  curl_setopt($curl,CURLOPT_POSTFIELDS,$json);
  curl_setopt($curl,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
  curl_setopt($curl,CURLOPT_HTTPHEADER,array("authorization: Bearer ".$token, "content-type: application/json"));

  $response = curl_exec($curl);
  
  $err = curl_error($curl);
  curl_close($curl);
  
  if ($err) {
    echo "CURL Error #:".$err;
  } else {
    echo json_encode($response);
  }

?>