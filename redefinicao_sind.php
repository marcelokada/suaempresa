<?php
  require_once("comum/autoload.php");
  error_reporting(0);
  $bd = new Database();
  
  $id_sessao = $_GET['idDef'];
  
  $sql = new Query ($bd);
  $txt = "SELECT CSESSSIND,
                 BLOCKSIND,
                 DSESSSIND,
                 HSESSSIND,
                 CEMAISIND
		         FROM TREDE_SINDICATOS
		        WHERE CSESSSIND = :session";
  $sql->addPAram(':session',$id_sessao);
  $sql->executeQuery($txt);
  
  $email          = $sql->result("CEMAISIND");
  $sessao         = $sql->result("CSESSSIND");
  $hora           = $sql->result("HSESSSIND");
  $horafixa       = $hora;
  $horaatual      = strtotime(date('H:i:s'));
  $resultado_hora = $horafixa - $horaatual;
  
  $res_hora  = gmp_sign($resultado_hora); //verifica se o numero é negativo ou nao, positivo 1, igual 0 e negativo -1
  $res_hora1 = md5($res_hora);            //coloquei tudo em md5 por causa de fraude no site
  $conf_hora = md5("-1");
  
  require_once("comum/layout.php");
  $tpl->addFile("CONTEUDO","redefinicao_sind.html");
  
  if($sessao == ""){
    $tpl->block("JA_ALTERADO");
  }else if ($res_hora < 0) {
    $tpl->block("SEM_TEMPO");
  } else {
    $tpl->block("NO_TEMPO");
    $tpl->KEY_GOOGLE = KEY_GOOGLE;
    
    if (isset($_POST['alterar'])) {
      $senha1 = $seg->antiInjection($_POST['senha1']);
      $senha2 = $seg->antiInjection($_POST['senha2']);
      
      $se1 = $seg->antiInjection($_POST['senha1']);
      $se2 = $seg->antiInjection($_POST['senha2']);
      
      $s1 = strlen($senha1);
      $s2 = strlen($senha2);
      
      $senha1 = md5($senha1);
      $senha2 = md5($senha2);
      
      if (($senha1 == '') or ($senha2 == '')) {
        $tpl->MSG = '<font color="red">Preencher a senha!!!</font><br>';
        $tpl->block("ERRO1");
      } else if (($s1 <= 7) or ($s2 <= 7)) {
        $tpl->MSG = '<font color="red">Quantidade de caracateres inválidos. mínimo 8 (oito).</font><br>';
        $tpl->block("ERRO1");
      } else if ($senha1 != $senha2) {
        $tpl->MSG = '<font color="red">As senhas são diferentes</font><br>';
        $tpl->block("ERRO1");
      } else {

        $sql1 = new Query ($bd);
        $txt1 = "UPDATE TREDE_SINDICATOS SET CSENHSIND = :senha,
                                                CSESSSIND = '',
									                              BLOCKSIND = 'n',
									                              HSESSSIND = ''
			  WHERE CSESSSIND = '".$id_sessao."'";
        $sql1->addPAram(':senha',$senha2);
        $sql1->executeSQL($txt1);
        
        $sql11 = new Query ($bd);
        $txt11 = "DELETE FROM TREDE_SEGTENTA
            WHERE CLOGITENTSEG = '".$email."'";
        $sql11->executeSQL($txt11);
        
        $tpl->MSG = '<font color="green">Alteração da senha realizada com sucesso! Faça o Login novamente.</font>';
        $tpl->block("SUCESSO");
        
        $tpl->DISA = "disabled";
        
      }
    }
  }
  
  $tpl->show();
  $bd->close();
?>