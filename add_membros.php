<?php
  require_once("./comum/autoload.php");
  $seg->secureSessionStart();
  $bd = new Database();
  
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","add_membros.html");
  
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
      $tpl->ID_USUA_CR = $_SESSION['idUsuario'];
      $tpl->IDUSUA = $_SESSION['idUsuario'];
      
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
      
      $sql = new Query ($bd);
      $txt = "SELECT 	REDE_SEQUSUA
					FROM TREDE_USUADMIN
					WHERE REDE_SEQUSUA = :seq";
      $sql->addParam(':seq',$seq);
      $sql->executeQuery($txt);
      
      $tpl->SEQ = $sql->result("REDE_SEQUSUA");
      
      $sql1 = new Query ($bd);
      $txt1 = "SELECT SEQ,
       					CNOMEUSUA,
							  EMAILUSUA,
							  CCPF_USUA,
							  NCEL_USUA,
							  DNASCUSUA,
							  CTIPOUSUA,
							  NNUMETITU,
							  CGRUPUSUA,
       					CGRUPUSUA
					FROM TREDE_MEMBROS
					WHERE NNUMETITU = :seq";
      $sql1->addParam(':seq',$seq);
      $sql1->executeQuery($txt1);
      
      
      while (!$sql1->eof()) {
        $tpl->ID = $sql1->result("SEQ");
        $tpl->NOME_M = $sql1->result("CNOMEUSUA");
        //$tpl->NOME = $sql1->result("EMAILUSUA");
        //$tpl->NOME = $sql1->result("CCPF_USUA");
        $data_nasc = $sql1->result("DNASCUSUA");
        $ctipousua = $sql1->result("CTIPOUSUA");
        
        list($ano,$mes,$dia) = explode('-',$data_nasc);
        
        // data atual
        $hoje = mktime(0,0,0,date('m'),date('d'),date('Y'));
        // Descobre a unix timestamp da data de nascimento do fulano
        $nascimento = mktime(0,0,0,$mes,$dia,$ano);
        
        // cálculo
        $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
        
        $tpl->IDADE = $idade;
        
        $tipos = $sql1->result("CGRUPUSUA");
        $tpl->TIPO = $func->TipoUsuarioMembros($tipos);
        
        $tpl->PARENT = $func->RetonaTipoUsuario($ctipousua);
        $sql1->next();
        $tpl->block("MEMBROS");
      }
      $tpl->SEQ = $sql->result("REDE_SEQUSUA");
      
      
      $validar_botao = $func->assinaturaUsuarioID($bd,$seq);
      
      if ($validar_botao == '3') {
        $tpl->DISA = "";
        $tpl->TEXT = "";
      } else {
        $tpl->DISA = "disabled";
        $tpl->TEXT = "Para adicionar membros você precisa ter uma plano ativo.";
      }
      
      $idplano = $func->RetornaIdPlanoUsua($seq);
      $plano_agregados = $func->RetornaQtdeAgregados($idplano);
      $plano_dependentes = $func->RetornaQtdeDependentes($idplano);
      
      $tpl->NOMEPLANO = $func->RetornaNomePlano($idplano);
      $tpl->DEP = $plano_dependentes;
      $tpl->AGR = $plano_agregados;
      
      $sqld = new Query($bd);
      $txtd = "SELECT COUNT(*) DEP
					FROM TREDE_MEMBROS
					WHERE NNUMETITU = :seq
					AND CGRUPUSUA = 'd'";
      $sqld->addParam(':seq',$seq);
      $sqld->executeQuery($txtd);
      
      $dep_usua = $sqld->result("DEP");
      
      if ($sqld->result("DEP") == 0) {
        $tpl->DEPUSUA = 0;
      } else {
        $tpl->DEPUSUA = $sqld->result("DEP");
      }
      
      $sqle = new Query($bd);
      $txte = "SELECT COUNT(*) AGRE
					FROM TREDE_MEMBROS
					WHERE NNUMETITU = :seq
					AND CGRUPUSUA = 'a'";
      $sqle->addParam(':seq',$seq);
      $sqle->executeQuery($txte);
      
      $agre_usua = $sqle->result("AGRE");
      
      if ($sqle->result("AGRE") == 0) {
        $tpl->AGREUSUA = 0;
      } else {
        $tpl->AGREUSUA = $sqle->result("AGRE");
      }
      
      /*if (($dep_usua == $plano_dependentes) and ($agre_usua == $plano_agregados)) {
        $tpl->MARIDO = "disabled";
        $tpl->ESPOSA = "disabled";
        $tpl->FILHOS = "disabled";
        $tpl->NETOS = "disabled";
        $tpl->PAI = "disabled";
        $tpl->MAE = "disabled";
        $tpl->IRMAO = "disabled";
        $tpl->SOGRO = "disabled";
        $tpl->DISA = "disabled";
        $tpl->TEXTOVAZIO = "Você Atingiu o limite de dependentes e agregados.";
        $tpl->INPUTS = "disabled";
      }
      else if ($dep_usua >= $plano_dependentes) {
        $tpl->MARIDO = "disabled";
        $tpl->ESPOSA = "disabled";
        $tpl->TEXTOFILHO = "- Somente Filhos acima de 21 anos.";
        $tpl->FILHOACIMA = 1;
      }
      else if ($agre_usua >= $plano_agregados) {
        $tpl->NETOS = "disabled";
        $tpl->PAI = "disabled";
        $tpl->MAE = "disabled";
        $tpl->IRMAO = "disabled";
        $tpl->SOGRO = "disabled";
        $tpl->TEXTOFILHO = "- Somente Filhos abaixo de 21 anos.";
        $tpl->FILHOACIMA = 2;
      }*/
      if (($dep_usua >= $plano_dependentes) and ($agre_usua >= $plano_agregados)) {
        $tpl->DEPE = "disabled";
        $tpl->AGRE = "disabled";
        $tpl->MARIDO = "disabled";
        $tpl->ESPOSA = "disabled";
        $tpl->FILHOS = "disabled";
        $tpl->NETOS = "disabled";
        $tpl->PAI = "disabled";
        $tpl->MAE = "disabled";
        $tpl->IRMAO = "disabled";
        $tpl->SOGRO = "disabled";
        $tpl->DISA = "disabled";
        $tpl->CUNHA = "disabled";
        $tpl->TEXTOVAZIO = "Você Atingiu o limite de dependentes e agregados.";
        $tpl->INPUTS = "disabled";
      } else if ($dep_usua >= $plano_dependentes) {
        $tpl->DEPE = "disabled";
        $tpl->block("ADD");
      } else if ($agre_usua >= $plano_agregados) {
        $tpl->AGRE = "disabled";
        $tpl->block("ADD");
      } else {
        $tpl->TEXTOFILHO = "";
        $tpl->block("ADD");
      }
      
      if (isset($_POST['add'])) {
        
        $nome = $seg->antiInjection($_POST['nome']);
        $cpf = $seg->antiInjection($_POST['cpf']);
        $cpf = str_replace('.','',$cpf);
        $cpf = str_replace('-','',$cpf);
        
        $email = $seg->antiInjection($_POST['mail']);
        
        $celular = $seg->antiInjection($_POST['phone']);
        $celular = str_replace('(','',$celular);
        $celular = str_replace(')','',$celular);
        $celular = str_replace(' ','',$celular);
        $celular = str_replace('-','',$celular);
        
        $dtnasc = $seg->antiInjection($_POST['dtnasc']);
        $val_acima = $seg->antiInjection($_POST['val_acima']);
        
        $parentesco = $seg->antiInjection($_POST['parentesco']);
        $grau = $seg->antiInjection($_POST['grau']);
        $titu = $seg->antiInjection($_POST['idusua']);
        
        /*list($dia,$mes,$ano) = explode('/',$dtnasc);
        
        // data atual
        $hoje = mktime(0,0,0,date('m'),date('d'),date('Y'));
        // Descobre a unix timestamp da data de nascimento do fulano
        $nascimento = mktime(0,0,0,$mes,$dia,$ano);
        
        // cálculo
        $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
        
        if (($parentesco == 'm') or ($parentesco == 'e') or ($parentesco == 'f')) {
          if (($parentesco == 'f') and ($idade <= 21)) {
            $grau = 'd';
          }
          else if (($parentesco == 'f') and ($idade >= 22)) {
            $grau = 'a';
          }
          else {
            $grau = 'd';
          }
        }
        else {
          $grau = 'a';
        }
        
        if (($parentesco == 'f') and ($val_acima == 1) and ($idade < 21)) {
          $tpl->MSG = '<center><font color="red">Somente filhos acima de 21 anos.</font></center>';
          $tpl->block("ERRO");
        }
        else if (($parentesco == 'f') and ($val_acima == 2) and ($idade >= 22)) {
          $tpl->MSG = '<center><font color="red">Somente filhos abaixo de 21 anos.</font></center>';
          $tpl->block("ERRO");
        }
        else {*/
        
        $dtnasc = $data->dataInvertida($dtnasc);
        
        $sql2 = new Query($bd);
        $txt2 = "INSERT INTO TREDE_MEMBROS
					(CNOMEUSUA,
					EMAILUSUA,
					CCPF_USUA,
					NCEL_USUA,
					DNASCUSUA,
					CTIPOUSUA,
					NNUMETITU,
					CGRUPUSUA)
					VALUES				   	
					('".$nome."',
					'".$email."',
					'".$cpf."',
					  '".$celular."',
					'".$dtnasc."',
					'".$parentesco."',
					'".$titu."',
					'".$grau."')";
        $sql2->executeSQL($txt2);
        
        echo "<script>alert('Alterações realizadas com sucesso'); window.location.href=window.location.href;</script>";
        // }
      }
    }
  }else{
    $seg->verificaSession($_SESSION['idUsuario']);
  }
  
  
  $tpl->show();
  $bd->close();
?>





