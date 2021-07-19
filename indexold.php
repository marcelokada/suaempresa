<?php
require_once("comum/autoload_log.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");
error_reporting(0);

$bd = new Database();

require_once("comum/layout_log.php");
$tpl->addFile("CONTEUDO", "comum/index.html");

if (isset($_POST['acessar'])) {


    $email_login = $seg->antiInjection($_POST['login']);

    $sql_login = new Query($bd);
    $txt_login = "SELECT REDE_EMAILUS FROM TREDE_USUADMIN
					WHERE REDE_EMAILUS = :email";
    $sql_login->addParam(':email', $email_login);
    $sql_login->executeQuery($txt_login);

    $res_email_usua = $sql_login->result("REDE_EMAILUS");
    //sdebug($email_login);


    if ($res_email_usua == null) {
        $sql_login1 = new Query($bd);
        $txt_login1 = "SELECT VCNPJJURICRE FROM TREDE_CREDENCIADOS
					WHERE VLOGEMAILCRE = :email";
        $sql_login1->addParam(':email', $email_login);
        $sql_login1->executeQuery($txt_login1);

        $cnpj_login = $sql_login1->result("VCNPJJURICRE");
        $cpf_count = $sql_login1->result("VCNPJJURICRE");
        //sdebug('juri '.$cpf_count);

    } else {
        $sql_login = new Query($bd);
        $txt_login = "SELECT REDE_CPFUSUA FROM TREDE_USUADMIN
					WHERE REDE_EMAILUS = :email";
        $sql_login->addParam(':email', $email_login);
        $sql_login->executeQuery($txt_login);

        $cpf_count = $sql_login->result("REDE_CPFUSUA");
        $cpf_usua = $sql_login->result("REDE_CPFUSUA");
        //sdebug('usua '.$cpf_count);
    }


    //$cpf_count			= $seg->antiInjection($_POST['cpf']);
    $cpf_count = $func->retirarPontostracosundelinebarra($cpf_count);
    $cpf_count = strlen($cpf_count);


    if ($cpf_count == '11') {

        $cpf = $cpf_usua;
        $cpf = $func->retirarPontostracosundelinebarra($cpf);
        $senha = $seg->antiInjection($_POST['senha']);
        $senha = md5($senha);


        //CONTAR A QUANTIDADES DE TENTATIVAS
        $sql2 = new Query ($bd);
        $txt2 = "SELECT COUNT(*) TENTATIVAS 
				FROM TREDE_SEGTENTA 
				WHERE VNUMEROIPSEG = :ip 
					AND VNUMERCPFSEG = :cpf";
        $sql2->addPAram(':ip', getenv("REMOTE_ADDR"));
        $sql2->addPAram(':cpf', $cpf);
        $sql2->executeQuery($txt2);

        $tentativas = $sql2->result("TENTATIVAS") + 1;


        //SELECT PARA VERIFICAR A SENHA
        $sql = new Query ($bd);
        $txt = "SELECT REDE_SEQUSUA,REDE_NOMEUSU,REDE_CPFUSUA,REDE_ADMINUS,REDE_SENHAUS,REDE_TIPOUSU ,REDE_USUBLOC 
				FROM TREDE_USUADMIN 
				WHERE REDE_CPFUSUA = :cpf";
        $sql->addPAram(':cpf', $cpf);
        $sql->executeQuery($txt);

        $sequsuario = $sql->result("REDE_SEQUSUA");
        $res_login = $sql->result("REDE_CPFUSUA");
        $res_senha = $sql->result("REDE_SENHAUS");
        $admin = $sql->result("REDE_ADMINUS");
        $blok = $sql->result("REDE_USUBLOC");


        $res = strcmp($res_senha, $senha);

        $cpf_r = $func->retirarPontostracosundelinebarra($cpf);

        if ($tentativas >= 5) { //APÓS 5 TENTATIVAS O USUARIO É BLOQUADO

            //DAR O UPDATE E BLOQUEAR O USUARIO
            $sql4 = new Query ($bd);
            $txt4 = "UPDATE  TREDE_USUADMIN SET REDE_USUBLOC = 's'
					WHERE REDE_CPFUSUA = :cpf";
            $sql4->addPAram(':cpf', $cpf_r);
            $sql4->executeSQL($txt4);

            $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
            $tpl->block("ERRO");

            $redefinicao = rand(20, 30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
            $valor_redefinicao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789@#&"), 0, $redefinicao);

            $_SESSION['idDef'] = $valor_redefinicao;
            $_SESSION['idSession'] = $valor_redefinicao;

            $util->redireciona("redefinicao.php?idDef=" . $_SESSION['idDef'], "N", "", "0");

            $_SESSION['redefinir'] = '<h5><font color="red">Seu login foi bloqueado, redefina a sua senha.</font></h5>';
        }


        if ($res_login <> $cpf) {
            $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 1!!!</font>';
            $tpl->block("ERRO");
        } elseif ($res <> 0) {
            $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 2!!!</font>';
            $tpl->MSG1 = 'Tentativa ' . $tentativas . ' de 5.';
            $tpl->block("ERRO");
            $seg->registraLogin($bd, $cpf);
        } elseif ($res_login <> $cpf && $res <> 0) {
            $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 3!!!</font>';
            $tpl->block("ERRO");
        } elseif ($cpf == '' && md5($senha == '')) {
            $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS 4!!!</font>';
            $tpl->block("ERRO");
        } elseif ($blok == 'S') {
            $tpl->MSG = '<font color="red">SEU LOGIN ESTA BLOQUEADO, CLIQUE EM REDEFINIR A SENHA.</font>';
            $tpl->block("ERRO");
        } else {

            $aleatorio = mt_rand(20, 30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
            $valor = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"), 0, $aleatorio);

            $_SESSION['idSessao'] = $valor;
            $_SESSION['idUsuario'] = $sequsuario;
            $cpf = $seg->antiInjection($_POST['cpf']);
            $_SESSION['usuadmin'] = $admin;


            //Apaga todos os registros do dia
            $sql3 = new Query($bd);
            $txt3 = "DELETE FROM TREDE_SEGTENTA
				WHERE VNUMERCPFSEG = :cpf
					AND DDATATENTSEG = :data";
            $sql3->addParam(":cpf", $cpf);
            $sql3->addParam(":data", date('Y-m-d'));
            $sql3->executeSQL($txt3);

            $util->redireciona("principal.php?idSessao=" . $_SESSION['idSessao']);

        }

    } elseif ($cpf_count == '14') {
        $cnpj = $cnpj_login;
        $cnpj = $func->retirarPontostracosundelinebarra($cnpj);
        $senha = $seg->antiInjection($_POST['senha']);
        $senha = md5($senha);

        //CONTAR A QUANTIDADES DE TENTATIVAS
        $sql2 = new Query ($bd);
        $txt2 = "SELECT COUNT(*) TENTATIVAS 
				FROM TREDE_SEGTENTA 
				WHERE VNUMEROIPSEG = :ip 
					AND VNUMERCPFSEG = :cnpj";
        $sql2->addPAram(':ip', getenv("REMOTE_ADDR"));
        $sql2->addPAram(':cnpj', $cnpj);
        $sql2->executeQuery($txt2);

        $tentativas = $sql2->result("TENTATIVAS") + 1;

        //SELECT PARA VERIFICAR A SENHA
        $sql = new Query ($bd);
        $txt = "SELECT SEQUENCIACRE,VNOMECREDCRE,VCNPJJURICRE,VSENHAREDCRE,CBLOCKREDCRE 
				FROM TREDE_CREDENCIADOS 
				WHERE VCNPJJURICRE = :cnpj";
        $sql->addPAram(':cnpj', $cnpj);
        $sql->executeQuery($txt);

        $idrede = $sql->result("SEQUENCIACRE");
        $res_login = $sql->result("VCNPJJURICRE");
        $res_senha = $sql->result("VSENHAREDCRE");
        $blok = $sql->result("CBLOCKREDCRE");
        $res = strcmp($res_senha, $senha);

        $cnpj = $func->retirarPontostracosundelinebarra($cnpj);

        if ($tentativas >= 5) { //APÓS 5 TENTATIVAS O USUARIO É BLOQUADO

            //DAR O UPDATE E BLOQUEAR O USUARIO
            $sql4 = new Query ($bd);
            $txt4 = "UPDATE TREDE_CREDENCIADOS SET CBLOCKREDCRE = 's'
					WHERE VCNPJJURICRE = :cnpj";
            $sql4->addPAram(':cnpj', $cnpj);
            $sql4->executeSQL($txt4);

            $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS!!!</font>';
            $tpl->block("ERRO");

            $redefinicao = rand(20, 30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
            $valor_redefinicao = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789@#&"), 0, $redefinicao);

            $_SESSION['idDef'] = $valor_redefinicao;
            $_SESSION['idSession'] = $valor_redefinicao;

            $util->redireciona("redefinicao.php?idDef=" . $_SESSION['idDef'], "N", "", "0");

            $_SESSION['redefinir'] = '<h5><font color="red">Seu login foi bloqueado, redefina a sua senha.</font></h5>';
        }

        if ($res_login <> $cnpj) {
            $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS pj1!!!</font>';
            $tpl->block("ERRO");
        } elseif ($res <> 0) {
            $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS pj2!!!</font>';
            $tpl->MSG1 = 'Tentativa ' . $tentativas . ' de 5.';
            $tpl->block("ERRO");
            $seg->registraLogin($bd, $cnpj);
        } elseif ($res_login <> $cnpj && $res <> 0) {
            $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS pj3!!!</font>';
            $tpl->block("ERRO");
        } elseif ($cnpj == '' && md5($senha == '')) {
            $tpl->MSG = '<font color="red">LOGIN OU SENHA INCORRETOS pj4!!!</font>';
            $tpl->block("ERRO");
        } elseif ($blok == 'S') {
            $tpl->MSG = '<font color="red">SEU LOGIN ESTA BLOQUEADO, CLIQUE EM REDEFINIR A SENHA.</font>';
            $tpl->block("ERRO");
        } else {

            $aleatorio = mt_rand(20, 30); // 5 À 10 CARACTERES - CRIAR SESSÃO E ENVIAR PARA OUTRA PG
            $valor = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz0123456789"), 0, $aleatorio);

            $_SESSION['idSessao'] = $valor;
            $_SESSION['idRede'] = $idrede;
            $cnpj = $seg->antiInjection($_POST['cnpj']);

            //Apaga todos os registros do dia
            $sql3 = new Query($bd);
            $txt3 = "DELETE FROM TREDE_SEGTENTA
				WHERE VNUMERCPFSEG = :cnpj
					AND DDATATENTSEG = :data";
            $sql3->addParam(":cnpj", $cnpj);
            $sql3->addParam(":data", date('Y-m-d'));
            $sql3->executeSQL($txt3);

            $util->redireciona("rede/index.php?idSessao=" . $_SESSION['idSessao']);


        }


    } else {
        $tpl->MSG = '<font color="red">LOGIN INCORRETO!!!</font>';
        $tpl->block("ERRO");

    }
}

$tpl->show();
$bd->close();
?>