<?php     
$tpl = new Template("comum/padrao.html");
  
/* Menu */ 

$tpl->addFile("MENU_NAV","comum/menuNav_teste.html"); 


$tpl->ANO = date('Y');
?>