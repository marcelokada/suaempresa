<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart(); 
require_once("comum/apagaArquivos.php");
//error_reporting(0);

	//$tpl = new Template("modal.html");

	$bd = new Database();

	$login	= 	$seg->antiInjection($_POST['email']);
	
	$sql = new Query ($bd);
	$txt = "SELECT REDE_SEQUSUA					
			    FROM TREDE_USUADMIN
				WHERE REDE_EMAILUS = :login";
	$sql->AddParam(':login',$login);
	$sql->executeQuery($txt);
    
	$resultado = $sql->result("REDE_SEQUSUA");

    if($resultado == ''){
        $sql1 = new Query ($bd);
        $txt1 = "SELECT SEQUENCIACRE 					
			    FROM TREDE_CREDENCIADOS
				WHERE VLOGEMAILCRE = :login";
        $sql1->AddParam(':login',$login);
        $sql1->executeQuery($txt1);

        $resultado_cre = $sql1->result("SEQUENCIACRE");

        if($resultado_cre == ''){


	        $sql2 = new Query ($bd);
	        $txt2 = "SELECT NNUMEADMIN FROM TREDE_ADMINS WHERE EMAILADMIN = :login";
	        $sql2->AddParam(':login',$login);
	        $sql2->executeQuery($txt2);

	        $resultado_cre2 = $sql2->result("NNUMEADMIN");


	        if($resultado_cre2 == ''){
		        $eventos['log']		= '0';//liberado
	        }else{
		        $eventos['log']		= $sql2->result("NNUMEADMIN");//nao liberado
	        }

        }else{
            $eventos['log']		= $sql1->result("SEQUENCIACRE");//nao liberado
        }

    }else{
        $eventos['log']		= $sql->result("REDE_SEQUSUA");
    }


echo json_encode($eventos);

$bd->close();
?>