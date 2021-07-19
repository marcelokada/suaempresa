<?php // pagina que recebe o status da venda
session_start();

$token = $_POST['token'];
$email = $_POST['email'];

	$data['token'] = $token;
	$data['email'] = $email;

//$data['token'] = '91490054-c12f-4aac-a118-fc521629304810e6fc9c49edb764b09826d640abc9f7bf39-023b-4994-baf1-ec97e3dc3e10'; //token gerado via conta no pagsegurp (existe um token de teste eum token de produção)
//$data['token'] = 'C842E37AAD194A8DA458FBCE2D101FB2'; //token gerado via conta no pagsegurp (existe um token de teste eum token de produção)
//$data['email'] = 'marcelookada@outlook.com';//email cadastrado no pagseguro
$valor = $_POST['valor'];//valor do pagseguro
//$valor = "12.00";//valor do pagseguro
$nome = $_POST['nome'];//valor do pagseguro
//$nome = "pontoss cred";//valor do pagseguro

$pedido = $_POST['idPedido'];
//$pedido = "r1r5";

$data['currency'] = 'BRL';
$data['itemId1'] = $pedido;
$data['reference'] = $pedido;
$data['itemQuantity1'] = 1;

$data['itemDescription1'] = $nome;

$valor = str_replace(",", ".", $valor);

$data['itemAmount1'] = $valor;

//$url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/checkout'; //teste só acrescentar o .sandbox
$url = 'https://ws.pagseguro.uol.com.br/v2/checkout'; //produção
$data = http_build_query($data);

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
$xml = curl_exec($curl);

curl_close($curl);


var_dump($xml);
//$xml = simplexml_load_string($xml);

$validador = substr($xml, 0, 12);

/*$conta = strlen($xml->code);

if($conta == 32){
	echo "Validado com Sucesso!!!";
}else{
	echo "Houve algum erro ao validar, verifique se os dados estão corretos.";
}*/

if ($validador == '<?xml versio') {
	echo "Validado com Sucesso!!! Seu registro está funcionando tudo Sucesso!";
}
elseif ($validador == 'Unauthorized') {
	echo "Houve algum erro ao validar, verifique se os dados estão corretos.";
}
else {
	echo "Entre em contato com o suporte, algo deu errado ou tente alguns minutos mais tarde!";
}
?>