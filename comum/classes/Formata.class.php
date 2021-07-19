<?php
  class Formata {
    function initCap($value){
      $palavras = explode(" ",$value);  
      $palavra = "";
         
      foreach ($palavras as $p) {
        if (($p == 'E') or 
            ($p == 'DE') or 
            ($p == 'DO') or 
            ($p == 'DA'))
          $palavra .= strtolower($p);
        else
          $palavra .= ucwords(strtolower($p));
         
        $palavra .= " ";
      }
      return substr($palavra,0,strlen($palavra)-1);  
    }
    
    function formataTelefone ($registro) {

      $registro = trim($registro);
      $registro = $this->somenteNumeros($registro);
      
      if ((strLen($registro) < 10) and ($registro <> ''))
        $registro = $this->acrescentaZeros($registro,10);
      
      if (strlen($registro) == 10)
        return '('.substr($registro,0,2).') '.substr($registro,2,4).'-'.substr($registro,6,4);
      else if (strlen($registro) == 11)
        return '('.substr($registro,0,2).') '.substr($registro,2,5).'-'.substr($registro,7,4);
      else
        return $registro;
    }
    
    function acrescentaZeros ($var_str,$tamanho) {
      $var_str = substr($var_str,0,$tamanho);
      $tam = $tamanho - strlen($var_str);
      
      for ($i = 0;$i < $tam;$i++) {
        $var_str = '0'.$var_str;  
      }
     
      return ($var_str);    
    } 
    
    function acrescentaBrancos ($var_str,$tamanho,$lado="D",$caracter="&nbsp;") {
      $var_str = substr($var_str,0,$tamanho);
      $tam = $tamanho - strlen($var_str);
      
      for ($i = 0;$i < $tam;$i++) {
        if ($lado == "E")
          $var_str = $caracter.$var_str;
        else
          $var_str = $var_str.$caracter;    
      }

      return ($var_str);    
    }    

    function formataCEP($cep) {
      $cep = $this->somenteNumeros($cep);
      
      if (strlen($cep) > 0) 
        $cep = substr($cep,0,5).'-'.substr($cep,5,3);
      return $cep;
    }
    
    function formataCPF($cpf) {
      if (strlen($cpf) == 11) 
        $cpf = substr($cpf,0,3).'.'.substr($cpf,3,3).'.'.substr($cpf,6,3).'-'.substr($cpf,9,2);
      return $cpf;
    }

    function formataCNPJ($cnpj) {
      if (strlen($cnpj) == 14) 
        $cnpj = substr($cnpj,0,2).'.'.substr($cnpj,2,3).'.'.substr($cnpj,5,3).'/'.substr($cnpj,8,4).'-'.substr($cnpj,12,2);
      return $cnpj;
    } 

    function formataCEI($cei) {
      if (strlen($cei) == 12) 
        $cei = substr($cei,0,2).'.'.substr($cei,2,3).'.'.substr($cei,5,5).'-'.substr($cei,10,2);
      return $cei;
    }    

    function formataCNS($cns) {
      $cns = $this->somenteNumeros($cns);
      
      if (strlen($cns) > 0) 
        $cns = substr($cns,0,3).' '.substr($cns,3,4).' '.substr($cns,7,4).' '.substr($cns,11,4);
        
      return $cns;
    }
    
   function formataNumero($variavel, $casas_decimais = 2) {
      //a variável $casas_decimais foi incluída para utilização no mat/med onde preciso de 3 caasas
      //$variavel = str_replace('.','',$variavel);
      $variavel = (double) str_replace(',','.',$variavel);
      return number_format($variavel,$casas_decimais,',','.');
    } 
	
	
    function removeCaracteresInvalidos($bd,$texto) {
      $sql = new Query($bd);
      $txt = "SELECT REMOVECARACTERESINVALIDOS(:Texto) VALIDADO FROM DUAL";
      $sql->addParam(":Texto",$texto); 
      $sql->executeQuery($txt);
      
      return $sql->result("VALIDADO");
    }
    
    function somenteNumeros($variavel) {
      return preg_replace("/[^0-9]/", "", $variavel);
    }     
    
    function somenteCaracteres ($variavel,$caracteres = 'ABCDEFGHIJKLMNOPQRSTUVXYWZ ') {
      $indice = 0;
      $retorno = "";
      while ($indice < strlen($variavel)) {
        if (!substr_count($caracteres,substr($variavel,$indice,1)) == 0)
          $retorno = $retorno.substr($variavel,$indice,1);
          
        $indice = $indice + 1;
      }
      
      return $retorno;
    } 
	
	function valorextensonumeros($valor) {
     if($valor == 1){
		 $retorno = 'uma';
	 }else if($valor == 2){
		 $retorno = 'duas';
	 }else if($valor == 3){
		 $retorno = 'três';
	 }else if($valor == 4){
		 $retorno = 'quatro';
	 }else if($valor == 5){
		 $retorno = 'cinco';
	 }else if($valor == 6){
		 $retorno = 'seis';
	 }else if($valor == 7){
		 $retorno = 'sete';
	 }else if($valor == 8){
		 $retorno = 'oito';
	 }else if($valor == 9){
		 $retorno = 'nove';
	 }else if($valor == 10){
		 $retorno = 'dez';
	 }
	 return $retorno;
	 
    }


    function removeAcento($bd,$variavel,$html = true) {
      $sql = new Query($bd);
      $sql->clear();
      $txt = "SELECT REMOVE_ACENTO(:variavel) RESULTADO FROM DUAL";
	  if($html == true)
        $sql->addParam(":variavel",$variavel);
	  else
	    $sql->addParam2(":variavel",$variavel);
      $sql->executeQuery($txt);
      return $sql->result("RESULTADO");
    }   	  
    
    function nomeValidoArquivo ($variavel) {
      $indice = 0;
      $retorno = "";
      $caracteres = "*§/()#-\`|:ªº°~!@$%^&+=;?€[]{}ÇçÁÉÍÓÚÀÈÌÒÙÃÕ";
      while ($indice < strlen($variavel)) {
        if (!substr_count($caracteres,substr($variavel,$indice,1)) > 0)
          $retorno = $retorno.substr($variavel,$indice,1);
          
        $indice = $indice + 1;
      }
      
      return $retorno;
    }  

    function organizaString ($mensagem,&$instrucoes) {
      $contador = 0;
      $posicao = 0;
      while (strlen($mensagem) > 0) {
        $contador = $contador + 1;
        $posicao = strpos($mensagem,chr(13).chr(10));
        if ($posicao > 0) {
          if ($contador < 8)
          $instrucoes[$contador] = substr($mensagem,0,$posicao);
          $mensagem = substr($mensagem,$posicao+2,strlen($mensagem));
        } else {
          $instrucoes[$contador] = $mensagem;
          $mensagem = "";
        }
      }
      return ("");
    }    
    
    function formataRegistroAns ($registro) {
      if (strlen($registro) == 9)
        return substr($registro,0,5).'/'.substr($registro,5,3).'-'.substr($registro,8,1);
      else if (strlen($registro) == 6)
        return substr($registro,0,5)."-".substr($registro,5,1);
      else
        return $registro;
    }    
    
    function valorPorExtenso($valor=0, $maiusculas=True,$n = false) {
      if (strpos($valor,",") > 0){
        $valor = str_replace(".","",$valor);
        $valor = str_replace(",",".",$valor);
      }
      
      $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
      $plural   = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
      
      $c   = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
      $d   = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
      $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
      $u   = array("", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");

      $z = 0;

      $valor = number_format($valor, 2, ".", ".");
      $inteiro = explode(".", $valor);
      $cont = count($inteiro);
      
      for($i=0;$i<$cont;$i++)
        for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
          $inteiro[$i] = "0".$inteiro[$i];

      $fim = $cont - ($inteiro[$cont-1] > 0 ? 1 : 2);
      
      $rt = '';
      for ($i=0;$i<$cont;$i++) {
        $valor = $inteiro[$i];
        $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
        $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
        $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
        
        if($n == true){
          $singular = '';
          $plural = '';
        }
        $r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd && $ru) ? " e " : "").$ru;
        $t = $cont-1-$i;
        $r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
        
        if ($valor == "000")
          $z++; 
        elseif ($z > 0) 
          $z--;
        
        if (($t==1) && ($z>0) && ($inteiro[0] > 0)) 
          $r .= (($z>1) ? " de " : "").$plural[$t];

        if ($r) 
          $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
      }
      
      if(!$maiusculas) {
        return(ltrim($rt ? $rt : "zero"));
      } elseif($maiusculas == "2") {
        return (strtoupper($rt) ? strtoupper($rt) : "Zero");
      } else {
        return (ucwords($rt) ? ucwords($rt) : "Zero");
      }
    }    
    
    function formataValor($variavel) {
      $variavel = str_replace(',','.',$variavel);
      return $variavel;
    }    
    
    function quebrarLinhas($texto,$tamanho) {
      
      if ($tamanho > 0) {
        $texto2 = '';
        $linha = '';

        while (strlen($texto) > 0) {
      
          if (strlen($texto) > $tamanho) {
            $posicao = strpos($texto,' ');
            $pedaco = substr($texto,0,$posicao+1);
            $texto = substr($texto,$posicao+1,strlen($texto));

            if (strlen(strip_tags($linha.$pedaco,'<p>')) <= $tamanho) {
              $linha .= $pedaco;
            } else {
              $texto2 .= $linha."<br />";         
              $linha = $pedaco;
            }
          } else {              
            if (strlen(strip_tags($linha.$texto,'<p>')) <= $tamanho) {
              $texto2 .= $linha.$texto;
            } else {
              $texto2 .= $linha."<br />".$texto;
            }

            $texto = '';
          }
        }
      }

      return $texto2;
    }    
    function moeda($get_valor) {
      $source = array('.', ',');
      $replace = array('', ',');
      $valor = str_replace($source, $replace, $get_valor); 
      return $valor; 
    }  

    function formataCPFCNPJ($campo){
     if (strlen($campo) == 11) 
       $campo = substr($campo,0,3).'.'.substr($campo,3,3).'.'.substr($campo,6,3).'-'.substr($campo,9,2);
       else
         $campo = substr($campo,0,2).'.'.substr($campo,2,3).'.'.substr($campo,5,3).'/'.substr($campo,8,4).'-'.substr($campo,12,2);
     
     return $campo;
    }
  }
?>