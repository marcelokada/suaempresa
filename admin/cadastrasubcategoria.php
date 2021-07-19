<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","cadastrasubcategoria.html");
  
  if (isset($_SESSION['aut_admin'])) {
    $autenticado = TRUE;
    $_SESSION['aut_admin'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
  
    //CATEGORIAS
    $sql = new Query();
    $sql->clear();
    $txt = "SELECT NNUMECATECAT,
                     VNOMECATECAT,
                     VSITUCATECAT
                FROM TREDE_CATEGORIAS
                WHERE NNUMECATECAT = 1";
    $sql->executeQuery($txt);
  
    $tpl->CAT1 = utf8_encode(ucwords($sql->result("VNOMECATECAT")));
  
    $sql = new Query();
    $sql->clear();
    $txt = "SELECT NNUMECATECAT,
                     VNOMECATECAT,
                     VSITUCATECAT
                FROM TREDE_CATEGORIAS
                WHERE NNUMECATECAT = 2";
    $sql->executeQuery($txt);
  
    $tpl->CAT2 = utf8_encode(ucwords($sql->result("VNOMECATECAT")));
  
    $sql = new Query();
    $sql->clear();
    $txt = "SELECT NNUMECATECAT,
                     VNOMECATECAT,
                     VSITUCATECAT
                FROM TREDE_CATEGORIAS
                WHERE NNUMECATECAT = 3";
    $sql->executeQuery($txt);
  
    $tpl->CAT3 = utf8_encode(ucwords($sql->result("VNOMECATECAT")));
  
    $sql = new Query();
    $sql->clear();
    $txt = "SELECT NNUMECATECAT,
                     VNOMECATECAT,
                     VSITUCATECAT
                FROM TREDE_CATEGORIAS
                WHERE NNUMECATECAT = 4";
    $sql->executeQuery($txt);
  
    $tpl->CAT4 = utf8_encode(ucwords($sql->result("VNOMECATECAT")));
  
    $sql = new Query();
    $sql->clear();
    $txt = "SELECT NNUMECATECAT,
                     VNOMECATECAT,
                     VSITUCATECAT
                FROM TREDE_CATEGORIAS
                WHERE NNUMECATECAT = '5'";
    $sql->executeQuery($txt);
  
    $tpl->CAT5 = utf8_encode(ucwords($sql->result("VNOMECATECAT")));
    
    if ($autenticado == TRUE) {
      
      $id_sessao = $_SESSION['idSessao_admin'];
      $id_confer = $_GET['idSessao'];
      $id_admin = $_SESSION['usuaAdmin'];
      
      $seg->verificaSession($id_sessao);
      
      $tpl->ID_SESSAO = $_SESSION['idSessao_admin'];
      $tpl->ID_ADMIN = $_SESSION['usuaAdmin'];
      
      $nivelusua = $func->RetornaPermissoes_Admin($id_admin);
      
      if ($nivelusua == 'C') {
        $tpl->DISABLE = "style='display:none;'";
      } else if ($nivelusua == 'CB') {
        $tpl->DISABLE = "style='display:none;'";
      } else if ($nivelusua == 'CCAB') {
        $tpl->block('NIVEL4_SUBCAT_BT1');
        $tpl->block('NIVEL4_SUBCAT_BT2');
        $tpl->block('NIVEL4_SUBCAT_BT3');
        $tpl->block('NIVEL4_SUBCAT_BT4');
        $tpl->block('NIVEL4_SUBCAT_BT5');
      } else if ($nivelusua == 'CCABA') {
        $tpl->block('NIVEL4_SUBCAT_BT1');
        $tpl->block('NIVEL4_SUBCAT_BT2');
        $tpl->block('NIVEL4_SUBCAT_BT3');
        $tpl->block('NIVEL4_SUBCAT_BT4');
        $tpl->block('NIVEL4_SUBCAT_BT5');
      }
      
      //$tpl->ID_ADMIN 	= $_SESSION['admin'];
      
      /*$sql = new Query($bd);
      $txt = "SELECT NNUMECATECAT, VNOMECATECAT
          FROM TREDE_CATEGORIAS
          WHERE VSITUCATECAT = 'a'";
      $sql->executeQuery($txt);
      
      while(!$sql->eof()){
        $tpl->CAT_NUM 	= $sql->result("NNUMECATECAT");
        $tpl->CAT_NOME 	= ucwords(utf8_encode($sql->result("VNOMECATECAT")));
      $sql->next();
      $tpl->block("CATE");
      }*/
      
      
      /////////////////ALIMENTOS E BEBIDAS/////////////////////
      $sql1 = new Query($bd);
      $txt1 = "SELECT NNUMECATESUB,VNOMECATESUB,NNUMECATECAT,VSITUCATESUB
			FROM TREDE_SUBCATEGORIA
			WHERE NNUMECATECAT = 1
			ORDER BY NNUMECATESUB";
      $sql1->executeQuery($txt1);
      
      $tpl->TITULO1 = ucwords($func->RetornaNomeCategoria($bd,$sql1->result("NNUMECATECAT")));
      $tpl->CATE1 = 1;
      
      while (!$sql1->eof()) {
        $tpl->ID1 = $sql1->result("NNUMECATESUB");
        $tpl->NOME1 = ucwords(utf8_encode($sql1->result("VNOMECATESUB")));
        
        $status = $sql1->result("VSITUCATESUB");
        
        if ($status == 'a') {
          $tpl->STATUS1 = "Ativo";
        } else {
          $tpl->STATUS1 = "Desativado";
        }
        
        $sql1->next();
        $tpl->block("SUB1");
      }
      /////////////////ALIMENTOS E BEBIDAS/////////////////////
      
      
      /////////////////LAZER/////////////////////
      $sql2 = new Query($bd);
      $txt2 = "SELECT NNUMECATESUB,VNOMECATESUB,NNUMECATECAT,VSITUCATESUB
			FROM TREDE_SUBCATEGORIA
			WHERE NNUMECATECAT = 2
			ORDER BY NNUMECATESUB";
      $sql2->executeQuery($txt2);
      
      $tpl->TITULO2 = ucwords(utf8_encode($func->RetornaNomeCategoria($bd,$sql2->result("NNUMECATECAT"))));
      $tpl->CATE2 = 2;
      
      while (!$sql2->eof()) {
        $tpl->ID2 = $sql2->result("NNUMECATESUB");
        $tpl->NOME2 = ucwords(utf8_encode($sql2->result("VNOMECATESUB")));
        
        $status = $sql2->result("VSITUCATESUB");
        if ($status == 'a') {
          $tpl->STATUS2 = "Ativo";
        } else {
          $tpl->STATUS2 = "Desativado";
        }
        $sql2->next();
        $tpl->block("SUB2");
      }
      /////////////////LAZER/////////////////////
      
      
      /////////////////BEM-ESTAR E SAÚDE/////////////////////
      $sql3 = new Query($bd);
      $txt3 = "SELECT NNUMECATESUB,VNOMECATESUB,NNUMECATECAT,VSITUCATESUB
			FROM TREDE_SUBCATEGORIA
			WHERE NNUMECATECAT = 3
			ORDER BY NNUMECATESUB";
      $sql3->executeQuery($txt3);
      
      $tpl->TITULO3 = ucwords(utf8_encode($func->RetornaNomeCategoria($bd,$sql3->result("NNUMECATECAT"))));
      $tpl->CATE3 = 3;
      
      while (!$sql3->eof()) {
        $tpl->ID3 = $sql3->result("NNUMECATESUB");
        $tpl->NOME3 = ucwords(utf8_encode($sql3->result("VNOMECATESUB")));
        
        $status = $sql3->result("VSITUCATESUB");
        if ($status == 'a') {
          $tpl->STATUS3 = "Ativo";
        } else {
          $tpl->STATUS3 = "Desativado";
        }
        $sql3->next();
        $tpl->block("SUB3");
      }
      /////////////////BEM-ESTAR E SAÚDE/////////////////////
      
      
      /////////////////EDUCAÇÃO/////////////////////
      $sql4 = new Query($bd);
      $txt4 = "SELECT NNUMECATESUB,VNOMECATESUB,NNUMECATECAT,VSITUCATESUB
			FROM TREDE_SUBCATEGORIA
			WHERE NNUMECATECAT = 4
			ORDER BY NNUMECATESUB";
      $sql4->executeQuery($txt4);
      
      $tpl->TITULO4 = ucwords(utf8_encode($func->RetornaNomeCategoria($bd,$sql4->result("NNUMECATECAT"))));
      $tpl->CATE4 = 4;
      
      while (!$sql4->eof()) {
        $tpl->ID4 = $sql4->result("NNUMECATESUB");
        $tpl->NOME4 = ucwords(utf8_encode($sql4->result("VNOMECATESUB")));
        
        $status = $sql4->result("VSITUCATESUB");
        if ($status == 'a') {
          $tpl->STATUS4 = "Ativo";
        } else {
          $tpl->STATUS4 = "Desativado";
        }
        $sql4->next();
        $tpl->block("SUB4");
      }
      /////////////////EDUCAÇÃO/////////////////////
      
      
      /////////////////PRODUTOS E SERVIÇOS/////////////////////
      $sql5 = new Query($bd);
      $txt5 = "SELECT NNUMECATESUB,VNOMECATESUB,NNUMECATECAT,VSITUCATESUB
			FROM TREDE_SUBCATEGORIA
			WHERE NNUMECATECAT = 5
			ORDER BY NNUMECATESUB";
      $sql5->executeQuery($txt5);
      
      $tpl->TITULO5 = ucwords(utf8_encode($func->RetornaNomeCategoria($bd,$sql5->result("NNUMECATECAT"))));
      $tpl->CATE5 = 5;
      
      while (!$sql5->eof()) {
        $tpl->ID5 = $sql5->result("NNUMECATESUB");
        $tpl->NOME5 = ucwords(utf8_encode($sql5->result("VNOMECATESUB")));
        
        $status = $sql5->result("VSITUCATESUB");
        if ($status == 'a') {
          $tpl->STATUS5 = "Ativo";
        } else {
          $tpl->STATUS5 = "Desativado";
        }
        $sql5->next();
        $tpl->block("SUB5");
      }
      /////////////////PRODUTOS E SERVIÇOS/////////////////////
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  
  $tpl->show();
  $bd->close();
?>