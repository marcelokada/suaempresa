<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  require_once("comum/apagaArquivos.php");
  //error_reporting(0);
  
  $bd = new Database();
  
  $id_sessao = $_SESSION['idSessao'];
  $id_confer = $_GET['idSessao'];
  $e = $_SESSION['admin'];
  $ver_admin = $_SESSION['admin'];
  $seq_admin = $_SESSION['idAdmin'];
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","rel_dependentes.html");
  $tpl->ID_SESSAO = $_SESSION['idSessao'];
  
  $tpl->USUA = $ver_admin;
  
  
  if (isset($_POST['listar'])) {
    
    $pesquisar = $_POST['pesquisar'];
    $tipo = $_POST['tipo'];
    
    if($pesquisar != ""){
      if ($tipo == '1') {
        $cond = "AND REDE_NOMEUSU = '%".strtoupper($pesquisar)."%' ";
      } else if ($tipo == '2') {
        $cond = "AND REDE_NOMEUSU LIKE '%".strtoupper($pesquisar)."%' ";
      }else if ($tipo == '3') {
        $cond = "AND REDE_CPFUSUA = '".$pesquisar."' ";
      } else if ($tipo == '4') {
        $cond = "AND REDE_CIDADE = '%".$pesquisar."%'";
      }
    }else{
      $cond = "";
    }
    
    
    
    
    $sql1 = new Query($bd);
    $txt1 = "SELECT REDE_SEQUSUA,
                    REDE_NOMEUSU,
                    REDE_CPFUSUA,
                    REDE_EMAILUS,
                    REDE_DNASCUS,
                    REDE_CELULAR,
                    REDE_ENDE,
                    REDE_NUM,
                    REDE_BAIRRO,
                    REDE_CEP,
                    REDE_CIDADE,
                    REDE_ESTADO
                    FROM TREDE_USUADMIN
                    WHERE REDE_ADMINUS != 's'
                    ".$cond." ";
    $sql1->executeQuery($txt1);
    
    
    while (!$sql1->eof()) {
      
      $nnumetitu = $sql1->result("REDE_SEQUSUA");
      $tpl->NOME = $sql1->result("REDE_NOMEUSU");
      $tpl->CPF = $sql1->result("REDE_CPFUSUA");
      $tpl->NASC = $data->formataData1($sql1->result("REDE_DNASCUS"));
      $tpl->ENDE = $sql1->result("REDE_ENDE");
      $tpl->CEL = $sql1->result("REDE_CELULAR");
  
      $sql11 = new Query($bd);
      $txt11 = "SELECT CNOMEUSUA,
                      CCPF_USUA,
                      NCEL_USUA,
                      DNASCUSUA
                    FROM TREDE_MEMBROS
                    WHERE NNUMETITU = '".$nnumetitu."'
                    AND CGRUPUSUA = 'd'";
      $sql11->executeQuery($txt11);
      
      $i = 0;
      
      while (!$sql11->eof()) {
        
        $nome[$i] = $sql11->result("CNOMEUSUA");
        $cpf[$i] = $sql11->result("CCPF_USUA");
        $datan[$i] =  $data->formataData1($sql11->result("DNASCUSUA"));
        
        
        $tpl->AGRE = $nome[$i].' - '.$cpf[$i].' - '.$datan[$i].'<br>';
        $tpl->block("NIVEIS2");
        $i++;
        $sql11->next();
      }

      $tpl->block("NIVEIS1");
      $sql1->next();
    }
  }
  
  $tpl->show();
  $bd->close();
?>