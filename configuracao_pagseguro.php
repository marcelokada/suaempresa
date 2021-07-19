<?php

//Necessário testar em dominio com SSL
//define("URL", "https://varasartesanais.com.br/pagseguro/");
define("URL", "https://testechina.varasartesanais.com.br/");

$sandbox = true;
if ($sandbox) {
    //Credenciais do SandBox
    define("EMAIL_PAGSEGURO", "marcelookada@outlook.com");
    define("TOKEN_PAGSEGURO", "C842E37AAD194A8DA458FBCE2D101FB2");
    define("URL_PAGSEGURO", "https://ws.sandbox.pagseguro.uol.com.br/v2/");
    define("SCRIPT_PAGSEGURO", "https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js");
    define("EMAIL_LOJA", "marcelookada@outlook.com");
    define("MOEDA_PAGAMENTO", "BRL");
    define("URL_NOTIFICACAO", "https://varasartesanais.com.br/notifica.html");
} else {
    //Credenciais do PagSeguro
    define("EMAIL_PAGSEGURO", "marcelookada@outlook.com");
    define("TOKEN_PAGSEGURO", "92ef26a8-bf07-4758-a017-b62b325850a797d66f9542aca5321dae0e12f671e763ad57-a548-46d9-8a4b-c459a974efa0");
    define("URL_PAGSEGURO", "https://ws.pagseguro.uol.com.br/v2/");
    define("SCRIPT_PAGSEGURO", "https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js");
    define("EMAIL_LOJA", "marcelookada@outlook.com");
    define("MOEDA_PAGAMENTO", "BRL");
    define("URL_NOTIFICACAO", "https://varasartesanais.com.br/notifica.html");
}