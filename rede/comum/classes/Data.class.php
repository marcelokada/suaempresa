<?php
 
  class Data {
  
    function __call($func,$arq) {
      if(!method_exists(get_class($this),$func)){
        throw new Exception("Classe: Data - O metodo \"$func\" nao existe");
      }
    }
    
    function dataAtual($bd,$mascara = 'DD/MM/YYYY') {
      //Conversao da mascara do php para a mascara do oracle;    
      if ($mascara == 'd/m/Y')
        $mascara = 'DD/MM/YYYY';
      else if ($mascara == 'd/m/Y H:i:s')
        $mascara = 'DD/MM/YYYY HH24:MI:SS';
      else if ($mascara == 'Ymd')
        $mascara = 'YYYYMMDD';
      else if ($mascara == 'm/Y')
        $mascara = 'MMYYYY';        
      else if ($mascara == 'Y')
        $mascara = 'YYYY';   
      else if ($mascara == 'm')
        $mascara = 'MM';          
	    else if ($mascara == 'd/m/Y HH24:mi')
        $mascara = 'DD/MM/YYYY HH24:MI';
        
      $sql = new Query($bd);
      $txt = "SELECT TO_CHAR(SYSDATE,:mascara) DATA FROM DUAL ";
      $sql->addParam(":mascara",$mascara);  
      $sql->executeQuery($txt);
      
      return $sql->result("DATA");      
    }
    
    function boasVindas() {
      $hora = date('G');
        
      if (($hora >= 0) and ($hora < 6)) {
        $mensagem = "Boa madrugada";
      } else if (($hora >= 6) and ($hora < 12)) {
        $mensagem = "Bom dia";
      } else if (($hora >= 12) and ($hora < 18)) {
        $mensagem = "Boa tarde";
      } else {
        $mensagem = "Boa noite".$hora;
      }
       
      return $mensagem;
    }
    
    function diaSemanaOracle($dia) {

      if($dia==1) { $semana =  "Domingo"; }
      elseif($dia==2) { $semana = "Segunda-feira"; }
      elseif($dia==3) { $semana = "Terça-feira"; }
      elseif($dia==4) { $semana = "Quarta-feira"; }
      elseif($dia==5) { $semana = "Quinta-feira"; }
      elseif($dia==6) { $semana = "Sexta-feira"; }
      elseif($dia==7) { $semana = "Sábado"; }
      elseif($dia==8) { $semana = "Feriado"; }  
      
      return $semana;
    }    
  
    function dataExtenso($data1,$completa) {
      $data = getDate(strtotime(substr($data1,6,4)."-".substr($data1,3,2)."-".substr($data1,0,2)));

      if($data["wday"]==0) { $semana =  "Domingo, "; }
      elseif($data["wday"]==1) { $semana = "Segunda-feira, "; }
      elseif($data["wday"]==2) { $semana = "Terça-feira, "; }
      elseif($data["wday"]==3) { $semana = "Quarta-feira, "; }
      elseif($data["wday"]==4) { $semana = "Quinta-feira, "; }
      elseif($data["wday"]==5) { $semana = "Sexta-feira, "; }
      elseif($data["wday"]==6) { $semana = "Sábado, "; }

      if($data["mon"]==1) { $mes="Janeiro"; }
      elseif($data["mon"]==2) { $mes="Fevereiro"; }
      elseif($data["mon"]==3) { $mes="Março"; }
      elseif($data["mon"]==4) { $mes="Abril"; }
      elseif($data["mon"]==5) { $mes="Maio"; }
      elseif($data["mon"]==6) { $mes="Junho"; }
      elseif($data["mon"]==7) { $mes="Julho"; }
      elseif($data["mon"]==8) { $mes="Agosto"; }
      elseif($data["mon"]==9) { $mes="Setembro"; }
      elseif($data["mon"]==10) { $mes="Outubro"; }
      elseif($data["mon"]==11) { $mes="Novembro"; }
      elseif($data["mon"]==12) { $mes="Dezembro"; }

      if ($completa==1)
        return $semana.$data["mday"]." de ".$mes." de ".$data["year"].".";
      else
        return $data["mday"]." de ".$mes." de ".$data["year"].".";
    }
    
    function mesExtenso($mes) {
      switch ($mes) {
        case '01' : $extenso = 'Janeiro';   break;
        case '02' : $extenso = 'Fevereiro'; break;
        case '03' : $extenso = 'Março';     break;
        case '04' : $extenso = 'Abril';     break;
        case '05' : $extenso = 'Maio';      break;
        case '06' : $extenso = 'Junho';     break;
        case '07' : $extenso = 'Julho';     break;
        case '08' : $extenso = 'Agosto';    break;
        case '09' : $extenso = 'Setembro';  break;
        case '10' : $extenso = 'Outubro';   break;
        case '11' : $extenso = 'Novembro';  break;
        case '12' : $extenso = 'Dezembro';  break;
      }
      
      return $extenso;
    }    
    
    function formataData ($bd,$data,$mascara,$mascara2 = 'DD/MM/RRRR') {
      $sql = new Query($bd);
      $sql->clear();
      $txt = "SELECT TO_CHAR(TO_DATE(:data,:mascara2),:mascara) DATA FROM DUAL ";
      $sql->addParam(":data",$data);
      $sql->addParam(":mascara2",$mascara2);
      $sql->addParam(":mascara",$mascara);  
      $sql->executeQuery($txt);
      
      return $sql->result("DATA");
    }    
	
	function formataData1 ($data) {
		$dia = substr(($data),8,2);
		$mes = substr(($data),5,2);
		$ano = substr(($data),0,4);
		
		$data_format = $dia.'/'.$mes.'/'.$ano;
		
      return $data_format;
    }    
    
    function dataInvertida($data) {
      if ($data != "")
        return substr($data,6,4)."-".substr($data,3,2)."-".substr($data,0,2);
      else
        return $data;
    } 
	
    function dataInvertida2($data) {
      if ($data != "")
        return substr($data,8,2)."/".substr($data,5,2)."/".substr($data,0,4);
      else
        return $data;
    } 	

    function anoBissexto($ano) {
      if($ano % 4 == 0)
        return true;
      else
        return false;
    } 

    function comparaData($bd,$data1,$data2,$sinal) {

      if ($data2 == '')
        $data2 = $this->dataAtual($bd);
        
      $sql = new Query($bd);
      $txt = "SELECT 1 RETORNO FROM DUAL 
               WHERE TO_DATE(:data1,'DD/MM/RRRR') ".$sinal." TO_DATE(:data2,'DD/MM/RRRR') ";
      $sql->addParam(":data1",$data1);
      $sql->addParam(":data2",$data2);
      $sql->executeQuery($txt);
      
      if ($sql->result("RETORNO") == 1 )
        return 1;
      else 
        return 0;
    } 
    
    function comparaDataHora($bd,$data1,$data2) {

      if ($data2 == '')
        $data2 = $this->dataAtual($bd,'DD/MM/YYYY HH24:mi');
        
      $sql = new Query($bd);
      $txt = "SELECT 1 RETORNO FROM DUAL 
               WHERE TO_DATE(:data1,'DD/MM/YYYY HH24:mi') TO_DATE(:data2,'DD/MM/YYYY HH24:mi') ";
      $sql->addParam(":data1",$data1);
      $sql->addParam(":data2",$data2);
      $sql->executeQuery($txt);
      
      if ($sql->result("RETORNO") == 1 )
        return 1;
      else 
        return 0;
    }       
	
	function calcular_tempo_trasnc($hora1,$hora2){
    $separar[1]=explode(':',$hora1);
    $separar[2]=explode(':',$hora2);

$total_minutos_trasncorridos[1] = ($separar[1][0]*60)+$separar[1][1];
$total_minutos_trasncorridos[2] = ($separar[2][0]*60)+$separar[2][1];
$total_minutos_trasncorridos = $total_minutos_trasncorridos[1]-$total_minutos_trasncorridos[2];
if($total_minutos_trasncorridos<=59) return($total_minutos_trasncorridos.' Minutos');
elseif($total_minutos_trasncorridos>59){
$HORA_TRANSCORRIDA = round($total_minutos_trasncorridos/60);
if($HORA_TRANSCORRIDA<=9) $HORA_TRANSCORRIDA='0'.$HORA_TRANSCORRIDA;
$MINUTOS_TRANSCORRIDOS = $total_minutos_trasncorridos%60;
if($MINUTOS_TRANSCORRIDOS<=9) $MINUTOS_TRANSCORRIDOS='0'.$MINUTOS_TRANSCORRIDOS;
return ($HORA_TRANSCORRIDA.':'.$MINUTOS_TRANSCORRIDOS.' Horas');

} }
    
    function diferenca ($data1,$data2="",$tipo=""){
      
      if ($data2 == "")
        $data2 = date("d/m/Y H:i");

      if ($tipo == "")
        $tipo = "h";

      $dia1      = substr($data1,0,2);
      $mes1      = substr($data1,3,2);
      $ano1      = substr($data1,6,4);
      $horas1    = substr($data1,11,2);
      $minutos1  = substr($data1,14,2);
      
      $dia2      = substr($data2,0,2);
      $mes2      = substr($data2,3,2);
      $ano2      = substr($data2,6,4);
      $horas2    = substr($data2,11,2);
      $minutos2  = substr($data2,14,2);
           
      $segundos = mktime($horas2,$minutos2,0,$mes2,$dia2,$ano2) - mktime($horas1,$minutos1,0,$mes1,$dia1,$ano1);

      switch($tipo){
        case "m": $difere = $segundos/60;
        break;
        case "H": $difere = $segundos/3600;
        break;
        case "h": $difere = round($segundos/3600);
        break;
        case "D": $difere = $segundos/86400;
        break;
        case "d": $difere = round($segundos/86400);
        break;
      }

      return $difere;
    }    
        

    
    function validaHora($horario) {
      $formata = new Formata();
      
      if ($formata->somenteNumeros($horario) == '')
        return false;
      else {
        if (strLen($horario) == 5) {
          $hora      = substr($horario,0,2);
          $separador = substr($horario,2,1);
          $minuto    = substr($horario,3,2); 

          //echo $hora." = ".$separador." = ".$minuto;
          if (($separador <> ':') or
              (intval($hora) > 23) or 
              (intval($minuto) > 60))
            return false;
          else
            return true;
        }
        else
          return false;
      }
    }  

    function incrementaData($bd,$data,$numero,$tipo='D') {
      $sql = new Query($bd);
      
      if ($tipo == 'D')
        $txt = "SELECT TO_CHAR(TO_DATE(:data1,'DD/MM/YYYY') + (:numero),'DD/MM/YYYY') DATA FROM DUAL ";
      else if ($tipo == 'M')
        $txt = "SELECT TO_CHAR(ADD_MONTHS(TO_DATE(:data1,'DD/MM/YYYY'),:numero),'DD/MM/YYYY') DATA FROM DUAL ";
      else if ($tipo == 'A')
        $txt = "SELECT TO_CHAR(ADD_MONTHS(TO_DATE(:data1,'DD/MM/YYYY'),:numero * 12),'DD/MM/YYYY') DATA FROM DUAL ";
      else if ($tipo == 'Y')
        $txt = "SELECT TO_CHAR(ADD_MONTHS(TO_DATE(:data1,'YYYY'),:numero * 12),'YYYY') DATA FROM DUAL";
      
      $sql->addParam(":data1",$data);
      $sql->addParam(":numero",$numero);
      $sql->executeQuery($txt);
      
      return $sql->result("DATA");
    }    
   
    function incrementaMeses($bd,$data,$numero) {
      $sql = new Query($bd);
      $sql->clear();
      $txt = "SELECT TO_CHAR(ADD_MONTHS(nvl(TO_DATE(:data,'DD/MM/YYYY'),trunc(sysdate)),nvl(:numero,0)),'DD/MM/YYYY') DATA FROM DUAL ";
      $sql->addParam(":data",$data);
      $sql->addParam(":numero",$numero);
      $sql->executeQuery($txt);
      
      return $sql->result("DATA");
    }    

    
    function ultimoDiaMes($bd, $data){
      $sql = new Query($bd);
      $txt = "SELECT LAST_DAY(:data) DATA FROM DUAL ";
      $sql->addParam(":data",$data);
      $sql->executeQuery($txt);  
      return $sql->result("DATA");
    }      


    function localData($bd, $cidade){
      $data1     = $this->dataAtual($bd);       
      
      $dia1      = substr($data1,0,2);
      $mes1      = $this->mesExtenso(substr($data1,3,2));
      $ano1      = substr($data1,6,4);
      
      //Exemplo: Londrina, 07 de Novembro de 2017  
      return $cidade . ', ' . $dia1 . ' de ' . $mes1 . ' de ' . $ano1; 
    }    
    
    function SomaDiminuiData($data, $dias, $operacao){
      
      $nova_data  = explode("/", $data); 
      $nova_data  = $nova_data[2] . "-" . $nova_data[1] . "-" . $nova_data[0];
      if ($operacao == 'A')
        $nova_data  =  date('d/m/Y', strtotime('+'.$dias.' days', strtotime($nova_data))); 
      else if ($operacao == 'S')
        $nova_data  =  date('d/m/Y', strtotime('-'.$dias.' days', strtotime($nova_data))); 
      
      return $nova_data; 
      
    }
  }
?>