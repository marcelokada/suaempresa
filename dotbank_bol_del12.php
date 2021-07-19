<?php
  require_once("comum/autoload.php");
  session_start();
  error_reporting(0);
  
  $bd = new Database();
  
  $id_dotbank = $_POST['idPedido'];
  
  $sql = new Query($bd);
  $txt = "DELETE FROM TREDE_PAGAPLANO
          WHERE IDPGSEGPLAN = '".$id_dotbank."'";
  $sql->executeSQL($txt);


  $sql1 = new Query($bd);
  $txt1 = "DELETE FROM TREDE_AFILIADOS_BONUS_MENSAL
          WHERE IDPGSEGPLAN = '".$id_dotbank."'";
  $sql1->executeSQL($txt1);


  $sql2 = new Query($bd);
  $txt2 = "DELETE FROM TREDE_TRANSAOCAO_USUA
          WHERE IDPGSEGPLAN = '".$id_dotbank."'";
  $sql2->executeSQL($txt2);

  
  $bd->close();
?>