<?php
  require_once("comum/autoload.php");
  $seg->secureSessionStart();
  require_once("comum/apagaArquivos.php");
  //error_reporting(0);
  
  $bd = new Database();
  
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","ativacaomensal.html");
  
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
      $id_confer = $_SESSION['idUsuario'];;
      $seq = $_SESSION['idUsuario'];
      
      $seg->verificaSession($id_sessao);
      $tpl->ID_SESSAO = $_SESSION['idSessao'];
      $tpl->IDUSUA = $_SESSION['idUsuario'];
      
      $idusua = $_SESSION['idUsuario'];
      
      $menu_dot12 = $func->RetornaPermissoes('MENU_DOTBANK12');
      
      //SELECT PARA VERIFICAR O USUARIO
      $tpl->NOME = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd,$seq)));
      //SELECT PARA VERIFICAR O USUARIO
      
      $func->AtualizaStatusUsuario($seq);
  
      //CASHBACK USUARIO
      $valortotal_cash = $func->RetornaValorCashBackUsuario($bd,$seq);
      $tpl->MEUCASH = $formata->formataNumero($valortotal_cash);
  
      $valortotal_bonus = $func->RetornaValorBonusUsuario($seq);
      $tpl->MEUBONUS = $formata->formataNumero($valortotal_bonus);
  
      $valortotal_voucher = $func->RetornaValorVoucherUsuario($seq);
      $tpl->MEUVOUCHER = $formata->formataNumero($valortotal_voucher);
  
      $tpl->MEUPLANO = $func->assinaturaUsuarioMimo($bd,$seq);
      //CASHBACK USUARIO
      
      $sql211 = new Query ($bd);
      $txt211 = "SELECT LAST_INSERT_ID(SEQUPAGPLAN) SEQUPAGPLAN,
                        DDTFIMPPLAN,
                        CSITPAGPLAN,
                        CSITUAPPLAN,
                        NSEQPAGPLAN,
       									 CTIPOTRPLAN
                FROM TREDE_PAGAPLANO
				WHERE NIDUPAGPLAN = :usua	
				ORDER BY 1 DESC				
				LIMIT 1";
      $sql211->AddParam(':usua',$seq);
      $sql211->executeQuery($txt211);
      
      $tpl->IDPLANO = $sql211->result('NSEQPAGPLAN');
      $idplano = $sql211->result('NSEQPAGPLAN');
      $situplan1 = $sql211->result('CSITUAPPLAN');
      
      $sql33 = new Query ($bd);
      $txt33 = "SELECT  SEQPLANO,
                    CNOMEPLANO,
                    CDESCPLANO,
                    CTEMPPLANO,
                    MENSAPLANO,
                    CCARNPLANO
            FROM TREDE_PLANOS
            WHERE SEQPLANO = :usua";
      $sql33->AddParam(':usua',$idplano);
      $sql33->executeQuery($txt33);
      
      $carnes = $sql33->result("CCARNPLANO");
      
      if ($carnes == 's') {
        $tpl->block("DOTBANK12");
      }
      
      
      $sql = new Query ($bd);
      $txt = "SELECT SEQUPAGPLAN,
                 NSEQPAGPLAN,
                 CSITPAGPLAN,
                 CTIPOPGPLAN,
                 DDTINIPPLAN,
                 DDTFIMPPLAN,
                 LINKBOLETO,
                 IDPGSEGPLAN,
                 CCOMPRPPLAN,
                 DVENCBOPLAN,
                 CSTABOLPLAN
			 FROM TREDE_PAGAPLANO
			WHERE NIDUPAGPLAN = :idusua
            AND CTIPOTRPLAN = 'm'
			ORDER BY DVENCBOPLAN ASC";
      $sql->addParam(':idusua',$seq);
      $sql->executeQuery($txt);
      
      $res_data_inicio = $sql->result("DDTINIPPLAN");
      $res_data_final = $sql->result("DDTFIMPPLAN");
      
      while (!$sql33->eof()) {
        $tpl->ID1 = $sql33->result("SEQPLANO");
        $tpl->NOMEPLANO1 = $sql33->result("CNOMEPLANO");
        $tpl->DESC1 = $sql33->result("CDESCPLANO");
        $tpl->VALOR1 = $sql33->result("MENSAPLANO");
        $tpl->VALIDADE1 = $sql33->result("CTEMPPLANO");
        
        
        if ($situplan1 == 'a') {
          //$tpl->block("PLANOS2");
          $tpl->block("PLANOS");
          
          //botoes colocados para ativação do plano
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
          
          if ($menu_dot12[0]['STATUS'] != '0') {
            $tpl->block('MENU_DOTBANK12');
          }
          
          //botoes colcoados para tivação do plano
          
        } else if ($situplan1 == 'p') {
          
          $tpl->block("PLANOS2");
        } else if ($situplan1 == 'c') {
          
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
          
          if ($menu_dot12[0]['STATUS'] != '0') {
            $tpl->block('MENU_DOTBANK12');
          }
          
          $tpl->block("PLANOS");
          
        }
        $sql33->next();
        
      }
      
      
      $sql21 = new Query ($bd);
      $txt21 = "SELECT REDE_PLANUSU
			   FROM TREDE_USUADMIN
			  WHERE REDE_SEQUSUA = :usua";
      $sql21->AddParam(':usua',$seq);
      $sql21->executeQuery($txt21);
      
      $res_assi = $sql21->result("REDE_PLANUSU");

      
      while (!$sql->eof()) {
        
        $idpagplan = $sql->result("NSEQPAGPLAN");
        $tpl->ID = $sql->result("SEQUPAGPLAN");
        
        $tpl->IDPAGA = $sql->result("IDPGSEGPLAN");
        $idpgsegplan = $sql->result("IDPGSEGPLAN");
        
        $comprovante = $sql->result("CCOMPRPPLAN");
        
        $data_inicio = $sql->result("DDTINIPPLAN");
        $data_final = $sql->result("DDTFIMPPLAN");
        $data_atual = date('Y-m-d');
        
        $data_atualSTR = strtotime(date('Y-m-d'));
        $databoletoSTR = strtotime($sql->result("DVENCBOPLAN"));
        
        
        /*    if (($data_inicio == NULL) or ($data_final == NULL)) {
              $vencida = "";
              $tpl->VALIDADE = "Em analise";
            }
            else if ($data_final < $data_atual) {
              $vencida = "<font color='red'>(Vencida)</font>";
              $tpl->VALIDADE = $data->formataData1($data_inicio) . ' - ' . $data->formataData1($data_final) . ' ' . $vencida;
            }
            else {
              $vencida = "";
              $tpl->VALIDADE = $data->formataData1($data_inicio) . ' - ' . $data->formataData1($data_final) . ' ' . $vencida;
            }*/
        
        //VENC - color:red;
        
        
        $tpl->VALIDADE = $data->formataData1($sql->result("DVENCBOPLAN"));
        
        $status_bol = $sql->result("CSTABOLPLAN");
        $situa_pag = $sql->result("CSITPAGPLAN");
        $tipo_pag = $sql->result("CTIPOPGPLAN");
        
        if ($situa_pag == '1') {
          
          if ($status_bol == 'c') {
            $tpl->SITUA = "Cancelada(retirou a segunda via)";
            $tpl->COLS = "colspan='2' ";
            $tpl->COR = "#red";
            $tpl->VENC = "color:red";
          } else {
            
            if ($data_atualSTR > $databoletoSTR) {
              $tpl->VENC = "color:red";
              $tpl->COLS = "";
              $tpl->SITUA = "Boleto Vencido";
              $tpl->block("SEG_BOL1");
              
            } else {
              $tpl->VENC = "";
              $tpl->COLS = "";
              if ($sql->result("DVENCBOPLAN") == date('Y-m-d')) {
                $tpl->SITUA = "Aguardando <font color='red'>(Vende Hoje)</font>";
              } else {
                $tpl->SITUA = "Aguardando";
              }
              
              $tpl->LINKBOLETO2 = $sql->result("LINKBOLETO");
              $tpl->block("SEG_BOL2");
            }
            
          }
          $tpl->COR = "#FAFBFA";
          
          if ($tipo_pag == '7') {
            if ($comprovante == '') {
              $tpl->block("DADOSTRANSF");
              $tpl->block("DADOSTRANSF1");
            } else {
              $tpl->block("DADOSTRANSFCC");
              $tpl->block("DADOSTRANSF1CC");
            }
          } else {
            $tpl->block("SEGBOL");
            $tpl->block("SEGBOL1");
          }
        } else if ($situa_pag == '2') {
          $tpl->SITUA = "Em análise";
          $tpl->COR = "#E0EBE1";
          $tpl->COLS = "";
          $tpl->LINKBOLETO = $sql->result("LINKBOLETO");
          if ($tipo_pag == '7') {
            if ($comprovante == '') {
              $tpl->block("DADOSTRANSF");
              $tpl->block("DADOSTRANSF1");
            } else {
              $tpl->block("DADOSTRANSFCC");
              $tpl->block("DADOSTRANSF1CC");
            }
          } else {
            $tpl->block("SEGBOL1");
          }
        } else if ($situa_pag == '3') {
          $tpl->SITUA = "Paga";
          $tpl->COR = "#8FE89C";
          $tpl->COLS = "";
          $tpl->block("SEGBOL_N");
        } else if ($situa_pag == '4') {
          $tpl->SITUA = "Disponível";
          $tpl->COR = "#E0EBE1";
          $tpl->COLS = "";
        } else if ($situa_pag == '5') {
          $tpl->SITUA = "Em disputa";
          $tpl->COLS = "";
        } else if ($situa_pag == '6') {
          $tpl->COR = "#C49AEB";
          $tpl->SITUA = "Devolvida/Extornada";
          $tpl->COR = "#F1BC96";
          $tpl->COLS = "";
        } else if ($situa_pag == '7') {
          $tpl->SITUA = "Cancelada";
          $tpl->COR = "#F9A3A3";
          $tpl->COLS = "";
        } else if ($situa_pag == '9') {
          $tpl->SITUA = "Expirada - Renovar Plano";
          $tpl->COR = "#F9A3A3";
          $tpl->COLS = "";
        }
        
        $sql1 = new Query ($bd);
        $txt1 = "SELECT SEQPLANO,
					          CNOMEPLANO,
					          CDESCPLANO,
					          CPRIMPLANO,
					          CSEGUPLANO,
					          CTERCPLANO,
					          CQUARPLANO,
					          VVALPPLANO,
					          VVALSPLANO,
					          VVALTPLANO,
					          CTEMPPLANO,
                    MENSAPLANO,
                    CCARNPLANO
			 FROM TREDE_PLANOS
			WHERE SEQPLANO = :idplano";
        $sql1->addParam(':idplano',$idpagplan);
        $sql1->executeQuery($txt1);
        
        
        $tpl->NOMEPLANO = ucwords(utf8_encode($sql1->result("CNOMEPLANO")));
        //$tpl->DESC 			= ucwords(utf8_encode($sql->result("CDESCPLANO")));
        $tpl->DESC = "Descrição do Plano";
        $tpl->VALOR = $formata->formataNumero($sql1->result("MENSAPLANO"));
        $tpl->TEMPO = $sql1->result("CTEMPPLANO");
        
        
        //tipo de pagamento//
        
        if ($tipo_pag == '1') {
          $tpl->TIPOP = "Cartão Crédito";
        } else if ($tipo_pag == '2') {
          $tpl->TIPOP = "Boleto";
        } else if ($tipo_pag == '3') {
          $tpl->TIPOP = "Cartão Débito";
        } else if ($tipo_pag == '4') {
          $tpl->TIPOP = "Saldo PagSeguro";
        } else if ($tipo_pag == '7') {
          $tpl->TIPOP = "Transferência Bancária";
        } else if ($tipo_pag == NULL) {
          $tpl->TIPOP = "Aguardando";
        } else if ($tipo_pag == 'a') {
          $tpl->TIPOP = "À vista";
        } else if ($tipo_pag == 'c') {
          $tpl->TIPOP = "Cancelado";
        }
        //tipo de pagamento//
        
        
        $tpl->block("PLANOS1");
        $sql->next();
      }
      
      
      $sql3 = new Query ($bd);
      $txt3 = "SELECT REDE_NOMEUSU,
                REDE_CPFUSUA,
                REDE_EMAILUS,
                REDE_CELULAR,
                REDE_DATAVENC
			   FROM TREDE_USUADMIN
			  WHERE REDE_SEQUSUA = :usua";
      $sql3->AddParam(':usua',$seq);
      $sql3->executeQuery($txt3);
      
      $resdia = $sql3->result("REDE_DATAVENC");
      
      if ($resdia == "") {
        $dia = "10";
      } else {
        $dia = $sql3->result("REDE_DATAVENC");
      }
      
      $res_datual = strtotime(date('Y-m-d'));
      //$res_datual = strtotime(date('2021-02-11'));
      $res_dbol = strtotime(date('Y-m-'.$dia));
      
      /*  sdebug('data atual: '.date('Y-m-d'));
         sdebug($res_datual);
        sdebug('data boleto: '.date('Y-m-' . $dia));
        sdebug($res_dbol);
        */
      
      if ($res_datual > $res_dbol) {
        
        $mes = date('m',strtotime('+1 months',strtotime(date('Y-m-d'))));
        
        $data_bol = date('Y-'.$mes.'-'.$dia);
      } else {
        $data_bol = date('Y-m-'.$dia);
        
      }
      
      
      //$tpl->DATA_VENC = date('Y/m/d', strtotime("+3 days", strtotime(date('Y-m-d'))));
      $tpl->DATA_VENC = $data_bol;
      //$tpl->NOME_USUA = $sql3->result("REDE_NOMEUSU");
      $tpl->CPF_USUA = $sql3->result("REDE_CPFUSUA");
      $tpl->EMAIL_USUA = $sql3->result("REDE_EMAILUS");
      $tpl->CELU_USUA = $sql3->result("REDE_CELULAR");
      
      
      if (isset($_POST['enviou']) && $_POST['enviou'] == 1) {
        
        // arquivo
        $arquivo = $_FILES['arquivo'];
        $idpagplano = $_POST['idpagplano'];
        
        
        // Tamanho máximo do arquivo (em Bytes)
        $tamanhoPermitido = 1024 * 1024 * 10; // 2Mb
        
        //Define o diretorio para onde enviaremos o arquivo
        $diretorio = "comprovantes/mensalidade/";
        
        // verifica se arquivo foi enviado e sem erros
        if ($arquivo['error'] == UPLOAD_ERR_OK) {
          
          // pego a extensão do arquivo
          $extensao = $func->extensao($arquivo['name']);
          
          // valida a extensão
          if (
          in_array($extensao,array(
            "pdf",
            "jpg",
            "jpeg",
            "png",
            "gif",
          ))
          ) {
            
            // verifica tamanho do arquivo
            if ($arquivo['size'] > $tamanhoPermitido) {
              
              $tpl->MSG = "<strong>Aviso!</strong> O arquivo enviado é muito grande, envie arquivos de até ".$tamanhoPermitido / MB." MB.";
              $tpl->block("ERRO");
              
            } else {
              $altura = "1000";
              $largura = "800";
              
              if ($extensao != 'pdf') {
                
                switch ($_FILES['arquivo']['type']):
                  case 'image/jpeg';
                  case 'image/pjpeg';
                    $imagem_temporaria = imagecreatefromjpeg($_FILES['arquivo']['tmp_name']);
                    
                    $largura_original = imagesx($imagem_temporaria);
                    
                    $altura_original = imagesy($imagem_temporaria);
                    
                    $nova_largura = $largura ? $largura : floor(($largura_original / $altura_original) * $altura);
                    
                    $nova_altura = $altura ? $altura : floor(($altura_original / $largura_original) * $largura);
                    
                    $imagem_redimensionada = imagecreatetruecolor($nova_largura,$nova_altura);
                    imagecopyresampled($imagem_redimensionada,$imagem_temporaria,0,0,0,0,$nova_largura,$nova_altura,$largura_original,$altura_original);
                    
                    $novo_nome = md5(date('YmdHis')).".".$extensao;
                    
                    imagejpeg($imagem_redimensionada,$diretorio.$novo_nome);
                    
                    //move_uploaded_file($_FILES['arquivo']['name'],$diretorio);
                    //echo "<img src='".$diretorio.$_FILES['arquivo']['name']. "'>";
                    $sql = new Query($bd);
                    $sql->clear();
                    $txt = "UPDATE TREDE_PAGAPLANO SET CCOMPRPPLAN = '".$diretorio.$novo_nome."',
																	CNOMCOPPLAN = '".$novo_nome."',
																	DCOMPRPPLAN = '".date('Y-m-d H:i:s')."'
																	WHERE IDPGSEGPLAN = '".$idpagplano."' ";
                    $sql->executeSQL($txt);
                    
                    echo "<script>alert('Enviado com Sucesso.');  window.location.href = window.location.href </script>";
                    
                    break;
                  
                  
                  //Caso a imagem seja extensão PNG cai nesse CASE
                  case 'image/png':
                  case 'image/x-png';
                    $imagem_temporaria = imagecreatefrompng($_FILES['arquivo']['tmp_name']);
                    
                    $largura_original = imagesx($imagem_temporaria);
                    $altura_original = imagesy($imagem_temporaria);
                    
                    /* Configura a nova largura */
                    $nova_largura = $largura ? $largura : floor(($largura_original / $altura_original) * $altura);
                    
                    /* Configura a nova altura */
                    $nova_altura = $altura ? $altura : floor(($altura_original / $largura_original) * $largura);
                    
                    /* Retorna a nova imagem criada */
                    $imagem_redimensionada = imagecreatetruecolor($nova_largura,$nova_altura);
                    
                    /* Copia a nova imagem da imagem antiga com o tamanho correto */
                    //imagealphablending($imagem_redimensionada, false);
                    //imagesavealpha($imagem_redimensionada, true);
                    
                    imagecopyresampled($imagem_redimensionada,$imagem_temporaria,0,0,0,0,$nova_largura,$nova_altura,$largura_original,$altura_original);
                    
                    $novo_nome = md5(date('YmdHis')).".".$extensao;
                    
                    imagejpeg($imagem_redimensionada,$diretorio.$novo_nome);
                    
                    $sql = new Query($bd);
                    $sql->clear();
                    $txt = "UPDATE TREDE_PAGAPLANO SET CCOMPRPPLAN = '".$diretorio.$novo_nome."',
																CNOMCOPPLAN = '".$novo_nome."',
																DCOMPRPPLAN = '".date('Y-m-d H:i:s')."'
																WHERE IDPGSEGPLAN = '".$idpagplano."' ";
                    $sql->executeSQL($txt);
                    
                    echo "<script>alert('Enviado com Sucesso.');  window.location.href = window.location.href </script>";
                    
                    break;
                
                endswitch;
              } else if ($extensao == 'pdf') {
                
                $novo_nome = md5(date('YmdHis')).".".$extensao;
                $enviou = move_uploaded_file($_FILES['arquivo']['tmp_name'],$diretorio.$novo_nome);
                
                $sql = new Query($bd);
                $sql->clear();
                $txt = "UPDATE TREDE_PAGAPLANO SET CCOMPRPPLAN = '".$diretorio.$novo_nome."',
															CNOMCOPPLAN = '".$novo_nome."',
															DCOMPRPPLAN = '".date('Y-m-d H:i:s')."'
															WHERE IDPGSEGPLAN = '".$idpagplano."' ";
                $sql->executeSQL($txt);
                
              }
            }
          } else {
            $tpl->MSG = "<strong>Erro!</strong> Somente arquivos de Imagens são permitidos.";
            $tpl->block("ERRO");
          }
          
        } else {
          $tpl->MSG = "<strong>Atenção!</strong> Você deve enviar um arquivo.";
          $tpl->block("ERRO");
        }
      }
    }
  } else {
    $seg->verificaSession($_SESSION['idUsuario']);
  }
  
  
  $tpl->show();
  $bd->close();
?>