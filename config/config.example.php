<?php
$produção = true;


if($produção){
    //#acesso ao banco de Dados
	define('HOST', "aurora1.acesso.in");
	define('DB', "cb_drultra_producao");
	define('USER', "acessocb");
	define('PASS', "43#403530fcfb975I86Yy!");
    //echo "Produção";
}else{
    define('HOST', "localhost");
    define('DB', "multiclube");
    define('USER', "root");
    define('PASS', "");
    echo "<div style='position: fixed;'>Teste</div>";
}


