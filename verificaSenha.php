<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$id_sessao 	= $_SESSION['idSessao'];
$id_confer 	= $_GET['idSessao'];
$seq 		= $_SESSION['idUsuario'];

if($id_confer != $id_sessao){
	require_once("comum/restrito.html");
	session_destroy();
}else{
	require_once("comum/layout.php");  
	$tpl->addFile("CONTEUDO","config_usuario.html");
	$tpl->ID_SESSAO = $_SESSION['idSessao'];
	
	$seq		= $seg->antiInjection($_POST['seq']);
	$senha		= md5($seg->antiInjection($_POST['senha']));

	$sql = new Query ($bd);
	$txt = "SELECT REDE_SENHAUS,
					REDE_SEQUSUA
					FROM TREDE_USUADMIN
			 WHERE REDE_SEQUSUA = :seq
			   AND REDE_SENHAUS = :senha";
	$sql->addParam(':senha',$senha);
	$sql->addParam(':seq',$seq);
	$sql->executeQuery($txt);
		
	$res = $sql->result("REDE_SEQUSUA");

	if($res <> null){
		echo true;
	}else{
		echo false;
	}

}
$tpl->show(); 
$bd->close();
?>