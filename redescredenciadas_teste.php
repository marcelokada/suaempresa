<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
error_reporting(0);

$bd = new Database();

$id_sessao 	= $_SESSION['idSessao'];
$id_confer 	= $_GET['idSessao'];
$seq 		= $_SESSION['idUsuario'];

if ($id_confer != $id_sessao) {
	require_once("comum/restrito.html");
	session_destroy();
} else {
	require_once("comum/layout_teste.php");
	$tpl->addFile("CONTEUDO", "redescredenciadas_teste.html");
	$tpl->ID_SESSAO = $_SESSION['idSessao'];
	$cat 			= $_GET['cat'];

	$palavra		= $_GET['pesq'];
	//$tpl->ID = "#";


	if ($cat == '1') {
		$tpl->CRUMB1 = "<li class='breadcrumb-item'>Alimentos e Bebidas</li>";
		$tpl->CAT = "Alimentos e Bebidas";
	} elseif ($cat == '2') {
		$tpl->CRUMB1 = "<li class='breadcrumb-item'>Lazer</li>";
		$tpl->CAT = "Lazer";
	} elseif ($cat == '3') {
		$tpl->CRUMB1 = "<li class='breadcrumb-item'>Bem-estar e Sáude</li>";
		$tpl->CAT = "Bem-Estar e Saúde";
	} elseif ($cat == '4') {
		$tpl->CRUMB1 = "<li class='breadcrumb-item'>Educação</li>";
		$tpl->CAT = "Educação";
	} elseif ($cat == '5') {
		$tpl->CRUMB1 = "<li class='breadcrumb-item'>Serviços</li>";
		$tpl->CAT = "Serviços";
	} elseif ($cat == '0') {
		$tpl->CRUMB1 = "<li class='breadcrumb-item'>Todas as Categorias</li>";
		$tpl->CAT = "Todas as Categorias";
	}

	//SELECT PARA VERIFICAR O USUARIO
	$tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $seq)));
	//SELECT PARA VERIFICAR O USUARIO

	// select para trazer a regiao //
	$sql = new Query($bd);
	$txt = "SELECT NNUMEREGIREG, VNOMEREGIREG FROM TREDE_REGIAO";
	$sql->executeQuery($txt);

	while (!$sql->eof()) {
		$tpl->ID_REGIAO 	= $sql->result("NNUMEREGIREG");
		$tpl->NOME_REGIAO 	= $sql->result("VNOMEREGIREG");

		$tpl->block("REGIAO");
		$sql->next();
	}
	// select para trazer a regiao //

	if (isset($_POST['listar'])) {
		$palavra = '#';
	}


	if ($cat != '0') {
		$cond_cat = "AND NNUMECATECRE = '" . $cat . "'";
	} else {
		$cond_cat = "AND NNUMECATECRE IS NOT NULL";
	}

	if ($palavra == '#') {
		$cond_cat2 = "WHERE VNOMECREDCRE IS NOT NULL ";
	} else {
		$cond_cat2 = "WHERE VNOMECREDCRE LIKE '%" . $palavra . "%' ";
	}

	if ($palavra != '#') {

		$sql23 = new Query($bd);
		$txt23 = "SELECT DISTINCT CESTADOUFEST					
	    FROM TREDE_CREDENCIADOS
		" . $cond_cat2 . "
		" . $cond_cat . "
		ORDER BY CESTADOUFMUN";
		$sql23->executeQuery($txt23);

		$res = $sql23->result("CESTADOUFEST");


		if ($res == null) {
			$tpl->ESTADO1 = "<font color='red'>NENHUM RESULTADO ENCONTRADO!</font>";
			$tpl->block("ESTADO_REDE");
		} else {

			while (!$sql23->eof()) {
				$uf	 		  = $sql23->result("CESTADOUFEST");
				$tpl->ESTADO1 = utf8_encode($func->RetonaNomeEstado($bd, $uf));

				if ($cat == '0') {
					$cond_cat3 = "AND NNUMECATECRE IS NOT NULL";
				} else {
					$cond_cat3 = "AND NNUMECATECRE = '" . $cat . "' ";
				}

				$sql22 = new Query($bd);
				$txt22 = "SELECT SEQUENCIACRE,
						VNOMECREDCRE,
						NNUMECATECRE,
						VCUPOMDESCRE						
				FROM TREDE_CREDENCIADOS
			WHERE CESTADOUFEST = :uf
			" . $cond_cat3 . "  
			ORDER BY NNUMECATECRE";

				$sql22->addParam(':uf', $uf);
				$sql22->executeQuery($txt22);

				while (!$sql22->eof()) {

					$tpl->NOMEREDE 		= $sql22->result("VNOMECREDCRE");
					$tpl->CATE_REDE 	= $func->RetonaNomeCategoria($bd, $sql22->result("NNUMECATECRE"));
					$tpl->IDCRED		= $sql22->result("SEQUENCIACRE");
					//$tpl->CUPOM			= $sql22->result("VCUPOMDESCRE");
					$id					= $sql22->result("SEQUENCIACRE");
					$imagem				= $func->RetornaImagem($bd, $id);
					if ($imagem <> '') {
						$tpl->IMAGEM		= $func->RetornaImagem($bd, $id);
					} else {
						$tpl->IMAGEM		= "comum/img/Sem-imagem.jpg";
					}

					$tpl->block("REDE_CIDADE");
					$sql22->next();
				}

				$tpl->block("ESTADO_REDE");
				$sql23->next();
			}
		}
	}

	// select para trazer a categoriaa //
	/*$sql1 = new Query ($bd);
	$txt1 = "SELECT SEQUENCIACRE,
					VNOMECREDCRE, 
					VNOMEENDECRE ,
					NNUMEENDECRE,
					VNOMEBAIRCRE,  
					VNOMECIDAMUN,
					CESTADOUFMUN,
					NUMETPENDCRE,
					CESTADOUFEST,
					NNUMECATECRE,
					NNUMESERVCRE,
					NNUMEIBGEMUN,
					CSITUACAOCRE,
					DDATACREDCRE,
					NNUMEREGIREG   
			    FROM TREDE_CREDENCIADOS
				WHERE NNUMECATECRE = :categoria ";
	$sql1->addParam(':categoria',$cat);
	$sql1->executeQuery($txt1);
	
	while(!$sql1->eof()){
	$tpl->NOMEREDE 		= $sql1->result("VNOMECREDCRE");	
	$tpl->block("REDE_CAT");	
	$sql1->next();	
	}	*/
	// select para trazer a categoriaa //


	if (isset($_POST['listar'])) {

		$idregiao	= 	$seg->antiInjection($_POST['regiao']);
		$id_reg		=	$func->RetonaNomeRegiao($bd, $idregiao);

		$idestado	= 	$seg->antiInjection($_POST['estado']);
		$id_est		=	utf8_encode($func->RetonaNomeEstado($bd, $idestado));

		$idcidade	= 	$seg->antiInjection($_POST['cid']);
		$id_cid		=	utf8_encode($func->RetonaNomeCidade($bd, $idcidade));

		$pesquisa	= 	$seg->antiInjection($_POST['pesquisar']);


		if (($idregiao == "#") or ($idregiao == "")) {
			$cond1 = "WHERE NNUMEREGIREG IS NOT NULL";
			$tpl->REGIAOREDE = "TODAS AS REGIÕES <i class='fa fa-chevron-circle-right' aria-hidden='true'></i> ";
		} else {
			$cond1 = "WHERE NNUMEREGIREG = '" . $idregiao . "' ";
			$tpl->CRUMB2 = "<li class='breadcrumb-item'>" . $id_reg . "</li>";
			$tpl->REGIAOREDE = 'REGIÃO ' . $id_reg . ' <i class="fa fa-chevron-circle-right" aria-hidden="true"></i> ';
		}

		if (($idestado == "#") or ($idestado == "")) {
			$cond2 = "AND CESTADOUFMUN IS NOT NULL";
			$tpl->ESTADOREDE = "TODOS OS ESTADOS <i class='fa fa-chevron-circle-right' aria-hidden='true'></i> ";
		} else {
			$cond2 = "AND CESTADOUFMUN = '" . $idestado . "' ";
			$tpl->CRUMB3 = "<li class='breadcrumb-item'>" . $id_est . "</li>";
			$tpl->ESTADOREDE = $id_est . ' <i class="fa fa-chevron-circle-right" aria-hidden="true"></i> ';
		}

		if (($idcidade == "#") or ($idcidade == "")) {
			$cond3 = "AND NNUMEREGIREG IS NOT NULL";
			$tpl->CIDADEREDE = 'TODAS AS CIDADES';
		} else {
			$cond3 = "AND NNUMEREGIREG = '" . $idcidade . "'";
			$tpl->CRUMB4 = "<li class='breadcrumb-item'>" . $id_cid . "</li>";
			$tpl->CIDADEREDE = $id_cid;
		}

		if ($pesquisa == null) {
			$cond4 = "AND VNOMECREDCRE IS NOT NULL";
			$tpl->PESQ = "";
		} else {
			$cond4 = "AND VNOMECREDCRE LIKE '%" . $pesquisa . "%'";
			$tpl->PESQ = "VOCÊ PESQUISOU <i><b>'" . $pesquisa . "'</b></i> | ";
		}

		if ($cat == '0') {
			$cond5 = "AND NNUMECATECRE IS NOT NULL";
		} else {
			$cond5 = "AND NNUMECATECRE = '" . $cat . "'";
		}

		if ($cat == '0') {
			$sql2 = new Query($bd);
			$txt2 = "SELECT DISTINCT CESTADOUFEST						
			    FROM TREDE_CREDENCIADOS
				" . $cond1 . " 
				" . $cond2 . " 
				" . $cond3 . "
				" . $cond4 . "
				" . $cond5 . " 
				ORDER BY CESTADOUFMUN";
			$sql2->executeQuery($txt2);


			while (!$sql2->eof()) {
				$uf	 		  = $sql2->result("CESTADOUFEST");
				$tpl->ESTADO1 = utf8_encode($func->RetonaNomeEstado($bd, $uf));

				$sql21 = new Query($bd);
				$txt21 = "SELECT SEQUENCIACRE,
								VNOMECREDCRE,
								NNUMECATECRE,
								VCUPOMDESCRE						
						FROM TREDE_CREDENCIADOS
					WHERE CESTADOUFEST = :uf
					ORDER BY NNUMECATECRE";
				$sql21->addParam(':uf', $uf);
				$sql21->executeQuery($txt21);

				while (!$sql21->eof()) {
					$tpl->NOMEREDE 		= $sql21->result("VNOMECREDCRE");
					$tpl->CATE_REDE 	= $func->RetonaNomeCategoria($bd, $sql21->result("NNUMECATECRE"));
					$tpl->IDCRED		= $sql21->result("SEQUENCIACRE");
					$tpl->CUPOM			= $sql21->result("VCUPOMDESCRE");
					$id					= $sql21->result("SEQUENCIACRE");
					$imagem				= $func->RetornaImagem($bd, $id);
					if ($imagem <> '') {
						$tpl->IMAGEM		= $func->RetornaImagem($bd, $id);
					} else {
						$tpl->IMAGEM		= "comum/img/Sem-imagem.jpg";
					}
					$tpl->block("REDE_CIDADE");
					$sql21->next();
				}

				$tpl->block("ESTADO_REDE");
				$sql2->next();
			}
		} else {
			$sql2 = new Query($bd);
			$txt2 = "SELECT SEQUENCIACRE,
							VNOMECREDCRE, 
							VNOMEENDECRE ,
							NNUMEENDECRE,
							VNOMEBAIRCRE,  
							VNOMECIDAMUN,
							CESTADOUFMUN,
							CESTADOUFEST,
							NNUMECATECRE,
							NNUMESERVCRE,
							NNUMEIBGEMUN,
							CSITUACAOCRE,
							DDATACREDCRE,
							NNUMEREGIREG,
							VCUPOMDESCRE						
					FROM TREDE_CREDENCIADOS
					" . $cond1 . " 
					" . $cond2 . " 
					" . $cond3 . "
					" . $cond4 . "
					" . $cond5 . " 
					ORDER BY CESTADOUFMUN";
			//$sql2->addParam(':categoria',$cat);
			$sql2->executeQuery($txt2);


			while (!$sql2->eof()) {
				$tpl->NOMEREDE 		= strtoupper($sql2->result("VNOMECREDCRE"));
				$tpl->IDCRED		= $sql2->result("SEQUENCIACRE");
				$tpl->CUPOM			= $sql2->result("VCUPOMDESCRE");
				$id					= $sql2->result("SEQUENCIACRE");
				$tpl->IMAGEM		= $func->RetornaImagem($bd, $id);
				$res_nome	 		= $sql2->result("VNOMECREDCRE");
				$tpl->block("REDE");
				$sql2->next();
			}
			$tpl->block("REDE1");
		}
	}
}


$tpl->show();
$bd->close();
