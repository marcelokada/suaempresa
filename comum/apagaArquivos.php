<?php
  date_default_timezone_set('America/Sao_Paulo');

  // Apaga todos os arquivos contidos na pasta temp de 1 dia para tras
  $data_atual = mktime (date("H")-2, 0, 0, date("m"), date("d"), date("Y"));

  foreach (glob("../temp/*",GLOB_NOCHECK ) as $filename) { 
    if (file_exists($filename)) {  
      $data_arquivo = mktime (date('H',filemtime($filename)), date('i',filemtime($filename)), 0, date('m',filemtime($filename)),date('d',filemtime($filename)),date('Y',filemtime($filename)));
     
      if ($data_arquivo < $data_atual) {
        unlink($filename);
      }
    }
  }
  
  // Apaga todos os arquivos contidos na pasta erros de 1 dia para tras
  $data_atual = mktime (0, 0, 0, date("m"), date("d")-10, date("Y"));

  foreach (glob("../erros/*",GLOB_NOCHECK ) as $filename) { 
    if (file_exists($filename)) {  
      $data_arquivo = mktime (date('H',filemtime($filename)), date('i',filemtime($filename)), 0, date('m',filemtime($filename)),date('d',filemtime($filename)),date('Y',filemtime($filename)));
     
      if ($data_arquivo > $data_atual) {
        unlink($filename);
      }
    }
  }  
?>