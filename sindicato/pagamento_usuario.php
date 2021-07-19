<?php
  
  require_once("comum/autoload.php");
  if (!isset($_SESSION)) {
    session_start();
  }
  
  $bd = new Database();
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","pagamento_usuario.html");
  
  if (isset($_SESSION['aut_sind'])) {
    $autenticado          = TRUE;
    $_SESSION['aut_sind'] = TRUE;
  } else {
    $autenticado = FALSE;
  }
  
  if ($_SESSION['nomeEmpresa'] == EMPRESA) { ///NOME DA EMPRESA
    
    if ($autenticado == TRUE) {
      
      $id_sessao_c    = $_GET['idSessao'];
      $id_sessao_s    = $_SESSION['idSessao_sind'];
      $idrede         = $_SESSION['idSind'];
      $tpl->ID_SESSAO = $_SESSION['idSessao_sind'];
      $tpl->ID_SIND   = $_SESSION['idSind'];
      
      $seg->verificaSession($id_sessao_s);
      
      if (isset($_POST['listar'])) {
        
        $cpf = $_POST['cpf'];
        $cpf = $func->retirarPontostracosundelinebarra($cpf);
        
        $vencimento = $_POST['vencimento'];
        $status     = $_POST['status'];
        $tipo       = $_POST['tipo'];
        
        
        if ($vencimento != '') {
          $cond = "AND REDE_DATAVENC = '".$vencimento."' ";
        } else {
          $cond = "";
        }
        
        if ($status == 'a') {
          $cond1 = "AND REDE_PLANUSU = 'a' ";
        } else if ($status == 'c') {
          $cond1 = "AND REDE_PLANUSU = 'c' ";
        } else {
          $cond1 = "AND REDE_PLANUSU IN ('c','a') ";
        }
        
        if ($cpf == '') {
          $cond2 = "AND REDE_CPFUSUA IS NOT NULL";
        } else {
          $cond2 = "AND REDE_CPFUSUA = '".$cpf."'";
        }
        
        if ($tipo == 'a') {
          $cond2 = "AND REDE_CPFUSUA IS NOT NULL";
        } else if ($tipo == 'm') {
          $cond2 = "AND REDE_CPFUSUA = '".$cpf."'";
        }
        
        
        $sql34 = new Query ($bd);
        $txt34 = "SELECT NSEQPAGPLAN
			   FROM TREDE_PAGAPLANO
			  WHERE NIDUPAGPLAN = '' ";
        //$sql34->executeQuery($txt34);
        
        $res_pagplan = $sql34->result('NSEQPAGPLAN');
        
        
        $sql1 = new Query($bd);
        $txt1 = "SELECT REDE_SEQUSUA,
                      REDE_NOMEUSU,
                      REDE_CPFUSUA,
                      REDE_EMAILUS,
                      REDE_DNASCUS,
                      REDE_LOGUSUA,
                      REDE_LOGBLOK,
                       REDE_SITUUSU,
                      REDE_DATAVENC
          FROM TREDE_USUADMIN
          WHERE REDE_ADMINUS = 'n'
            ".$cond."
            ".$cond1."
            ".$cond2."
            AND REDE_SEQUSUA IN(SELECT NNUMEUSUA FROM TREDE_SINDICATO_USUA
                WHERE NNUMESIND = '".$idrede."')";
        $sql1->executeQuery($txt1);
        
        while (!$sql1->eof()) {
          
          if ($sql1->count("REDE_SEQUSUA") == 0) {
            
            $tpl->block("NUSUARIOS");
            
          } else {
            
            $seqprodu          = $sql1->result("REDE_SEQUSUA");
            $tpl->REDE_SEQUSUA = $sql1->result("REDE_SEQUSUA");
            $tpl->VENC1        = $sql1->result("REDE_DATAVENC");
            $tpl->REDE_CPFUSUA = $sql1->result("REDE_CPFUSUA");
            $tpl->REDE_NOMEUSU = ucwords(utf8_encode($sql1->result("REDE_NOMEUSU")));
            $tpl->REDE_EMAILUS = $sql1->result("REDE_EMAILUS");
            
            $sql33 = new Query ($bd);
            $txt33 = "SELECT NNUMEPLAN
			   FROM TREDE_AFILIADOS_VEND
			  WHERE NNUMEUSUA = '".$seqprodu."' ";
            $sql33->executeQuery($txt33);
            
            $tpl->PLANO = $func->RetornaNomePlano($sql33->result("NNUMEPLAN"));
            
            $sql34 = new Query ($bd);
            $txt34 = "SELECT LAST_INSERT_ID(NSEQPAGPLAN) NSEQPAGPLAN
			   FROM TREDE_PAGAPLANO
			  WHERE NIDUPAGPLAN = '".$seqprodu."'
			  ORDER BY 1 DESC
        LIMIT 1";
            $sql34->executeQuery($txt34);
            
            $res_pagplan = $sql34->result('NSEQPAGPLAN');
            
            if ($res_pagplan == '') {
              $tpl->TIPO = 'Adesão';
              
              $sql35 = new Query ($bd);
              $txt35 = "SELECT VVALTPLANO,MENSAPLANO
			                FROM TREDE_PLANOS
			               WHERE SEQPLANO = '".$res_pagplan."'";
              $sql35->executeQuery($txt35);
              
              $res2 = $sql35->result('VVALTPLANO');
              
              if ($res2 == '') {
                
                $sql34a = new Query ($bd);
                $txt34a = "SELECT NNUMEPLAN
			           FROM TREDE_AFILIADOS_VEND
			          WHERE NNUMEUSUA = '".$seqprodu."'";
                $sql34a->executeQuery($txt34a);
                
                $res_plan = $sql34a->result("NNUMEPLAN");
                
                $sql35a = new Query ($bd);
                $txt35a = "SELECT VVALTPLANO,MENSAPLANO
			                FROM TREDE_PLANOS
			               WHERE SEQPLANO = '".$res_plan."'";
                $sql35a->executeQuery($txt35a);
  
                if($sql35a->result('VVALTPLANO') == ""){
                  $tpl->VALOR = number_format("0.00",2,',','.');
                }else{
                  $tpl->VALOR = number_format($sql35a->result('VVALTPLANO'),2,',','.');
                }
                
              } else {
                $tpl->VALOR = number_format($sql35->result('VVALTPLANO'),2,',','.');
              }
              
              
            } else {
              
              $tpl->TIPO = 'Mensalidade';
              
              $sql36 = new Query ($bd);
              $txt36 = "SELECT VVALTPLANO,MENSAPLANO
			                FROM TREDE_PLANOS
			               WHERE SEQPLANO = '".$res_pagplan."'";
              $sql36->executeQuery($txt36);
              
              $tpl->VALOR = number_format($sql36->result('MENSAPLANO'),2,',','.');
            }
            
            
            if ($status == 'a') {
              $tpl->STATUS1 = "Ativo";
            } else {
              $tpl->STATUS1 = "<font color='red'>Inativo</font>";
            }
            
            $tpl->block("USUARIOS");
            $sql1->next();
          }
          
          
        }
        
      }
      
      
      if (isset($_POST['gerar'])) {
        
        $usuarios = $_POST['usuarios'];
        
        
        $dtvend  = date('Ymd');
        $dtvend1 = strtotime(date('Ymd'));
        
        $valor_ano = date('Y');
        
        $aleatorion = mt_rand(10,10);
        $valorn     = substr(str_shuffle('0123456789'),0,$aleatorion);
        
        $aleatorio = mt_rand(20,20); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
        $valor     = substr(str_shuffle('AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789'),0,$aleatorio);
        
        $id_pact_vend = $valor_ano.$valorn.$valor;
        
        
        foreach ($usuarios as $id) {
          
          $id1   = explode('/',$id);
          $tipos = $id1[1];
          
          $idusu    = $id1[0];
          $tipo     = 'dotbank';
          $oppag    = 'dotbank';
          $tipopags = 2;
          $bol12    = 'n';
          $idseq    = 1;
          
          /*  $idusu      = "147";
            $idplano    = "6";
            $tempo      = "30";
            $valor      = "300.00";
            $adesao     = "300.00";
            $mensa      = "40.00";
            $tipo       = "dotbank";
            $oppag = "dotbank";
            $tipopags = 2;
            $bol12 = 'n';
            $datavencBOL = '2021/03/10';
            $idseq = 1;*/
          
          $sql3 = new Query ($bd);
          $txt3 = "SELECT REDE_NOMEUSU,
                REDE_CPFUSUA,
                REDE_EMAILUS,
                REDE_CELULAR,
                REDE_DATAVENC
			   FROM TREDE_USUADMIN
			  WHERE REDE_SEQUSUA = :usua";
          $sql3->AddParam(':usua',$idusu);
          $sql3->executeQuery($txt3);
          
          $resdia = $sql3->result("REDE_DATAVENC");
          
          if ($resdia == "") {
            $dia = "10";
          } else {
            $dia = $sql3->result("REDE_DATAVENC");
          }
          
          $sql3b = new Query ($bd);
          $txt3b = 'SELECT NNUMEVEND, NNUMEPLAN
			   FROM TREDE_AFILIADOS_VEND
			  WHERE NNUMEUSUA = :usua';
          $sql3b->AddParam(':usua',$idusu);
          $sql3b->executeQuery($txt3b);
          
          $idvend      = $sql3b->result("NNUMEVEND");
          $id_plan_usu = $sql3b->result("NNUMEPLAN");
          
          $sql_plano = new Query($bd);
          $txt_plano = "SELECT  LAST_INSERT_ID (NSEQPAGPLAN) NSEQPAGPLAN
                FROM TREDE_PAGAPLANO
                WHERE NIDUPAGPLAN = '".$idusu."'
                 AND CTIPOTRPLAN = 'a'
                ORDER BY 1 DESC
                LIMIT 1";
          $sql_plano->executeQuery($txt_plano);
          
          $idplano = $sql_plano->result('NSEQPAGPLAN');
          
          if ($idplano == '') {
            $sql3d = new Query ($bd);
            $txt3d = 'SELECT NNUMEVEND, NNUMEPLAN
			   FROM TREDE_AFILIADOS_VEND
			  WHERE NNUMEUSUA = :usua';
            $sql3d->AddParam(':usua',$idusu);
            $sql3d->executeQuery($txt3d);
            
            $idplano = $sql3b->result("NNUMEPLAN");
          }
          
          $sql_planos = new Query($bd);
          $txt_planos = "SELECT CTEMPPLANO,
                            VVALTPLANO,
                            MENSAPLANO
                            FROM TREDE_PLANOS
                            WHERE SEQPLANO = '".$idplano."'";
          $sql_planos->executeQuery($txt_planos);
          
          $tempo  = $sql_planos->result("CTEMPPLANO");
          $valor  = $sql_planos->result("VVALTPLANO");
          $adesao = $sql_planos->result("VVALTPLANO");
          $mensa  = $sql_planos->result("MENSAPLANO");
          
          $datavencBOL = date('Y-m-'.$dia,strtotime('+1 days',strtotime(date('Y-m-d'))));;
          
          $mes_posterior = $datavencBOL;
          $datavenc      = $datavencBOL;
          $idplano       = 6;
          
          $eventos['dia'] = $dia;
          
          $_SESSION['valor'] = $valor;
          
          $sql2 = new Query ($bd);
          $txt2 = "INSERT INTO TREDE_PAGAPLANO (NIDUPAGPLAN,
										  NSEQPAGPLAN,
										  NVALPAGPLAN,
										  CSITPAGPLAN,
										  CTEMPAGPLAN,
										  DDATPAGPLAN,
                      MENSAPLANO,
                      ADESAOPLANO,
                      CTIPOTRPLAN,
                      CTIPOOPPLAN,
                      CTIPOPGPLAN,
                      CSITUAPPLAN,
                      DVENCBOPLAN,
                      CIDSEQPPLAN,
                      CSTABOLPLAN,
                      IDVENDEPLAN)
			VALUES
			('".$idusu."','".$idplano."','".$valor."','1','".$tempo."',
			'".date('Y-m-d H:i:s')."','".$mensa."','".$adesao."','".$tipos."',
			'".$oppag."','".$tipopags."','p','".$mes_posterior."','".$idseq."','a','".$id_pact_vend."')";
          $sql2->executeSQL($txt2);
          
          $sql1 = new Query ($bd);
          $txt1 = "SELECT LAST_INSERT_ID(SEQUPAGPLAN) SEQUPAGPLAN, NSEQPAGPLAN FROM TREDE_PAGAPLANO
			  WHERE NIDUPAGPLAN = :idusu
			    AND SUBSTR(DDATPAGPLAN,1,10) = '".date('Y-m-d')."'
			    AND CSITPAGPLAN != '7'
				ORDER BY SEQUPAGPLAN DESC
				LIMIT 1";
          $sql1->addParam(':idusu',$idusu);
          $sql1->executeQuery($txt1);
          
          $res_id_pag = $sql1->result("SEQUPAGPLAN");
          $id_plano   = $sql1->result("NSEQPAGPLAN");
          
          $seqplan = 'p'.$id_plano.'p'.$res_id_pag;
          
          $sql5 = new Query ($bd);
          $txt5 = "UPDATE TREDE_PAGAPLANO SET IDPGSEGPLAN = :seqplapp
			 WHERE SEQUPAGPLAN = :res_id_pag";
          $sql5->addParam(':res_id_pag',$res_id_pag);
          $sql5->addParam(':seqplapp',$seqplan);
          $sql5->executeSQL($txt5);
          
          $sql3 = new Query ($bd);
          $txt3 = "SELECT CNOMEPLANO,CTEMPPLANO FROM TREDE_PLANOS
			  WHERE SEQPLANO = :idplano";
          $sql3->addParam(':idplano',$idplano);
          $sql3->executeQuery($txt3);
          
          $res_mes = $sql3->result("CTEMPPLANO");
          
          $eventos['id']  = $res_id_pag;
          $eventos['mes'] = $res_mes;
          $eventos['xxx'] = $seqplan;
          
          $sql6 = new Query($bd);
          $txt6 = "SELECT VEMAILPAGSEG,VTOKENPAGSEG FROM TREDE_PAGSEGURO
				WHERE SEQUENCIACRE = 'a' ";
          $sql6->executeQuery($txt6);
          
          $eventos['email'] = $sql6->result("VEMAILPAGSEG");
          $eventos['token'] = $sql6->result("VTOKENPAGSEG");
          
          $sql61 = new Query($bd);
          $txt61 = "SELECT TOKEN
            FROM TREDE_DOTBANK";
          $sql61->executeQuery($txt61);
          
          $eventos['token_dotbank'] = $sql61->result("TOKEN");
          
          $res_usua           = 0;
          $valor_porct_adesao = 0;
          
          $sql5 = new Query ($bd);
          $txt5 = "INSERT INTO TREDE_TRANSAOCAO_USUA
             (USUARIO_PATROC,
              DEBITO,
              CREDITO,
              TIPO_TRANS,
              NNUMEUSUA,
              DATAHORA_TRANS)
              VALUES
              ('".$res_usua."',
              '0.00',
              '".$valor_porct_adesao."',
              '1',
              '".$idusu."',
              '".date('Y-m-d H:i:s')."')";
          $sql5->executeSQL($txt5);
          
          $sql5a = new Query ($bd);
          $txt5a = "INSERT INTO TREDE_VENDEDOR_USUA
             (NNUMESIND,
              NNUMEVEND,
              NNUMEUSUA,
              DINCLVENU,
              IDPGSEGPLAN,
              IDVENDEPLAN)
              VALUES
              ('".$idrede."',
              '".$idvend."',
              '".$idusu."',
              '".date('Y-m-d H:i:s')."',
              '".$seqplan."',
              '".$id_pact_vend."' )";
          $sql5a->executeSQL($txt5a);
          
        }
        
        
      }
    }
  } else {
    $seg->verificaSession($_SESSION['aut_sind']);
  }
  
  $tpl->show();
  $bd->close();
?>