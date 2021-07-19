<?php
require_once("comum/autoload_log.php");
$seg->secureSessionStart();
//error_reporting(0);

$bd = new Database();

require_once("comum/layout_log.php");
$tpl->addFile("CONTEUDO", "cadplano.html");

$msg = $_SESSION['msg'];
if($msg == 's'){

}

$idusua_plan = $_SESSION['idusua_plan'];
$tpl->IDUSUA = $_SESSION['idusua_plan'];

$sql7 = new Query ($bd);
$txt7 = "SELECT SEQPLANO,
					CNOMEPLANO,
					CDESCPLANO,
					CTEMPPLANO,
					CPRIMPLANO,
					CSEGUPLANO,
					CTERCPLANO,
					CQUARPLANO,
					VVALPPLANO,
					VVALSPLANO,
					VVALTPLANO,
					MENSAPLANO
			 FROM TREDE_PLANOS
			 ORDER BY CNOMEPLANO ASC";
$sql7->executeQuery($txt7);

while(!$sql7->eof()){
    $tpl->NOMEPLANO = utf8_encode($sql7->result("CNOMEPLANO"));
    $tpl->DESC 		= utf8_encode($sql7->result("CDESCPLANO"));
    $tpl->TEMPO     = $sql7->result("CTEMPPLANO");
    $tpl->IDPLANO 	= $sql7->result("SEQPLANO");
    $tpl->PRECO 	= number_format($sql7->result("VVALTPLANO"),2,',','.');
    $tpl->VALOR 	= $sql7->result("VVALTPLANO");
    $tpl->VALMES 	= number_format($sql7->result("MENSAPLANO"),2,',','.');

    $tpl->block("PLANO");
    $sql7->next();

}

if(isset($_POST['adquirir'])){

    $idplan = $seg->antiInjection($_POST['idplan']);

    $redefinicao = rand(20, 30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
    $sessao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789@#&"), 0, $redefinicao);
    $_SESSION['idSessao'] = $sessao;
    $_SESSION['idUsuario'] = $_SESSION['idusua_plan'];
    $_SESSION['idPlano'] = $idplan;

    //header("Location: pagamentoplano.php?idSessao=".$sessao."&idUsuario=".$_SESSION['idusua_plan']."&idPlano=".$idplan."&f=s");
}

$tpl->show();
$bd->close();
?>