<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
error_reporting(0);

$bd = new Database();


$id_sessao_c 	    = $_GET['idSessao'];
$id_sessao_s 	    = $_SESSION['idSessao'];

$idrede			= $_SESSION['idRede'];
$seg->verificaSession($id_sessao_s);

	require_once("comum/layout.php"); 
	$tpl->addFile("CONTEUDO","produtoscadastrados.html");
	$tpl->ID_SESSAO = $_SESSION['idSessao'];

	$tpl->ID_REDE 	= $_SESSION['idRede'];

if(isset($_GET['idmsg'])) {
	$tpl->ID_MSG = $_GET['idMsg'];
}
	$idmsg 			= $_GET['idMsg'];

	$sql1 = new Query($bd);
	$txt1 = "SELECT NSEQUPRODU,
				  SEQUENCIACRE,
				  VNOMEPRODU,
				  VDESCPRODU,
				  VVALOPRODU,
				  VCASHPRODU,
				  CSITUPRODU,
				  NQTDEPRODU,
				  IMAGEM,
				  DDATAPRODU,
				  VFECHACARR,
				  CIMAGPRODU				  
		  FROM TREDE_PRODUTOS
	     WHERE SEQUENCIACRE = :seqcre
		 ORDER BY SEQUENCIACRE DESC";
	$sql1->addParam(':seqcre',$idrede);
	$sql1->executeQuery($txt1);

while(!$sql1->eof()){
	$seqprodu			= $sql1->result("NSEQUPRODU");
	$tpl->SEQ			= $sql1->result("NSEQUPRODU");
	$tpl->NOMEPRODUTO	= ucwords(utf8_encode($sql1->result("VNOMEPRODU")));
	$tpl->DESC 			= ucwords(utf8_encode($sql1->result("VDESCPRODU")));
	$tpl->VALOR 		= $formata->formataNumero($sql1->result("VVALOPRODU"));
	//$tpl->IMAGEM	 	= $func->RetornaImagemProdutos($bd,$seqprodu);	
	$imagem				= $sql1->result("CIMAGPRODU");
	
	if(($imagem == null) and (substr($imagem,0,7) != 'uploads')){
		$tpl->IMAGEM 		= '../comum/img/Sem-imagem.jpg';
	}else{
		$tpl->IMAGEM 		= $imagem; 
	}
					
	$tpl->CASH	 		= $sql1->result("VCASHPRODU");
	$status	 			= $sql1->result("CSITUPRODU");
	
	if($status == 'a'){
		$tpl->COR = "";
		$tpl->CHK = "checked";
		$tpl->ATIV = "desativar";
	}else{
		$tpl->COR = "alert alert-danger";
		$tpl->CHK = "";
		$tpl->ATIV = "ativar";
	}
	
	$tpl->block("CADAREDE");	
	$sql1->next();
}

$tpl->block("REDE1");

if(isset($_POST['alterar'])){
	$idprodu	= 	$seg->antiInjection($_POST['id']);
	$nome		= 	utf8_decode($seg->antiInjection($_POST['nomes']));
	$desc		= 	utf8_decode($seg->antiInjection($_POST['desc']));
	$valor		= 	$seg->antiInjection($_POST['valor']);
		
	$valor		= 	str_replace(".","",$valor);
	$valor		= 	str_replace(",",".",$valor);

	
	$imagem = $_FILES['imagem'];

	$extensao = pathinfo($imagem['name'], PATHINFO_EXTENSION);
	$novonome = md5(date('YmdHis'));
		
	$nome_arquivo = $novonome.'.'.$extensao;

	if($imagem['name'] == ""){

		$sql1 = new Query ($bd);
		$txt1 = "UPDATE TREDE_PRODUTOS SET
						VNOMEPRODU = :nome,
						VDESCPRODU = :desc,
						VVALOPRODU = :valor
				WHERE NSEQUPRODU = :idprodu";
		$sql1->addParam(':nome',$nome);
		$sql1->addParam(':desc',$desc);
		$sql1->addParam(':valor',$valor);
		$sql1->addParam(':idprodu',$idprodu);
		$sql1->executeSQL($txt1);

		echo "<script>window.location.href=window.location.href;</script>";
		$_SESSION['msg'] = 's';
	}else{

	$_SESSION['nome_arquivo'] = $nome_arquivo;
	
	$conteudo = $nome_arquivo;	

	if ($util->validaExtensaoArquivo($nome_arquivo,array('jpg','png','gif','jpeg')) == '') {
    $path = getcwd();
	
    $dir = "uploads/";
   	$dir = $util->criaDiretorio($dir);

	$dirimg = 'uploads/img/';

    if (isset($imagem)) {      
        ini_set("max_execution_time", 240);
        		
		move_uploaded_file($imagem['tmp_name'],$dir."/img/".$novonome.'.'.$extensao);
			
        if ((file_exists($dir."/".$nome_arquivo)) and ($nome_arquivo <> '')) {      

          libxml_use_internal_errors(true); 

          $objDom = new DomDocument('1.0','ISO-8859-1'); 
          $objDom->load($dir."/".$nome_arquivo);   
        
          $erros = new DOMDocument('1.0','UTF-8');
          $erros->preserveWhiteSpace = false;
          $erros->formatOutput = true;       
          $root = $erros->createElement('erros');
        
          chdir($path);
		}
	}
		$sql1 = new Query ($bd);
		$txt1 = "UPDATE TREDE_PRODUTOS SET
						VNOMEPRODU = :nome,
						VDESCPRODU = :desc,
						VVALOPRODU = :valor,
						CIMAGPRODU = :cima
				WHERE NSEQUPRODU = :idprodu";
		$sql1->addParam(':nome',$nome); 
		$sql1->addParam(':desc',$desc); 
		$sql1->addParam(':cima',$dirimg.$nome_arquivo); 
		$sql1->addParam(':valor',$valor);
		$sql1->addParam(':idprodu',$idprodu); 
		$sql1->executeSQL($txt1); 


	$_SESSION['msg'] = 's';   
		
	//header("Location: produtoscadastrados.php?idSessao=".$_GET['idSessao']."&idRede=".$_GET['idRede']."&idMsg=".$_SESSION['msg']);
	
	
	}else{
		//echo "<script> alert('Extensão da imagem errada ou arquivo inválido!')</script>";
	}
	//echo "<script>window.location.href=window.location.href;</script>";
}
}

$tpl->ID_SESSAO = $_GET['idSessao'];

$tpl->show(); 
$bd->close();
?>