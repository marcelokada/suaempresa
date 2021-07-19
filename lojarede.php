<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  //error_reporting(0);
  
  $bd   = new Database();
  $func = new Funcao();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","lojarede.html");
  $tpl->ID_SESSAO = $_SESSION['idSessao'];
  
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
  
      $seg->verificaSession($_SESSION['idUsuario']);
      $func->AtualizaStatusUsuario($seq);
      
      $id_loja = $_GET['idLoja'];
      
      $tpl->IDCART     = $_GET['idcart'];
      $idcart          = $_GET['idcart'];
      $tpl->ID_CRED    = $_GET['idLoja'];
      $tpl->IDUSUA     = $_SESSION['idUsuario'];
      
      //SELECT PARA VERIFICAR O USUARIO
      $tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
      //SELECT PARA VERIFICAR O USUARIO

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
      // select nome da rede //
      
      $sql_token = new Query ($bd);
      $txt_token = "SELECT TOKEN FROM TREDE_DOTBANK_REDE WHERE SEQUENCIACRE = :seq";
      $sql_token->addparam(':seq',$id_loja);
      $sql_token->executeQuery($txt_token);
      
      $token = $sql_token->result("TOKEN");
      
      $sql_token1 = new Query ($bd);
      $txt_token1 = "SELECT TOKEN FROM TREDE_PAGSEGURO_REDE WHERE SEQUENCIACRE = :seq";
      $sql_token1->addparam(':seq',$id_loja);
      $sql_token1->executeQuery($txt_token1);
      
      $token1 = $sql_token1->result("TOKEN");
      
      $sql_ende = new Query ($bd);
      $txt_ende = "SELECT REDE_ENDE, REDE_CEP FROM TREDE_USUADMIN WHERE REDE_SEQUSUA = :seq";
      $sql_ende->addparam(':seq',$seq);
      $sql_ende->executeQuery($txt_ende);
      
      $res_ende = $sql_ende->result("REDE_ENDE");
      $res_cep  = $sql_ende->result("REDE_CEP");
      
      /*      if (($token == "") or ($token == "NÃO CADASTRADO")) {
              $tpl->PONTOSREDE = "Você não pode finalizar a compra, pois a Rede não possui conta configurada para realização das Compras. (DotBank)";
              $tpl->HREF = "desabilitar";
            } else if (($token1 == "") or ($token1 == "NÃO CADASTRADO")) {
              $tpl->PONTOSREDE = "Você não pode finalizar a compra, pois a Rede não possui conta configurada para realização das Compras. (PagSeguro)";
              $tpl->HREF = "desabilitar";
            } else if (($res_ende == "") or ($res_cep == "")) {
              $tpl->PONTOSREDE = "Você não pode finalizar a compra, seu cadastros está incompleto, vá em configurações e complete seu cadastro (endereço completo)";
              $tpl->HREF = "desabilitar";
            }*/
      
      $sql2 = new Query ($bd);
      $txt2 = "SELECT NSEQUPRODU,
					 SEQUENCIACRE,  
					 VNOMEPRODU,     
					 VDESCPRODU,  
					 VVALOPRODU, 
					 VCASHPRODU,  
					 CSITUPRODU,  
					 NQTDEPRODU,  
					 IMAGEM,
					 DDATAPRODU,
					 CIMAGPRODU
			   FROM TREDE_PRODUTOS
		      WHERE SEQUENCIACRE = :idloja 
					AND CSITUPRODU = 'a'
		ORDER BY NSEQUPRODU";
      $sql2->addParam(':idloja',$id);
      $sql2->executeQuery($txt2);
      
      while (!$sql2->eof()) {
        
        $tpl->NOMEPRODU = ucwords(utf8_encode($sql2->result("VNOMEPRODU")));
        $tpl->ID        = $sql2->result("NSEQUPRODU");
        //$tpl->DESCPRODU		= ucwords(utf8_encode($sql2->result("VDESCPRODU")));
        
        $valor_produ  = $sql2->result("VVALOPRODU");
        $valor_produ1 = number_format($sql2->result("VVALOPRODU"),2,',','.');
        $valor_produ  = str_replace('.',',',$valor_produ);
        
        $tpl->VALORPRODU = $valor_produ1;
        $valor           = $sql2->result("VVALOPRODU");
        $valor           = str_replace(',','.',$valor);
        
        //$tpl->VALOR_SHOP = str_replace(',','.',$valor);
        
        $idl = $sql2->result("NSEQUPRODU");
        
        // $tpl->IMAGEM		= $func->RetornaImagemProdutos($bd,$idl);
        
        $imagem1 = $sql2->result("CIMAGPRODU");
        
        if (($imagem1 == NULL) or (substr($imagem1,0,7) != 'uploads')) {
          $tpl->IMAGEM = 'comum/img/Sem-imagem.jpg';
        } else {
          $tpl->IMAGEM = 'rede/'.$imagem1;
        }
        
        $sql2c = new Query ($bd);
        $txt2c = "SELECT  SEQUENCIACRE,
					 CLASSIFICCRE
			   FROM TREDE_CREDENCIADOS
		      WHERE SEQUENCIACRE = :idloja";
        $sql2c->addParam(':idloja',$id);
        $sql2c->executeQuery($txt2c);
        
        $class = $sql2c->result("CLASSIFICCRE");
        
        $sql2cc = new Query ($bd);
        $txt2cc = "SELECT NNUMECLASS,CASHBCLASS
			   FROM TREDE_CLASSREDE
		      WHERE NNUMECLASS = :class";
        $sql2cc->addParam(':class',$class);
        $sql2cc->executeQuery($txt2cc);

        if (TIPO_PORC_PRODUTO == 'cred') {
          $tpl->CASHBACK       = 'Cashback de '.$sql2cc->result("CASHBCLASS").'%';
          $tpl->CASHBACK_VALOR = $sql2cc->result("CASHBCLASS");
          $cashback            = $sql2cc->result("CASHBCLASS");
        } else if (TIPO_PORC_PRODUTO == 'prod') {
          $tpl->CASHBACK       = 'Cashback de '.$sql2->result("VCASHPRODU").'%';
          $tpl->CASHBACK_VALOR = $sql2->result("VCASHPRODU");
          $cashback            = $sql2->result("VCASHPRODU");
        }
        
        //$tpl->CASHBACK		= 'Cashback de '.$sql2->result("VCASHPRODU").'%';
        
        //CASH BACK SUBSTITUIDO POR CASHBACK TOTAL DO CREDENCIADO.
        //$tpl->CASHBACK		= 'Cashback de '.$sql2->result("VCASHPRODU").'%';
        
        //valor do cash antigo por produto
        //$cashback = $sql2->result("VCASHPRODU");
        
        $valorcash = $valor * $cashback / 100;
        
        //$tpl->VALORCASH		= 'R$ '.$formata->formataNumero($valorcash);
        
        $tpl->block("PRODUTOS");
        $sql2->next();
      }
      
      $sql3 = new Query ($bd);
      $txt3 = "SELECT  NSEQUECARR,
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
		        		AND VFECHACARR = 'n'
		        ORDER BY NSEQUECARR DESC";
      $sql3->addParam(':idloja',$id_loja);
      $sql3->addParam(':idcart',$idcart);
      $sql3->executeQuery($txt3);
      
      $quantidade   = $sql3->count();
      $tpl->QTDECAR = $quantidade;
      
      if ($quantidade == '0') {
        $tpl->HREF = "desabilitar";
      } /*else {
    $tpl->HREF = "habilitar";
}*/
      
      $valortotalcash = '0';
      
      while (!$sql3->eof()) {
        
        $tpl->ID   = $sql3->result("NSEQUPRODU");
        $tpl->ID2  = $sql3->result("NSEQUECARR");
        $tpl->DESC = ucwords(utf8_encode($sql3->result("VNOMEPCARR")));
        $tpl->QTDE = $sql3->result("NQUATICARR");
        
        $tpl->VALOR = $formata->formataNumero($sql3->result("NVVALOCARR"));
        
        $seqprod = $sql3->result("NSEQUPRODU");
        $seqloja = $sql3->result("SEQUENCIACRE");
        
        
        $sql32 = new Query ($bd);
        $txt32 = "SELECT VCASHPRODU
			   FROM TREDE_PRODUTOS
		      WHERE SEQUENCIACRE = :seqloja
				AND NSEQUPRODU = :seqprod";
        $sql32->addParam(':seqloja',$seqloja);
        $sql32->addParam(':seqprod',$seqprod);
        $sql32->executeQuery($txt32);
        
        
        //$cashback_lista = $sql32->result("VCASHPRODU");
        $cashback_lista = $cashback;
        
        $valor_totalcar = $sql3->result("NVVALOCARR");
        
        //$valor_total_cash = $valor_totalcar * $cashback_lista / 100;
        $valor_total_cash = $sql3->result("VVACASCARR");
        
        $tpl->CASHVALOR    = $formata->formataNumero($valor_total_cash);
        
        $tpl->CASHVALOR_DB = $formata->formataNumero($sql3->result("VVACASCARR"));;
        
        $valortotalcash += $valor_total_cash;
        
        $tpl->TOTALCASH = $formata->formataNumero($valortotalcash);
        
        //$idl				= $sql2->result("NSEQUPRODU");
        //$tpl->IMAGEM		= $func->RetornaImagemProdutos($bd,$idl);
        
        $tpl->block("CARRINHO");
        $sql3->next();
        
      }
  
      $sql2 = new Query ($bd);
      $sql2->clear();
      $txt2 = "SELECT VALCREDREDE
			   FROM TREDE_CREDITOREDE
		      WHERE SEQUENCIACRE = :idloja ";
      $sql2->addParam(':idloja',$id);
      $sql2->executeQuery($txt2);
  
      $pontosrede = $sql2->result("VALCREDREDE");
  
      if(PONTUACAO_CRED == 'on'){
        if ($pontosrede < $valortotalcash) {
          $tpl->PONTOSREDE = "Você não pode finalizar a compra, pois a Rede não possui mais pontuação. Entre em contato com a Rede.";
          $tpl->HREF       = "desabilitar";
      
        }
      }
  
      $tpl->block("BTN_FINALIZAR");
      
      $sql31 = new Query ($bd);
      $txt31 = "SELECT SUM(NVVALOCARR) SOMA
			   FROM TREDE_CARRINHO
		      WHERE SEQUENCIACRE = :idloja 
				AND VIDCARCARR = :idcart";
      $sql31->addParam(':idloja',$id_loja);
      $sql31->addParam(':idcart',$idcart);
      $sql31->executeQuery($txt31);
      
      $valortotal = $sql31->result("SOMA");
      
      $tpl->TOTAL = $formata->formataNumero($valortotal);
      
      if ($quantidade > 0) {
        //$tpl->PISCAR = "fa-blink";
        $tpl->block("SHOP");
      } else {
        $tpl->VAZIO = "<br><h3><i><font color='red'><i class='fa fa-shopping-cart' aria-hidden='true'></i> CARRINHO VAZIO</font></i></h3><br>";
        $tpl->block("VAZIO");
      }
    }
  } else {
    $seg->verificaSession($_SESSION['idUsuario']);
  }
  
  
  $tpl->show();
  $bd->close();
?>