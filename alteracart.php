<?php
  require_once("comum/autoload.php");
  session_start();
  error_reporting(0);
  
  $bd      = new Database();
  $formata = new Formata();
  $seg     = new Seguranca();
  
  $link = $seg->antiInjection($_POST['link']);
  
  $tipo_op = $seg->antiInjection($_POST['tipo']);
  $idcart  = $seg->antiInjection($_POST['idcart']);
  $idloja  = $seg->antiInjection($_POST['idloja']);
  $idprod  = $seg->antiInjection($_POST['idprod']);
  
  $sql2 = new Query ($bd);
  $txt2 = "SELECT VCASHPRODU,VVALOPRODU
      FROM TREDE_PRODUTOS
      WHERE NSEQUPRODU = :idprod
        AND SEQUENCIACRE = :idloja";
  $sql2->addParam(':idloja',$idloja);
  $sql2->addParam(':idprod',$idprod);
  $sql2->executeQuery($txt2);
  
  $valor_prod = $sql2->result("VVALOPRODU");
  
  if (TIPO_PORC_PRODUTO == 'prod') {
    //% pro classe produto
    $porcash = $sql2->result("VCASHPRODU");
    
    //% pro classe produto
  } else if (TIPO_PORC_PRODUTO == 'cred') {
    
    //% pro classe cred
    $sql2c = new Query ($bd);
    $txt2c = "SELECT SEQUENCIACRE,
               CLASSIFICCRE
             FROM TREDE_CREDENCIADOS
              WHERE SEQUENCIACRE = :idloja";
    $sql2c->addParam(':idloja',$idloja);
    $sql2c->executeQuery($txt2c);
    
    $class = $sql2c->result("CLASSIFICCRE");
    
    $sql2cc = new Query ($bd);
    $txt2cc = "SELECT NNUMECLASS,CASHBCLASS
             FROM TREDE_CLASSREDE
              WHERE NNUMECLASS = :class";
    $sql2cc->addParam(':class',$class);
    $sql2cc->executeQuery($txt2cc);
    
    $porcash = $sql2cc->result("CASHBCLASS");
    //% pro classe cred
  }
  
  $valor_cash = $valor_prod * $porcash / 100;
  
  $sql1 = new Query ($bd);
  $txt1 = "SELECT NVALORCARR, NQUATICARR, VVACASCARR,NVVALOCARR
        FROM TREDE_CARRINHO
       WHERE SEQUENCIACRE = :idloja
        AND VIDCARCARR = :idcart
        AND NSEQUPRODU = :idprod";
  $sql1->addParam(':idloja',$idloja);
  $sql1->addParam(':idcart',$idcart);
  $sql1->addParam(':idprod',$idprod);
  $sql1->executeQuery($txt1);
  
  $valor_carr = $sql1->result("NVVALOCARR");
  $cash_carr  = $sql1->result("VVACASCARR");
  $qtde_carr  = $sql1->result("NQUATICARR");
  
  if ($tipo_op == '1') {
    $qtde = $qtde_carr + 1;
    $valortotalcash = $cash_carr + $valor_cash;
    $valortotalcash = $formata->formataNumero($valortotalcash);
    $valortotalcash = str_replace('.','',$valortotalcash);
    $valortotalcash = str_replace(',','.',$valortotalcash);
  
    $valor_total = $valor_prod + $valor_carr;
    $valor_total = $formata->formataNumero($valor_total);
    $valor_total = str_replace('.','',$valor_total);
    $valor_total = str_replace(',','.',$valor_total);
    
  } else {
    
    $qtde = $qtde_carr - 1;
    $valortotalcash = $cash_carr - $valor_cash;
    $valortotalcash = $formata->formataNumero($valortotalcash);
    $valortotalcash = str_replace('.','',$valortotalcash);
    $valortotalcash = str_replace(',','.',$valortotalcash);
  
    $valor_total = $valor_carr - $valor_prod;
    $valor_total = $formata->formataNumero($valor_total);
    $valor_total = str_replace('.','',$valor_total);
    $valor_total = str_replace(',','.',$valor_total);
  }
  
 
  
  
  if ($link == 1) {
    
    $sql = new Query ($bd);
    $txt = "UPDATE TREDE_CARRINHO SET NQUATICARR = '".$qtde."',
                    NVVALOCARR = '".$valor_total."',
                    VVACASCARR = '".$valortotalcash."'
       WHERE SEQUENCIACRE = :idloja
        AND VIDCARCARR = :idcart
        AND NSEQUPRODU = :idprod";
    $sql->addParam(':idloja',$idloja);
    $sql->addParam(':idcart',$idcart);
    $sql->addParam(':idprod',$idprod);
    $sql->executeSQL($txt);
    
    $eventos['id_cart'] = $idcart;
    //echo "Quantidade alterado com sucesso.";
    //echo  $idcart.' - '.$idloja.' - '.$idprod.' - '.$qtde.' - '.$valor.' - '. $valor_cash;
    
    $sql12 = new Query ($bd);
    $txt12 = "SELECT NVVALOCARR, VVACASCARR
        FROM TREDE_CARRINHO
       WHERE SEQUENCIACRE = :idloja
        AND VIDCARCARR = :idcart
        AND NSEQUPRODU = :idprod";
    $sql12->addParam(':idloja',$idloja);
    $sql12->addParam(':idcart',$idcart);
    $sql12->addParam(':idprod',$idprod);
    $sql12->executeQuery($txt12);
    
    $eventos['valor_total'] = number_format($sql12->result("NVVALOCARR"),2,',','.');
    $eventos['valor_cash']  = number_format($sql12->result("VVACASCARR"),2,',','.');
    
    $sql13 = new Query ($bd);
    $txt13 = "SELECT SUM(NVVALOCARR) VALOR_CAR, SUM(VVACASCARR) VALOR_CAR_CASH
        FROM TREDE_CARRINHO
       WHERE SEQUENCIACRE = :idloja
        AND VIDCARCARR = :idcart";
    $sql13->addParam(':idloja',$idloja);
    $sql13->addParam(':idcart',$idcart);
    $sql13->executeQuery($txt13);
    
    $eventos['valor_total_car']  = number_format($sql13->result("VALOR_CAR"),2,',','.');
    $eventos['valor_total_cash'] = number_format($sql13->result("VALOR_CAR_CASH"),2,',','.');
    
    //echo json_encode($eventos);
    
  } else if ($link == 2) {
    $sql12 = new Query ($bd);
    $txt12 = "SELECT NQUATICARR,
                     NVVALOCARR,
                     NVVALOCARR,
                     VVACASCARR
        FROM TREDE_CARRINHO
       WHERE SEQUENCIACRE = :idloja
        AND VIDCARCARR = :idcart
        AND NSEQUPRODU = :idprod";
    $sql12->addParam(':idloja',$idloja);
    $sql12->addParam(':idcart',$idcart);
    $sql12->addParam(':idprod',$idprod);
    $sql12->executeQuery($txt12);
    
    $atual_qtde  = $sql12->result("NQUATICARR");
    $atual_valor = $sql12->result("NVVALOCARR");
    $atual_cash  = $sql12->result("VVACASCARR");
    
    if ($tipo_op == '1') {
      $valortotalcash1 = $atual_cash + $valor_cash;
      $valortotal1     = $atual_valor + $valor;
      $qtde_total1     = $atual_qtde + $qtde;
      
    } else {
      $valortotalcash1 = $atual_cash - $valor_cash;
      $valortotal1     = $atual_valor - $valor;
      $qtde_total1     = $atual_qtde - $qtde;
    }
    
    $sql = new Query ($bd);
    $txt = "UPDATE TREDE_CARRINHO SET NQUATICARR = '".$qtde_total1."',
                    NVVALOCARR = '".$valortotal1."',
                    VVACASCARR = '".$valortotalcash1."'
       WHERE SEQUENCIACRE = :idloja
        AND VIDCARCARR = :idcart
        AND NSEQUPRODU = :idprod";
    $sql->addParam(':idloja',$idloja);
    $sql->addParam(':idcart',$idcart);
    $sql->addParam(':idprod',$idprod);
    $sql->executeSQL($txt);
    
  }
  
  echo json_encode($eventos);
  
  $bd->close();
?>