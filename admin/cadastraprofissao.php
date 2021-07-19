<?php
  require_once "comum/autoload.php";
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd  = new Database();
  $seg = new Seguranca();
  
  require_once "comum/layout.php";
  $tpl->addFile("CONTEUDO","cadastraprofissao.html");
  
  if (isset($_SESSION['aut_admin'])) {
    $autenticado           = TRUE;
    $_SESSION['aut_admin'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      $id_sessao = $_SESSION['idSessao_admin'];
      $id_confer = $_GET['idSessao'];
      $id_admin  = $_SESSION['usuaAdmin'];
      
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao_admin'];
      $tpl->ID_ADMIN  = $_SESSION['usuaAdmin'];
      
      
      $sqlP = new Query($bd);
      $txtP = "SELECT NNUMECATECAT, VNOMECATECAT
              FROM TREDE_CATEGORIAS
             WHERE VSITUCATECAT = 'a'";
      $sqlP->executeQuery($txtP);
      
      while (!$sqlP->eof()) {
        $tpl->NNUMECATE  = $sqlP->result("NNUMECATECAT");
        $tpl->NNUMECATE1 = $sqlP->result("NNUMECATECAT");
        $tpl->CNOMECATE  = utf8_encode(strtoupper($sqlP->result("VNOMECATECAT")));
        $tpl->CNOMECATE1 = utf8_encode(strtoupper($sqlP->result("VNOMECATECAT")));
        $tpl->block("CATE_P");
        $tpl->block("CATE_P1");
        $sqlP->next();
      }
      
      
      $sql = new Query($bd);
      $txt = "SELECT NNUMEPROF, CNOMEPROF,NCATEPROF,CSITUPROF
              FROM TREDE_PROFISSAO";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $tpl->NNUMEPROF = $sql->result("NNUMEPROF");
        
        $tpl->CNOMEPROF = utf8_encode(strtoupper($sql->result("CNOMEPROF")));
        
        $ncateprof = $sql->result("NCATEPROF");
        
        $sqlc = new Query ($bd);
        $txtc = "SELECT NNUMECATECAT,VNOMECATECAT
                 FROM TREDE_CATEGORIAS
				        WHERE NNUMECATECAT = :ncateprof";
        $sqlc->addParam(':ncateprof',$ncateprof);
        $sqlc->executeQuery($txtc);
        
        $tpl->NCATEPROF = utf8_encode(strtoupper($sqlc->result("VNOMECATECAT")));
        
        $status = $sql->result("CSITUPROF");
        
        if ($status == 'a') {
          $tpl->CHECK  = 'checked';
          $tpl->STATUS = 'Ativado';
          $tpl->TITU = "Desativar";
          
        } else {
          $tpl->CHECK  = '';
          $tpl->STATUS = 'Desativado';
          $tpl->TITU = "Ativar";
        }
        
        
        $tpl->block("PROF");
        $sql->next();
      }
      
      
      if (isset($_POST['salvar'])) {
        
        $nome = utf8_decode($seg->antiInjection($_POST['nome']));
        $cate = utf8_decode($seg->antiInjection($_POST['categoria']));
        
        $sql1 = new Query ($bd);
        $txt1 = "INSERT INTO TREDE_PROFISSAO (CNOMEPROF,CSITUPROF, NCATEPROF)
															 VALUES 
									   					('".$nome."','a','".$cate."') ";
        $sql1->executeSQL($txt1);
        
        $tpl->MSG = "Cadastrado com Sucesso";
        $tpl->block('SUCESSO');
        
        echo "<script> setTimeout(function () {
                window.location.href = window.location.href;
              }, 3000); </script>";
        
      }
      
      if (isset($_POST['editar'])) {
        
        $nnumeprof  = $seg->antiInjection($_POST['nnumeprof']);
        $cnomeprof  = utf8_decode($seg->antiInjection($_POST['cnomeprof']));
        $ncategoria = $seg->antiInjection($_POST['ncategoria']);
        
        $sql1 = new Query ($bd);
        $txt1 = "UPDATE TREDE_PROFISSAO SET   CNOMEPROF = '".$cnomeprof."',
                                              NCATEPROF = '".$ncategoria."'
                 WHERE NNUMEPROF = '".$nnumeprof."'";
        $sql1->executeSQL($txt1);
        
        $tpl->MSG = "Alterado com Sucesso";
        $tpl->block('SUCESSO');
        
        echo "<script> setTimeout(function () {
                window.location.href = window.location.href;
              }, 3000); </script>";
        
      }
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  $tpl->show();
  $bd->close();
