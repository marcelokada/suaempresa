<?php   
date_default_timezone_set("America/Sao_Paulo");
setlocale(LC_ALL, 'pt_BR');
header ('Content-type: text/html; charset=UTF-8');


  function autoload($classe) {

    if ($classe <> 'FPDF')
      include_once "classes/{$classe}.class.php";      
  }  

  spl_autoload_register('autoload');

  header( "Content-type: text/html; charset=utf-8" );

  $seg     = new Seguranca(); 
  $util    = new Util();  
  $data    = new Data();
  $formata = new Formata();
  $func    = new Funcao();
	
  function sdebug($var, $exit = false,$filename = '') { 
    
    if ($filename <> '') {
      $arquivo  = $filename."_".date('YmdHis');
      $ponteiro = fopen ("erros/debug_".$arquivo.".txt", "w");    

      $erro = '';      
      
      if (is_array($var) || is_object($var)) {
        $erro = print_r($var,true);          
      }
      else
        $erro = $var;
    
      fwrite($ponteiro,$erro); 
      fclose ($ponteiro);      
    }
    else {
      echo "\n<pre>"; 
      
      if (is_array($var) || is_object($var)) { 
        echo htmlentities(print_r($var,true),ENT_NOQUOTES,'ISO-8859-1',true); 
      } 
      elseif (is_string($var)) { 
        echo "string(" . strlen($var) . ") \"" . htmlentities($var,ENT_NOQUOTES,'ISO-8859-1',true) . "\"\n"; 
      } 
      else { 
        var_dump($var); 
      } 
      
      echo "</pre>"; 
    }
    
    if ($exit)
      exit; 
  } 
  
  function myIsSet(&$var) {
    if (isset($var))
      return $var;
    else
      return '';    
  }
    
  function gravaErro($pagina,$erro) {    
  
    $arquivo  = date('YmdHis');
    $ponteiro = fopen ("../erros/".$arquivo.".txt", "w");
    fwrite($ponteiro,$erro); 
    
    fwrite($ponteiro,'============ Trace ==============='.chr(13)); 
    ob_start();
    debug_print_backtrace();
   
    fwrite($ponteiro,ob_get_clean()); 
      
    fclose ($ponteiro); 
      
    if (DEBUG) {             
      echo "<script>window.open('erros/".$arquivo."');</script>";
    }
    else {
      echo "<script>window.location.href='../comum/erro.php?codigo=".$arquivo."&pagina=".$pagina."';</script>";
    }
  }
  
  
?>