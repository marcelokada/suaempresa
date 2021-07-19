<?php
require_once("comum/autoload.php");
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php");

error_reporting(0);

$bd = new Database();

$id_sessao 	    = $_SESSION['idSessao'];
$id_confer 	    = $_GET['idSessao'];
$e 				= $_SESSION['admin'];
$ver_admin		= $_SESSION['admin'];
$seq_admin		= $_SESSION['idAdmin'];


require_once("comum/layout.php");
$tpl->addFile("CONTEUDO", "rel_usua_niveis.html");
$tpl->ID_SESSAO = $_SESSION['idSessao'];


$tpl->USUA = $_SESSION['idAdmin'];

$sql1 = new Query($bd);
$txt1 = "SELECT NIDUSNIVE,NNUMENIVE FROM TREDE_NIVEL
		WHERE NNUMEFILI  = :sequsu
		ORDER BY 1";
$sql1->addParam(':sequsu', $seq_admin);
$sql1->executeQuery($txt1);

while (!$sql1->eof()) {
    $id1 = $sql1->result("NIDUSNIVE");
    $tpl->ID1 = $sql1->result("NIDUSNIVE");
    $tpl->LEVEL1 = $sql1->count();
    $nome = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $id1)));
    $tpl->NOME1 = $nome;

    $sql2 = new Query($bd);
    $txt2 = "SELECT NIDUSNIVE,NNUMENIVE FROM TREDE_NIVEL
		WHERE NNUMEFILI  = :sequsu
		ORDER BY 1";
    $sql2->addParam(':sequsu', $id1);
    $sql2->executeQuery($txt2);

    while (!$sql2->eof()) {
        $id2 = $sql2->result("NIDUSNIVE");
        $tpl->ID2 = $sql2->result("NIDUSNIVE");
        $tpl->LEVEL2 = $sql2->count();
        $nome2 = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $id2)));
        $tpl->NOME2 = $nome2;

        $sql3 = new Query($bd);
        $txt3 = "SELECT NIDUSNIVE,NNUMENIVE FROM TREDE_NIVEL
		        WHERE NNUMEFILI  = :sequsu
		        ORDER BY 1";
        $sql3->addParam(':sequsu', $id2);
        $sql3->executeQuery($txt3);


        while (!$sql3->eof()) {
            $id3 = $sql3->result("NIDUSNIVE");
            $tpl->ID3 = $sql3->result("NIDUSNIVE");
            $tpl->LEVEL3 = $sql3->count();
            $nome3 = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $id3)));
            $tpl->NOME3 = $nome3;

            $sql4 = new Query($bd);
            $txt4 = "SELECT NIDUSNIVE,NNUMENIVE FROM TREDE_NIVEL
		        WHERE NNUMEFILI  = :sequsu
		        ORDER BY 1";
            $sql4->addParam(':sequsu', $id3);
            $sql4->executeQuery($txt3);
            $tpl->LEVEL4 = $sql4->count();
            while (!$sql4->eof()) {
                $id4 = $sql4->result("NIDUSNIVE");
                $tpl->ID4 = $sql4->result("NIDUSNIVE");
                $tpl->LEVEL4 = $sql4->count();
                $nome4 = utf8_encode(ucwords($func->RetonaNomeUsuarioPorSeq($bd, $id4)));
                $tpl->NOME4 = $nome4;


                $tpl->block("NIVEIS4");
                $sql4->next();
            }

            $tpl->block("NIVEIS3");
            $sql3->next();
        }


        $tpl->block("NIVEIS2");
        $sql2->next();
    }

    $tpl->block("NIVEIS1");
    $sql1->next();
}

$tpl->show();
$bd->close();
?>