<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  //error_reporting(0);
  
  $bd = new Database();
  
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","relatorios.html");
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
      
      $sql = new Query($bd);
      $sql->clear();
      $txt = "SELECT DISTINCT SUBSTR(DDATPAGPLAN,1,4) DDATPAGPLAN
			FROM TREDE_PAGAPLANO
			GROUP BY DDATPAGPLAN
			ORDER BY 1 DESC";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $tpl->ANO = $sql->result("DDATPAGPLAN");
        $tpl->block("ANO1");
        $sql->next();
      }
      
      $sql = new Query($bd);
      $sql->clear();
      $txt = "SELECT SEQPLANO, CNOMEPLANO
			FROM TREDE_PLANOS
			ORDER BY 2";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $tpl->SEQPLAN = $sql->result("SEQPLANO");
        $tpl->CNOMEPLANO = $sql->result("CNOMEPLANO");
        $tpl->block("PLANOS");
        $sql->next();
      }
      
      $sql = new Query($bd);
      $sql->clear();
      $txt = "SELECT DISTINCT CSITPAGPLAN
			FROM TREDE_PAGAPLANO
			ORDER BY 1";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $tpl->IDSITU = $sql->result("CSITPAGPLAN");
        $tpl->CSITPAGPLAN = $func->RetornaSituaPagamento($sql->result("CSITPAGPLAN"));
        $tpl->block("SITUPAG");
        $sql->next();
      }
      
      $sql = new Query($bd);
      $sql->clear();
      $txt = "SELECT DISTINCT CTIPOPGPLAN
			FROM TREDE_PAGAPLANO
			ORDER BY 1";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $tpl->IDTPAG = $sql->result("CTIPOPGPLAN");
        $tpl->CTIPOPGPLAN = $func->Tipopagamento($sql->result("CTIPOPGPLAN"));
        $tpl->block("TPAG");
        $sql->next();
      }
      
      
      if (isset($_POST['listar'])) {
        
        $mes = $seg->antiInjection($_POST['mes']);
        if ($mes == 'todos') {
          $cond1 = "WHERE DDATPAGPLAN IS NOT NULL";
        } else {
          $cond1 = "WHERE SUBSTR(DDATPAGPLAN,6,2) = '".$mes."' ";
        }
        
        $ano = $seg->antiInjection($_POST['ano']);
        
        if ($ano != 'todos') {
          $cond2 = "AND SUBSTR(DDATPAGPLAN,1,4) = ".$ano."";
        } else {
          $cond2 = "";
        }
        
        $plano = $seg->antiInjection($_POST['plano']);
        
        if ($plano == 'todos') {
          $cond3 = "AND NSEQPAGPLAN IS NOT NULL";
        } else {
          $cond3 = "AND NSEQPAGPLAN = '".$plano."' ";
        }
        
        $situa = $seg->antiInjection($_POST['situa']);
        
        if ($situa == 'todos') {
          $cond4 = "AND CSITPAGPLAN IS NOT NULL";
        } else {
          $cond4 = "AND CSITPAGPLAN = '".$situa."' ";
        }
        
        $tipopg = $seg->antiInjection($_POST['tpag']);
        
        if ($tipopg == 'todos') {
          $cond5 = "AND CTIPOPGPLAN IS NOT NULL";
        } else {
          $cond5 = "AND CTIPOPGPLAN = '".$tipopg."' ";
        }
        
        $sql = new Query($bd);
        $sql->clear();
        $txt = "SELECT NIDUPAGPLAN,
                   NSEQPAGPLAN,
                   NVALPAGPLAN,
                   CSITPAGPLAN,
                   DDATPAGPLAN,
                   DDTINIPPLAN,
                   DDTFIMPPLAN,
                   CTIPOPGPLAN
			FROM TREDE_PAGAPLANO
			".$cond1."
			".$cond2."
			".$cond3."
			".$cond4."
			".$cond5."
			";
        $sql->executeQuery($txt);
        
        while (!$sql->eof()) {
          $tpl->NOMEUSUA = utf8_encode($func->RetonaNomeUsuarioPorSeq($bd,$sql->result("NIDUPAGPLAN")));
          $seq_usua = $sql->result("NIDUPAGPLAN");
          $tpl->NOMEPLANO = $func->RetornaNomePlano($sql->result("NSEQPAGPLAN"));
          $tpl->DATA = $data->formataData1($sql->result("DDATPAGPLAN"));
          $tpl->STATUSPAG = $func->RetornaSituaPagamento($sql->result("CSITPAGPLAN"));
          $tpl->TIPOPAG = $func->Tipopagamento($sql->result("CTIPOPGPLAN"));
          $tpl->VALOR = number_format($sql->result("NVALPAGPLAN"),2,',','.');
          
          $sql12 = new Query($bd);
          $txt12 = "SELECT  VLOGIINDCOL ,NSEQPATRCOL
							  FROM TREDE_AFILIADOS
							  WHERE NSEQUSUACOL = :usua
							  ORDER BY 1 DESC			
							  LIMIT 1";
          $sql12->addParam(':usua',$seq_usua);
          $sql12->executeQuery($txt12);
          
          $idpat = $sql12->result("NSEQPATRCOL");
          
          if ($idpat == "") {
            $tpl->PAT = 'Não tem';
          } else {
            $tpl->PAT = $func->RetonaNomeUsuarioPorSeq($bd,$idpat).'/'.$sql12->result("VLOGIINDCOL");
          }
          
          
          $tpl->block("REL1");
          $sql->next();
        }
        
        $sql = new Query($bd);
        $sql->clear();
        $txt = "SELECT SUM(NVALPAGPLAN) VALOR
			FROM TREDE_PAGAPLANO
			".$cond1."
			".$cond2."
			".$cond3."
			".$cond4."
			".$cond5."
			";
        $sql->executeQuery($txt);
        
        $tpl->TOTAL = number_format($sql->result("VALOR"),2,',','.');
      }
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  
  $tpl->show();
  $bd->close();
?><?php
