<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","rel_dep_agregados.html");
  
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
      
      
      if (isset($_POST['listar'])) {
        
        $pesquisar = $_POST['pesquisar'];
        $grau = $_POST['grau'];
        $tipo = $_POST['tipo'];
        
        if ($pesquisar != "") {
          if ($tipo == '1') {
            $cond = "AND REDE_NOMEUSU = '%".strtoupper($pesquisar)."%' ";
          } else if ($tipo == '2') {
            $cond = "AND REDE_NOMEUSU LIKE '%".strtoupper($pesquisar)."%' ";
          } else if ($tipo == '3') {
            $cond = "AND REDE_CPFUSUA = '".$pesquisar."' ";
          } else if ($tipo == '4') {
            $cond = "AND REDE_CIDADE = '%".$pesquisar."%'";
          }
        } else {
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
          
          if ($grau == 't') {
            $cond1 = "AND CGRUPUSUA IS NOT NULL";
          } else if ($grau == 'a') {
            $cond1 = "AND CGRUPUSUA = 'a'";
          } else if ($grau == 'd') {
            $cond1 = "AND CGRUPUSUA = 'd'";
          }
          
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
                      DNASCUSUA,
                      CGRUPUSUA
                    FROM TREDE_MEMBROS
                    WHERE NNUMETITU = '".$nnumetitu."'
                    ".$cond1." ";
          $sql11->executeQuery($txt11);
          
          $i = 0;
          
          while (!$sql11->eof()) {
            
            $nome[$i] = $sql11->result("CNOMEUSUA");
            $cpf[$i] = $sql11->result("CCPF_USUA");
            $grau[$i] = $sql11->result("CGRUPUSUA");
            $datan[$i] = $data->formataData1($sql11->result("DNASCUSUA"));
            
            if ($grau[$i] == 'a') {
              $deag = 'Agregado';
            } else if ($grau[$i] == 'd') {
              $deag = 'Dependente';
            }
            
            $tpl->AGRE = $nome[$i].' - '.$cpf[$i].' - '.$datan[$i].'('.$deag.')<br>';
            $tpl->block("NIVEIS2");
            $i++;
            $sql11->next();
          }
          
          $tpl->block("NIVEIS1");
          $sql1->next();
        }
      }
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  $tpl->show();
  $bd->close();
?>