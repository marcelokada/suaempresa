<?php 
  class Query {
    private $connect;    
    private $binds = array();    
    private $results;
    private $count;
    private $start = 0;
    private $pos   = 0;
    private $end   = 0;
    private $close;
    private $bd;
    
    public function __construct($bd="") {
    
      if ($bd == '') {
        $bd = new Database();
        $this->close = true;        
      } else
        $this->close = false;

      $this->bd      = $bd;
      $this->connect = $this->bd->conn();        
    }
        
    public function executeQuery($sql) {
    
      try {       
        $stmt = $this->connect->prepare($sql); 
        
        for ($b = 0;$b < sizeof($this->binds);$b++) {
          $stmt->bindParam($this->binds[$b][0],$this->binds[$b][1]);
        }
                
        if ($stmt->execute()) {
          $r    = $stmt->fetchAll(PDO::FETCH_ASSOC);          

          $contador = 0;
          for ($i = 0;$i < sizeOf($r);$i++) {
            $contador++;
            foreach ($r[$i] as $key => $value) {
              $this->results[strtoupper($key)][] = $value;
            }
          }
        
          $this->count   = $contador;
          $this->pos     = 0;
          $this->end     = $this->count;        
        } else {
          $this->count   = 0;      
        }  
      } catch (PDOException $erro) {       
        echo "\n<pre >";
        echo "Function executeQuery\n\n";
        echo "Erro: ".htmlentities($e->getMessage())."\n\n";         
        echo "SQL: \"" . htmlentities($sql) . "\"\n"; 
        echo "Parâmetros: \n";
        $this->printParams();
        echo "</pre>";
        exit;
      }

      if ($this->close)
        $this->bd->close();                
    }

    public function executeSQL($sql) {
      try {
        $stmt = $this->connect->prepare($sql); 
        
        for ($b = 0;$b < sizeof($this->binds);$b++) {
          $stmt->bindParam($this->binds[$b][0],$this->binds[$b][1]);
        }
        
        if (!$stmt->execute()) {
          $arr = $stmt->errorInfo();
          print_r($arr);
          exit;          
        }
        else if ($this->connect->lastInsertId() > 0)
          return $this->connect->lastInsertId();             
      
      } catch (PDOException $erro) {       
        echo "\n<pre >";
        echo "Function executeSQL\n\n";
        echo "Erro: ".htmlentities($e->getMessage())."\n\n";         
        echo "SQL: \"" . htmlentities($sql) . "\"\n"; 
        echo "Parâmetros: \n";
        $this->printParams();
        echo "</pre>";
		gravaErro("Query.class.php",$erroQuery);
		exit;
      }           
    }
    
   public function getReturn ($bind) {
      $indice = -1;
      
      for ($b = 0;$b < sizeof($this->binds);$b++) {
        if ($this->binds[$b][0] == $bind) {
          $indice = $b;        
        }
      }

      return $this->binds[$indice][1];
    }  

    public function printParams ($return=false) {     
      $retorno = '';
      
      for ($b = 0;$b < sizeof($this->binds);$b++) {
        if ($return)
          $retorno .= $this->binds[$b][0]." = ".$this->binds[$b][1].chr(13).chr(10);
        else
          echo $this->binds[$b][0]." = ".$this->binds[$b][1]."<br/>";
      }
      
      if ($return)
        return $retorno;
      
    } 
    
    public function getParams () {     
      return $this->binds;
    }      
    
    public function setParams ($arr) {     
      $this->binds = $arr;
    }     

    public function addParam($bind,$value,$return=-1) { 
      array_push($this->binds,array($bind,trim($value),$return));
    }
    
    public function removeParam($bind) { 
      $temp = array();
      
      for ($b = 0;$b < sizeof($this->binds);$b++) {
        if ($this->binds[$b][0] !== $bind)
          $temp[] = $this->binds[$b];
      }
      
      $this->binds = $temp;      
    }
    
    public function eof() {
      if ($this->pos == $this->count)
        $value = true;
      else
        $value = false;   
      
      return $value;
    }
    
    public function next() {
      if ($this->pos < $this->count)    
        $this->pos = $this->pos + 1;  
    }
    
    public function result($column) {                 
      if ($this->results <> null && sizeOf($this->results) > 0) {           
        if (array_key_exists($column, $this->results)) {
          if (array_key_exists($this->pos, $this->results[$column]))
            return $this->results[$column][$this->pos];
          else
            return "";
        }
        else {
          echo "\n<pre >";
          echo "Function result\n\n";
          echo "Erro: A coluna ".$column." não existe.\n\n";         
          echo "</pre>";
          exit;
        } 
      }
      else
        return "";
    }
    
    public function count() {
      return $this->count;
    }
    
    public function first() {
      $this->pos = 0;
    }

    public function end() {
      $this->pos = $this->count-1;
    }
    
    public function clear() {
      $this->count = 0;    
      $this->binds = array();
    }  


    
  }
?>  