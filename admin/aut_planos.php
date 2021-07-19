<?php
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  error_reporting(0);
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","aut_planos.html");
  
  if (isset($_SESSION['aut_admin'])) {
    $autenticado           = TRUE;
    $_SESSION['aut_admin'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      $tpl->EMPRESA = LINK_EMPRESA;
      
      $id_sessao = $_SESSION['idSessao_admin'];
      $id_confer = $_GET['idSessao'];
      
      $seg->verificaSession($id_sessao);
      $tpl->ID_SESSAO = $_SESSION['idSessao_admin'];
      $tpl->ID_ADMIN  = $_SESSION['usuaAdmin'];
      $seg->verificaSession($id_sessao);
      $nivelusua = $func->RetornaPermissoes_Admin($id_admin);
      
      if ($nivelusua == 'C') {
        $tpl->DISABLE = "style='display:none;'";
      } else if ($nivelusua == 'CB') {
        $tpl->DISABLE = "style='display:none;'";
      } else if ($nivelusua == 'CCAB') {
        $tpl->DISABLE = "style='display:none;'";
      }
      
      $sql1 = new Query($bd);
      $txt1 = "SELECT SEQUPAGPLAN,
                NIDUPAGPLAN,
                NSEQPAGPLAN,
                NVALPAGPLAN,
                CSITPAGPLAN,
                CTIPOPGPLAN,
                DDATPAGPLAN,
                CSITUAPPLAN,    
                CTIPOTRPLAN,
                MENSAPLANO,
                CTIPOOPPLAN,
       					IDPGSEGPLAN,
       					CCOMPRPPLAN
			FROM TREDE_PAGAPLANO
        WHERE CSITUAPPLAN = 'p'
			ORDER BY SEQUPAGPLAN DESC";
      $sql1->executeQuery($txt1);
      
      //WHERE CSITUAPPLAN = 'p'
      
      while (!$sql1->eof()) {
        
        $tpl->ID  = $sql1->result("SEQUPAGPLAN");
        $seq_usua = $sql1->result("SEQUPAGPLAN");
        //$tpl->IDUSU = $sql1->result("NIDUPAGPLAN");
        $tpl->NOME       = ucwords(utf8_encode($func->RetonaNomeUsuarioPorSeq1($bd,$sql1->result("NIDUPAGPLAN"))));
        $tpl->PLANO      = ucwords(utf8_encode($func->RetornaNomePlano($sql1->result("NSEQPAGPLAN"))));
        $tpl->DATA       = $data->formataData1($sql1->result("DDATPAGPLAN"));
        $tpl->STATUS_PAG = $func->RetornaSituaPagamento($sql1->result("CSITPAGPLAN"));
        $tpl->TPPG       = ucwords($sql1->result("CTIPOOPPLAN"));
        
        $situplan = $sql1->result("CSITUAPPLAN");
        $tipopag  = $sql1->result("CTIPOTRPLAN");
        
        $tpl->IDPAGA = $sql1->result("IDPGSEGPLAN");
        $comprovante = $sql1->result("CCOMPRPPLAN");
        
        if ($comprovante == '') {
          $tpl->block("VISU1");
        } else {
          $tpl->block("VISU");
        }
        
        if ($tipopag == 'a') {
          $tpl->TIPO       = 'Adesão';
          $tpl->VALORPLANO = $formata->formataNumero($sql1->result("NVALPAGPLAN"));
        } else if ($tipopag == 'm') {
          $tpl->TIPO       = 'Mensalidade';
          $tpl->VALORPLANO = $formata->formataNumero($sql1->result("MENSAPLANO"));
        }
        
        if ($situplan == 'p') {
          $tpl->DISA  = "";
          $tpl->DISA1 = "";
          $tpl->DISA2 = "";
          $tpl->block("CANCELAR");
          $tpl->STATUSUSUA = 'Aguardando o Admin autorizar';
          $tpl->COR        = "warning";
          $tpl->COR1       = "primary";
        } else if ($situplan == 'a') {
          $tpl->DISA       = "disabled";
          $tpl->DISA1      = "disabled";
          $tpl->DISA2      = "disabled";
          $tpl->STATUSUSUA = 'Autorizado';
          $tpl->COR        = "success";
          $tpl->COR1       = "secondary";
        } else if ($situplan == 'c') {
          $tpl->DISA       = "disabled";
          $tpl->DISA1      = "disabled";
          $tpl->DISA2      = "disabled";
          $tpl->STATUSUSUA = 'Plano cancelado';
          $tpl->COR        = "red";
          $tpl->COR1       = "secondary";
        } else if (($situplan == '') and ($situplan == NULL)) {
          $tpl->DISA  = "";
          $tpl->DISA1 = "";
          $tpl->DISA2 = "";
          $tpl->block("CANCELAR");
          $tpl->STATUSUSUA = 'Aguardando';
          $tpl->COR        = "black";
          $tpl->COR1       = "primary";
        }
        
        $sql1->next();
        $tpl->block("AUT");
      }
      
      
      $sql1 = new Query($bd);
      $sql1->clear();
      $txt1 = "SELECT SEQUPAGPLAN,
                NIDUPAGPLAN,
                NSEQPAGPLAN,
                NVALPAGPLAN,
                CSITPAGPLAN,
                CTIPOPGPLAN,
                DDATPAGPLAN,
                CSITUAPPLAN,
                CTIPOTRPLAN,
                MENSAPLANO,
                CTIPOOPPLAN,
       					IDPGSEGPLAN,
       					CCOMPRPPLAN
			FROM TREDE_PAGAPLANO
     WHERE CSITUAPPLAN = 'a'
			ORDER BY SEQUPAGPLAN DESC";
      $sql1->executeQuery($txt1);
      
      //WHERE CSITUAPPLAN = 'p'
      
      while (!$sql1->eof()) {
        
        $tpl->ID1 = $sql1->result("SEQUPAGPLAN");
        $seq_usua = $sql1->result("SEQUPAGPLAN");
        //$tpl->IDUSU = $sql1->result("NIDUPAGPLAN");
        $tpl->NOME1       = ucwords(utf8_encode($func->RetonaNomeUsuarioPorSeq1($bd,$sql1->result("NIDUPAGPLAN"))));
        $tpl->PLANO1      = ucwords(utf8_encode($func->RetornaNomePlano($sql1->result("NSEQPAGPLAN"))));
        $tpl->DATA1       = $data->formataData1($sql1->result("DDATPAGPLAN"));
        $tpl->STATUS_PAG1 = $func->RetornaSituaPagamento($sql1->result("CSITPAGPLAN"));
        $tpl->TPPG1       = ucwords($sql1->result("CTIPOOPPLAN"));
        
        $situplan = $sql1->result("CSITUAPPLAN");
        $tipopag  = $sql1->result("CTIPOTRPLAN");
        
        $tpl->IDPAGA1 = $sql1->result("IDPGSEGPLAN");
        $comprovante  = $sql1->result("CCOMPRPPLAN");
        
        if ($comprovante == '') {
          $tpl->block("VISU11");
        } else {
          $tpl->block("VISU2");
        }
        
        if ($tipopag == 'a') {
          $tpl->TIPO1       = 'Adesão';
          $tpl->VALORPLANO1 = $formata->formataNumero($sql1->result("NVALPAGPLAN"));
        } else if ($tipopag == 'm') {
          $tpl->TIPO1       = 'Mensalidade';
          $tpl->VALORPLANO1 = $formata->formataNumero($sql1->result("MENSAPLANO"));
        }
        
        $sql1->next();
        $tpl->block("AUT1");
      }
      
      
      $sql1 = new Query($bd);
      $sql1->clear();
      $txt1 = "SELECT SEQUPAGPLAN,
                NIDUPAGPLAN,
                NSEQPAGPLAN,
                NVALPAGPLAN,
                CSITPAGPLAN,
                CTIPOPGPLAN,
                DDATPAGPLAN,
                CSITUAPPLAN,
                CTIPOTRPLAN,
                MENSAPLANO,
                CTIPOOPPLAN,
       					IDPGSEGPLAN,
       					CCOMPRPPLAN
			FROM TREDE_PAGAPLANO
     WHERE CSITUAPPLAN = 'c'
			ORDER BY SEQUPAGPLAN DESC";
      $sql1->executeQuery($txt1);
      
      //WHERE CSITUAPPLAN = 'p'
      
      while (!$sql1->eof()) {
        
        $tpl->ID2 = $sql1->result("SEQUPAGPLAN");
        $seq_usua = $sql1->result("SEQUPAGPLAN");
        //$tpl->IDUSU = $sql1->result("NIDUPAGPLAN");
        $tpl->NOME2       = ucwords(utf8_encode($func->RetonaNomeUsuarioPorSeq1($bd,$sql1->result("NIDUPAGPLAN"))));
        $tpl->PLANO2      = ucwords(utf8_encode($func->RetornaNomePlano($sql1->result("NSEQPAGPLAN"))));
        $tpl->DATA2       = $data->formataData1($sql1->result("DDATPAGPLAN"));
        $tpl->STATUS_PAG2 = $func->RetornaSituaPagamento($sql1->result("CSITPAGPLAN"));
        $tpl->TPPG2       = ucwords($sql1->result("CTIPOOPPLAN"));
        
        $situplan = $sql1->result("CSITUAPPLAN");
        $tipopag  = $sql1->result("CTIPOTRPLAN");
        
        $tpl->IDPAGA2 = $sql1->result("IDPGSEGPLAN");
        $comprovante  = $sql1->result("CCOMPRPPLAN");
        
        if ($comprovante == '') {
          $tpl->block("VISU111");
        } else {
          $tpl->block("VISU3");
        }
        
        if ($tipopag == 'a') {
          $tpl->TIPO2       = 'Adesão';
          $tpl->VALORPLANO2 = $formata->formataNumero($sql1->result("NVALPAGPLAN"));
        } else if ($tipopag == 'm') {
          $tpl->TIPO2       = 'Mensalidade';
          $tpl->VALORPLANO2 = $formata->formataNumero($sql1->result("MENSAPLANO"));
        }
        
        $sql1->next();
        $tpl->block("AUT2");
      }
      if (isset($_POST['auto'])) {
        
        $seq_usuas = $_POST['auto'];
        $data      = date('Y-m-d');
        
        /*	sdebug('----------');
        sdebug('----------');
        sdebug('----------');*/
        
        $sql1 = new Query($bd);
        $txt1 = "UPDATE TREDE_PAGAPLANO SET CSITUAPPLAN = 'a',
                                        CSITPAGPLAN = '3',
                                        DDTINIPPLAN = '".date('Y-m-d')."',
                                        DDTFIMPPLAN = '".date('Y-m-d',strtotime("+30 days",strtotime($data)))."'
                WHERE SEQUPAGPLAN = :idpagplan";
        $sql1->addParam(':idpagplan',$seq_usuas);
        $sql1->executeSQL($txt1);
        
        $sql29 = new Query($bd);
        $txt29 = "SELECT SEQUPAGPLAN,
                NIDUPAGPLAN,
                NSEQPAGPLAN,
                NVALPAGPLAN,
                CSITPAGPLAN,
                CTIPOPGPLAN,
                DDATPAGPLAN,
                CSITUAPPLAN,    
                CTIPOTRPLAN,
                MENSAPLANO,
                CTIPOOPPLAN,
       					IDPGSEGPLAN
			FROM TREDE_PAGAPLANO
			WHERE CSITUAPPLAN = 'a'
				AND SEQUPAGPLAN = '".$seq_usuas."'
			ORDER BY SEQUPAGPLAN DESC";
        $sql29->executeQuery($txt29);
        
        $reference = $sql29->result("IDPGSEGPLAN");
        
        $sqla = new Query ($bd);
        $txta = "SELECT NIDUPAGPLAN,ADESAOPLANO,MENSAPLANO,CTIPOTRPLAN,SEQUPAGPLAN FROM TREDE_PAGAPLANO
			WHERE IDPGSEGPLAN = :refe";
        $sqla->addParam(':refe',$reference);
        $sqla->executeQuery($txta);
        
        $idusu      = $sqla->result("NIDUPAGPLAN");
        $tipo_trans = $sqla->result("CTIPOTRPLAN");
        $seqpagplan = $sqla->result("SEQUPAGPLAN");
        
        if ($tipo_trans == 'a') {
          $valor_total = $sqla->result("ADESAOPLANO");
        } else if ($tipo_trans == 'm') {
          $valor_total = $sqla->result("MENSAPLANO");
        }
        
        
        /************* BONIFICAÇÃOO **************/
        /************* BONIFICAÇÃOO **************/
        /************* BONIFICAÇÃOO **************/
        
        //	sdebug('id usuario comprador: '.$idusu);
        
        $sqln1 = new Query($bd);
        $txtn1 = "SELECT NNUMEFILI,NNUMENIVE,NPORCNIVE FROM TREDE_NIVEL WHERE NIDUSNIVE = :sequ
            AND NNUMEFILI NOT IN(SELECT REDE_SEQUSUA FROM TREDE_USUADMIN WHERE REDE_ADMINUS = 's')";
        $sqln1->addParam(':sequ',$idusu);
        $sqln1->executeQuery($txtn1);
        
        while (!$sqln1->eof()) {
          $seq_patrocinador1 = $sqln1->result("NNUMEFILI");
          $nivel_comprador_1 = 1;
          $porcetagem_nivel1 = $sqln1->result("NPORCNIVE");
          
          $sqln2 = new Query($bd);
          $txtn2 = "SELECT NNUMEFILI,NNUMENIVE,NPORCNIVE FROM TREDE_NIVEL WHERE NIDUSNIVE = :sequ
							AND NNUMEFILI NOT IN(SELECT REDE_SEQUSUA FROM TREDE_USUADMIN WHERE REDE_ADMINUS = 's')";
          $sqln2->addParam(':sequ',$seq_patrocinador1);
          $sqln2->executeQuery($txtn2);
          
          while (!$sqln2->eof()) {
            $seq_patrocinador2 = $sqln2->result("NNUMEFILI");
            $nivel_comprador_2 = 2;
            $porcetagem_nivel2 = $sqln2->result("NPORCNIVE");
            
            $sqln3 = new Query($bd);
            $txtn3 = "SELECT NNUMEFILI,NNUMENIVE,NPORCNIVE FROM TREDE_NIVEL WHERE NIDUSNIVE = :sequ
							  AND NNUMEFILI NOT IN(SELECT REDE_SEQUSUA FROM TREDE_USUADMIN WHERE REDE_ADMINUS = 's')";
            $sqln3->addParam(':sequ',$seq_patrocinador2);
            $sqln3->executeQuery($txtn3);
            
            while (!$sqln3->eof()) {
              $seq_patrocinador3 = $sqln3->result("NNUMEFILI");
              $nivel_comprador_3 = 3;
              $porcetagem_nivel3 = $sqln3->result("NPORCNIVE");
              
              $sqln4 = new Query($bd);
              $txtn4 = "SELECT NNUMEFILI,NNUMENIVE,NPORCNIVE FROM TREDE_NIVEL WHERE NIDUSNIVE = :sequ
  								AND NNUMEFILI NOT IN(SELECT REDE_SEQUSUA FROM TREDE_USUADMIN WHERE REDE_ADMINUS = 's')";
              $sqln4->addParam(':sequ',$seq_patrocinador3);
              $sqln4->executeQuery($txtn4);
              
              while (!$sqln4->eof()) {
                $seq_patrocinador4 = $sqln4->result("NNUMEFILI");
                $nivel_comprador_4 = 4;
                $porcetagem_nivel4 = $sqln4->result("NPORCNIVE");
                
                $nnumeidplano4 = $func->RetornaIDPlanoPatrocinador($seq_patrocinador4);
                
                if ($tipo_trans == 'a') {
                  $porc_nivel4 = $func->RetornaPorcentagemnivel($nivel_comprador_4);
                } else if ($tipo_trans == 'm') {
                  $porc_nivel4 = $func->RetornaPorcentagemNivelAtivos($nivel_comprador_4);
                }
                
                $valor_total_pat4   = $valor_total * $porc_nivel4 / 100;
                $valor_do_limite4   = $func->RetornaValorUnivelUsuario($nnumeidplano4);
                $usuario_vem_conus4 = $func->RetornaIDUsuaPagaPlano($seq_usuas);
                
                if (($valor_do_limite4 == 0) or ($valor_do_limite4 == '0.00')) {
                  $sql7n4 = new Query ($bd);
                  $txt7n4 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '".$valor_ade_mensal4."' WHERE NIDUPAGPLAN = :idusuas1";
                  $sql7n4->addParam(':idusuas1',$seq_patrocinador4);
                  $sql7n4->executeSQL($txt7n4);
                  
                  $sql24n4 = new Query ($bd);
                  $txt24n4 = "UPDATE TREDE_VOUCHER SET NVALORVOUCH = '".$valor_ade_mensal4."' WHERE NNUMEUSUA = :idusuas1";
                  $sql24n4->addParam(':idusuas1',$seq_patrocinador4);
                  $sql24n4->executeSQL($txt24n4);
                  
                  $sql2n44 = new Query ($bd);
                  $txt2n44 = "INSERT INTO TREDE_EXTRATO_USUA (NNUMEUSUA,DEBITO,CREDITO,DTRAEXTRA,NPATEXTRA,CTIPEXTRA,CTPOEXTRA,NNUMEUSUA1,SEQUPAGPLAN)
														VALUES
						('".$seq_patrocinador3."',0,'".$valor_total_pat4."','".date('Y-m-d H:i:s')."',
						'".$seq_patrocinador4."','".$tipo_trans."','C','".$usuario_vem_conus4."','".$seqpagplan."')";
                  $sql2n44->executeSQL($txt2n44);
                } else {
                  
                  $sql644 = new Query ($bd);
                  $txt644 = "SELECT VALORTOTAL FROM TREDE_ADESAO_MENSA_USU
            WHERE NIDUPAGPLAN = :idusuas";
                  $sql644->addParam('idusuas',$seq_patrocinador4);
                  $sql644->executeQuery($txt644);
                  
                  $res_valorT4       = floatval($sql644->result("VALORTOTAL"));
                  $valor_ade_mensal4 = floatval($res_valorT4 + $valor_total_pat4);
                  
                  
                  if ($valor_ade_mensal4 > $valor_do_limite4) {
                    
                    $sql6n4 = new Query ($bd);
                    $txt6n4 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '".$valor_do_limite4."' WHERE NIDUPAGPLAN = :idusuas1";
                    $sql6n4->addParam(':idusuas1',$seq_patrocinador4);
                    $sql6n4->executeSQL($txt6n4);
                    
                    $sql14n4 = new Query ($bd);
                    $txt14n4 = "UPDATE TREDE_VOUCHER SET NVALORVOUCH = '".$valor_do_limite4."' WHERE NNUMEUSUA = :idusuas1";
                    $sql14n4->addParam(':idusuas1',$seq_patrocinador4);
                    $sql14n4->executeSQL($txt14n4);
                    
                    $sql2n44 = new Query ($bd);
                    $txt2n44 = "INSERT INTO TREDE_EXTRATO_USUA (NNUMEUSUA,DEBITO,CREDITO,DTRAEXTRA,NPATEXTRA,CTIPEXTRA,CTPOEXTRA,NNUMEUSUA1,SEQUPAGPLAN)
														VALUES
									('".$seq_patrocinador3."',0,'".$valor_total_pat4."','".date('Y-m-d H:i:s')."',
									'".$seq_patrocinador4."','".$tipo_trans."','C','".$usuario_vem_conus4."','".$seqpagplan."')";
                    $sql2n44->executeSQL($txt2n44);
                    
                  } else {
                    
                    $sql7n4 = new Query ($bd);
                    $txt7n4 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '".$valor_ade_mensal4."' WHERE NIDUPAGPLAN = :idusuas1";
                    $sql7n4->addParam(':idusuas1',$seq_patrocinador4);
                    $sql7n4->executeSQL($txt7n4);
                    
                    $sql24n4 = new Query ($bd);
                    $txt24n4 = "UPDATE TREDE_VOUCHER SET NVALORVOUCH = '".$valor_ade_mensal4."' WHERE NNUMEUSUA = :idusuas1";
                    $sql24n4->addParam(':idusuas1',$seq_patrocinador4);
                    $sql24n4->executeSQL($txt24n4);
                    
                    $sql2n44 = new Query ($bd);
                    $txt2n44 = "INSERT INTO TREDE_EXTRATO_USUA (NNUMEUSUA,DEBITO,CREDITO,DTRAEXTRA,NPATEXTRA,CTIPEXTRA,CTPOEXTRA,NNUMEUSUA1,SEQUPAGPLAN)
														VALUES
						('".$seq_patrocinador3."',0,'".$valor_total_pat4."','".date('Y-m-d H:i:s')."',
						'".$seq_patrocinador4."','".$tipo_trans."','C','".$usuario_vem_conus4."','".$seqpagplan."')";
                    $sql2n44->executeSQL($txt2n44);
                  }
                }
                
                /*					$usuario_vem_conus4 = $func->RetornaIDUsuaPagaPlano($seq_usuas);
      
                          sdebug('seq Patrocinador: '.$seq_patrocinador4.
                            ' - Nivel Patrocinador: '.$nivel_comprador_4.
                            ' - Plano: '.$nnumeidplano4.
                            ' - porcentagem que PAT recebe:'.$porc_nivel4.
                            ' - valor que recebe: '.$valor_total_pat4.
                            ' - valor limite do plano: '.$valor_do_limite4.
                            ' | vem de quem o bonus: '.$usuario_vem_conus4
                          );*/
                
                
                $sqln4->next();
              }
              
              $nnumeidplano3 = $func->RetornaIDPlanoPatrocinador($seq_patrocinador3);
              
              if ($tipo_trans == 'a') {
                $porc_nivel3 = $func->RetornaPorcentagemnivel($nivel_comprador_3);
              } else if ($tipo_trans == 'm') {
                $porc_nivel3 = $func->RetornaPorcentagemNivelAtivos($nivel_comprador_3);
              }
              
              $valor_total_pat3   = $valor_total * $porc_nivel3 / 100;
              $valor_do_limite3   = $func->RetornaValorUnivelUsuario($nnumeidplano3);
              $usuario_vem_conus3 = $func->RetornaIDUsuaPagaPlano($seq_usuas);
              
              
              if (($valor_do_limite3 == 0) or ($valor_do_limite3 == '0.00')) {
                $sql7n3 = new Query ($bd);
                $txt7n3 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '".$valor_ade_mensal3."' WHERE NIDUPAGPLAN = :idusuas1";
                $sql7n3->addParam(':idusuas1',$seq_patrocinador3);
                $sql7n3->executeSQL($txt7n3);
                
                $sql23n3 = new Query ($bd);
                $txt23n3 = "UPDATE TREDE_VOUCHER SET NVALORVOUCH = '".$valor_ade_mensal3."' WHERE NNUMEUSUA = :idusuas1";
                $sql23n3->addParam(':idusuas1',$seq_patrocinador3);
                $sql23n3->executeSQL($txt23n3);
                
                $sql2n33 = new Query ($bd);
                $txt2n33 = "INSERT INTO TREDE_EXTRATO_USUA (NNUMEUSUA,DEBITO,CREDITO,DTRAEXTRA,NPATEXTRA,CTIPEXTRA,CTPOEXTRA,NNUMEUSUA1,SEQUPAGPLAN)
														VALUES
						('".$seq_patrocinador2."',0,'".$valor_total_pat3."','".date('Y-m-d H:i:s')."',
						'".$seq_patrocinador3."','".$tipo_trans."','C','".$usuario_vem_conus3."','".$seqpagplan."')";
                $sql2n33->executeSQL($txt2n33);
              } else {
                
                $sql633 = new Query ($bd);
                $txt633 = "SELECT VALORTOTAL FROM TREDE_ADESAO_MENSA_USU
            WHERE NIDUPAGPLAN = :idusuas";
                $sql633->addParam('idusuas',$seq_patrocinador3);
                $sql633->executeQuery($txt633);
                
                $res_valorT3       = floatval($sql633->result("VALORTOTAL"));
                $valor_ade_mensal3 = floatval($res_valorT3 + $valor_total_pat3);
                
                if ($valor_ade_mensal3 > $valor_do_limite3) {
                  
                  $sql6n3 = new Query ($bd);
                  $txt6n3 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '".$valor_do_limite3."' WHERE NIDUPAGPLAN = :idusuas1";
                  $sql6n3->addParam(':idusuas1',$seq_patrocinador3);
                  $sql6n3->executeSQL($txt6n3);
                  
                  $sql13n3 = new Query ($bd);
                  $txt13n3 = "UPDATE TREDE_VOUCHER SET NVALORVOUCH = '".$valor_do_limite3."' WHERE NNUMEUSUA = :idusuas1";
                  $sql13n3->addParam(':idusuas1',$seq_patrocinador3);
                  $sql13n3->executeSQL($txt13n3);
                  
                  $sql1n33 = new Query ($bd);
                  $txt1n33 = "INSERT INTO TREDE_EXTRATO_USUA (NNUMEUSUA,DEBITO,CREDITO,DTRAEXTRA,NPATEXTRA,CTIPEXTRA,CTPOEXTRA,NNUMEUSUA1,SEQUPAGPLAN)
														VALUES
								('".$seq_patrocinador2."',0,'".$valor_total_pat3."','".date('Y-m-d H:i:s')."',
								'".$seq_patrocinador3."','".$tipo_trans."','C','".$usuario_vem_conus3."','".$seqpagplan."')";
                  $sql1n33->executeSQL($txt1n33);
                } else {
                  
                  $sql7n3 = new Query ($bd);
                  $txt7n3 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '".$valor_ade_mensal3."' WHERE NIDUPAGPLAN = :idusuas1";
                  $sql7n3->addParam(':idusuas1',$seq_patrocinador3);
                  $sql7n3->executeSQL($txt7n3);
                  
                  $sql23n3 = new Query ($bd);
                  $txt23n3 = "UPDATE TREDE_VOUCHER SET NVALORVOUCH = '".$valor_ade_mensal3."' WHERE NNUMEUSUA = :idusuas1";
                  $sql23n3->addParam(':idusuas1',$seq_patrocinador3);
                  $sql23n3->executeSQL($txt23n3);
                  
                  $usuario_vem_conus3 = $func->RetornaIDUsuaPagaPlano($seq_usuas);
                  
                  $sql2n33 = new Query ($bd);
                  $txt2n33 = "INSERT INTO TREDE_EXTRATO_USUA (NNUMEUSUA,DEBITO,CREDITO,DTRAEXTRA,NPATEXTRA,CTIPEXTRA,CTPOEXTRA,NNUMEUSUA1,SEQUPAGPLAN)
														VALUES
						('".$seq_patrocinador2."',0,'".$valor_total_pat3."','".date('Y-m-d H:i:s')."',
						'".$seq_patrocinador3."','".$tipo_trans."','C','".$usuario_vem_conus3."','".$seqpagplan."')";
                  $sql2n33->executeSQL($txt2n33);
                }
              }
              /*				$usuario_vem_conus3 = $func->RetornaIDUsuaPagaPlano($seq_usuas);
      
                      sdebug('seq Patrocinador: '.$seq_patrocinador3.
                        ' - Nivel Patrocinador: '.$nivel_comprador_3.
                        ' - Plano: '.$nnumeidplano3.
                        ' - porcentagem que PAT recebe:'.$porc_nivel3.
                        ' - valor que recebe: '.$valor_total_pat3.
                        ' - valor limite do plano: '.$valor_do_limite3.
                        ' | vem de quem o bonus: '.$usuario_vem_conus3);*/
              
              $sqln3->next();
            }
            
            $nnumeidplano2 = $func->RetornaIDPlanoPatrocinador($seq_patrocinador2);
            
            if ($tipo_trans == 'a') {
              $porc_nivel2 = $func->RetornaPorcentagemnivel($nivel_comprador_2);
            } else if ($tipo_trans == 'm') {
              $porc_nivel2 = $func->RetornaPorcentagemNivelAtivos($nivel_comprador_2);
            }
            
            $valor_total_pat2   = $valor_total * $porc_nivel2 / 100;
            $valor_do_limite2   = $func->RetornaValorUnivelUsuario($nnumeidplano2);
            $usuario_vem_conus2 = $func->RetornaIDUsuaPagaPlano($seq_usuas);
            
            if (($valor_do_limite2 == 0) or ($valor_do_limite2 == '0.00')) {
              
              $sql7n2 = new Query ($bd);
              $txt7n2 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '".$valor_ade_mensal2."'
            WHERE NIDUPAGPLAN = :idusuas1";
              $sql7n2->addParam(':idusuas1',$seq_patrocinador2);
              $sql7n2->executeSQL($txt7n2);
              
              $sql22n2 = new Query ($bd);
              $txt22n2 = "UPDATE TREDE_VOUCHER SET NVALORVOUCH = '".$valor_ade_mensal2."' WHERE NNUMEUSUA = :idusuas1";
              $sql22n2->addParam(':idusuas1',$seq_patrocinador2);
              $sql22n2->executeSQL($txt22n2);
              
              
              $sql2n22 = new Query ($bd);
              $txt2n22 = "INSERT INTO TREDE_EXTRATO_USUA (NNUMEUSUA,DEBITO,CREDITO,DTRAEXTRA,NPATEXTRA,CTIPEXTRA,CTPOEXTRA,NNUMEUSUA1,SEQUPAGPLAN)
														VALUES
						('".$seq_patrocinador1."',0,'".$valor_total_pat2."','".date('Y-m-d H:i:s')."',
						'".$seq_patrocinador2."','".$tipo_trans."','C','".$usuario_vem_conus2."','".$seqpagplan."')";
              $sql2n22->executeSQL($txt2n22);
            } else {
              
              $sql622 = new Query ($bd);
              $txt622 = "SELECT VALORTOTAL FROM TREDE_ADESAO_MENSA_USU
            WHERE NIDUPAGPLAN = :idusuas";
              $sql622->addParam('idusuas',$seq_patrocinador2);
              $sql622->executeQuery($txt622);
              
              $res_valorT2       = floatval($sql622->result("VALORTOTAL"));
              $valor_ade_mensal2 = floatval($res_valorT2 + $valor_total_pat2);
              
              if ($valor_ade_mensal2 > $valor_do_limite2) {
                
                $sql6n2 = new Query ($bd);
                $txt6n2 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '".$valor_do_limite2."'
            WHERE NIDUPAGPLAN = :idusuas1";
                $sql6n2->addParam(':idusuas1',$seq_patrocinador2);
                $sql6n2->executeSQL($txt6n2);
                
                $sql12n2 = new Query ($bd);
                $txt12n2 = "UPDATE TREDE_VOUCHER SET NVALORVOUCH = '".$valor_do_limite2."' WHERE NNUMEUSUA = :idusuas1";
                $sql12n2->addParam(':idusuas1',$seq_patrocinador2);
                $sql12n2->executeSQL($txt12n2);
                
                $sql1n22 = new Query ($bd);
                $txt1n22 = "INSERT INTO TREDE_EXTRATO_USUA (NNUMEUSUA,DEBITO,CREDITO,DTRAEXTRA,NPATEXTRA,CTIPEXTRA,CTPOEXTRA,NNUMEUSUA1,SEQUPAGPLAN)
														VALUES
							('".$seq_patrocinador1."',0,'".$valor_total_pat2."','".date('Y-m-d H:i:s')."',
							'".$seq_patrocinador2."','".$tipo_trans."','C','".$usuario_vem_conus2."','".$seqpagplan."')";
                $sql1n22->executeSQL($txt1n22);
              } else {
                
                $sql7n2 = new Query ($bd);
                $txt7n2 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '".$valor_ade_mensal2."'
            WHERE NIDUPAGPLAN = :idusuas1";
                $sql7n2->addParam(':idusuas1',$seq_patrocinador2);
                $sql7n2->executeSQL($txt7n2);
                
                $sql22n2 = new Query ($bd);
                $txt22n2 = "UPDATE TREDE_VOUCHER SET NVALORVOUCH = '".$valor_ade_mensal2."' WHERE NNUMEUSUA = :idusuas1";
                $sql22n2->addParam(':idusuas1',$seq_patrocinador2);
                $sql22n2->executeSQL($txt22n2);
                
                $usuario_vem_conus2 = $func->RetornaIDUsuaPagaPlano($seq_usuas);
                
                $sql2n22 = new Query ($bd);
                $txt2n22 = "INSERT INTO TREDE_EXTRATO_USUA (NNUMEUSUA,DEBITO,CREDITO,DTRAEXTRA,NPATEXTRA,CTIPEXTRA,CTPOEXTRA,NNUMEUSUA1,SEQUPAGPLAN)
														VALUES
						('".$seq_patrocinador1."',0,'".$valor_total_pat2."','".date('Y-m-d H:i:s')."',
						'".$seq_patrocinador2."','".$tipo_trans."','C','".$usuario_vem_conus2."','".$seqpagplan."')";
                $sql2n22->executeSQL($txt2n22);
              }
            }
            /*			$usuario_vem_conus2 = $func->RetornaIDUsuaPagaPlano($seq_usuas);
      
                  sdebug('seq Patrocinador: '.$seq_patrocinador2.
                    ' - Nivel Patrocinador: '.$nivel_comprador_2.
                    ' - Plano: '.$nnumeidplano2.
                    ' - porcentagem que PAT recebe:'.$porc_nivel2.
                    ' - valor que recebe: '.$valor_total_pat2.
                    ' - valor limite do plano: '.$valor_do_limite2.
                    ' | vem de quem o bonus: '.$usuario_vem_conus2);*/
            
            $sqln2->next();
          }
          
          $nnumeidplano1 = $func->RetornaIDPlanoPatrocinador($seq_patrocinador1);
          
          if ($tipo_trans == 'a') {
            $porc_nivel1 = $func->RetornaPorcentagemnivel($nivel_comprador_1);
          } else if ($tipo_trans == 'm') {
            $porc_nivel1 = $func->RetornaPorcentagemNivelAtivos($nivel_comprador_1);
          }
          
          $valor_total_pat1   = $valor_total * $porc_nivel1 / 100;
          $valor_do_limite1   = $func->RetornaValorUnivelUsuario($nnumeidplano1);
          $usuario_vem_conus1 = $func->RetornaIDUsuaPagaPlano($seq_usuas);
          
          if (($valor_do_limite1 == 0) or ($valor_do_limite1 == '0.00')) {
            
            $sql61 = new Query ($bd);
            $txt61 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '".$valor_ade_mensal1."'
            WHERE NIDUPAGPLAN = :idusuas1";
            $sql61->addParam(':idusuas1',$seq_patrocinador1);
            $sql61->executeSQL($txt61);
            
            $sql12n1 = new Query ($bd);
            $txt12n1 = "UPDATE TREDE_VOUCHER SET NVALORVOUCH = '".$valor_ade_mensal1."' WHERE NNUMEUSUA = :idusuas1";
            $sql12n1->addParam(':idusuas1',$seq_patrocinador1);
            $sql12n1->executeSQL($txt12n1);
            
            $sql2n11 = new Query ($bd);
            $txt2n11 = "INSERT INTO TREDE_EXTRATO_USUA (NNUMEUSUA,DEBITO,CREDITO,DTRAEXTRA,NPATEXTRA,CTIPEXTRA,CTPOEXTRA,NNUMEUSUA1,SEQUPAGPLAN)
														VALUES
						('".$idusu."',0,'".$valor_total_pat1."','".date('Y-m-d H:i:s')."','".$seq_patrocinador1."',
						'".$tipo_trans."','C','".$usuario_vem_conus1."','".$seqpagplan."')";
            $sql2n11->executeSQL($txt2n11);
          } else {
            
            $sql6 = new Query ($bd);
            $txt6 = "SELECT VALORTOTAL FROM TREDE_ADESAO_MENSA_USU
            WHERE NIDUPAGPLAN = :idusuas";
            $sql6->addParam('idusuas',$seq_patrocinador1);
            $sql6->executeQuery($txt6);
            
            $res_valorT        = floatval($sql6->result("VALORTOTAL"));
            $valor_ade_mensal1 = floatval($res_valorT + $valor_total_pat1);
            
            if ($valor_ade_mensal1 > $valor_do_limite1) {
              
              $sql62 = new Query ($bd);
              $txt62 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '".$valor_do_limite1."'
            WHERE NIDUPAGPLAN = :idusuas1";
              $sql62->addParam(':idusuas1',$seq_patrocinador1);
              $sql62->executeSQL($txt62);
              
              $sql11n1 = new Query ($bd);
              $txt11n1 = "UPDATE TREDE_VOUCHER SET NVALORVOUCH = '".$valor_do_limite1."' WHERE NNUMEUSUA = :idusuas1";
              $sql11n1->addParam(':idusuas1',$seq_patrocinador1);
              $sql11n1->executeSQL($txt11n1);
              
              $sql1n11 = new Query ($bd);
              $txt1n11 = "INSERT INTO TREDE_EXTRATO_USUA (NNUMEUSUA,DEBITO,CREDITO,DTRAEXTRA,NPATEXTRA,CTIPEXTRA,CTPOEXTRA,NNUMEUSUA1,SEQUPAGPLAN)
														VALUES
						('".$idusu."',0,'".$valor_total_pat1."','".date('Y-m-d H:i:s')."','".$seq_patrocinador1."',
						'".$tipo_trans."','C','".$usuario_vem_conus1."','".$seqpagplan."')";
              $sql1n11->executeSQL($txt1n11);
            } else {
              
              
              $sql61 = new Query ($bd);
              $txt61 = "UPDATE TREDE_ADESAO_MENSA_USU SET VALORTOTAL = '".$valor_ade_mensal1."'
            WHERE NIDUPAGPLAN = :idusuas1";
              $sql61->addParam(':idusuas1',$seq_patrocinador1);
              $sql61->executeSQL($txt61);
              
              $sql12n1 = new Query ($bd);
              $txt12n1 = "UPDATE TREDE_VOUCHER SET NVALORVOUCH = '".$valor_ade_mensal1."' WHERE NNUMEUSUA = :idusuas1";
              $sql12n1->addParam(':idusuas1',$seq_patrocinador1);
              $sql12n1->executeSQL($txt12n1);
              
              
              $usuario_vem_conus1 = $func->RetornaIDUsuaPagaPlano($seq_usuas);
              
              $sql2n11 = new Query ($bd);
              $txt2n11 = "INSERT INTO TREDE_EXTRATO_USUA (NNUMEUSUA,DEBITO,CREDITO,DTRAEXTRA,NPATEXTRA,CTIPEXTRA,CTPOEXTRA,NNUMEUSUA1,SEQUPAGPLAN)
														VALUES
						('".$idusu."',0,'".$valor_total_pat1."','".date('Y-m-d H:i:s')."','".$seq_patrocinador1."',
						'".$tipo_trans."','C','".$usuario_vem_conus1."','".$seqpagplan."')";
              $sql2n11->executeSQL($txt2n11);
              
            }
          }
          $usuario_vem_conus1 = $func->RetornaIDUsuaPagaPlano($seq_usuas);
          
          /*				sdebug('seq Patrocinador: '.$seq_patrocinador1.
                    ' | Nivel Patrocinador: '.$nivel_comprador_1.
                    ' | Plano: '.$nnumeidplano1.
                    ' | porcentagem que PAT recebe: '.$porc_nivel1.
                    ' | valor que recebe: '.$valor_total_pat1.
                    ' | valor limite do plano: '.$valor_do_limite1.
                    ' | vem de quem o bonus: '.$usuario_vem_conus1);*/
          
          $sqln1->next();
        }
        
        /************* BONIFICAÇÃOO **************/
        /************* BONIFICAÇÃOO **************/
        /************* BONIFICAÇÃOO **************/
        
        echo "<script>window.location.href = window.location.href</script>";
        //$util->redireciona("aut_planos.php?idSessao=" . $_SESSION['idSessao']);
      }
      
      
      if (isset($_POST['altera'])) {
        $util = new Util();
        $util->redireciona("alterar_planusua.php?idSessao=".$_SESSION['idSessao'].'seqplan='.$_POST['altera']);
      }
      
      if (isset($_POST['del'])) {
        
        $seq_usuas = $_POST['del'];
        
        $sql1 = new Query($bd);
        $txt1 = "DELETE FROM TREDE_PAGAPLANO
                WHERE SEQUPAGPLAN = :idpagplan";
        $sql1->addParam(':idpagplan',$seq_usuas);
        $sql1->executeSQL($txt1);
        
        echo "<script>window.location.href = window.location.href</script>";
        //$util->redireciona("../principal.php?idSessao=" . $_SESSION['idSessao']);
      }
      
      if (isset($_POST['canc'])) {
        
        $seq_usuas = $_POST['canc'];
        
        $sql1 = new Query($bd);
        $txt1 = "UPDATE TREDE_PAGAPLANO SET CSITUAPPLAN = 'c',
                                        CSITPAGPLAN = '7'
                WHERE SEQUPAGPLAN = :idpagplan";
        $sql1->addParam(':idpagplan',$seq_usuas);
        $sql1->executeSQL($txt1);
        
        echo "<script>window.location.href = window.location.href</script>";
        //$util->redireciona("../principal.php?idSessao=" . $_SESSION['idSessao']);
      }
    } else {
      $seg->verificaSession($_SESSION['aut_admin']);
    }
  }
  
  
  $tpl->show();
  $bd->close();
?>