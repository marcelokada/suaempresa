<?php
require_once("comum/autoload.php"); 
$seg->secureSessionStart();
require_once("comum/apagaArquivos.php"); 
//error_reporting(0);

$bd = new Database();

$id_sessao 		= $_SESSION['idSessao'];
$id_confer 		= $_GET['idSessao'];
$seq 			= $_SESSION['idUsuario'];
$admin			= $_SESSION['usuadmin'];

$seg->verificaSession($id_sessao);

	require_once("comum/layout.php");  
$tpl->addFile("CONTEUDO","inseririmg.html");
	
	$sql = new Query ($bd);
	$txt = "SELECT LINIMAGEMIMG 
			   FROM TREDE_IMAGEMCRED";
	$sql->executeQuery($txt); 

    $file='temp/'.md5(uniqid(rand(), true)).'.jpg';

    if ($sql->count() > 0) {
      $foto_usuario = $sql->result("LINIMAGEMIMG");

      if ($foto_usuario <> '') {
        $f=fopen($file,'wb');
        if(!$f)
          $this->Error('Não foi possivel criar o arquivo: '.$file);
        fwrite($f,$foto_usuario,strlen($foto_usuario));
        fclose($f);

        $_SESSION['arquivo_foto'] = $file;
        $tpl->IMG1 = $file;
      } 
      else
        echo "2";
    }
	
$nomeEvento = $_POST['nome_evento'];
$imagem = $_FILES['imagem']['tmp_name'];
$tamanho = $_FILES['imagem']['size'];
$tipo = $_FILES['imagem']['type'];
$nome = $_FILES['imagem']['name'];


if(isset($_POST['salvar'])){
 
$sql3 = new Query ($bd);
$txt3 = "DELETE FROM TREDE_IMAGEMCRED";
$sql3->executeSQL($txt3);  
 
 
if ($imagem != "none"){

$fp = fopen($imagem, "rb");
$conteudo = fread($fp, $tamanho);
$conteudo = addslashes($conteudo);
fclose($fp);
  
$sql2 = new Query ($bd);
$txt2 = "INSERT INTO TREDE_IMAGEMCRED 
(SEQUENCIACRE,VDIRPASTAIMG,VNOMEIMAGIMG,VEXTENSAOIMG,VTAMANHOIIMG,LINIMAGEMIMG) 
VALUES ('2','pasta','".$nome."','".$tipo."','".$tamanho."','".$conteudo."')";
$sql2->executeSQL($txt2); 


}

}



$tpl->show(); 
$bd->close();
?>