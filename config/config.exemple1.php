<?php
$produção = true;


if($produção){
    //#acesso ao banco de Dados
    define('HOST',"localhost");
    define('DB',"club9875_doutorultra");
    define('USER',"club9875_doutorultra");
    define('PASS',"C8mxKTm0gjNm");
    //echo "Produção";
}else{
    define('HOST', "localhost");
    define('DB', "multiclube");
    define('USER', "root");
    define('PASS', "");
    echo "<div style='position: fixed;'>Teste</div>";
}


