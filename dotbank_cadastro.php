<?php

/*$data['name'] = "Marcelo Okada 3";
$data['email'] = "marcelookada17@outlook.com";
$data['person_type'] = "physical";
$data['invoice_due_date'] = 2;

$data['photo'] = ['filename' => "",
                    'fileformat' => "",
                    'filesize' => "",
                    'value' => ""];

$data['legal'] = ["cnpj" => "",
                  "ie" => ""];

$data['physical'] = ["cpf" => "21698439067",
                      "rg" => "42496620",
	"date_of_birth" => "1987-11-20",
	"sex" => "true"];

$data['user'] = ["password" => "123mudar",
                 "password_confirmation" => "123mudar"];

$data['addresses'][] = ['street'          => "asdasdas",
											  'number'         => "asdasdas",
											  'city'           => "asdasdas",
											  'state'          => "asdasdas",
											  'zip_code'       => "asdasdas",
											  'complement'     => "asdasdas",
											  'neighborhood'   => "asdasdas"];

$data['telephones'][] = ["type_id" => "afd0b036-625a-3aa8-b639-9dc8c8fff0ff",
												    "ddd"  => "11",
                         "number"  => "123456789"];*/

/*********************************************************/

$data['name'] = $_POST['nome'];
$data['email'] = $_POST['mail'];
$data['person_type'] = "physical";
$data['invoice_due_date'] = 10;

$data['photo'] = ['filename' => "",
	'fileformat' => "",
	'filesize' => "",
	'value' => ""];

$data['legal'] = ["cnpj" => "",
	"ie" => ""];

$datas = $_POST['dnasc'];

$dnasc = substr($datas, 6, 4) . "-" . substr($datas, 3, 2) . "-" . substr($datas, 0, 2);

$data['physical'] = ["cpf" => $_POST['cpf'],
	"rg" => $_POST['rg'],
	"date_of_birth" => $dnasc,
	"sex" => "true"
];

$data['main_user'] = ["password" => $_POST['senhas1'],
	"password_confirmation" => $_POST['senha2']];

$n_cep = str_replace("-", "", $_POST['cep']);;

$data['addresses'][] = ['street'          => $_POST['ende'],
	'number'         => $_POST['nende'],
	'city'           => $_POST['cidade'],
	'state'          => $_POST['estado'],
	'zip_code'       => $n_cep,
	'complement'     => $_POST['comp'],
	'neighborhood'   => $_POST['bairro']];

$celular = $_POST['phone'];

$ddd = substr($celular,0,2);
$cel = substr($celular,2,9);

$data['telephones'][] = ["type_id" => "afd0b036-625a-3aa8-b639-9dc8c8fff0ff",
	"ddd"  => $ddd,
	"number"  => $cel];

//$url = "https://stage.api.dotbank.com.br/v1/person";
$url = "https://api.dotbank.com.br/v1/person";

//$token = $_POST['token_dotbank']; 

$json = json_encode($data);

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
//curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: Bearer ".$token, "content-type: application/json"));
curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvd2ViLmRvdGJhbmsuY29tLmJyIiwiYXVkIjoiaHR0cHM6XC9cL3dlYi5kb3RiYW5rLmNvbS5iciIsImlhdCI6MTYwNTcwNzMwMSwiZXhwIjoxNjY4Nzc5MzAxLCJzdWIiOiI3NDQyMmYyMi02ZWMwLTQ3OWYtOTI5MS0yNDI2MzAwYTA4ODkiLCJwaWQiOiI2MDJlOTA4MC1jMjNlLTQ4YzItOGU2Yi1jYzYyOGNmZDIzMGUiLCJhY2NvdW50IjoiMTkyNTViMzAtYzVkOC00N2ZjLTg5Y2YtZGJmYTdlOWJhOGVhIn0.n9o0a0EVHlf3Q6eeDitwQDPpkFsXoERqZcK9BvD-Pa4", "content-type: application/json"));

$response = curl_exec($curl);

$err = curl_error($curl);
curl_close($curl);

if ($err) {
	echo "CURL Error #:" . $err;
	echo $_POST['nome'];
} else {
	echo $response;
}