<?php
  
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  //error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","redescredenciadas.html");
  
  if (isset($_SESSION['aut'])) {
    $autenticado     = TRUE;
    $_SESSION['aut'] = TRUE;
    $tpl->CON        = 1;
  } else {
    $autenticado = FALSE;
    $tpl->CON    = 0;
  }
  
  if (isset($_GET['cat'])) {
    $cat = $_GET['cat'];
  }
  
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($_SESSION['nomeEmpresa'] == SUAEMPRESA) {
      $tpl->block("SUAEMPRESA");
      $tpl->block("VER_OFERTAS");
    }
    
    if ($_SESSION['nomeEmpresa'] == SUAEMPRESA) {
      $tpl->block("VER_CALENDARIO_MIMO");
    }
    
    if ($_SESSION['nomeEmpresa'] == MIMOCLUBE) {
      $tpl->block("VER_CALENDARIO_MIMO");
    }
    
    $sql_rede = new Query($bd);
    $txt_rede = "SELECT TEXTO FROM TREDE_CONFIG_BASICS WHERE TIPOCONFIG = 'rede_cred'";
    $sql_rede->executeQuery($txt_rede);
    
    $tpl->REDE_CRED = utf8_encode($sql_rede->result("TEXTO"));
    
    if ($autenticado != TRUE) {
      
      $tpl->block("BTN_PN");
      $tpl->block("BTN_OF_CAL_NLOGADO");
      
      if (isset($_POST['listar'])) {
        
        $idregiao = $seg->antiInjection($_POST['regiao']);
        $id_reg   = $func->RetonaNomeRegiao($bd,$idregiao);
        
        $idestado = $seg->antiInjection($_POST['estado']);
        $id_est   = utf8_encode($func->RetonaNomeEstado($bd,$idestado));
        
        $idcidade = $seg->antiInjection($_POST['cid']);
        $id_cid   = utf8_encode($func->RetonaNomeCidade($bd,$idcidade));
        
        $pesquisa = strtoupper($seg->antiInjection($_POST['pesquisar']));
        
        $subcategoria = $seg->antiInjection($_POST['subcategoria']);
        
        if ($autenticado == TRUE) {
          $tpl->block("MENU_VER_OFERTAS");
        }
        
        if (($idregiao == "#") or ($idregiao == "")) {
          $cond1           = "WHERE NNUMEREGIREG IS NOT NULL";
          $tpl->REGIAOREDE = "TODAS AS REGIÕES <i class='fa fa-chevron-circle-right' aria-hidden='true'></i> ";
        } else {
          $cond1           = "WHERE NNUMEREGIREG = '".$idregiao."' ";
          $tpl->CRUMB2     = "<li class='breadcrumb-item'>".$id_reg."</li>";
          $tpl->REGIAOREDE = '<font color="red">REGIÃO '.$id_reg.' </font><i class="fa fa-chevron-circle-right" aria-hidden="true"></i> ';
        }
        
        if (($idestado == "#") or ($idestado == "")) {
          $cond2           = "AND CESTADOUFMUN IS NOT NULL";
          $tpl->ESTADOREDE = "TODOS OS ESTADOS <i class='fa fa-chevron-circle-right' aria-hidden='true'></i> ";
        } else {
          $cond2           = "AND CESTADOUFMUN = '".$idestado."' ";
          $tpl->CRUMB3     = "<li class='breadcrumb-item'>".$id_est."</li>";
          $tpl->ESTADOREDE = '<font color="red">'.$id_est.' <i class="fa fa-chevron-circle-right" aria-hidden="true"></i> ';
        }
        
        if (($idcidade == "#") or ($idcidade == "")) {
          $cond3           = "AND NNUMEIBGEMUN IS NOT NULL";
          $tpl->CIDADEREDE = 'TODAS AS CIDADES';
        } else {
          $cond3           = "AND NNUMEIBGEMUN = '".$idcidade."'";
          $tpl->CRUMB4     = "<li class='breadcrumb-item'><font color='red'>".$id_cid."</font></li>";
          $tpl->CIDADEREDE = $id_cid;
        }
        
        if ($pesquisa == NULL) {
          $cond4     = "AND VNOMECREDCRE IS NOT NULL";
          $tpl->PESQ = "";
        } else {
          $cond4     = "AND VNOMECREDCRE LIKE '%".$pesquisa."%'";
          $tpl->PESQ = "VOCÊ PESQUISOU <i><b>'".$pesquisa."'</b></i> | ";
        }
        
        if ($cat == '0') {
          $cond5 = "AND NNUMECATECRE IS NOT NULL";
        } else {
          $cond5 = "AND NNUMECATECRE = '".$cat."'";
        }
        
        if ($subcategoria == '#') {
          $cond6 = "AND NNUMECATESUB IS NOT NULL ";
        } else {
          $cond6           = "AND NNUMECATESUB = '".$subcategoria."' ";
          $tpl->SUBCATNOME = ucwords(utf8_encode($func->RetornaNomeSubCategoria($bd,$subcategoria)));
          $tpl->block("SUBCATE_TEXT");
        }
        
        
        if ($cat == '0') {
          $sql2 = new Query ($bd);
          $txt2 = "SELECT DISTINCT CESTADOUFEST
			    FROM TREDE_CREDENCIADOS
				".$cond1."
				".$cond2."
				".$cond3."
				".$cond4."
				".$cond5."
				".$cond6."
				AND CSITUACAOCRE = 'a'
				ORDER BY CESTADOUFMUN ASC";
          $sql2->executeQuery($txt2);
          
          $res_cat0 = $sql2->result("CESTADOUFEST");
          
          if ($res_cat0 == "") {
            $tpl->ESTADO1 = "<font color='red'>NENHUM RESULTADO ENCONTRADO!</font>";
            $tpl->block("ESTADO_REDE");
          } else {
            
            while (!$sql2->eof()) {
              $uf           = $sql2->result("CESTADOUFEST");
              $tpl->ESTADO1 = utf8_encode($func->RetonaNomeEstado($bd,$uf));
              
              $cates = $sql2->result("CESTADOUFEST");
              
              if ($subcategoria == '#') {
                $cond12 = "AND NNUMECATESUB IS NOT NULL ";
              } else {
                $cond12 = "AND NNUMECATESUB = '".$subcategoria."' ";
              }
              
              $sql21 = new Query ($bd);
              $txt21 = "SELECT SEQUENCIACRE,
								VNOMECREDCRE,
								NNUMECATECRE,
								NNUMECATESUB,
								VCUPOMDESCRE,
								CVIMAGEMCCRE
						FROM TREDE_CREDENCIADOS
					WHERE CESTADOUFEST = :uf
					".$cond12."
					".$cond3."
					ORDER BY NNUMECATECRE";
              $sql21->addParam(':uf',$uf);
              $sql21->executeQuery($txt21);
              
              while (!$sql21->eof()) {
                $tpl->NOMEREDE  = $sql21->result("VNOMECREDCRE");
                $tpl->CATE_REDE = $func->RetonaNomeCategoria($bd,$sql21->result("NNUMECATECRE"));
                $tpl->IDCRED    = $sql21->result("SEQUENCIACRE");
                //$tpl->CUPOM			= $sql21->result("VCUPOMDESCRE");
                
                $id     = $sql21->result("SEQUENCIACRE");
                $subcat = $sql21->result("NNUMECATESUB");
                
                $tpl->SUBCATS = ucwords(utf8_encode($func->RetornaNomeSubCategoria($bd,$subcat)));
                
                $imagem = $sql21->result("CVIMAGEMCCRE");
                
                if (($imagem == NULL) or (substr($imagem,0,7) != 'uploads')) {
                  $tpl->IMAGEM = '../comum/img/Sem-imagem.jpg';
                } else {
                  $tpl->IMAGEM = 'admin/'.$imagem;
                }
                
                $tpl->block("REDE_CIDADE");
                $sql21->next();
              }
              
              $tpl->block("ESTADO_REDE");
              $sql2->next();
            }
          }
        } else {
          
          $sql2 = new Query ($bd);
          $sql2->clear();
          $txt2 = "SELECT DISTINCT CESTADOUFEST
			    FROM TREDE_CREDENCIADOS
				".$cond1."
				".$cond2."
				".$cond3."
				".$cond4."
				".$cond5."
				".$cond6."
				AND CSITUACAOCRE = 'a'
				ORDER BY CESTADOUFMUN ASC";
          $sql2->executeQuery($txt2);
          
          while (!$sql2->eof()) {
            $res_cat0 = $sql2->result("CESTADOUFEST");
            
            $tpl->ESTADO1 = utf8_encode($func->RetonaNomeEstado($bd,$res_cat0));
            
            $sql21 = new Query ($bd);
            $sql21->clear();
            $txt21 = "SELECT SEQUENCIACRE,
							VNOMECREDCRE,
							VNOMEENDECRE,
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
							NNUMECATESUB,
							VCUPOMDESCRE,
							CVIMAGEMCCRE,
                CTIPOCRED
					FROM TREDE_CREDENCIADOS
					WHERE CESTADOUFEST = :uf
					AND  NNUMECATECRE = :cat
					".$cond3."
					ORDER BY CESTADOUFMUN ASC";
            $sql21->addParam(':uf',$res_cat0);
            $sql21->addParam(':cat',$cat);
            $sql21->executeQuery($txt21);
            
            while (!$sql21->eof()) {
              $tpl->NOMEREDE = ucwords(utf8_encode($sql21->result("VNOMECREDCRE")));
              $tpl->IDCRED   = $sql21->result("SEQUENCIACRE");
              //$tpl->CUPOM			= $sql2->result("VCUPOMDESCRE");
              $tpl->CATE_REDE = $func->RetonaNomeCategoria($bd,$sql21->result("NNUMECATECRE"));
              
              $subcat       = $sql21->result("NNUMECATESUB");
              $tpl->SUBCATS = ucwords(utf8_encode($func->RetornaNomeSubCategoria($bd,$subcat)));
              
              $id = $sql21->result("SEQUENCIACRE");
              
              $imagem    = $sql21->result("CVIMAGEMCCRE");
              $tipo_cred = $sql21->result("CTIPOCRED");
              
              if (($imagem == NULL) or (substr($imagem,0,7) != 'uploads')) {
                $tpl->IMAGEM = '../comum/img/Sem-imagem.jpg';
              } else {
                $tpl->IMAGEM = 'admin/'.$imagem;
              }
              
              $res_nome = $sql21->result("VNOMECREDCRE");
              $tpl->block("REDE");
              $sql21->next();
            }
            
            $tpl->block("REDE1");
            $sql2->next();
          }
        }
      }//listar
      
    } else {
      
      $tpl->block("BTN_OF_CAL_LOGADO");
      $tpl->block("BTN_P");
      
      $tpl->ID_SESSAO = $_SESSION['idSessao'];
      
      $id_sessao = $_SESSION['idSessao'];
      $id_confer = $_GET['idSessao'];
      $seq       = $_SESSION['idUsuario'];
      
      $seg->verificaSession($id_sessao);
      
      $func->AtualizaStatusUsuario($seq);
      $tpl->IDUSUA     = $_SESSION['idUsuario'];
      //$tpl->ID = "#";
      
      //INFORMAÇÕES DO USUARIO
      $tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
      
      //INFORMAÇÕES DO USUARIO
      
      $func->AtualizaStatusUsuario($seq);
      
      $sql_v = new Query ($bd);
      $txt_v = "SELECT SEQUPAGPLAN, DDTFIMPPLAN,CSITPAGPLAN FROM TREDE_PAGAPLANO
				WHERE NIDUPAGPLAN = :idusua";
      $sql_v->addParam(':idusua',$seq);
      $sql_v->executeQuery($txt_v);
      
      $seqplano  = $sql_v->result("SEQUPAGPLAN");
      $datafim   = $sql_v->result("DDTFIMPPLAN");
      $dataatual = date('Y-m-d');
      $tipopg    = $sql_v->result("CSITPAGPLAN");
      
      if ($tipopg != 3) {
        $tpl->DISAB = '';
      }
      
      $sql21 = new Query ($bd);
      $txt21 = "SELECT REDE_PLANUSU
			   FROM TREDE_USUADMIN
			  WHERE REDE_SEQUSUA = :usua";
      $sql21->AddParam(':usua',$seq);
      $sql21->executeQuery($txt21);
      
      $res_assi = $sql21->result("REDE_PLANUSU");
      
      $sql22 = new Query ($bd);
      $txt22 = "SELECT DDTFIMPPLAN
			   FROM TREDE_PAGAPLANO
			  WHERE NIDUPAGPLAN = :usua";
      $sql22->AddParam(':usua',$seq);
      $sql22->executeQuery($txt22);
      
      $data_vence = $data->formataData1($sql22->result("DDTFIMPPLAN"));
      
      
      if ($cat == '0') {
        $tpl->CRUMB1  = "<li class='breadcrumb-item'>Todas as Categorias</li>";
        $tpl->CAT     = "Todas as Categorias";
        $tpl->ACTIVE1 = "active";
        $tpl->ACTIVE2 = "active";
        $tpl->ACTIVE3 = "active";
        $tpl->ACTIVE4 = "active";
        $tpl->ACTIVE5 = "active";
        
      } else {
        
        $sql = new Query();
        $sql->clear();
        $txt = "SELECT NNUMECATECAT,
                     VNOMECATECAT,
                     VSITUCATECAT
                FROM TREDE_CATEGORIAS
                WHERE NNUMECATECAT = '".$cat."'";
        $sql->executeQuery($txt);
        
        $tpl->CAT = utf8_encode($sql->result("VNOMECATECAT"));
        $nome_cat = utf8_encode($sql->result("VNOMECATECAT"));
        $ncat     = $sql->result("NNUMECATECAT");
        
        $tpl->CRUMB1 = "<li class='breadcrumb-item'>".$nome_cat."</li>";
        
        if ($ncat == '1') {
          $tpl->ACTIVE1 = "active";
          
        } else if ($ncat == '2') {
          $tpl->ACTIVE2 = "active";
          
        } else if ($ncat == '3') {
          $tpl->ACTIVE3 = "active";
          
        } else if ($ncat == '4') {
          $tpl->ACTIVE4 = "active";
          
        } else if ($ncat == '5') {
          $tpl->ACTIVE5 = "active";
        }
      }
      
      
      // select para trazer a regiao //
      $sql = new Query ($bd);
      $txt = "SELECT NNUMEREGIREG, VNOMEREGIREG FROM TREDE_REGIAO";
      $sql->executeQuery($txt);
      
      while (!$sql->eof()) {
        $tpl->ID_REGIAO   = $sql->result("NNUMEREGIREG");
        $tpl->NOME_REGIAO = $sql->result("VNOMEREGIREG");
        
        $tpl->block("REGIAO");
        $sql->next();
      }
      // select para trazer a regiao //
      
      
      // select para trazer a subcategoria //
      if ($cat != '0') {
        $cond1_cat = "WHERE NNUMECATECAT = '".$cat."' ";
      } else {
        $cond1_cat = "WHERE NNUMECATECAT IS NOT NULL";
      }
      
      $sql33 = new Query ($bd);
      $txt33 = "SELECT VNOMECATESUB, NNUMECATESUB
				FROM TREDE_SUBCATEGORIA
			   ".$cond1_cat."
			   ORDER BY VNOMECATESUB ASC";
      $sql33->executeQuery($txt33);
      
      
      while (!$sql33->eof()) {
        $tpl->ID_SUBCAT   = $sql33->result("NNUMECATESUB");
        $tpl->NOME_SUBCAT = ucwords(utf8_encode($sql33->result("VNOMECATESUB")));
        
        $tpl->block("SUBCATE");
        $sql33->next();
      }
      // select para trazer a subcategoria //
      
      
      ///////////////////////////////////////////
      ///////////////////////////////////////////
      ///////////////////////////////////////////
      
      if (isset($_GET['pesq'])) {
        $palavra = $_GET['pesq'];
        
        if (($palavra != "") or ($palavra != NULL)) {
          $sql2 = new Query ($bd);
          $txt2 = "SELECT DISTINCT CESTADOUFEST
			    FROM TREDE_CREDENCIADOS
                WHERE SEQUENCIACRE IN (SELECT SEQUENCIACRE FROM TREDE_PRODUTOS WHERE VNOMEPRODU LIKE '%".$palavra."%')
				ORDER BY CESTADOUFEST ASC";
          $sql2->executeQuery($txt2);
          
          $res_cat0  = $sql2->result("CESTADOUFEST");
          $tpl->PESQ = "VOCÊ PESQUISOU <i><b>'".$palavra."'</b></i>";
          
          if ($res_cat0 == "") {
            $tpl->ESTADO1 = "<font color='red'>NENHUM RESULTADO ENCONTRADO!</font>";
            $tpl->block("ESTADO_REDE");
          } else {
            
            while (!$sql2->eof()) {
              $uf           = $sql2->result("CESTADOUFEST");
              $tpl->ESTADO1 = utf8_encode($func->RetonaNomeEstado($bd,$uf));
              
              $cates = $sql2->result("CESTADOUFEST");
              
              $sql21 = new Query ($bd);
              $txt21 = "SELECT SEQUENCIACRE,
								VNOMECREDCRE,
								NNUMECATECRE,
								NNUMECATESUB,
								VCUPOMDESCRE,
								CVIMAGEMCCRE,
                CTIPOCRED
						FROM TREDE_CREDENCIADOS
					WHERE CESTADOUFEST = :uf
            AND SEQUENCIACRE IN (SELECT SEQUENCIACRE FROM TREDE_PRODUTOS WHERE VNOMEPRODU LIKE '%".$palavra."%')
            AND CSITUACAOCRE = 'a'
					ORDER BY NNUMECATECRE";
              $sql21->addParam(':uf',$uf);
              $sql21->executeQuery($txt21);
              
              while (!$sql21->eof()) {
                $tpl->NOMEREDE  = utf8_encode($sql21->result("VNOMECREDCRE"));
                $tpl->CATE_REDE = utf8_encode($func->RetonaNomeCategoria($bd,$sql21->result("NNUMECATECRE")));
                $tpl->IDCRED    = $sql21->result("SEQUENCIACRE");
                //$tpl->CUPOM			= $sql21->result("VCUPOMDESCRE");
                
                $id     = $sql21->result("SEQUENCIACRE");
                $subcat = $sql21->result("NNUMECATESUB");
                
                $tpl->SUBCATS = ucwords(utf8_encode($func->RetornaNomeSubCategoria($bd,$subcat)));
                
                $imagem = $sql21->result("CVIMAGEMCCRE");
                
                
                if (($imagem == NULL) or (substr($imagem,0,7) != 'uploads')) {
                  $tpl->IMAGEM = 'comum/img/Sem-imagem.jpg';
                } else {
                  $tpl->IMAGEM = 'admin/'.$imagem;
                }
                
                $tpl->block("REDE_CIDADE");
                $sql21->next();
              }
              
              $tpl->block("ESTADO_REDE");
              $sql2->next();
            }
          }
        }
        
        //////////////////////////////////////////////////
        //////////////////////////////////////////////////
        //////////////////////////////////////////////////
      }
      
      
      if (isset($_POST['listar'])) {
        
        $idregiao = $seg->antiInjection($_POST['regiao']);
        $id_reg   = $func->RetonaNomeRegiao($bd,$idregiao);
        
        $idestado = $seg->antiInjection($_POST['estado']);
        $id_est   = utf8_encode($func->RetonaNomeEstado($bd,$idestado));
        
        $idcidade = $seg->antiInjection($_POST['cid']);
        $id_cid   = utf8_encode($func->RetonaNomeCidade($bd,$idcidade));
        
        $pesquisa = strtoupper($seg->antiInjection($_POST['pesquisar']));
        
        $subcategoria = $seg->antiInjection($_POST['subcategoria']);
        
        
        if (($idregiao == "#") or ($idregiao == "")) {
          $cond1           = "WHERE NNUMEREGIREG IS NOT NULL";
          $tpl->REGIAOREDE = "TODAS AS REGIÕES <i class='fa fa-chevron-circle-right' aria-hidden='true'></i> ";
        } else {
          $cond1           = "WHERE NNUMEREGIREG = '".$idregiao."' ";
          $tpl->CRUMB2     = "<li class='breadcrumb-item'>".$id_reg."</li>";
          $tpl->REGIAOREDE = '<font color="red">REGIÃO '.$id_reg.' </font><i class="fa fa-chevron-circle-right" aria-hidden="true"></i> ';
        }
        
        if (($idestado == "#") or ($idestado == "")) {
          $cond2           = "AND CESTADOUFMUN IS NOT NULL";
          $tpl->ESTADOREDE = "TODOS OS ESTADOS <i class='fa fa-chevron-circle-right' aria-hidden='true'></i> ";
        } else {
          $cond2           = "AND CESTADOUFMUN = '".$idestado."' ";
          $tpl->CRUMB3     = "<li class='breadcrumb-item'>".$id_est."</li>";
          $tpl->ESTADOREDE = '<font color="red">'.$id_est.' <i class="fa fa-chevron-circle-right" aria-hidden="true"></i> ';
        }
        
        if (($idcidade == "#") or ($idcidade == "")) {
          $cond3           = "AND NNUMEIBGEMUN IS NOT NULL";
          $tpl->CIDADEREDE = 'TODAS AS CIDADES';
        } else {
          $cond3           = "AND NNUMEIBGEMUN = '".$idcidade."'";
          $tpl->CRUMB4     = "<li class='breadcrumb-item'><font color='red'>".$id_cid."</font></li>";
          $tpl->CIDADEREDE = $id_cid;
        }
        
        if ($pesquisa == NULL) {
          $cond4     = "AND VNOMECREDCRE IS NOT NULL";
          $tpl->PESQ = "";
        } else {
          $cond4     = "AND VNOMECREDCRE LIKE '%".$pesquisa."%'";
          $tpl->PESQ = "VOCÊ PESQUISOU <i><b>'".$pesquisa."'</b></i> | ";
        }
        
        if ($cat == '0') {
          $cond5 = "AND NNUMECATECRE IS NOT NULL";
        } else {
          $cond5 = "AND NNUMECATECRE = '".$cat."'";
        }
        
        if ($subcategoria == '#') {
          $cond6 = "AND NNUMECATESUB IS NOT NULL ";
        } else {
          $cond6           = "AND NNUMECATESUB = '".$subcategoria."' ";
          $tpl->SUBCATNOME = ucwords(utf8_encode($func->RetornaNomeSubCategoria($bd,$subcategoria)));
          $tpl->block("SUBCATE_TEXT");
        }
        
        
        if ($cat == '0') {
          $sql2 = new Query ($bd);
          $txt2 = "SELECT DISTINCT CESTADOUFEST
			    FROM TREDE_CREDENCIADOS
				".$cond1."
				".$cond2."
				".$cond3."
				".$cond4."
				".$cond5."
				".$cond6."
				AND CSITUACAOCRE = 'a'
				ORDER BY CESTADOUFMUN ASC";
          $sql2->executeQuery($txt2);
          
          $res_cat0 = $sql2->result("CESTADOUFEST");
          
          if ($res_cat0 == "") {
            $tpl->ESTADO1 = "<font color='red'>NENHUM RESULTADO ENCONTRADO!</font>";
            $tpl->block("ESTADO_REDE");
          } else {
            
            while (!$sql2->eof()) {
              $uf           = $sql2->result("CESTADOUFEST");
              $tpl->ESTADO1 = utf8_encode($func->RetonaNomeEstado($bd,$uf));
              
              $cates = $sql2->result("CESTADOUFEST");
              
              if ($subcategoria == '#') {
                $cond12 = "AND NNUMECATESUB IS NOT NULL ";
              } else {
                $cond12 = "AND NNUMECATESUB = '".$subcategoria."' ";
              }
              
              $sql21 = new Query ($bd);
              $txt21 = "SELECT SEQUENCIACRE,
								VNOMECREDCRE,
								NNUMECATECRE,
								NNUMECATESUB,
								VCUPOMDESCRE,
								CVIMAGEMCCRE,
                CTIPOCRED
						FROM TREDE_CREDENCIADOS
					WHERE CESTADOUFEST = :uf
					".$cond12."
					".$cond3."
					ORDER BY NNUMECATECRE";
              $sql21->addParam(':uf',$uf);
              $sql21->executeQuery($txt21);
              
              while (!$sql21->eof()) {
                $tpl->NOMEREDE  = $sql21->result("VNOMECREDCRE");
                $tpl->CATE_REDE = $func->RetonaNomeCategoria($bd,$sql21->result("NNUMECATECRE"));
                $tpl->IDCRED    = $sql21->result("SEQUENCIACRE");
                //$tpl->CUPOM			= $sql21->result("VCUPOMDESCRE");
                
                $id     = $sql21->result("SEQUENCIACRE");
                $subcat = $sql21->result("NNUMECATESUB");
                
                
                $tpl->SUBCATS = ucwords(utf8_encode($func->RetornaNomeSubCategoria($bd,$subcat)));
                
                $imagem = $sql21->result("CVIMAGEMCCRE");
                
                if (($imagem == NULL) or (substr($imagem,0,7) != 'uploads')) {
                  $tpl->IMAGEM = 'comum/img/Sem-imagem.jpg';
                } else {
                  $tpl->IMAGEM = 'admin/'.$imagem;
                }
                
                
                $tpl->block("REDE_CIDADE");
                $sql21->next();
              }
              
              $tpl->block("VER_OFERTAS");
              $tpl->block("ESTADO_REDE");
              $sql2->next();
            }
          }
        } else {
          
          $sql2 = new Query ($bd);
          $sql2->clear();
          $txt2 = "SELECT DISTINCT CESTADOUFEST
			    FROM TREDE_CREDENCIADOS
				".$cond1."
				".$cond2."
				".$cond3."
				".$cond4."
				".$cond5."
				".$cond6."
				AND CSITUACAOCRE = 'a'
				ORDER BY CESTADOUFMUN ASC";
          $sql2->executeQuery($txt2);
          
          while (!$sql2->eof()) {
            $res_cat0 = $sql2->result("CESTADOUFEST");
            
            $tpl->ESTADO1 = utf8_encode($func->RetonaNomeEstado($bd,$res_cat0));
            
            $sql21 = new Query ($bd);
            $sql21->clear();
            $txt21 = "SELECT SEQUENCIACRE,
							VNOMECREDCRE, 
							VNOMEENDECRE,
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
							NNUMECATESUB,
							VCUPOMDESCRE,
							CVIMAGEMCCRE,
                CTIPOCRED
					FROM TREDE_CREDENCIADOS
					WHERE CESTADOUFEST = :uf    
					AND  NNUMECATECRE = :cat
					".$cond3."
					ORDER BY CESTADOUFMUN ASC";
            $sql21->addParam(':uf',$res_cat0);
            $sql21->addParam(':cat',$cat);
            $sql21->executeQuery($txt21);
            
            while (!$sql21->eof()) {
              $tpl->NOMEREDE = ucwords(utf8_encode($sql21->result("VNOMECREDCRE")));
              $tpl->IDCRED   = $sql21->result("SEQUENCIACRE");
              //$tpl->CUPOM			= $sql2->result("VCUPOMDESCRE");
              $tpl->CATE_REDE = $func->RetonaNomeCategoria($bd,$sql21->result("NNUMECATECRE"));
              
              $subcat       = $sql21->result("NNUMECATESUB");
              $tpl->SUBCATS = ucwords(utf8_encode($func->RetornaNomeSubCategoria($bd,$subcat)));
              
              $id        = $sql21->result("SEQUENCIACRE");
              $tipo_cred = $sql21->result("CTIPOCRED");
              

              $imagem = $sql21->result("CVIMAGEMCCRE");
              
              if (($imagem == NULL) or (substr($imagem,0,7) != 'uploads')) {
                $tpl->IMAGEM = 'comum/img/Sem-imagem.jpg';
              } else {
                $tpl->IMAGEM = 'admin/'.$imagem;
              }
              
              $res_nome = $sql21->result("VNOMECREDCRE");
              $tpl->block("REDE");
              $sql21->next();
            }
            
            $tpl->block("REDE1");
            $sql2->next();
          }
        }
        
      }//listar
      
      
    }
    
    if (isset($_POST['acessar'])) {
      
      $email_login = strtolower(trim($seg->antiInjection($_POST['login'])));
      $senha_login = md5($seg->antiInjection($_POST['senha']));
      
      $sqlc = new Query ($bd);
      $txtc = "SELECT NNUMESIND
				FROM TREDE_SINDICATOS
				WHERE CEMAISIND = :email";
      $sqlc->addPAram(':email',$email_login);
      $sqlc->executeQuery($txtc);
      
      $email_sindicatio = $sqlc->result("NNUMESIND");
      
      if ($email_sindicatio != "") {
        
        $tipo_usua = "sindicato";
        
      } else {
        
        $sqla = new Query ($bd);
        $txta = "SELECT REDE_SEQUSUA,REDE_ADMINUS
				FROM TREDE_USUADMIN
				WHERE REDE_EMAILUS = :email";
        $sqla->addPAram(':email',$email_login);
        $sqla->executeQuery($txta);
        
        $res_ctipo_usua = $sqla->result("REDE_ADMINUS");
        
        if ($res_ctipo_usua == 's') {
          $tipo_usua = "admin";
        } else {
          $res_email_usua = $sqla->result("REDE_SEQUSUA");
          
          if ($res_email_usua == '') {
            $sqlb = new Query ($bd);
            $txtb = "SELECT SEQUENCIACRE
			    FROM TREDE_CREDENCIADOS
				WHERE VLOGEMAILCRE = :email";
            $sqlb->addPAram(':email',$email_login);
            $sqlb->executeQuery($txtb);
            
            $res_email_rede = $sqlb->result("SEQUENCIACRE");
            
            if ($res_email_rede == '') {
              $tipo_usua = "noexist";
            } else {
              $tipo_usua = "rede";
            }
            
          } else {
            $tipo_usua = "usua";
          }
        }
      }
      
      //CONTAR A QUANTIDADES DE TENTATIVAS
      
      if ($tipo_usua == 'usua') {
        
        $sql2 = new Query ($bd);
        $txt2 = "SELECT COUNT(*) TENTATIVAS
				FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email";
        $sql2->addPAram(':email',$email_login);
        $sql2->executeQuery($txt2);
        
        $tentativas = $sql2->result("TENTATIVAS") + 1;
        
        if ($tentativas >= 5) { //APÓS 5 TENTATIVAS O USUARIO É BLOQUADO
          
          //DAR O UPDATE E BLOQUEAR O USUARIO
          $sql4 = new Query ($bd);
          $txt4 = "UPDATE TREDE_USUADMIN SET REDE_USUBLOC = 's'
					WHERE REDE_EMAILUS = '".$email_login."' ";
          $sql4->executeSQL($txt4);
          
          $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
          $tpl->block("ERRO");
          
          $redefinicao       = rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
          $valor_redefinicao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789@#&"),0,$redefinicao);
          
          $_SESSION['idDef']     = $valor_redefinicao;
          $_SESSION['idSession'] = $valor_redefinicao;
          
          $util->redireciona("redefinicao.php?idDef=".$_SESSION['idDef'],"N","","0");
          
          $_SESSION['redefinir'] = '<h5><font color="red">Seu login foi bloqueado, redefina a sua senha.</font></h5>';
        }
        
        
        //SELECT PARA VERIFICAR A SENHA
        $sql = new Query ($bd);
        $txt = "SELECT REDE_SEQUSUA,
                    REDE_NOMEUSU,
                    REDE_CPFUSUA,
                    REDE_ADMINUS,
                    REDE_EMAILUS,
                    REDE_SENHAUS,
                    REDE_TIPOUSU,
                    REDE_USUBLOC,
       							REDE_LOGBLOK
				FROM TREDE_USUADMIN
				WHERE REDE_EMAILUS = :email_login";
        $sql->addPAram(':email_login',$email_login);
        $sql->executeQuery($txt);
        
        $seq_usuas = $sql->result("REDE_SEQUSUA");
        $res_senha = $sql->result("REDE_SENHAUS");
        $res_admin = $sql->result("REDE_ADMINUS");
        $res_block = $sql->result("REDE_USUBLOC");
        $res_login = $sql->result("REDE_EMAILUS");
        $ubloqueio = $sql->result("REDE_LOGBLOK");
        
        $res = strcmp($res_senha,$senha_login);
        
        if ($seq_usuas == '') {
          $tpl->MSG = '<font color="red">USUÁRIO NÃO EXISTE!!!</font>';
          $tpl->block("ERRO");
        } else if ($ubloqueio == 's') {
          $tpl->MSG = '<font color="red">LOGIN BLOQUEADO, ENTRAR EM CONTATO COM ADMINISTRADOR!!!</font>';
          $tpl->block("ERRO");
        } else if ($res_login <> $email_login) {
          $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
          $tpl->block("ERRO");
        } else if ($res <> 0) {
          $tpl->MSG  = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
          $tpl->MSG1 = 'Tentativa '.$tentativas.' de 5.';
          $tpl->block("ERRO");
          $seg->registraLogin($bd,$res_login);
        } else if ($res_login <> $email_login && $res <> 0) {
          $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
          $tpl->block("ERRO");
        } else if ($email_login == '' && md5($senha == '')) {
          $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
          $tpl->block("ERRO");
        } else if ($res_block == 'S') {
          $tpl->MSG = '<font color="red">SEU LOGIN ESTA BLOQUEADO, CLIQUE EM REDEFINIR A SENHA.</font>';
          $tpl->block("ERRO");
        } else {
          
          $aleatorio = mt_rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
          $valor     = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$aleatorio);
          
          $_SESSION['idSessao']  = $valor;
          $_SESSION['idUsuario'] = $seq_usuas;
          $_SESSION['aut']       = TRUE;
          $_COOKIE['idUsuario']  = $seq_usuas;
          
          
          //Apaga todos os registros do dia
          $sql3 = new Query($bd);
          $txt3 = "DELETE FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email
					AND DDATATENTSEG = :data";
          $sql3->addParam(":email",$email_login);
          $sql3->addParam(":data",date('Y-m-d'));
          $sql3->executeSQL($txt3);
          
          //            $sql4 = new Query($bd);
          //            $txt4 = "INSERT INTO ";
          //            $sql4->addParam(":email", $email_login);
          //            $sql4->addParam(":data", date('Y-m-d'));
          //            $sql4->executeSQL($txt4);
          
          $util->redireciona("index.php?idSessao=".$_SESSION['idSessao']);
        }
      } else if ($tipo_usua == 'rede') {
        
        $sql2 = new Query ($bd);
        $txt2 = "SELECT COUNT(*) TENTATIVAS
				FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email";
        $sql2->addPAram(':email',$email_login);
        $sql2->executeQuery($txt2);
        
        $tentativas = $sql2->result("TENTATIVAS") + 1;
        
        if ($tentativas >= 5) { //APÓS 5 TENTATIVAS O USUARIO É BLOQUADO
          
          //DAR O UPDATE E BLOQUEAR O USUARIO
          $sql4 = new Query ($bd);
          $txt4 = "UPDATE TREDE_CREDENCIADOS SET CBLOCKLOGCRE = 's'
					WHERE VLOGEMAILCRE = '".$email_login."' ";
          $sql4->executeSQL($txt4);
          
          $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
          $tpl->block("ERRO");
          
          $redefinicao       = rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
          $valor_redefinicao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789@#&"),0,$redefinicao);
          
          $_SESSION['idDef']     = $valor_redefinicao;
          $_SESSION['idSession'] = $valor_redefinicao;
          
          $util->redireciona("redefinicao.php?idDef=".$_SESSION['idDef'],"N","","0");
          
          $_SESSION['redefinir'] = '<h5><font color="red">Seu login foi bloqueado, redefina a sua senha.</font></h5>';
        }
        
        $sql = new Query ($bd);
        $txt = "SELECT SEQUENCIACRE,
                    VSENHAREDCRE,
                    VLOGEMAILCRE,
                    CBLOCKLOGCRE,
                    CSITUACAOCRE
				FROM TREDE_CREDENCIADOS
				WHERE VLOGEMAILCRE = :email_login ";
        $sql->addPAram(':email_login',$email_login);
        $sql->executeQuery($txt);
        
        $seq_usuas   = $sql->result("SEQUENCIACRE");
        $res_senha   = $sql->result("VSENHAREDCRE");
        $res_block   = $sql->result("CBLOCKLOGCRE");
        $res_login   = strtolower($sql->result("VLOGEMAILCRE"));
        $status_cred = $sql->result("CSITUACAOCRE");
        
        $res = strcmp($res_senha,$senha_login);
        
        
        if ($seq_usuas == '') {
          $tpl->MSG = '<font color="red">USUÁRIO ADMIN NÃO EXISTE!!!</font>';
          $tpl->block("ERRO");
        } else if ($res_login <> $email_login) {
          $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 1!!!</font>';
          $tpl->block("ERRO");
        } else if ($res <> 0) {
          $tpl->MSG  = '<font color="red">LOGIN OU SENHA INCORRETOS 2!!!</font>';
          $tpl->MSG1 = 'Tentativa '.$tentativas.' de 5.';
          $tpl->block("ERRO");
          $seg->registraLogin($bd,$res_login);
        } else if ($res_login <> $email_login && $res <> 0) {
          $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 3!!!</font>';
          $tpl->block("ERRO");
        } else if ($email_login == '' && md5($senha == '')) {
          $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 4!!!</font>';
          $tpl->block("ERRO");
        } else if ($res_block == 'S') {
          $tpl->MSG = '<font color="red">SEU LOGIN ESTA BLOQUEADO, CLIQUE EM REDEFINIR A SENHA.</font>';
          $tpl->block("ERRO");
        } else if ($status_cred == 'p') {
          $tpl->MSG = '<font color="red">SEU CADASTRO ESTÁ PENDENTE, POR FAVOR AGUARDE O ADMINISTRADOR ANALISAR.</font>';
          $tpl->block("ERRO");
        } else if ($status_cred == 'c') {
          $tpl->MSG = '<font color="red">SEU CADASTRO FOI CANCELADO. ENTRAR EM CONTATO COM O ADMINISTRADOR</font>';
          $tpl->block("ERRO");
        } else {
          
          $aleatorio = mt_rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
          $valor     = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$aleatorio);
          
          $_SESSION['idSessao_rede'] = $valor;
          $_SESSION['idRede']        = $seq_usuas;
          $_SESSION['aut_rede']      = TRUE;
          
          //Apaga todos os registros do dia
          $sql3 = new Query($bd);
          $txt3 = "DELETE FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email
					AND DDATATENTSEG = :data";
          $sql3->addParam(":email",$email_login);
          $sql3->addParam(":data",date('Y-m-d'));
          $sql3->executeSQL($txt3);
          
          $util->redireciona('rede/index.php?idSessao='.$_SESSION['idSessao']);
        }
        
        
      } else if ($tipo_usua == 'admin') {
        
        $sql2 = new Query ($bd);
        $txt2 = "SELECT COUNT(*) TENTATIVAS
				FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email";
        $sql2->addPAram(':email',$email_login);
        $sql2->executeQuery($txt2);
        
        $tentativas = $sql2->result("TENTATIVAS") + 1;
        
        if ($tentativas >= 5) { //APÓS 5 TENTATIVAS O USUARIO É BLOQUADO
          
          //DAR O UPDATE E BLOQUEAR O USUARIO
          $sql4 = new Query ($bd);
          $txt4 = "UPDATE TREDE_USUADMIN SET REDE_USUBLOC = 's'
					WHERE REDE_EMAILUS = '".$email_login."' ";
          $sql4->executeSQL($txt4);
          
          $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
          $tpl->block("ERRO");
          
          $redefinicao       = rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
          $valor_redefinicao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789@#&"),0,$redefinicao);
          
          $_SESSION['idDef']     = $valor_redefinicao;
          $_SESSION['idSession'] = $valor_redefinicao;
          $_SESSION['usuadmin']  = $admin;
          $_SESSION['admin']     = $admin;
          
          $util->redireciona("redefinicao.php?idDef=".$_SESSION['idDef'],"N","","0");
          
          $_SESSION['redefinir'] = '<h5><font color="red">Seu login foi bloqueado, redefina a sua senha.</font></h5>';
        }
        
        
        //SELECT PARA VERIFICAR A SENHA
        $sql = new Query ($bd);
        $txt = "SELECT REDE_SEQUSUA,
                    REDE_NOMEUSU,
                    REDE_CPFUSUA,
                    REDE_ADMINUS,
                    REDE_EMAILUS,
                    REDE_SENHAUS,
                    REDE_TIPOUSU,
                    REDE_USUBLOC
				FROM TREDE_USUADMIN
				WHERE REDE_EMAILUS = :email_login
				  AND REDE_ADMINUS = 's' ";
        $sql->addPAram(':email_login',$email_login);
        $sql->executeQuery($txt);
        
        $seq_usuas = $sql->result("REDE_SEQUSUA");
        $res_senha = $sql->result("REDE_SENHAUS");
        $res_admin = $sql->result("REDE_ADMINUS");
        $res_block = $sql->result("REDE_USUBLOC");
        $res_login = $sql->result("REDE_EMAILUS");
        $admin     = $sql->result("REDE_SEQUSUA");
        
        
        $res = strcmp($res_senha,$senha_login);
        
        if ($seq_usuas == '') {
          $tpl->MSG = '<font color="red">USUÁRIO ADMIN NÃO EXISTE!!!</font>';
          $tpl->block("ERRO");
        } else if ($res_login <> $email_login) {
          $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 1!!!</font>';
          $tpl->block("ERRO");
        } else if ($res <> 0) {
          $tpl->MSG  = '<font color="red">LOGIN OU SENHA INCORRETOS 2!!!</font>';
          $tpl->MSG1 = 'Tentativa '.$tentativas.' de 5.';
          $tpl->block("ERRO");
          $seg->registraLogin($bd,$res_login);
        } else if ($res_login <> $email_login && $res <> 0) {
          $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 3!!!</font>';
          $tpl->block("ERRO");
        } else if ($email_login == '' && md5($senha == '')) {
          $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 4!!!</font>';
          $tpl->block("ERRO");
        } else if ($res_block == 'S') {
          $tpl->MSG = '<font color="red">SEU LOGIN ESTA BLOQUEADO, CLIQUE EM REDEFINIR A SENHA.</font>';
          $tpl->block("ERRO");
        } else {
          
          $aleatorio = mt_rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
          $valor     = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$aleatorio);
          
          $_SESSION['idSessao'] = $valor;
          $_SESSION['idAdmin']  = $seq_usuas;
          $_SESSION['usuadmin'] = $admin;
          $_SESSION['admin']    = $admin;
          
          //sdebug($seq_usuas,true);
          
          //Apaga todos os registros do dia
          $sql3 = new Query($bd);
          $txt3 = "DELETE FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email
					AND DDATATENTSEG = :data";
          $sql3->addParam(":email",$email_login);
          $sql3->addParam(":data",date('Y-m-d'));
          $sql3->executeSQL($txt3);
          
          $util->redireciona('admin/index.php?idSessao='.$_SESSION['idSessao'].'&idAdmin='.$_SESSION['idAdmin']);
        }
      } else if ($tipo_usua == 'sindicato') {
        
        $sql2 = new Query ($bd);
        $txt2 = "SELECT COUNT(*) TENTATIVAS
				FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email";
        $sql2->addPAram(':email',$email_login);
        $sql2->executeQuery($txt2);
        
        $tentativas = $sql2->result("TENTATIVAS") + 1;
        
        if ($tentativas >= 5) { //APÓS 5 TENTATIVAS O USUARIO É BLOQUADO
          
          //DAR O UPDATE E BLOQUEAR O USUARIO
          $sql4 = new Query ($bd);
          $txt4 = "UPDATE TREDE_SINDICATOS SET BLOCKSIND = 's'
					WHERE CEMAISIND = '".$email_login."' ";
          $sql4->executeSQL($txt4);
          
          $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
          $tpl->block("ERRO");
          
          $redefinicao       = rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
          $valor_redefinicao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789@#&"),0,$redefinicao);
          
          $_SESSION['idDef']     = $valor_redefinicao;
          $_SESSION['idSession'] = $valor_redefinicao;
          $_SESSION['usuadmin']  = $admin;
          $_SESSION['admin']     = $admin;
          
          $util->redireciona("redefinicao.php?idDef=".$_SESSION['idDef'],"N","","0");
          
          $_SESSION['redefinir'] = '<h5><font color="red">Seu login foi bloqueado, redefina a sua senha.</font></h5>';
        }
        
        
        //SELECT PARA VERIFICAR A SENHA
        $sql = new Query ($bd);
        $txt = "SELECT CNPJ_SIND,
									 NNUMESIND,
									 CNOMESIND,
									 CEMAISIND,
									 CSENHSIND ,
       						 BLOCKSIND
				FROM TREDE_SINDICATOS
				WHERE CEMAISIND = :email_login";
        $sql->addPAram(':email_login',$email_login);
        $sql->executeQuery($txt);
        
        $seq_usuas = $sql->result("NNUMESIND");
        $res_senha = $sql->result("CSENHSIND");
        $res_block = $sql->result("BLOCKSIND");
        $res_login = $sql->result("CEMAISIND");
        $admin     = $sql->result("NNUMESIND");
        
        
        $res = strcmp($res_senha,$senha_login);
        
        if ($seq_usuas == '') {
          $tpl->MSG = '<font color="red">USUÁRIO ADMIN NÃO EXISTE!!!</font>';
          $tpl->block("ERRO");
        } else if ($res_login <> $email_login) {
          $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 1!!!</font>';
          $tpl->block("ERRO");
        } else if ($res <> 0) {
          $tpl->MSG  = '<font color="red">LOGIN OU SENHA INCORRETOS 2!!!</font>';
          $tpl->MSG1 = 'Tentativa '.$tentativas.' de 5.';
          $tpl->block("ERRO");
          $seg->registraLogin($bd,$res_login);
        } else if ($res_login <> $email_login && $res <> 0) {
          $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 3!!!</font>';
          $tpl->block("ERRO");
        } else if ($email_login == '' && md5($senha == '')) {
          $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 4!!!</font>';
          $tpl->block("ERRO");
        } else if ($res_block == 'S') {
          $tpl->MSG = '<font color="red">SEU LOGIN ESTA BLOQUEADO, CLIQUE EM REDEFINIR A SENHA.</font>';
          $tpl->block("ERRO");
        } else {
          
          $aleatorio = mt_rand(20,30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
          $valor     = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"),0,$aleatorio);
          
          $_SESSION['idSessao'] = $valor;
          $_SESSION['idAdmin']  = $seq_usuas;
          $_SESSION['usuadmin'] = $admin;
          $_SESSION['admin']    = $admin;
          
          //sdebug($seq_usuas,true);
          
          //Apaga todos os registros do dia
          $sql3 = new Query($bd);
          $txt3 = "DELETE FROM TREDE_SEGTENTA
				WHERE CLOGITENTSEG = :email
					AND DDATATENTSEG = :data";
          $sql3->addParam(":email",$email_login);
          $sql3->addParam(":data",date('Y-m-d'));
          $sql3->executeSQL($txt3);
          
          $util->redireciona('sindicato/index.php?idSessao='.$_SESSION['idSessao'].'&idAdmin='.$_SESSION['idAdmin']);
        }
      }//
    }//acessar
  }
  
  $tpl->show();
  $bd->close();
?>