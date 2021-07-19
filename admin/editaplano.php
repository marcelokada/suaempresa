<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","editaplano.html");
  
  
  if (isset($_SESSION['aut_admin'])) {
    $autenticado           = TRUE;
    $_SESSION['aut_admin'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      
      $idplan       = $_GET['idplano'];
      $tpl->IDPLANO = $_GET['idplano'];
      $id_sessao    = $_SESSION['idSessao_admin'];
      $id_confer    = $_GET['idSessao'];
      $id_admin     = $_SESSION['usuaAdmin'];
      
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao_admin'];
      $tpl->ID_ADMIN  = $_SESSION['usuaAdmin'];
      
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT SEQPLANO,
                CNOMEPLANO,
                CDESCPLANO,
                CTEMPPLANO,
                VVALTPLANO,
                MENSAPLANO,
       					AGREGPLANO,
       					DEPENPLANO,
                CCARNPLANO,
                CSITUPLANO
			FROM TREDE_PLANOS
			WHERE SEQPLANO = :idplan
			ORDER BY CNOMEPLANO";
      $sql1->addParam(':idplan',$idplan);
      $sql1->executeQuery($txt1);
      
      while (!$sql1->eof()) {
        $tpl->SEQPLANO   = $sql1->result("SEQPLANO");
        $nnumeplano      = $sql1->result("SEQPLANO");
        $tpl->CNOMEPLANO = ucwords(utf8_encode($sql1->result("CNOMEPLANO")));
        $tpl->CDESCPLANO = $sql1->result("CDESCPLANO");
        $tpl->CTEMPPLANO = $sql1->result("CTEMPPLANO");
        $tpl->VVALTPLANO = number_format($sql1->result("VVALTPLANO"),2,',','.');
        $tpl->MENSAPLANO = number_format($sql1->result("MENSAPLANO"),2,',','.');
        $tpl->DEP        = $sql1->result("DEPENPLANO");
        $tpl->AGRE       = $sql1->result("AGREGPLANO");
        
        $tpl->VAL_CARNE = $sql1->result("CCARNPLANO");
        $carne          = $sql1->result("CCARNPLANO");
        
        if ($carne == 's') {
          $tpl->VCARNE = "SIM";
        } else {
          $tpl->VCARNE = "NÃO";
        }
        
        $status        = $sql1->result("CSITUPLANO");
        $tpl->V_STATUS = $sql1->result("CSITUPLANO");
        
        if ($status == 'a') {
          $tpl->STATUS = "Ativo";
        } else if ($status == 'c') {
          $tpl->STATUS = "Inativo";
        }
        
        
        $sql21 = new Query();
        $txt21 = "SELECT VALOR, NNUMEPLAN FROM TREDE_VALOR_UNILEVEL
					WHERE NNUMEPLAN = :nnumeplan";
        $sql21->addParam(':nnumeplan',$nnumeplano);
        $sql21->executeQuery($txt21);
        
        $tpl->LIMITE = $sql21->result("VALOR");
        
        $sql1->next();
      }
      /////////////////ALIMENTOS E BEBIDAS/////////////////////
      
      if (isset($_POST['alterar'])) {
        
        $nome  = $_POST['nome'];
        $desc  = $_POST['desc'];
        $tempo = $_POST['tempo'];
        
        $adesao = $_POST['adesao'];
        $adesao = str_replace('.','',$adesao);
        $adesao = str_replace(',','.',$adesao);
        
        $mensa = $_POST['mensa'];
        $mensa = str_replace('.','',$mensa);
        $mensa = str_replace(',','.',$mensa);
        
        $unilevel = $_POST['unilevel'];
        $unilevel = str_replace('.','',$unilevel);
        $unilevel = str_replace(',','.',$unilevel);
        
        $dependentes = $_POST['depen'];
        $agregado    = $_POST['agreg'];
        $carne       = $_POST['carne'];
        $status      = $_POST['status'];
        
        $sql2 = new Query($bd);
        $txt2 = "UPDATE TREDE_PLANOS SET CNOMEPLANO  = :nome,
                                        CDESCPLANO = :desc,
                                        CTEMPPLANO = :tempo,
                                        VVALTPLANO = :valort,
                                        MENSAPLANO = :mensa,
                        								AGREGPLANO = :agreg,
                        								DEPENPLANO = :dep,
                                        CCARNPLANO = '".$carne."',
                                        CSITUPLANO = '".$status."'
				WHERE SEQPLANO = :id";
        $sql2->addParam(':nome',$nome);
        $sql2->addParam(':desc',$desc);
        $sql2->addParam(':tempo',$tempo);
        $sql2->addParam(':valort',$adesao);
        $sql2->addParam(':mensa',$mensa);
        $sql2->addParam(':id',$idplan);
        $sql2->addParam(':agreg',$agregado);
        $sql2->addParam(':dep',$dependentes);
        $sql2->executeSQL($txt2);
        
        $sql24 = new Query($bd);
        $txt24 = "UPDATE TREDE_VALOR_UNILEVEL SET VALOR = '".$unilevel."'
				WHERE NNUMEPLAN = :id";
        $sql24->addParam(':id',$idplan);
        $sql24->executeSQL($txt24);
        
        
        echo "<script>alert('Alteração Realizado com Sucesso!!'); window.location.href = window.location.href</script>";
        $tpl->MSG = "Alteração Realizado com Sucesso!";
        $tpl->block("SUCESSO");
      }
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  $tpl->show();
  $bd->close();
?>
