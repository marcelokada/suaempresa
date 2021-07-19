<?php    // pagina que recebe o status da venda
session_start();

//$data['token'] = 'fd3914dd-db43-425f-a964-84965915fe342d2bf86c48cea5735907760e8bc26ae16f4d-0b80-4c89-ad64-d0580c49efc4'; //token gerado via conta no pagsegurp (existe um token de Teste eum token de produção)
$data['token'] = 'C842E37AAD194A8DA458FBCE2D101FB2'; //token gerado via conta no pagsegurp (existe um token de Teste eum token de produção)
$data['email'] = 'marcelookada@outlook.com';//email cadastrado no pagseguro

//$pedido = $_SESSION['idPedido'];// gerar um codigo unico para o pedido, isso será importante dentro do pagseguro
$pedido_idcar 	= $_POST['idPedido'];// gerar um codigo unico para o pedido, isso será importante dentro do pagseguro

$valor  		= $_POST['valor'];//valor do pagseguro

//$pedido = "15";// gerar um codigo unico para o pedido, isso será importante dentro do pagseguro
//$valor  = "40.00";//valor do pagseguro
//$meses	= "4";//mes do plano

$data['currency'] = 'BRL';
$data['itemId1'] = $pedido_idcar;
$data['reference'] = $pedido_idcar;
$data['itemQuantity1'] = 1;
$data['itemDescription1'] = "ID DO CARRINHO - ".$idcart;

//$valor = $_SESSION['valor'];
$valor = str_replace(",",".",$valor);

$data['itemAmount1'] = $valor; 
//$data['itemAmount1'] = '50.00'; 

$url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/checkout'; //Teste só acrescentar o .sandbox
//$url = 'https://ws.pagseguro.uol.com.br/v2/checkout'; //produção
$data = http_build_query($data);

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
$xml = curl_exec($curl);

curl_close($curl); 

$xml = simplexml_load_string($xml);

echo $xml->code;

?>