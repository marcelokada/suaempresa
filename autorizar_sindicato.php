<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  require_once("comum/apagaArquivos.php");
  error_reporting(0);
  
  $bd = new Database();
  $formatar = new Formata();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","autorizar_sindicato.html");
  
  if (isset($_SESSION['aut'])) {
    $autenticado = TRUE;
    $_SESSION['aut'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      
      $id_sessao = $_SESSION['idSessao'];
      $id_confer = $_SESSION['idUsuario'];;
      $seq = $_SESSION['idUsuario'];
      
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao'];
      $tpl->IDUSUA = $_SESSION['idUsuario'];
      
      $idusua = $_SESSION['idUsuario'];
      
      //SELECT PARA VERIFICAR O USUARIO
      $tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
      //SELECT PARA VERIFICAR O USUARIO
      
      $sql = new Query ($bd);
      $txt = "SELECT SEQUPAGPLAN,NSEQPAGPLAN,CSITPAGPLAN,CTIPOPGPLAN,DDTINIPPLAN,DDTFIMPPLAN
			 FROM TREDE_PAGAPLANO
			WHERE NIDUPAGPLAN = :idusua
			ORDER BY DDATPAGPLAN DESC";
      $sql->addParam(':idusua',$seq);
      $sql->executeQuery($txt);
      
      $res_data_inicio = $sql->result("DDTINIPPLAN");
      $res_data_final = $sql->result("DDTFIMPPLAN");
      
      $sql21 = new Query ($bd);
      $txt21 = "SELECT REDE_PLANUSU
			   FROM TREDE_USUADMIN
			  WHERE REDE_SEQUSUA = :usua";
      $sql21->AddParam(':usua',$seq);
      $sql21->executeQuery($txt21);
      
      $res_assi = $sql21->result("REDE_PLANUSU");
      
      
      $sql2 = new Query ($bd);
      $txt2 = "SELECT U.NNUMESIND,
								CNOMESIND,
								CCIDASIND,
								CESTASIND,
       					CSITUSIUS,
       					NNUMESIUS,
       					DINCLSIUS
		   FROM TREDE_SINDICATO_USUA U, TREDE_SINDICATOS S
		  WHERE U.NNUMESIND = S.NNUMESIND
		      AND NNUMEUSUA = :usua";
      $sql2->AddParam(':usua',$seq);
      $sql2->executeQuery($txt2);
      
      while (!$sql2->eof()) {
        $tpl->ID = $sql2->result("NNUMESIUS");
        $tpl->NOMESIND = $sql2->result("CNOMESIND");
        $tpl->CIDA = utf8_encode($sql2->result("CCIDASIND"));
        $tpl->ESTA = $sql2->result("CESTASIND");
        
        $status = $sql2->result("CSITUSIUS");
        
        if ($status == 'a') {
          $tpl->STATUS = '<font color="green">Ativo</font>';
        } else if ($status == 'p') {
          $tpl->STATUS = '<font color="blue">Pendente</font>';
          $tpl->block('AUT');
          $tpl->block('REC');
          
        }
        
        $tpl->DATA = $data->formataData1($sql2->result("DINCLSIUS"));
        
        
        $sql2->next();
      }
      
      if (isset($_POST['autorizar'])) {
        
        $sql3 = new Query ($bd);
        $txt3 = "UPDATE TREDE_SINDICATO_USUA SET CSITUSIUS = 'a'
						WHERE NNUMEUSUA = :usua";
        $sql3->AddParam(':usua',$seq);
        $sql3->executeQuery($txt3);
        
        echo "<script>alert('Autorização Realizada com Sucecsso!'); window.location.href = window.location.href</script>";
      }
      
      if (isset($_POST['recusar'])) {
        
        $sql3 = new Query ($bd);
        $txt3 = "DELETE FROM TREDE_SINDICATO_USUA WHERE NNUMEUSUA = :usua";
        $sql3->AddParam(':usua',$seq);
        $sql3->executeQuery($txt3);
        
        echo "<script>alert('Autorização Recusada com Sucecsso!'); window.location.href = window.location.href</script>";
      }
    }
  } else {
    $seg->verificaSession($_SESSION['idUsuario']);
  }
  
  
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////
  
  $tpl->show();
  $bd->close();
?>