<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  require_once("comum/apagaArquivos.php");
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","rel_usua_niveis.html");
  
  if (isset($_SESSION['aut'])) {
    $autenticado = TRUE;
    $_SESSION['aut'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
  
    $seg->verificaSession($_SESSION['idUsuario']);
    
    if ($autenticado == TRUE) {
      
      $id_sessao = $_SESSION['idSessao'];
      $id_confer = $_GET['idSessao'];
      $seq = $_SESSION['idUsuario'];
      
      $seg->verificaSession($id_sessao);
      $tpl->ID_SESSAO = $_SESSION['idSessao'];
      
      //SELECT PARA VERIFICAR O USUARIO
      $tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
      //SELECT PARA VERIFICAR O USUARIO
      
      //CASHBACK USUARIO
      $valortotal_cash = $func->RetornaValorCashBackUsuario($bd,$seq);
      $tpl->MEUCASH = $formata->formataNumero($valortotal_cash);
      
      $valortotal_bonus = $func->RetornaValorBonusUsuario($seq);
      $tpl->MEUBONUS = $formata->formataNumero($valortotal_bonus);
      
      $valortotal_voucher = $func->RetornaValorVoucherUsuario($seq);
      $tpl->MEUVOUCHER = $formata->formataNumero($valortotal_voucher);
      
      $tpl->MEUPLANO = $func->assinaturaUsuarioMimo($bd,$seq);
      //CASHBACK USUARIO
      
      
      $func->AtualizaStatusUsuario($seq);
      
      $sql21 = new Query ($bd);
      $txt21 = "SELECT REDE_PLANUSU
			   FROM TREDE_USUADMIN
			  WHERE REDE_SEQUSUA = :usua
create table TREDE_USUADMIN
(
	REDE_PLANUSU int null,
	REDE_SEQUSUA int null
);

";
      $sql21->AddParam(':usua',$seq);
      $sql21->executeQuery($txt21);
      
      $res_assi = $sql21->result("REDE_PLANUSU");
      
      $sql22 = new Query ($bd);
      $txt22 = "SELECT DDTFIMPPLAN
			   FROM TREDE_PAGAPLANO
			  WHERE NIDUPAGPLAN = :usua";
      $sql22->AddParam(':usua',$seq);
      $sql22->executeQuery($txt22);
      
      $data_vence = $data->formataData1($sql22->result("DDTFIMPPLAN"));
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT NIDUSNIVE,NNUMENIVE FROM TREDE_NIVEL
		WHERE NNUMEFILI  = :sequsu
		ORDER BY 1";
      $sql1->addParam(':sequsu',$seq);
      $sql1->executeQuery($txt1);
      
      while (!$sql1->eof()) {
        $id1 = $sql1->result("NIDUSNIVE");
        $tpl->ID1 = $sql1->result("NIDUSNIVE");
        $nome = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$id1)));
        $tpl->NOME1 = $nome;
        
        $sql2 = new Query($bd);
        $txt2 = "SELECT NIDUSNIVE,NNUMENIVE FROM TREDE_NIVEL
		WHERE NNUMEFILI  = :sequsu
		ORDER BY 1";
        $sql2->addParam(':sequsu',$id1);
        $sql2->executeQuery($txt2);
        
        while (!$sql2->eof()) {
          $id2 = $sql2->result("NIDUSNIVE");
          $tpl->ID2 = $sql2->result("NIDUSNIVE");
          $nome2 = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$id2)));
          $tpl->NOME2 = $nome2;
          
          $sql3 = new Query($bd);
          $txt3 = "SELECT NIDUSNIVE,NNUMENIVE FROM TREDE_NIVEL
		        WHERE NNUMEFILI  = :sequsu
		        ORDER BY 1";
          $sql3->addParam(':sequsu',$id2);
          $sql3->executeQuery($txt3);
          
          while (!$sql3->eof()) {
            $id3 = $sql3->result("NIDUSNIVE");
            $tpl->ID3 = $sql3->result("NIDUSNIVE");
            $nome3 = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$id3)));
            $tpl->NOME3 = $nome3;
            
            $sql4 = new Query($bd);
            $txt4 = "SELECT NIDUSNIVE,NNUMENIVE FROM TREDE_NIVEL
		        WHERE NNUMEFILI  = :sequsu
		        ORDER BY 1";
            $sql4->addParam(':sequsu',$id3);
            $sql4->executeQuery($txt3);
            
            while (!$sql4->eof()) {
              $id4 = $sql4->result("NIDUSNIVE");
              $tpl->ID4 = $sql4->result("NIDUSNIVE");
              $nome4 = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$id4)));
              $tpl->NOME4 = $nome4;
              
              $tpl->block("NIVEIS4");
              $sql4->next();
            }
            
            $tpl->block("NIVEIS3");
            $sql3->next();
          }
          
          $tpl->block("NIVEIS2");
          $sql2->next();
        }
        
        $tpl->block("NIVEIS1");
        $sql1->next();
      }
    }
  } else {
    $seg->verificaSession($_SESSION['idUsuario']);
  }
  
  $tpl->show();
  $bd->close();
?>