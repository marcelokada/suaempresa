<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","finalizarcompra.html");
  
  if (isset($_SESSION['aut'])) {
    $autenticado     = TRUE;
    $_SESSION['aut'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
  
    $seg->verificaSession($_SESSION['idUsuario']);
    
    if ($autenticado == TRUE) {
      
      $id_sessao = $_SESSION['idSessao'];
      $id_confer = $_GET['idSessao'];
      $seq       = $_SESSION['idUsuario'];
      $admin     = $_SESSION['usuadmin'];
      
      
      if ($tipopg == '1') {
        $idsloja = $_SESSION['idLoja'];
      } else if ($tipopg == '2') {
        $idsloja = $id_loja;
      }
      
      $seq            = $_SESSION['idUsuario'];
      $tpl->ID_SESSAO = $_SESSION['idSessao'];
      $tpl->IDUSUA    = $_SESSION['idUsuario'];
      
      //&idLoja={ID_CRED}&idcart={IDCART}&tipopg=1
      
      $pagina = explode("/purchases/",$_SERVER['REQUEST_URI']);
      $ff     = explode("/",$pagina[1]);
      
      $tpl->ID_CRED = $ff[0];
      $tpl->IDCRED  = $ff[0];
      $idcart       = $ff[1];
      $tpl->IDCART  = $ff[1];
      $id_loja      = $ff[0];
      
      $menu_afiliados = $func->RetornaPermissoes('MENU_PAGA_VIACASHBACK');
      
      if ($menu_afiliados[0]['STATUS'] != '0') {
        $tpl->block('MENU_PAGA_VIACASHBACK');
      }
      
      $tpl->MD5PAG1 = md5('1');
      //$tpl->MD5PAG3 = md5('3');
      
      
      //SELECT PARA VERIFICAR O USUARIO
      $tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
      //SELECT PARA VERIFICAR O USUARIO
      
      $pags = $func->AtivoPagSeguro();
      if ($pags == 's') {
        $tpl->block("PAGS");
      }
      
      $dots = $func->AtivoDotBank();
      if ($dots == 's') {
        $tpl->block("DOTS");
      }
      
      $transf = $func->AtivoTransf();
      if ($transf == 's') {
        $tpl->block("TRANSF");
      }
            
      
      // select nome da rede //
      $sql = new Query ($bd);
      $txt = "SELECT SEQUENCIACRE, VNOMECREDCRE, NNUMECATECRE
			  FROM TREDE_CREDENCIADOS
			 WHERE SEQUENCIACRE = :seq";
      $sql->addparam(':seq',$id_loja);
      $sql->executeQuery($txt);
      
      $tpl->NOMELOJA      = ucwords(utf8_encode($sql->result("VNOMECREDCRE")));
      $tpl->CATEGORIALOJA = ucwords(utf8_encode($func->RetornaNomeCategoria($bd,$sql->result("NNUMECATECRE"))));
      $id                 = $sql->result("SEQUENCIACRE");
      
      $sql3 = new Query ($bd);
      $txt3 = "SELECT NSEQUECARR,
					 NSEQUPRODU,
					 SEQUENCIACRE,
					 VIDCARCARR,
					 NVALORCARR,
					 NQUATICARR,
					 VNOMEPCARR,
					 NVVALOCARR,
           VVACASCARR
			   FROM TREDE_CARRINHO
		      WHERE SEQUENCIACRE = :idloja
				AND VIDCARCARR = :idcart
		ORDER BY NSEQUECARR DESC";
      $sql3->addParam(':idloja',$id_loja);
      $sql3->addParam(':idcart',$idcart);
      $sql3->executeQuery($txt3);
      
      
      $quantidade = $sql3->count();
      
      
      if ($quantidade == '0') {
        $tpl->HREF  = "desabilitar";
        $tpl->VAZIO = "CARRINHO VAZIO <font color='red'><a href='lojarede.php?idSessao=".$_GET['idSessao']."&idLoja=".$_GET['idLoja']."&idcart=".$_GET['idcart']."'> ->VOLTAR<- </font></a>";
        
      } else {
        $tpl->HREF  = "habilitar";
        $tpl->VAZIO = "RESUMO DA COMPRA";
      }
      
      
      $valortotalcash = '0';
      
      while (!$sql3->eof()) {
        
        $tpl->ID   = $sql3->result("NSEQUPRODU");
        $tpl->ID2  = $sql3->result("NSEQUECARR");
        $tpl->DESC = ucwords(utf8_encode($sql3->result("VNOMEPCARR")));
        $qtdes     = $sql3->result("NQUATICARR");
        
        $tpl->QTDE = $sql3->result("NQUATICARR");
        
        $valor_unit = $sql3->result("NVALORCARR");
        $tpl->VALOR = $formata->formataNumero($sql3->result("NVALORCARR"));
        
        $seqprod = $sql3->result("NSEQUPRODU");
        $seqloja = $sql3->result("SEQUENCIACRE");
        
        
        $sql2c = new Query ($bd);
        $txt2c = "SELECT  SEQUENCIACRE,
					 CLASSIFICCRE
			   FROM TREDE_CREDENCIADOS
		      WHERE SEQUENCIACRE = :idloja";
        $sql2c->addParam(':idloja',$seqloja);
        $sql2c->executeQuery($txt2c);
        
        $class = $sql2c->result("CLASSIFICCRE");
        
        $sql2cc = new Query ($bd);
        $txt2cc = "SELECT NNUMECLASS,CASHBCLASS
			   FROM TREDE_CLASSREDE
		      WHERE NNUMECLASS = :class";
        $sql2cc->addParam(':class',$class);
        $sql2cc->executeQuery($txt2cc);
        
        
        $sql32 = new Query ($bd);
        $txt32 = "SELECT VCASHPRODU,NSEQUPRODU,CIMAGPRODU
			   FROM TREDE_PRODUTOS
		      WHERE SEQUENCIACRE = :seqloja
				AND NSEQUPRODU = :seqprod";
        $sql32->addParam(':seqloja',$seqloja);
        $sql32->addParam(':seqprod',$seqprod);
        $sql32->executeQuery($txt32);
        
        //$cashback_lista = $sql32->result("VCASHPRODU");
        $cashback_lista = $sql2cc->result("CASHBCLASS");
        
        $valor_totalcar = $sql3->result("NVVALOCARR");
        
        $valor_total_cash = $valor_totalcar * $cashback_lista / 100;
        
        //$tpl->CASHVALOR    = $formata->formataNumero($valor_total_cash);
        $tpl->CASHVALOR    = number_format($sql3->result("VVACASCARR"),2,',','.');
        
        $tpl->CASHVALOR_DB = $valor_total_cash;
        
        $valortotalcash += $valor_total_cash;
  
        $sql31 = new Query ($bd);
        $txt31 = "SELECT SUM(NVVALOCARR) VALOT_TOTAL,
                         SUM(VVACASCARR) CASH_TOTAL
			          FROM TREDE_CARRINHO
		            WHERE SEQUENCIACRE = :idloja
				        AND VIDCARCARR = :idcart";
        $sql31->addParam(':idloja',$id_loja);
        $sql31->addParam(':idcart',$idcart);
        $sql31->executeQuery($txt31);
                
        
        $tpl->TOTALCASH  = number_format($sql31->result('CASH_TOTAL'),2,',','.');
        //$tpl->TOTALCASH  = $formata->formataNumero($valortotalcash);
                
        //$tpl->TOTALCASH1 = $valortotalcash;
        $tpl->TOTALCASH1 = $sql31->result('CASH_TOTAL');
        
        $valor_total            = $valor_unit * $qtdes;
        $tpl->VALOR_TOTAL_PRODU = $formata->formataNumero($valor_total);
        
        $idl = $sql32->result("NSEQUPRODU");
        
        $imagem1 = $sql32->result("CIMAGPRODU");
        
        if (($imagem1 == NULL) or (substr($imagem1,0,7) != 'uploads')) {
          $tpl->IMAGEM = 'comum/img/Sem-imagem.jpg';
        } else {
          $tpl->IMAGEM = 'rede/'.$imagem1;
        }
        
        // $tpl->IMAGEM		= $func->RetornaImagemProdutos($bd,$idl);
        
        $tpl->block("CARRINHO");
        $sql3->next();
        
      }
      
      $sql31 = new Query ($bd);
      $txt31 = "SELECT SUM(NVVALOCARR) SOMA
			   FROM TREDE_CARRINHO
		      WHERE SEQUENCIACRE = :idloja
				AND VIDCARCARR = :idcart";
      $sql31->addParam(':idloja',$id_loja);
      $sql31->addParam(':idcart',$idcart);
      $sql31->executeQuery($txt31);
      
      $valortotal = $sql31->result("SOMA");
      
      $tpl->TOTAL       = $formata->formataNumero($valortotal);
      $tpl->VALOR_TOTAL = $valortotal;
      
      
      $sql32 = new Query ($bd);
      $txt32 = "SELECT SUM(VVACASCARR) SOMA_CASH
			    FROM TREDE_CARRINHO
				WHERE VIDCARCARR = :idcart
				  AND SEQUENCIACRE = :idloja";
      $sql32->addParam(':idloja',$id_loja);
      $sql32->addParam(':idcart',$idcart);
      $sql32->executeQuery($txt32);
      
      $valortotal = $sql32->result("SOMA_CASH");
      $valortotal = str_replace('.','',$valortotal);
      $valortotal = str_replace(',','.',$valortotal);
      
      //$tpl->TOTAL_MODAL	= $formata->formataNumero($valortotal);
      
      
      $sql34 = new Query ($bd);
      $txt34 = "SELECT REDE_NOMEUSU,
                REDE_CPFUSUA,
                REDE_EMAILUS,
                REDE_CELULAR,
                REDE_DATAVENC
			   FROM TREDE_USUADMIN
			  WHERE REDE_SEQUSUA = :usua";
      $sql34->AddParam(':usua',$seq);
      $sql34->executeQuery($txt34);
      
      $resdia = $sql34->result("REDE_DATAVENC");
      
      if ($resdia == "") {
        $dia = "10";
      } else {
        $dia = $sql34->result("REDE_DATAVENC");
      }
      
      //$tpl->DATA_VENC = date('Y/m/d', strtotime("+3 days", strtotime(date('Y-m-d'))));
      $tpl->DATA_VENC = date('Y/m/'.$dia);
      
      //$tpl->DATA_VENC = date('Y/m/d', strtotime("+3 days", strtotime(date('Y-m-d'))));
      //$tpl->NOME_USUA = $sql34->result("REDE_NOMEUSU");
      $tpl->CPF_USUA   = $sql34->result("REDE_CPFUSUA");
      $tpl->EMAIL_USUA = $sql34->result("REDE_EMAILUS");
      $tpl->CELU_USUA  = $sql34->result("REDE_CELULAR");
      
      if (isset($_POST['transf'])) {
        
        $idcart = $_POST['transf'];
        
        $sql0 = new Query ($bd);
        $txt0 = "SELECT NSEQUECARR,
									NSEQUPRODU,
									SEQUENCIACRE,
									VIDCARCARR,
									NVALORCARR,
									NQUATICARR,
									VNOMEPCARR,
									NVVALOCARR,
									VFECHACARR,
									VVACASCARR,
									DDATACCARR,
									REDE_SEQUSUA
						FROM TREDE_CARRINHO
					WHERE VIDCARCARR = :idcart";
        $sql0->addParam(':idcart',$idcart);
        $sql0->executeQuery($txt0);
        
        $idloja = $sql0->result("SEQUENCIACRE");
        $valor  = $sql0->result("NVALORCARR");
        $idusua = $sql0->result("REDE_SEQUSUA");
        
        $sql = new Query ($bd);
        $txt = "UPDATE TREDE_CARRINHO SET VFECHACARR = 's'
			 WHERE VIDCARCARR = :idcart";
        $sql->addParam(':idcart',$idcart);
        $sql->executeSQL($txt);
        
        $sql1 = new Query ($bd);
        $txt1 = "INSERT INTO TREDE_PAGACOMPRA
	(SEQUENCIACRE,VIDCARCARR,NVALORCPAG,NIDUSPAGCOMPRA,DDATAPAGCOMPRA,CSITUPAGCOMPRA,CTIPOPAGCOMPRA,CSITUAPGCOMPRA,TIPOPAGAMENTOP)
	VALUES
	('".$idloja."','".$idcart."','".$valor."','".$idusua."','".date('Y-m-d H:i:s')."','1','7','f','c')";
        $sql1->executeSQL($txt1);
        
      }
    }
  } else {
    $seg->verificaSession($_SESSION['idUsuario']);
  }
  
  
  $tpl->show();
  $bd->close();
?>