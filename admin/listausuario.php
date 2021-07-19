<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","listausuario.html");
  
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
      
      
      /////////////////ALIMENTOS E BEBIDAS/////////////////////
      $sql1 = new Query($bd);
      $txt1 = "SELECT REDE_SEQUSUA,
                    CNOMEADMIN,
                    EMAILADMIN,
                    NIVELADMIN ,
       							CSITUADMIN
			FROM TREDE_ADMINS
			ORDER BY REDE_SEQUSUA";
      $sql1->executeQuery($txt1);
      
      while (!$sql1->eof()) {
        $tpl->ID = $sql1->result("REDE_SEQUSUA");
        $tpl->NOME = ucwords(utf8_encode($sql1->result("CNOMEADMIN")));
        $tpl->EMAIL = $sql1->result("EMAILADMIN");
        
        $cnivel = $sql1->result("NIVELADMIN");
        
        if ($cnivel == '1') {
          $tpl->NIVEL = 'Apenas Consultar';
        } else if ($cnivel == '2') {
          $tpl->NIVEL = 'Consultar e BackOffice (Usuário)';
        } else if ($cnivel == '3') {
          $tpl->NIVEL = 'Consultar, Cadastrar, Alterações (configurações) e BackOffice (Usuário)';
        } else if ($cnivel == '4') {
          $tpl->NIVEL = 'Consultar, Cadastrar,Alterações (configurações), BackOffice (Usuário) e Autorizar (Pagamentos)';
        }
        
        
        $status = $sql1->result("CSITUADMIN");
        
        if ($status == 'a') {
          $tpl->STATUS1 = "Ativo";
        } else {
          $tpl->STATUS1 = "Desativado";
        }
        
        $sql1->next();
        $tpl->block("SUB1");
      }
      /////////////////ALIMENTOS E BEBIDAS/////////////////////
      
      if (isset($_POST['alterar'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $nivel = $_POST['nivel'];
        $situ = $_POST['situ'];
        
        $id = $_POST['id'];
        
        $sql2 = new Query($bd);
        $txt2 = "UPDATE TREDE_ADMINS SET CNOMEADMIN = '".$nome."',
                        						 EMAILADMIN = '".$email."',
                        						 NIVELADMIN = '".$nivel."',
                        						 CSITUADMIN = '".$situ."'
				WHERE REDE_SEQUSUA = :id ";
        $sql2->addParam(':id',$id);
        $sql2->executeSQL($txt2);
        
        header("location: listausuario.php?idSessao=".$id_sessao);
      }
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  
  $tpl->show();
  $bd->close();
?>