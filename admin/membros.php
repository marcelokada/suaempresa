<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
//error_reporting(0);

$bd = new Database();

$id = $seg->antiInjection($_POST['id']);
$id = 20;

$sql = new Query ($bd);
$txt = "SELECT CNOMEUSUA,
								CTIPOUSUA,
								CGRUPUSUA 
FROM TREDE_MEMBROS 
	WHERE NNUMETITU = :id";
$sql->AddParam(':id', $id);
$sql->executeQuery($txt);
$eventos = [];

while(!$sql->eof()){
	$eventos[] = ['nome' => $sql->result("CNOMEUSUA"),
		            'tipo' => $sql->result("CTIPOUSUA"),
		            'grup' => $sql->result("CGRUPUSUA"),];
	$sql->next();
}

/*for($i = 0; $i < 3;$i++){
	$eventos[]['nome'] = $sql->result("CNOMEUSUA");
	$eventos[]['tipo'] = $sql->result("CTIPOUSUA");
	$eventos[]['grup'] = $sql->result("CGRUPUSUA");
}*/
echo json_encode($eventos);

$bd->close();
?>