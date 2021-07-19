<?php
/*require_once("comum/autoload.php");
$seg->secureSessionStart();
ob_start();

$bd = new Database();
//require_once("comum/layout_log.php");
//$tpl->addFile("CONTEUDO","termos.html");

$sql = new Query($bd);
$txt = "SELECT TEXTO FROM TREDE_CONTRATO_TERMOS";
$sql->executeQuery($txt);

//$tpl->TEXTO = utf8_encode($sql->result("TEXTO"));
$pagina = $sql->result("TEXTO");

// Incluindo o autoload do DOM PDF
require_once 'dompdf/autoload.inc.php';

//Criando a instancia do Dom PDF.
//Criando o namespace para evitar erros
use Dompdf\Dompdf;

// Instanciando a classe do Dom DPF
$dompdf = new Dompdf();

//Criando o código HTML que será transformado em pdf
$dompdf->loadHtml($pagina);

//Define o tipo de papel de impressão (opcional)
//tamanho (A4, A3, A2, etc)
//oritenação do papel:'portrait' (em pé) ou 'landscape' (deitado)
$dompdf->setPaper('A4','portrait');

// Vai renderizar o HTML como PDF
$dompdf->render();

// Saída do pdf para a renderização do navegador.
//Coloca o nome que deseja que seja renderizado.
$f;
$l;
if (headers_sent($f, $l)) {
	echo $f, '
', $l, '
';
	die('now detect line');
}
$dompdf->stream("Termos.pdf", array("Attachment"=>0));

$bd->close();

ob_end_flush();
exit();*/


require_once("comum/autoload.php");
session_start();
ob_start();

$bd = new Database();

$sql = new Query($bd);
$sql->clear();
$txt = "SELECT LAST_INSERT_ID(SEQ), NOME 
			FROM TREDE_CONTRATOS
			ORDER BY 1 DESC
			LIMIT 1";
$sql->executeQuery($txt);

$filename = $sql->result('NOME');

$file = 'admin/uploads/contrato/' . $filename;

header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');

readfile($file);
ob_end_flush();
?>