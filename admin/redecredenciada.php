<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  error_reporting(0);
  
  $bd = new Database();
  $func = new Funcao();
  
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","redecredenciada.html");
  
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
      
      $nivelusua = $func->RetornaPermissoes_Admin($id_admin);
      
      if ($nivelusua == 'C') {
        $tpl->DISABLE = "style='display:none;'";
      } else if ($nivelusua == 'CB') {
        $tpl->DISABLE = "style='display:none;'";
      }
      
      $msg = $_GET['msg'];
      
      if ($msg == 's') {
        $tpl->MSG = '<font color="green">**Cadastro Realizado com Sucesso!!** <a href="cadastrarede.php?idSessao={ID_SESSAO}&admin={ID_ADMIN}">Cadastrar novamente</a></font><br>';
        $tpl->block("SUCESSO");
        
        $sql0 = new Query($bd);
        $txt0 = "SELECT LAST_INSERT_ID(SEQUENCIACRE) SEQ FROM TREDE_CREDENCIADOS
                ORDER BY 1 DESC
                LIMIT 1";
        $sql0->executeQuery($txt0);
        
        $seq = $sql0->result("SEQ");
        
        $sql = new Query($bd);
        $txt = "SELECT SEQUENCIACRE,
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
			   VCUPOMDESCRE,
			   VLINKDESCCRE,
			   VCOMPLEMECRE,
			   NNUMECATESUB,
			   VIMAGEMCRCRE,
			   VCNPJJURICRE,
			   VNUMECCEPCRE,
			   CVIMAGEMCCRE,
			   CLASSIFICCRE,
			   NNUMECELUCRE,
			   NNUMETELECRE
		  FROM TREDE_CREDENCIADOS
		 WHERE SEQUENCIACRE = :seq
		 ORDER BY SEQUENCIACRE DESC";
        $sql->addParam('seq',$seq);
        $sql->executeQuery($txt);
        
        $class = $sql->result("CLASSIFICCRE");
        
        if ($class == '1') {
          $tpl->CHECK1 = 'checked';
        } else if ($class == '2') {
          $tpl->CHECK2 = 'checked';
        } else if ($class == '3') {
          $tpl->CHECK3 = 'checked';
        } else if ($class == '4') {
          $tpl->CHECK4 = 'checked';
        } else if ($class == '5') {
          $tpl->CHECK5 = 'checked';
        }
        
        
        $tpl->SEQ = $sql->result("SEQUENCIACRE");
        $sequenciacre = $sql->result("SEQUENCIACRE");
        $tpl->NOME = utf8_encode(ucwords($sql->result("VNOMECREDCRE")));
        $tpl->RUA = utf8_encode(ucwords($sql->result("VNOMEENDECRE")));
        $tpl->NUMERO = $sql->result("NNUMEENDECRE");
        $tpl->BAIRRO = utf8_encode(ucwords($sql->result("VNOMEBAIRCRE")));
        $tpl->CIDA = utf8_encode(ucwords($sql->result("VNOMECIDAMUN")));
        $tpl->UF = ucwords($sql->result("CESTADOUFMUN"));
        $tpl->CAT_NUM = $sql->result("NNUMECATECRE");
        $tpl->CEP1 = $sql->result("VNUMECCEPCRE");
        $tpl->CEL = $sql->result("NNUMECELUCRE");
        $tpl->TEL1 = $sql->result("NNUMETELECRE");
        
        $cat_num = $sql->result("NNUMECATECRE");
        $tpl->CAT_NOME = utf8_encode(ucwords($func->RetornaNomeCategoria($bd,$sql->result("NNUMECATECRE"))));
        
        //$tpl->SCAT_NOME 	= $sql->result("NNUMESERVCRE");
        //$tpl->NOME 	= $sql->result("NNUMESERVCRE");
        $tpl->IBGE = $sql->result("NNUMEIBGEMUN");
        //$tpl->NOME 	= $sql->result("CSITUACAOCRE");
        //$tpl->DATA 	= $sql->result("DDATACREDCRE");
        //$tpl->NOME 	= $sql->result("NNUMEREGIREG");
        $tpl->CUPOM = $sql->result("VCUPOMDESCRE");
        $tpl->LINKS = $sql->result("VLINKDESCCRE");
        $tpl->COMPLE = $sql->result("VCOMPLEMECRE");
        $tpl->SCAT_NOME = utf8_encode(ucwords($func->RetornaNomeSubCategoria($bd,$sql->result("NNUMECATESUB"))));
        // $tpl->IMG 	= $func->RetornaImagem($bd,$sequenciacre);
        $imagem = $sql->result("CVIMAGEMCCRE");;
        if ($imagem == NULL) {
          $tpl->IMG = 'comum/img/Sem-imagem.jpg';
        } else {
          $tpl->IMG = $imagem;
        }
        
        $tpl->CNPJ = $sql->result("VCNPJJURICRE");
        $tpl->CEP = $sql->result("VNUMECCEPCRE");
        
        $tpl->block("REDE");
        
      } else {
        $sql1 = new Query($bd);
        $txt1 = "SELECT SEQUENCIACRE,
			   VNOMECREDCRE,
			   VNOMEENDECRE,
			   NNUMEENDECRE,
			   VNOMEBAIRCRE,
			   VNOMECIDAMUN,
			   CESTADOUFMUN,
			   CESTADOUFEST,
			   NNUMECATECRE,
			   NNUMECATESUB,
			   NNUMESERVCRE,
			   NNUMEIBGEMUN,
			   CSITUACAOCRE,
			   DDATACREDCRE,
			   NNUMEREGIREG,
			   VCUPOMDESCRE,
			   VLINKDESCCRE,
			   VCOMPLEMECRE,
			   NNUMECATESUB,
			   VIMAGEMCRCRE,
			   VCNPJJURICRE,
			   VNUMECCEPCRE,
			   CVIMAGEMCCRE,
			   CLASSIFICCRE
		  FROM TREDE_CREDENCIADOS
		 ORDER BY SEQUENCIACRE DESC";
        $sql1->executeQuery($txt1);
        
        
        while (!$sql1->eof()) {
          $tpl->SEQ = $sql1->result("SEQUENCIACRE");
          $sequenciacre = $sql1->result("SEQUENCIACRE");
          
          $sql2a = new Query ($bd);
          $txt2a = "SELECT VALCREDREDE FROM TREDE_CREDITOREDE
                WHERE SEQUENCIACRE = :cred";
          $sql2a->addParam(':cred',$sequenciacre);
          $sql2a->executeQuery($txt2a);
          
          $pontuacao = $sql2a->result("VALCREDREDE");
          if ($pontuacao == '') {
            $tpl->PONTOS = '0,00';
          } else {
            $tpl->PONTOS = number_format($sql2a->result("VALCREDREDE"),2,',','.');
          }
          
          $tpl->CAT_NUM = $sql1->result("NNUMECATECRE");
          
          $tpl->CEP = $sql1->result("VNUMECCEPCRE");
          
          $categoria = $sql1->result("NNUMECATECRE");
          $subcategoria = $sql1->result("NNUMECATESUB");
          $tpl->NOMECRE = ucwords(utf8_encode($sql1->result("VNOMECREDCRE")));
          $tpl->CATEGORIA = ucwords(utf8_encode($func->RetornaNomeCategoria($bd,$categoria)));
          $tpl->SUBCATEGORIA = ucwords(utf8_encode($func->RetornaNomeSubCategoria($bd,$subcategoria)));
          // $tpl->LINIMAGEMIMG 	= $func->RetornaImagem($bd,$sequenciacre);
          $imagem = $sql1->result("CVIMAGEMCCRE");
          
          
          if ($imagem == NULL) {
            $tpl->IMAGEM = 'comum/img/Sem-imagem.jpg';
          } else {
            $tpl->IMAGEM = $imagem;
          }
          // $tpl->IMAGEM 		= $imagem;
          //$tpl->STATUS	 	= $sql1->result("CSITUACAOCRE");
          $status = $sql1->result("CSITUACAOCRE");
          
          if ($status == 'a') {
            $tpl->COR = "";
            $tpl->CHK = "checked";
            $tpl->ATIV = "desativar";
          } else {
            $tpl->COR = "alert-danger";
            $tpl->CHK = "";
            $tpl->ATIV = "ativar";
          }
          
          
          $tpl->block("CADAREDE");
          $sql1->next();
          
        }
        
      }//FINALIZAÇÃO DO CADASTRO
      
      $tpl->ID_SESSAO = $_SESSION['idSessao'];
      $tpl->ID_ADMIN = $_SESSION['admin'];
      
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  
  $tpl->show();
  $bd->close();
?>