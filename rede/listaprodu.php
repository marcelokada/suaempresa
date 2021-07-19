<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	//$tpl = new Template("modal.html");

	$bd = new Database();

	$id	= 	$seg->antiInjection($_POST['id']);
	
	$sql = new Query ($bd);
	$txt = "SELECT NSEQUPRODU,
				 SEQUENCIACRE,
				 VNOMEPRODU,
				 VDESCPRODU,
				 VVALOPRODU,
				 VCASHPRODU,
				 CSITUPRODU,
				 NQTDEPRODU,
				 DDATAPRODU,
				 CIMAGPRODU
				 FROM TREDE_PRODUTOS
				WHERE NSEQUPRODU = :id";
	$sql->AddParam(':id',$id);
	$sql->executeQuery($txt);
    
	$seqprodu 				= $sql->result("NSEQUPRODU");
	$eventos['id']			= $seqprodu;
	$eventos['nome']		= ucwords(utf8_encode($sql->result("VNOMEPRODU")));
	$eventos['desc']		= ucwords(utf8_encode($sql->result("VDESCPRODU")));
	$eventos['valor']		= $formata->formataNumero($sql->result("VVALOPRODU"));
	$eventos['cash']		= $sql->result("VCASHPRODU");
	// $eventos['img']			= $func->RetornaImagemProdutos($bd,$seqprodu);
	$imagem					= $sql->result("CIMAGPRODU");
	
	
	if(($imagem == null) and (substr($imagem,0,7) != 'uploads')){
		$eventos['img'] 		= '../comum/img/Sem-imagem.jpg';
	}else{
		$eventos['img'] 		= $imagem;
	}
		
	//$eventos['img']			= 'uploads/img/'.$sql->result("CIMAGPRODU");
	
echo json_encode($eventos);

$bd->close();
?>