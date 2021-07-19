<?php
  class Seguranca {

    private $iduser;
    private $idperf;
    
	
	function registraLogin ($bd,$operador) {
      $sql = new Query($bd);
      $txt = "INSERT INTO TREDE_SEGTENTA(CLOGITENTSEG,DDATATENTSEG,NNUMETENTSEG,VHORATENTSEG ) 
	  VALUES 
	  (:login,:datas,'1',:hora)";
      $sql->addParam(":login",$operador);
      $sql->addParam(":datas",date('Y-m-d'));
      $sql->addParam(":hora",date('H:i:s'));
      $sql->executeSQL($txt);
    }

      public function verificaSession($url_session){
          if (!headers_sent()){
              if($url_session == null){
                  $pagina = 'comum/restrito.php';
                  header("Location: $pagina");
                  unset($_SESSION['idSessao']);
              }
          }
      }
    
    private function dadosModulo () {
      
      if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
      else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
      else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
      else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
      else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
      else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
      else
        $ipaddress = 'UNKNOWN';
      $ipaddress  = substr($ipaddress, 0,12); 
      $user_agent = getenv("HTTP_USER_AGENT");  
      $chrome     = strpos($user_agent,'Chrome');
      $firefox    = strpos($user_agent,'Firefox');
      $ie         = strpos($user_agent,'MSIE');
      $ie2        = strpos($user_agent,'WOW64');
      $safari     = strpos($user_agent,'Safari');
      
      if ($chrome > 0) {
        $navegador = substr($user_agent,$chrome); 
          
        if (strpos($navegador,' ') == true)
          $fim = strpos($navegador,' ');
        else
          $fim = strlen($navegador);
                
        $navegador = substr($navegador,0,$fim); 
      }
      else if ($firefox > 0) {
        $navegador = substr($user_agent,$firefox); 
      }
      else if ($ie > 0) {
        $navegador = substr($user_agent,$ie); 
          
        if (strpos($navegador,';') == true)
          $fim = strpos($navegador,';');
        else
          $fim = strlen($navegador);
                
        $navegador = substr($navegador,0,$fim); 
        $navegador = str_replace('MSIE','Internet Explorer ',$navegador);    
      }
      else if ($ie2 > 0) {
        $inicio    = strpos($user_agent,'rv:');

        if ($inicio > 0) {              
          $navegador = substr($user_agent,$inicio); 
          
          if (strpos($navegador,')') == true)
            $fim = strpos($navegador,')');
          else
            $fim = strlen($navegador);
                
          $navegador = substr($navegador,0,$fim); 
          $navegador = str_replace('rv:','Internet Explorer ',$navegador);   
        }
      }
      else if ($safari > 0) {
        $inicio    = strpos($user_agent,'Version');
        
        if ($inicio > 0) {
          $navegador = substr($user_agent,$inicio); 
          
          if (strpos($navegador,' ') == true)
            $fim = strpos($navegador,' ');
          else
            $fim = strlen($navegador);
                  
          $navegador = substr($navegador,0,$fim);
        
          $navegador = str_replace('Version','Safari',$navegador);
        }
        else {
          $inicio    = strpos($user_agent,'Safari');
          $navegador = substr($user_agent,$inicio); 
        }
      }
      else
        $navegador = $user_agent;      
      
      $dadosModulo = array();
      $dadosModulo['modulo']     =  $modulo;
      $dadosModulo['ipaddress']  =  $ipaddress;
      $dadosModulo['navegador']  =  $navegador;
   
      return $dadosModulo;          
    }
    
    public function setIdUser($iduser){
     $this->iduser = $iduser;
    }
    public function getIdUser(){
     return $this->iduser;
    }
    public function setIdPerf($idperf){
     $this->idperf = $idperf;
    }
    public function getIdPerf(){
     return $this->idperf;
    }

    
   
		public function antiInjection($sql,$html = true){    
      // remove palavras que contenham sintaxe sql
      $sql = preg_replace(strtolower("/(from|select|insert|delete|where|drop table|show tables|\|--|\\\\)/"),"",$sql);
      
      if (!is_array($sql)) {
        $sql = trim($sql);//limpa espaços vazio
        $sql = strip_tags($sql);//tira tags html e php
        $sql = addslashes($sql);//Adiciona barras invertidas a uma string
              
	     // if($html == true)
         // $sql = htmlentities($sql,ENT_NOQUOTES,'ISO-8859-1',false);
      }
        
      return $sql;
	  }
    
      function secureSessionStart() {
      
      if ( php_sapi_name() !== 'cli' ) {
        if ( version_compare(phpversion(), '5.4.0', '>=') )
          $startSession = session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        else
          $startSession = session_id() === '' ? FALSE : TRUE;
      }
    
      if ($startSession === FALSE )
        session_start();
	} 

    function geraSenha($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false) {
      $lmin = 'abcdefghijklmnopqrstuvwxyz';
      $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $num  = '1234567890';
      $simb = '!@#$%*-';
      $retorno    = '';
      $caracteres = '';
      
      $caracteres .= $lmin;
      if ($maiusculas) $caracteres .= $lmai;
      if ($numeros) $caracteres .= $num;
      if ($simbolos) $caracteres .= $simb;
      
      $len = strlen($caracteres);
      for ($n = 1; $n <= $tamanho; $n++) {
        $rand = mt_rand(1, $len);
        $retorno .= $caracteres[$rand-1];
      }
      return $retorno;
    }
    
   
  }
?>