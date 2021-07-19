<?php
session_start();

$id_pedido = $_POST['idPedido'];
$token = $_POST['token'];

$arquivo = file_get_contents("boletos_pacotes_rede/".$id_pedido.'.txt');

$json1 = json_decode($arquivo);

$data['customer_name']      = $json1->customer_name;
$data['customer_document']  = $json1->customer_document;
$data['customer_mail']      = $json1->customer_mail;
$data['customer_phone']     = $json1->customer_phone;
$data['address_line1']      = $json1->address_line1;
$data['address_line2']      = "";
$data['neighborhood']       = $json1->neighborhood;
$data['city']               = $json1->city;
$data['state']              = $json1->state;
$data['zip_code']           = $json1->zip_code;
$data['external_number']    = $json1->external_number;
$data['mail_to_send']       = $json1->mail_to_send;
$data['phone_to_send']      = $json1->phone_to_send;
$data['due_date']           = $json1->due_date;
$data['total_value']        = $json1->total_value;
$data['fine_percent']       = 0;
$data['interest_percent']   = 0;


//$url = "https://stage.api.dotbank.com.br/v1/boleto";
$url = "https://api.dotbank.com.br/v1/boleto";

$json = json_encode($data);

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: Bearer ".$token, "content-type: application/json"));
//curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvc3RhZ2Uud2ViLmRvdGJhbmsuY29tLmJyIiwiYXVkIjoiaHR0cHM6XC9cL3N0YWdlLndlYi5kb3RiYW5rLmNvbS5iciIsImlhdCI6MTYwNDQzMzE4MSwiZXhwIjoxNjY3NTA1MTgxLCJzdWIiOiIzMjEyZjA1Ni0xZGFiLTQ1ZjYtOTU0MC1jMzFmODM0ZjFjNmEiLCJwaWQiOiIzZDYyNDdlOS0yNGZkLTRjYjMtOTFhZC03YjNmYTkyYmEzOTQiLCJhY2NvdW50IjoiMzljMWVkNjMtNTY1OC00ZTE2LTk3NmItOTdiYzk1ODlkNzJjIn0.6U_voTmnFDUARZhuBb9ZtKtrCsWADmQKH9GB2dOfePo", "content-type: application/json"));

$response = curl_exec($curl);

$err = curl_error($curl);
curl_close($curl);

if ($err) {
	echo "CURL Error #:" . $err;
} else {
	echo json_encode($response);
}

?>