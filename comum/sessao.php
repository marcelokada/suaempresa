<?php 
  if (isset($_SESSION['uf_operadora'])) {
    if (($_SESSION['uf_operadora'] == 'RR') or
        ($_SESSION['uf_operadora'] == 'AM') or
        ($_SESSION['uf_operadora'] == 'RO') or
        ($_SESSION['uf_operadora'] == 'MT') or
        ($_SESSION['uf_operadora'] == 'MS')) 
      date_default_timezone_set('America/Manaus');
    else if ($_SESSION['uf_operadora'] == 'AC')
      date_default_timezone_set('America/Mexico_City');
    else
      date_default_timezone_set('America/Sao_Paulo');
  }

  $diretorio = 'comum';
  if (isset($_SESSION['diretorio'])){
    $diretorio =  $_SESSION['diretorio']; 
  }  
  
  if(isset($_GET['idSessao'])){     
    if (isset($_SESSION['idSessao'])) {
      if (($_GET['idSessao'] <> $_SESSION['idSessao']) or ($_SESSION['idSessao'] == '') ) {
        if (!headers_sent()) {
          if ($_SESSION['diretorio'] <> '')
            header('Location: '.$_SESSION['diretorio'].'/index.php?erro=-1');
          else
            header('Location: ../index.html');
        }
        else {
          $seg->validaSessao(0,$diretorio,'Área restrita','É preciso realizar o login novamente para continuar');
        }      
      }
    }else{
      $seg->validaSessao(0,$diretorio,'Área restrita','É preciso realizar o login novamente para continuar');
    }
  }else{
    $seg->validaSessao(0,$diretorio,'Área restrita','É preciso realizar o login novamente para continuar');
  }
  
  
?>