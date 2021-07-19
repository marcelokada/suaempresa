<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","aut_usua_sind.html");
  
  if (isset($_SESSION['aut_admin'])) {
    $autenticado = TRUE;
    $_SESSION['aut_admin'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      $id_sessao = $_SESSION['idSessao_admin'];
      $id_confer = $_GET['idSessao'];
      $id_admin = $_SESSION['usuaAdmin'];
      
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao_admin'];
      $tpl->ID_ADMIN = $_SESSION['usuaAdmin'];
      
      $nivelusua = $func->RetornaPermissoes_Admin($id_admin);
      
      //if($nivelusua == 'C'){
      //	$tpl->DISABLE = "style='display:none;'";
      //}else if($nivelusua == 'CB'){
      //	$tpl->DISABLE = "style='display:none;'";
      //}else if($nivelusua == 'CCAB'){
      //	$tpl->DISABLE = "style='display:none;'";
      //}
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT NNUMESIUS,
								U.NNUMESIND,
								NNUMEUSUA,
								CSITUSIUS,
								DINCLSIUS,
								CSTATSIUS,
								DCANCSIUS,
								NNUMESIUS,
       					CNOMESIND,
								CCIDASIND,
								CESTASIND
FROM TREDE_SINDICATO_USUA U, TREDE_SINDICATOS S
WHERE U.NNUMESIND = S.NNUMESIND
    AND CSTATSIUS = 'p'";
      $sql1->executeQuery($txt1);
      
      while (!$sql1->eof()) {
        
        $tpl->ID = $sql1->result("NNUMESIUS");
        $tpl->NNUMEUSUA = $sql1->result("NNUMEUSUA");
        $tpl->NNUMESIND = $sql1->result("NNUMESIND");
        $seq_usua = $sql1->result("NNUMEUSUA");
        $tpl->NOMEUSUA = ucwords($func->RetonaNomeUsuarioPorSeq($bd,$sql1->result("NNUMEUSUA")));
        $tpl->NOMESIND = ucwords($sql1->result("CNOMESIND"));
        $tpl->CIDASIND = utf8_encode($sql1->result("CCIDASIND")).' - '.$sql1->result("CESTASIND");
        $tpl->DATA = $data->formataData1($sql1->result("DCANCSIUS"));
        
        $statss = $sql1->result("CSTATSIUS");
        
        if ($statss == 'p') {
          $tpl->STATUS = "<font color='red'>Pendente</font>";
        }
        
        $tpl->block("AUT");
        $sql1->next();
      }
      
      if (isset($_POST['auto'])) {
        $usua = $_POST['usua'];
        $sind = $_POST['sind'];
        
        $sql3 = new Query ($bd);
        $txt3 = "DELETE FROM TREDE_SINDICATO_USUA WHERE NNUMEUSUA = :usua";
        $sql3->AddParam(':usua',$usua);
        $sql3->executeSQL($txt3);
        
        $sql2 = new Query ($bd);
        $txt2 = "INSERT INTO TREDE_SINDICATO_USUA_CANCELADOS
						 (NNUMESIND,
							NNUMEUSUA,
							CSITUSIUS,
							DCANCSIUS)
							VALUE
						('".$sind."','".$usua."','c','".date('Y-m-d H:i:s')."')";
        $sql2->executeSQL($txt2);
        
        echo "<script>alert('Autorização Realizada com Sucecsso!'); window.location.href = window.location.href</script>";
        
      }
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  $tpl->show();
  $bd->close();
?>