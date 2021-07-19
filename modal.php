<?php
  
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  require_once("comum/apagaArquivos.php");
  //error_reporting(0);
  
  //$tpl = new Template("modal.html");
  
  $bd = new Database();
  
  $id = $seg->antiInjection($_POST['id']);
  
  $sql = new Query ($bd);
  $txt = "SELECT SEQUENCIACRE,
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
					VCUPOMDESCRE,
					VLINKDESCCRE,
					VIMAGEMCRCRE,
					CVIMAGEMCCRE,
       		CLASSIFICCRE,
       	  NNUMETELECRE,
          NNUMECELUCRE,
          CTIPOCRED
			    FROM TREDE_CREDENCIADOS
				WHERE SEQUENCIACRE = :id";
  $sql->AddParam(':id',$id);
  $sql->executeQuery($txt);
  
  $sequenciacre        = $sql->result("SEQUENCIACRE");
  $eventos['id']       = $sequenciacre;
  $eventos['nome']     = utf8_encode($sql->result("VNOMECREDCRE"));
  $eventos['endereco'] = strtoupper(utf8_encode($sql->result("VNOMEENDECRE")).', <b>nº</b> '.utf8_encode($sql->result("NNUMEENDECRE")));
  $eventos['cidade']   = strtoupper(utf8_encode($sql->result("VNOMECIDAMUN").' - '.$sql->result("CESTADOUFMUN")));
  $eventos['cupom']    = $sql->result("VCUPOMDESCRE");
  
  $imagem = $sql->result("CVIMAGEMCCRE");
  
  if (($imagem == NULL) or (substr($imagem,0,7) != 'uploads')) {
    $eventos['imagem'] = 'comum/img/Sem-imagem.jpg';
  } else {
    $eventos['imagem'] = 'admin/'.$imagem;
  }
  
  //$eventos['imagem']		= $func->RetornaImagem($bd,$sequenciacre);
  
  
  $eventos['links'] = $sql->result("SEQUENCIACRE");
  $eventos['ctipo'] = $sql->result("CTIPOCRED");
  
  /*$sql2 = new Query ($bd);
  $txt2 = "SELECT NEXT VALUE FOR CARTCOMPRA SEQCART FROM DUAL";
  $sql2->executeQuery($txt2);
  */
  
  $sql2 = new Query ($bd);
  $txt2 = "SELECT LAST_INSERT_ID(NSEQUECARR)+1 SEQCART FROM TREDE_CARRINHO
			ORDER BY 1 DESC
			LIMIT 1";
  $sql2->executeQuery($txt2);
  
  $res_valor = $sql2->result("SEQCART");
  
  if ($res_valor == '') {
    $res_valor = '1';
  }
  
  $_SESSION['seqcart'] = $res_valor;
  
  $eventos['idcart'] = $res_valor;
  
  $eventos['tele']    = $sql->result("NNUMETELECRE");
  $eventos['celular'] = $sql->result("NNUMECELUCRE");
  
  $_SESSION['idLoja'] = $sql->result("SEQUENCIACRE");
  
  $class = $sql->result("CLASSIFICCRE");
  
  $eventos['Teste'] = $class;
  
  if(CLASS_REDE == 'on'){
    
    if ($class == '1') {
      $eventos['class'] = '<label for="cm_star-1"><i class="fa"></i></label><input type="radio" disabled id="cm_star-1" name="fb" value="1" />';
    } else if ($class == '2') {
      $eventos['class'] = '<label for="cm_star-1"><i class="fa"></i></label><input type="radio" disabled id="cm_star-1" name="fb" value="1" />
											 <label for="cm_star-2"><i class="fa"></i></label><input type="radio" disabled id="cm_star-2" name="fb" value="2" />';
    } else if ($class == '3') {
      $eventos['class'] = '<label for="cm_star-1"><i class="fa"></i></label><input type="radio" disabled id="cm_star-1" name="fb" value="1" />
											 <label for="cm_star-2"><i class="fa"></i></label><input type="radio" disabled id="cm_star-2" name="fb" value="2" />
											 <label for="cm_star-3"><i class="fa"></i></label><input type="radio" disabled id="cm_star-3" name="fb" value="3" />';
    } else if ($class == '4') {
      $eventos['class'] = '<label for="cm_star-1"><i class="fa"></i></label><input type="radio" disabled id="cm_star-1" name="fb" value="1" />
										   <label for="cm_star-2"><i class="fa"></i></label><input type="radio" disabled id="cm_star-2" name="fb" value="2" />
										   <label for="cm_star-3"><i class="fa"></i></label><input type="radio" disabled id="cm_star-3" name="fb" value="3" />
    								   <label for="cm_star-4"><i class="fa"></i></label><input type="radio" disabled id="cm_star-4" name="fb" value="4" />';
    } else if ($class == '5') {
      $eventos['class'] = '<label for="cm_star-1"><i class="fa"></i></label><input type="radio" disabled id="cm_star-1" name="fb" value="1" />
											 <label for="cm_star-2"><i class="fa"></i></label><input type="radio" disabled id="cm_star-2" name="fb" value="2" />
											 <label for="cm_star-3"><i class="fa"></i></label><input type="radio" disabled id="cm_star-3" name="fb" value="3" />
    									 <label for="cm_star-4"><i class="fa"></i></label><input type="radio" disabled id="cm_star-4" name="fb" value="4" />
    									 <label for="cm_star-5"><i class="fa"></i></label><input type="radio" disabled id="cm_star-5" name="fb" value="5" />';
    }
    
  }
  
  
  
  
  echo json_encode($eventos);
  
  $bd->close();
?>