<?php
  class Util {
  
    function abreArquivo ($url,$largura=500,$altura=100) {
       
      $txt = '';
      $txt .= '<script type="text/javascript">      
      
            var tam1 = (window.screen.availWidth / 2) - ('.$largura.' / 2) - (window.screen.availWidth * 0.01);
            var tam2 = (window.screen.availHeight / 2) - ('.$altura.'/ 2) - (window.screen.availHeight * 0.062);

            var win = open("'.$url.'","janela","top="+tam2+",left="+tam1+",width='.$largura.',height='.$altura.',status=yes,scrollbars=yes,resizable=yes");
            win.focus();
            ';
        
      $txt .= '</script>';
      
      return $txt;      
    }
    
    function redireciona ($url,$novapagina="N",$paginaatual="",$forma="0") {
       
      $txt = '';
      if ($novapagina == "S") {      
        $txt .= '<script type="text/javascript">      
        
              var tam1 = (window.screen.availWidth / 2) - (500 / 2) - (window.screen.availWidth * 0.01);
              var tam2 = (window.screen.availHeight / 2) - (600 / 2) - (window.screen.availHeight * 0.062);

              var win = open("'.$url.'","janela","top="+tam2+",left="+tam1+",width=500,height=600,status=yes,scrollbars=yes,resizable=yes");';              
                
        if ($paginaatual <> '')
          $txt .= ' window.location.href="'.$paginaatual.'";';
          
        $txt .= '</script>';
      } 
      else {
          $txt .= '<script type="text/javascript">
                  window.location.href="'.$url.'";
                </script>
                <noscript>
                  <meta http-equiv="refresh" content="0;url='.$url.'" />
                </noscript> ';
      }
      
      if ($forma == '1')
        return $txt;
      else
        echo $txt;
      
    }
    
    function redireciona2 ($url,$parametros = array(),$novapagina="N",$paginaatual="") {
      
      $txt = '';      
      $txt .= '<form id="formSubmit" name="formSubmit" method="post" action="" target="">';
      
      foreach ($parametros as $p) {
        $p1 = array();
        $p1 = $p;
        
        $txt .= '<input type="hidden" id="'.$p1['nome'].'" name="'.$p1['nome'].'" value="'.$p1['valor'].'">';
      }
              
      $txt .= '</form>';
  
      $txt .= '<script type="text/javascript" src="../comum/js/jquery-1.8.2.min.js"></script>
               <script type="text/javascript">';
            
      if ($novapagina == "S") {
        $titulo = "janela";
        
        $txt .= '
        
              var tam1 = (window.screen.availWidth / 2) - (500 / 2) - (window.screen.availWidth * 0.01);
              var tam2 = (window.screen.availHeight / 2) - (600 / 2) - (window.screen.availHeight * 0.062);

              var win = open("'.$url.'","'.$titulo.'","top="+tam2+",left="+tam1+",width=600,height=500,status=yes,scrollbars=yes,resizable=yes");';
      }
      else
        $titulo = "";
   /*  
      foreach ($parametros as $p) {
        $p1 = array();
        $p1 = $p;
        
        $txt .= '$("#'.$p1['nome'].'").val('.$p1['valor'].');';
      }
      */
      
      $txt .= '     
              $("#formSubmit").attr("target","'.$titulo.'");
              $("#formSubmit").attr("action","'.$url.'");
              $("#formSubmit").submit();
              $("#formSubmit").hide();
          
            </script>';
            
      return $txt;
    }          
    
    
    function insereLogs ($bd,$tabela,$chave,$campo_chave,$operacao,$valor_posterior,$valor_anterior) {
      
      /* 
        Operacao
          1 = Inserção
          2 = Exclusão
          3 = Atualização
      */
      
      $sql = new Query($bd);
      $txt = "  BEGIN ".
             "    LOGS.INSERE_LOG(:tabela,:chave,:campo_chave,:operacao,:valor_posterior,:valor_anterior); ".
             "  END; ";
             
      $sql->addParam(":tabela",$tabela);  
      $sql->addParam(":chave",$chave); 
      $sql->addParam(":campo_chave",$campo_chave);                                                 
      $sql->addParam(":operacao",$operacao);      
      $sql->addParam(":valor_posterior",$valor_posterior);  
      $sql->addParam(":valor_anterior",$valor_anterior); 
      $retorno = $sql->executeSQL($txt);    

      return $retorno;
    }       
    
    function criaDiretorio($nome) {  
      if (!file_exists($nome)) {
        $oldmask = umask(0);        
        mkdir($nome,0777,true);
        umask($oldmask);
        chdir($nome);
        $nome = getcwd();
      } else {
        chdir($nome);  
        $nome = getcwd();    
      }
      return $nome;
    }    

    function excluiArquivo($dir,$arquivo) {
      $dir .= '/*';
      $contador = 1;

      foreach (glob($dir,GLOB_NOCHECK ) as $filename) { 

        if (is_dir($filename) <> 1) {
          if (basename($filename) == $arquivo) {
            unlink($filename);
            return 1;
          }
        }
        else {
          $this->excluiArquivo($filename,$arquivo);
        }
      }               
    }
    
    function corrigeNomeArquivo($arquivo) {
    
      $arquivo = strtolower($arquivo);
      $alterar = array( "á" => "Á","à" => "À","ã" => "Ã","â" => "Â","é" => "É","ê" => "Ê","í" => "Í","ó" => "Ó","ô" => "Ô","õ" => "Õ", 
                        "ú" => "Ú","ü" => "Ü","ç" => "Ç","Á" => "á","À" => "à","Ã" => "ã","Â" => "â","É" => "é","Ê" => "ê","Í" => "í", 
                        "Ó" => "ó","Ô" => "ô","Õ" => "õ","Ú" => "ú","Ü" => "ü","Ç" => "ç","°" => "_","º" => "_","ª" => "_","°" => "_", 
                        "§" => "_","'" => "_","š" => "_","!" => "_","?" => "_","¨" => "_"," " => " ");
      $arquivo = strtr($arquivo, $alterar);
          
      return $arquivo;
    }  

    function validaExtensaoArquivo($arquivo,$extensoes=array()) {
      $extensao  = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
      
      if (sizeOf($extensoes) > 0){
        $extensoesPermitidas = $extensoes;
      }
      else{
        $extensoesPermitidas = array("jpg","jpeg","png","gif");
      }
      
      if (in_array($extensao,$extensoesPermitidas) == false)
        return utf8_decode('- Não é permitido o envio de arquivos com extensao ".' . $extensao . '"<br>');
      else
        return '';
    }    
        
    function modulo11 ($numero) {
      $soma = 0;
      $fator = 1;

      for ($i = strlen($numero)-1;$i >= 0;$i--) {
        $fator++;
        if ($fator == 10)
          $fator = 2;
        $soma += $numero[$i] * $fator;
      }

      $resto = $soma % 11;
      $resto = 11 - $resto;
      if ($resto < 10)
        return $resto;
      else
        return 0;
    } 

    function nomeValido ($nome) {
      $formata = new Formata();
      
      if ($formata->somenteCaracteres($nome) <> $nome)
        return false;
      else if (strlen($formata->somenteCaracteres($nome)) < 6)
        return false;
      else if (substr_count($nome,' ') == 0)
        return false;
      else
        return true;

    }  

    function caracteresInvalidos ($variavel) {
      $indice = 0;
      $retorno = "";
      $caracteres = "*§./()_#-\`|:ªº°~!@$%^&+=;?€[]{}ÇçÁÉÍÓÚÀÈÌÒÙÃÕ";
      while ($indice < strlen($variavel)) {
        if (substr_count($caracteres,substr($variavel,$indice,1)) > 0)
          $retorno = $retorno.substr($variavel,$indice,1);
          
        $indice = $indice + 1;
      }
      
      return $retorno;
    }

    function nomeAbreviado($nome){       
      $TOKENS_INICIAIS_VALIDOS = array("D", "I", "O", "U", "Y");
      $TOKENS_FINAIS_VALIDOS = array("I", "O", "U", "Y");
      $TOKENS_INTERMEDIARIOS_VALIDOS = array("E", "Y");
      
      $nome = preg_replace('/\s+/', " ",$nome);
      $resultado = explode(" ", $nome);
      $num = count($resultado);
      
      for ($i = 0; $i < $num; $i++) {
        if (strlen($resultado[$i]) == 1){
          if (($i == 0) and (array_search($TOKENS_INICIAIS_VALIDOS, $resultado[$i]) < 0))
            return true;
          elseif (($i == $num - 1) and (array_search($TOKENS_FINAIS_VALIDOS, $resultado[$i]) < 0))
            return true;
          elseif ($i == 1 || (($i > 1 and $i < $num - 1) and !((array_search(TOKENS_INTERMEDIARIOS_VALIDOS,$resultado[$i]) >= 0))))
            return true;
          else
            return false;
        }
      }
    }
     
    function validarCampo($bd,$nome,$valor,$validacoes = array()) {
      
      $formata = new Formata();
      $datas   = new Data();
      $func    = new Funcao();
      
      $retorno = '';
      
      foreach ($validacoes as $regra) {      

        if ((substr($regra,0,11) == 'obrigatorio') and ($valor == '')) {
          $retorno .= $nome." é de preenchimento obrigatório<br>";
        }
        
        if (substr($regra,0,6) == 'minimo') {
          $tamanho = substr($regra,7);
          
          if (strlen($valor) < $tamanho)
            $retorno .= $nome." deve ter no mínimo ".$tamanho." caracteres<br>";          
        }
        
        if (substr($regra,0,6) == 'maximo') {
          $tamanho = substr($regra,7);
          
          if (strlen($valor) > $tamanho)
            $retorno .= $nome." deve ter no máximo ".$tamanho." caracteres<br>";          
        }
        
        if ($valor <> '') {
          if (substr($regra,0,6) == 'letras')
            if ($formata->somenteCaracteres(strToUpper($valor)) <> strToUpper($valor))
              $retorno .= $nome." deve conter somente letras (a-z) sem acentuação.<br>";    

          if (substr($regra,0,9) == 'abreviado')
            if ($this->nomeAbreviado($valor))
              $retorno .= $nome." não deve estar abreviado.<br>";    
              
          if (substr($regra,0,5) == 'email') 
            if (!$func->validaEmail($bd,$valor))
              $retorno .= $nome." informado não é válido (".$valor.")<br>";          

          if (substr($regra,0,3) == 'cpf')
            if (!$this->validaCPF($bd,$valor))
              $retorno .= $nome." informado não é válido (".$valor.")<br>";          

          if (substr($regra,0,3) == 'cns') 
            if (!$this->validaCNS($bd,$valor))
              $retorno .= $nome." informado não é válido (".$valor.")<br>";          
              
          if (substr($regra,0,3) == 'data') 
            if (!$datas->validaData($valor))
              $retorno .= $nome." informada não é válida (".$valor.")<br>";          
              
              
        }
      }
              
      return $retorno;
    } 
      
       
    function sdebug($var, $exit = false) { 
      echo "\n<pre>"; 
      if (is_array($var) || is_object($var)) { 
        echo htmlentities(print_r($var, true)); 
      } elseif (is_string($var)) { 
        echo "string(" . strlen($var) . ") \"" . htmlentities($var) . "\"\n"; 
      } else { 
        var_dump($var); 
      } 
      echo "</pre>"; 
      if ($exit) { 
        exit; 
      } 
    }
    
     
  }
?>