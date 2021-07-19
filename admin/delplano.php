<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
//error_reporting(0);

$bd = new Database();

$id		= 	$seg->antiInjection($_POST['id']);
    
    $sql = new Query ($bd);
    $txt = "SELECT SEQPLANO FROM TREDE_PLANOS
		WHERE SEQPLANO = :id";
    $sql->AddParam(':id',$id);
    $sql->executeSQL($txt);
    
    $res = $sql->result("SEQPLANO");
    
    if($res != ""){
        $sql1 = new Query ($bd);
        $txt1 = "UPDATE TREDE_PLANOS SET
		WHERE SEQPLANO = :id";
        $sql1->AddParam(':id',$id);
        $sql1->executeSQL($txt1);
    }else{
        $sql1 = new Query ($bd);
        $txt1 = "DELETE FROM TREDE_PLANOS
		WHERE SEQPLANO = :id";
        $sql1->AddParam(':id',$id);
        $sql1->executeSQL($txt1);
    }

    echo "Removido com sucesso!";

$bd->close();
?>
