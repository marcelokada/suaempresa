<?php
require_once("comum/autoload.php");
	if (!isset($_SESSION)) {
		session_start();
	}
	
	
	$bd = new Database();

	require_once("comum/layout.php");  
	$tpl->addFile("CONTEUDO","config_usuario.html");
	
	
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
			$tpl->IDUSUA = $_SESSION['idUsuario'];
			
			$func->AtualizaStatusUsuario($seq);
			
			//SELECT PARA VERIFICAR O USUARIO
			$tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
			//SELECT PARA VERIFICAR O USUARIO
			
			$sql = new Query ($bd);
			$txt = "SELECT 	REDE_SEQUSUA,
					REDE_NOMEUSU,
					REDE_CPFUSUA,
					REDE_ADMINUS,
					REDE_SENHAUS,
					REDE_TIPOUSU,
					REDE_USUBLOC,
					REDE_EMAILUS,
					REDE_SERECUS,
					REDE_DRECUSU,
					REDE_HRECUSU,
					REDE_DNASCUS,
					REDE_CELULAR,
       	  REDE_ENDE,
					REDE_NUM,
					REDE_BAIRRO,
					REDE_CEP,
					REDE_CIDADE,
					REDE_ESTADO
					FROM TREDE_USUADMIN
					WHERE REDE_SEQUSUA = :seq";
			$sql->addParam(':seq',$seq);
			$sql->executeQuery($txt);
			
			$tpl->SEQ = $sql->result("REDE_SEQUSUA");
			$tpl->EMAI = $sql->result("REDE_EMAILUS");
			$tpl->CCPF = $sql->result("REDE_CPFUSUA");
			$tpl->CELU = $sql->result("REDE_CELULAR");
			$tpl->NASC = $sql->result("REDE_DNASCUS");
			
			$tpl->RUA = $sql->result("REDE_ENDE");
			$tpl->NUM = $sql->result("REDE_NUM");
			$tpl->BAIRRO = $sql->result("REDE_BAIRRO");
			$tpl->CEP = $sql->result("REDE_CEP");
			$tpl->CIDADE = $sql->result("REDE_CIDADE");
			$tpl->UF = $sql->result("REDE_ESTADO");
			
			
			if (isset($_POST['alterar'])) {
				$senha = md5($seg->antiInjection($_POST['senha_atual']));
				
				$nome = $seg->antiInjection($_POST['nome']);
				$email = $seg->antiInjection($_POST['mail']);
				$cpf = $seg->antiInjection($_POST['cpf']);
				$phone = $seg->antiInjection($_POST['phone']);
				
				$rua = $seg->antiInjection($_POST['rua']);
				$number = $seg->antiInjection($_POST['nume']);
				$bairro = $seg->antiInjection($_POST['bairro']);
				$cep = $seg->antiInjection($_POST['cep']);
				$cidade = $seg->antiInjection($_POST['cidade']);
				$numibg = $seg->antiInjection($_POST['ibge']);
				$estado = $seg->antiInjection($_POST['uf']);
				
				
				$dtnasc = $seg->antiInjection($_POST['dtnasc']);
				$cc = $_POST['se'];
				
				$tpl->SEQ = $seg->antiInjection($_POST['nome']);
				$tpl->EMAI = $seg->antiInjection($_POST['mail']);
				$tpl->CCPF = $seg->antiInjection($_POST['cpf']);
				$tpl->CELU = $seg->antiInjection($_POST['phone']);
				$tpl->NASC = $seg->antiInjection($_POST['dtnasc']);
				
				$sql1 = new Query ($bd);
				$txt1 = "SELECT REDE_SENHAUS
					FROM TREDE_USUADMIN
					WHERE REDE_SEQUSUA = :seq";
				$sql1->addParam(':seq',$seq);
				$sql1->executeQuery($txt1);
				
				$senha_res = $sql1->result("REDE_SENHAUS");
				
				if ($senha != $senha_res) {
					$tpl->MSG = '<center><font color="RED">Sua senha não confere.</font></center>';
					$tpl->block("ERRO");
				} else {
					
					if ($cc == 'on') {
						$nova_senha = md5($seg->antiInjection($_POST['nova_senha1']));
						
						$sql3 = new Query ($bd);
						$txt3 = "UPDATE TREDE_USUADMIN SET
						REDE_SENHAUS = '".$nova_senha."'
				WHERE REDE_SEQUSUA = :seq";
						$sql3->addParam(':seq',$seq);
						$sql3->executeSQL($txt3);
					}
					
					
					$sql2 = new Query ($bd);
					$txt2 = "UPDATE TREDE_USUADMIN SET
						REDE_NOMEUSU = :nome,
						REDE_CPFUSUA = :cpf,
						REDE_EMAILUS = :email,
						REDE_DNASCUS = :dnasc,
						REDE_CELULAR = :celu,
            REDE_ENDE = '".$rua."',
						REDE_NUM = '".$number."',
						REDE_BAIRRO = '".$bairro."',
						REDE_CEP = '".$cep."',
						REDE_CIDADE = '".$cidade."',
						REDE_CI_IBGE = '".$numibg."',
						REDE_ESTADO    = '".$estado."'
				WHERE REDE_SEQUSUA = :seq ";
					$sql2->addParam(':seq',$seq);
					$sql2->addParam(':nome',$nome);
					$sql2->addParam(':email',$email);
					$sql2->addParam(':cpf',$cpf);
					$sql2->addParam(':dnasc',$dtnasc);
					$sql2->addParam(':celu',$phone);
					$sql2->executeSQL($txt2);
					
					$tpl->MSG = '<center><font color="green">Alterações realizadas com sucesso.</font></center>';
					$tpl->block("SUCESSO");
				}
			}
		}else{
			$seg->verificaSession($_SESSION['idUsuario']);
		}
	}else{
		$seg->verificaSession($_SESSION['idUsuario']);
	}
	

$tpl->show(); 
$bd->close();
?>





