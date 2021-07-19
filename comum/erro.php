<?php
  require_once('../comum/autoload.php');
  $seg->secureSessionStart();

  if (isset($_SESSION['idSessao']))
    unset($_SESSION['idSessao']);
  
  require_once("../comum/layout.php");   
  
  $tpl->addFile("CONTEUDO","../comum/erro.html");     
    
  $erro   = '';
  $codigo = '';
  $pagina = '';
        
  if (isset($_GET['codigo']))
    $codigo = $seg->antiInjection($_GET['codigo']);
    
  if (isset($_GET['pagina']))
    $pagina = $seg->antiInjection($_GET['pagina']);

  if ($pagina <> '')
    $erro = $pagina."<br>";
    
  if ($codigo <> '')
    $erro .= "Código do erro: ".$codigo;
    
  if ($erro <> '') {
    $tpl->ERRO = $erro;
    $tpl->block("MOSTRA_ERRO");    
  }       
  
  if (isset($_SESSION['diretorio']) and ($_SESSION['diretorio'] <> ''))
    $tpl->ENDERECO = $_SESSION['diretorio']."/index.php";
  else
    $tpl->ENDERECO = "../index.html";
  
  $tpl->show();  
?>