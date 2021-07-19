<?php
  $seq = $_SESSION['idUsuario'];
  
  //CASHBACK USUARIO
  $valortotal_cash = $func->RetornaValorCashBackUsuario($bd,$seq);
  $tpl->MEUCASH = $formata->formataNumero($valortotal_cash);
  
  $valortotal_bonus = $func->RetornaValorBonusUsuario($seq);
  $tpl->MEUBONUS = $formata->formataNumero($valortotal_bonus);
  
  $valortotal_voucher = $func->RetornaValorVoucherUsuario($seq);
  $tpl->MEUVOUCHER = $formata->formataNumero($valortotal_voucher);
  
  $tpl->MEUPLANO = $func->assinaturaUsuarioMimo($bd,$seq);
  $tpl->block("MEUPLANO");
  //CASHBACK USUARIO
  ?>

<div class="container float-right">
  <!-- BEGIN MEUPLANO -->
  <a href="index.html"><i>{MEUPLANO}</i></a><br>
  <!-- END MEUPLANO -->
  <hr>
  <i>Meu Cashback: <b>R$ {MEUCASH}</b></i><br>
  <i>Meu Voucher: <b>R$ {MEUVOUCHER}</b></i><br>
  <i>Meu Bônus Unilevel: <b>R$ {MEUBONUS}</b></i>
</div>