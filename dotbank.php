<?php
session_start();

/*$data['customer_name'] = 'marcelo okada';
$data['customer_document'] = "36883489827";

$data['customer_mail'] = "cliente@mailinator.com";
$data['customer_phone'] = "1111111111";

$data['address_line1'] = "Rua das Flores 123";
$data['address_line2'] = "";
$data['neighborhood'] = "Ipanema";
$data['city'] = "Birigui";
$data['state'] = "SP";
$data['due_date'] = "2020/12/20";


$data['zip_code'] = "16200382";
$data['external_number'] = "552";
$data['phone_to_send'] = "14998471702";
$data['mail_to_send'] = "Teste@pteste.com.br";

$data['total_value'] = "15.00";
$data['fine_percent'] = "0";
$data['interest_percent'] = "0";*/

/*********************************************************/

$data['customer_name'][]      = $_POST['customer_name'];
$data['customer_document'][]  = $_POST['customer_document'];
$data['customer_mail'][]      = $_POST['customer_mail'];
$data['customer_phone'][]     = $_POST['customer_phone'];
$numero_ende                = $_POST['numero_ende'];
$data['address_line1'][]     = $_POST['address_line1'].' '.$numero_ende;
$data['address_line2']      = "";
$data['neighborhood']       = $_POST['neighborhood'];
$data['city']               = $_POST['city'];
$data['state']              = $_POST['state'];
$data['zip_code']           = $_POST['zip_code'];
$data['external_number']    = $_POST['external_number'];
$data['mail_to_send']       = $_POST['mail_to_send'];
$data['phone_to_send']      = $_POST['phone_to_send'];
$data['due_date']           = $_POST['due_date'];
$data['total_value']        = $_POST['total_value'];
$data['fine_percent']       = 0;
$data['interest_percent']   = 0;

$url = "https://stage.api.dotbank.com.br/v1/boleto";
//$url = "https://api.dotbank.com.br/v1/boleto";

//$token = $_POST['token_dotbank'];

$json = json_encode($data);

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
//curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: Bearer ".$token, "content-type: application/json"));
//curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvd2ViLmRvdGJhbmsuY29tLmJyIiwiYXVkIjoiaHR0cHM6XC9cL3dlYi5kb3RiYW5rLmNvbS5iciIsImlhdCI6MTYwNTcwNzMwMSwiZXhwIjoxNjY4Nzc5MzAxLCJzdWIiOiI3NDQyMmYyMi02ZWMwLTQ3OWYtOTI5MS0yNDI2MzAwYTA4ODkiLCJwaWQiOiI2MDJlOTA4MC1jMjNlLTQ4YzItOGU2Yi1jYzYyOGNmZDIzMGUiLCJhY2NvdW50IjoiMTkyNTViMzAtYzVkOC00N2ZjLTg5Y2YtZGJmYTdlOWJhOGVhIn0.n9o0a0EVHlf3Q6eeDitwQDPpkFsXoERqZcK9BvD-Pa4", "content-type: application/json"));
curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvc3RhZ2Uud2ViLmRvdGJhbmsuY29tLmJyIiwiYXVkIjoiaHR0cHM6XC9cL3N0YWdlLndlYi5kb3RiYW5rLmNvbS5iciIsImlhdCI6MTYwNDQzMzE4MSwiZXhwIjoxNjY3NTA1MTgxLCJzdWIiOiIzMjEyZjA1Ni0xZGFiLTQ1ZjYtOTU0MC1jMzFmODM0ZjFjNmEiLCJwaWQiOiIzZDYyNDdlOS0yNGZkLTRjYjMtOTFhZC03YjNmYTkyYmEzOTQiLCJhY2NvdW50IjoiMzljMWVkNjMtNTY1OC00ZTE2LTk3NmItOTdiYzk1ODlkNzJjIn0.6U_voTmnFDUARZhuBb9ZtKtrCsWADmQKH9GB2dOfePo", "content-type: application/json"));

$response = curl_exec($curl);

$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo "CURL Error #:" . $err;
} else {
    echo $response;
}