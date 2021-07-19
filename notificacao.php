
<?php
$notificationCode = preg_replace('/[^[:alnum:]-]/','',$_POST["notificationCode"]);

$data['token'] = 'C842E37AAD194A8DA458FBCE2D101FB';//token gerado via conta no pagsegurp (existe um token de Teste eum token de produção)
//$data['token'] = 'fd3914dd-db43-425f-a964-84965915fe342d2bf86c48cea5735907760e8bc26ae16f4d-0b80-4c89-ad64-d0580c49efc4';//token gerado via conta no pagsegurp (existe um token de Teste eum token de produção)
$data['email'] = 'marcelookada@outlook.com';//email cadastrado no pagseguro

$data = http_build_query($data);

$url = 'https://ws.pagseguro.uol.com.br/v3/transactions/notifications/'.$notificationCode.'?'.$data;
//$url = "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/".$notificationCode."?".$data;

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_URL, $url);
$xml = curl_exec($curl);
curl_close($curl);

$xml = simplexml_load_string($xml);

$reference 	= $xml->reference;
$status 	= $xml->status;
$tipopagmto = $xml->paymentmethod->type;


	$sql = new Query ($bd);
	$txt = "UPDATE psi_meuhorario SET situa_pagmto = :pagm WHERE seq_horarios = :seqhora "; 
	$sql->addParam(':pagm',$status);
	$sql->addParam(':seqhora',$reference);
	$sql->executeSQL($txt);
	



?>
