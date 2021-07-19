<?php
  require_once("comum/autoload.php");
  session_start();
  //error_reporting(0);
  
  //$tpl = new Template("modal.html");
  
  $bd = new Database();
  
  $idprod = $_GET['idprod'];
  //$idcart	= 	$_GET['idcart'];
  $idloja = $_GET['idloja'];
  
  $nome     = utf8_decode($seg->antiInjection($_POST['nome']));
  $idcart   = $seg->antiInjection($_POST['idcart']);
  $valor    = $seg->antiInjection($_POST['valor']);
  $cashback = $seg->antiInjection($_POST['cashs']);
  $idusua   = $seg->antiInjection($_POST['idusua']);
  
  
  $valor_cash_back = $valor * $cashback / 100;
  $valor_cash_back = str_replace('.','',$valor_cash_back);
  $valor_cash_back = str_replace(',','.',$valor_cash_back);
  
  $sql = new Query ($bd);
  $txt = "SELECT NSEQUPRODU FROM TREDE_CARRINHO
			 WHERE SEQUENCIACRE = :idloja 
				AND VIDCARCARR = :idcart
				AND NSEQUPRODU = :idprod";
  $sql->addParam(':idloja',$idloja);
  $sql->addParam(':idcart',$idcart);
  $sql->addParam(':idprod',$idprod);
  $sql->executeQuery($txt);
  
  $res_produ = $sql->count();
  
  if ($res_produ > 0) {
    echo "<script>alert('Produto já adicionado.')</script>";
  } else {
    
    $sql1 = new Query ($bd);
    $txt1 = "INSERT INTO TREDE_CARRINHO
	(NSEQUPRODU,SEQUENCIACRE,VIDCARCARR,VNOMEPCARR,NVALORCARR,NQUATICARR,NVVALOCARR,VFECHACARR, VVACASCARR,DDATACCARR,REDE_SEQUSUA) 
			VALUES
	('".$idprod."','".$idloja."','".$idcart."','".$nome."','".$valor."','1', '".$valor."','n','".$valor_cash_back."','".date('Y-m-d H:i:s')."','".$idusua."')";
    $sql1->executeSQL($txt1);
    
    echo "Adicionado com sucessso.";
    //echo $cashback;
  }
  
  $bd->close();
?>