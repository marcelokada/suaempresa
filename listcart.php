<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  //error_reporting(0);
  
  $bd = new Database();
  
  $id = $seg->antiInjection($_POST['id']);
  
  $sql = new Query ($bd);
  $txt = "SELECT  NSEQUPRODU,
                    SEQUENCIACRE,
					VNOMEPRODU,     
					VDESCPRODU,  
					VVALOPRODU, 
					VCASHPRODU,  
					CSITUPRODU,  
					NQTDEPRODU,  
					IMAGEM
			    FROM TREDE_PRODUTOS
				WHERE NSEQUPRODU = :id";
  $sql->AddParam(':id',$id);
  $sql->executeQuery($txt);
  
  $nsequprodu = $sql->result("NSEQUPRODU");
  $seqloja    = $sql->result("SEQUENCIACRE");
  
  $sql2c = new Query ($bd);
  $txt2c = "SELECT  SEQUENCIACRE,
					 CLASSIFICCRE
			   FROM TREDE_CREDENCIADOS
		      WHERE SEQUENCIACRE = :idloja";
  $sql2c->addParam(':idloja',$seqloja);
  $sql2c->executeQuery($txt2c);
  
  $class = $sql2c->result("CLASSIFICCRE");
  
  $sql2cc = new Query ($bd);
  $txt2cc = "SELECT NNUMECLASS,CASHBCLASS
			   FROM TREDE_CLASSREDE
		      WHERE NNUMECLASS = :class";
  $sql2cc->addParam(':class',$class);
  $sql2cc->executeQuery($txt2cc);
  
  $cashback = $sql2cc->result("CASHBCLASS");
  
  $eventos['nome'] = ucwords(utf8_encode($sql->result("VNOMEPRODU")));
  
  $valor            = $sql->result("VVALOPRODU");
  $valor            = str_replace(',','.',$valor);
  $eventos['valor'] = $valor;
  
  //antigo metodo de ver cashback
  if (TIPO_PORC_PRODUTO == 'prod') {
    $cs = $sql->result("VCASHPRODU");
  } else if (TIPO_PORC_PRODUTO == 'cred') {
    $cs = $cashback;
  }
  
  $eventos['cash'] = $cs;
  $eventos['qtde'] = $sql->result("NQTDEPRODU");
  
  echo json_encode($eventos);
  
  $bd->close();
?>