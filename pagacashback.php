<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	$bd = new Database();
	
	$idusua		= 	$seg->antiInjection($_POST['idusua']);
	$valor		= 	$seg->antiInjection($_POST['valor']);
	$idcart		= 	$seg->antiInjection($_POST['idcart']);
	$idloja		= 	$seg->antiInjection($_POST['idloja']);
$valor_cash_p = $seg->antiInjection($_POST['valor_cash_p']);

										
	$valortotal_cash    = $func->RetornaValorCashBackUsuario($bd,$idusua);	
		
	$valor_atual = $valortotal_cash - $valor;
			
	$sql = new Query ($bd);
	$txt = "INSERT INTO TREDE_CASHBACK_TRANS 
	(DATACSTRANS,VIDCARCARR,VVALCSTRANS,SEQUENCIACRE,REDE_SEQUSUA)
	VALUES
	('".date('Y-m-d H:i:s')."','".$idcart."','".$valor."','".$idloja."','".$idusua."')";
	$sql->executeSQL($txt);
	
	
	$sql1 = new Query ($bd);
	$txt1 = "UPDATE TREDE_CASHBACK_USU SET VVALUSCASH = :valor_atual
			 WHERE NIDUSUCASH = :idusua";
	$sql1->addParam(':valor_atual',$valor_atual);
	$sql1->addParam(':idusua',$idusua);
	$sql1->executeSQL($txt1);
	
	$sql2 = new Query ($bd);
	$txt2 = "SELECT LAST_INSERT_ID(NSEQCSTRANS) NSEQCSTRANS FROM TREDE_CASHBACK_TRANS
			  WHERE SEQUENCIACRE = :seqcred
			    AND VIDCARCARR = :idcart
				AND REDE_SEQUSUA = :idusua
				ORDER BY 1 DESC
				LIMIT 1";
	$sql2->addParam(':seqcred',$idloja);
	$sql2->addParam(':idcart',$idcart);
	$sql2->addParam(':idusua',$idusua);
	$sql2->executeQuery($txt2);
	
	$seqcash = $sql2->result("NSEQCSTRANS");
		
	$sql3 = new Query ($bd);
	$txt3 = "INSERT INTO TREDE_PAGACOMPRA
	(SEQUENCIACRE,VIDCARCARR,NVALORCPAG,NIDUSPAGCOMPRA,DDATAPAGCOMPRA,CSITUPAGCOMPRA,CTIPOPAGCOMPRA,CSITUAPGCOMPRA,IDTRANSCASHCOMPRA,TIPOPAGAMENTOP)
	VALUES
	('".$idloja."','".$idcart."','".$valor."','".$idusua."','".date('Y-m-d H:i:s')."','9','9','f','".$seqcash."','c')";
	$sql3->executeSQL($txt3);
	
	$sql4 = new Query ($bd);
	$txt4 = "INSERT INTO TREDE_CASHBACKREDE 
	(DATACASHREDE,VIDCCASHREDE,VVALCASHREDE,SEQUENCIACRE,IDUSCASHREDE,NSEQCSTRANS)
	VALUES
	('".date('Y-m-d H:i:s')."','".$idcart."','".$valor."','".$idloja."','".$idusua."','".$seqcash."')";
	$sql4->executeSQL($txt4);

$sql3a = new Query ($bd);
$txt3a = "SELECT VALCREDREDE FROM TREDE_CREDITOREDE
			 WHERE SEQUENCIACRE = :idloja ";
$sql3a->addParam(':idloja', $idloja);
$sql3a->executeQuery($txt3a);

$valor_cashrede = $sql3a->result("VALCREDREDE");
$valor_descon_rede = $valor_cashrede - $valor_cash_p;

$sql3 = new Query ($bd);
$txt3 = "UPDATE TREDE_CREDITOREDE SET VALCREDREDE = '" . $valor_descon_rede . "'
			 WHERE SEQUENCIACRE = :idloja ";
$sql3->addParam(':idloja', $idloja);
$sql3->executeSQL($txt3);


$sql3 = new Query ($bd);
$txt3 = "INSERT INTO TESTE (TESTE,TESTE1,TESTE2) VALUES('".$idloja."','".$valor_cashrede."','".$valor_descon_rede."')";
$sql3->addParam(':idloja', $idloja);
//$sql3->executeSQL($txt3);
	
    echo $seqcash;

$bd->close();
?>