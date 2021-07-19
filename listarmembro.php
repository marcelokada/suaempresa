<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  //error_reporting(0);
  
  $bd = new Database();
  
  $idusua = $_POST['idusu'];
  //$idusua = '28';
  
  $sql = new Query ($bd);
  $txt = "SELECT      SEQ,
                      CNOMEUSUA,
                      EMAILUSUA,
                      CCPF_USUA,
                      NCEL_USUA,
                      DNASCUSUA,
                      CTIPOUSUA,
                      NNUMETITU,
                      CGRUPUSUA
			    FROM TREDE_MEMBROS
				WHERE SEQ = :idusua";
  $sql->AddParam(':idusua',$idusua);
  $sql->executeQuery($txt);
  
  $resultado = $sql->result("CTIPOUSUA");
  
  $eventos['nome'] = $sql->result("CNOMEUSUA");
  
  $tipos = $sql->result("CTIPOUSUA");
  
       if($tipos == 'm'){ $eventos['tiponome'] = "Marido"; }
  else if($tipos == 'e'){ $eventos['tiponome'] = "Esposa"; }
  else if($tipos == 'f'){ $eventos['tiponome'] = "Filha(o)"; }
  else if($tipos == 'n'){ $eventos['tiponome'] = "Neta(o)"; }
  else if($tipos == 'p'){ $eventos['tiponome'] = "Pai"; }
  else if($tipos == 'a'){ $eventos['tiponome'] = "Mãe"; }
  else if($tipos == 'i'){ $eventos['tiponome'] = "Irmão(ã)";}
  else if($tipos == 's'){ $eventos['tiponome'] = "Sogro(a)"; }
  
  $eventos['tipo'] = $sql->result("CTIPOUSUA");
  
  $grau            = $sql->result("CGRUPUSUA");
  
  if($grau == 'd'){
    $eventos['graunome'] = "Dependente";
  }else if($grau == 'a'){
    $eventos['graunome'] = "Agregado";
  }
  $eventos['grau'] = $sql->result("CGRUPUSUA");
  
  echo json_encode($eventos);
  
  $bd->close();
?>