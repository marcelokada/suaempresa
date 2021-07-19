<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$idusu = $seg->antiInjection($_POST['idusu']);
$idplano = $seg->antiInjection($_POST['idplano']);
$valor = $seg->antiInjection($_POST['valor']);
$pontos = $seg->antiInjection($_POST['pontos']);
$tipopg = $seg->antiInjection($_POST['tipopg']);

$_SESSION['valor'] = $valor;

$sql2 = new Query ($bd);
$txt2 = "INSERT INTO TREDE_PAGAPACOTE (NNUMEREDE,
																		   NNUMEPAC,
																		   SITPAGPAC,
																		   NVALOPPAC,
																		   NPONTPPAC,
																		   TIPOPPPAC,
                              				 DDATAPPAC,
                              				 CSITUAPAC)
			VALUES
			('" . $idusu . "','" . $idplano . "','1','" . $valor . "','".$pontos."','".$tipopg."','".date('Y-m-d H:i:s')."','p')";
$sql2->executeSQL($txt2);


$sql1 = new Query ($bd);
$txt1 = "SELECT LAST_INSERT_ID(NNUMEPPAC) NNUMEPPAC,NNUMEPAC
				   FROM TREDE_PAGAPACOTE
			  WHERE NNUMEREDE = :idusu
			    AND SUBSTR(DDATAPPAC,1,10) = '" . date('Y-m-d') . "'
				ORDER BY NNUMEPPAC DESC
				LIMIT 1";
$sql1->addParam(':idusu', $idusu);
$sql1->executeQuery($txt1);

$res_id_pag = $sql1->result("NNUMEPPAC");
$id_plano = $sql1->result("NNUMEPAC");

$seqplan = 'r' . $id_plano . 'r' . $res_id_pag;

$sql5 = new Query ($bd);
$txt5 = "UPDATE TREDE_PAGAPACOTE SET CIDPGPPAC = :seqplapp
			 WHERE NNUMEPPAC = :res_id_pag";
$sql5->addParam(':res_id_pag', $res_id_pag);
$sql5->addParam(':seqplapp', $seqplan);
$sql5->executeSQL($txt5);

$sql6 = new Query($bd);
$txt6 = "SELECT VEMAILPAGSEG,VTOKENPAGSEG FROM TREDE_PAGSEGURO
				WHERE SEQUENCIACRE = 'a' ";
$sql6->executeQuery($txt6);

$eventos['xxx'] = $seqplan;

$eventos['email'] = $sql6->result("VEMAILPAGSEG");
$eventos['token'] = $sql6->result("VTOKENPAGSEG");

$sql61 = new Query($bd);
$txt61 = "SELECT TOKEN
            FROM TREDE_DOTBANK";
$sql61->executeQuery($txt61);

$eventos['token_dotbank'] = $sql61->result("TOKEN");

$eventos['valor'] = $valor;

echo json_encode($eventos);

$bd->close();
?>