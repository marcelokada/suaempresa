<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  //error_reporting(0);
  
  $bd = new Database();
  
  $idusu = $_POST['idusua'];
  $nome1 = $_POST['nome1'];
  $parentesco1 = $_POST['parentesco1'];
  $grau1 = $_POST['grau1'];
  
  
  $sql21 = new Query($bd);
  $txt21 = "UPDATE TREDE_MEMBROS SET  CNOMEUSUA = '" . $nome1 . "',
																			CTIPOUSUA = '" . $parentesco1 . "',
																			CGRUPUSUA = '" . $grau1 . "'
						WHERE SEQ = '" . $idusu . "' ";
  $sql21->executeSQL($txt21);
  
  echo "Alterado com Sucesso!";
  
  $bd->close();
?>