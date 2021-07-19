<p>
  Teste de envio de SMS
  
  <form method="post" id="form1" action="enviaSMS.php">
    <table>
      <tr>
        <td>Fornecedor</td>
        <td><input type="text" id="fornecedor" name="fornecedor" value="webapi"></td>
      </tr>
      <tr>
        <td>Conta</td>
        <td><input type="text" id="conta" name="conta" value="NDM5NSw2MDAxOTcsZFc1cFlXOD0"></td>
      </tr>
      <tr>
        <td>Senha</td>
        <td><input type="text" id="senha" name="senha" value="MEI3xcUM0s"></td>
      </tr>
      <tr>
        <td>Destinatário</td>
        <td><input type="text" id="para" name="para" value="554391616033"></td>
      </tr>
      <tr>
        <td>Mensagem</td>
        <td><input type="text" id="msg" name="msg" value="Solus computacao!!"></td>
      </tr>      
      <tr>
        <td>ID</td>
        <td><input type="text" id="id" name="id" value="10"></td>
      </tr>  
      <tr>
        <td>Desc. Rementente</td>
        <td><input type="text" id="remetente" name="remetente" value="Solus"></td>
      </tr>  
      <tr>
        <td></td>
        <td><input type="submit" value="enviar"></td>
      </tr>
    </table>    
  </form>
</p>  
<br>
<p>
  Teste de envio de email
  
  <form method="post" id="form1" action="enviaEmail.php" enctype="multipart/form-data">
    <table>
      <tr>
        <td>Host</td>
        <td><input type="text" id="host" name="host" value="smtp.solus.inf.br"></td>
      </tr>
      <tr>
        <td>Porta</td>
        <td><input type="text" id="porta" name="porta" value="587"></td>
      </tr>
      <tr>
        <td>Autenticacao</td>
        <td><input type="text" id="autenticacao" name="autenticacao" value="tls"></td>
      </tr>      
      <tr>
        <td>Usuário SMTP</td>
        <td><input type="text" id="usuariosmtp" name="usuariosmtp" value="angelo@solus.inf.br"></td>
      </tr>
      <tr>
        <td>Senha SMTP</td>
        <td><input type="password" id="senhasmtp" name="senhasmtp" value="solus@123"></td>
      </tr>
      <tr>
        <td>De</td>
        <td><input type="text" id="de" name="de" value="angelo@solus.inf.br"></td>
      </tr>      
      <tr>
        <td>Para</td>
        <td><input type="text" id="para" name="para" value="angelomm@gmail.com"></td>
      </tr>  
      <tr>
        <td>Assunto</td>
        <td><input type="text" id="assunto" name="assunto" value="Teste"></td>
      </tr>  
      <tr>
        <td>Texto</td>
        <td><input type="text" id="texto" name="texto" value="Teste de envio de email"></td>
      </tr>  
      <tr>
        <td>Arquivo 1</td>
        <td><input type="file" id="arquivo1" name="arquivo1" ></td>
      </tr>       
      <tr>
        <td>Arquivo 2</td>
        <td><input type="file" id="arquivo2" name="arquivo2" ></td>
      </tr>       
      <tr>
        <td>Arquivo 3</td>
        <td><input type="file" id="arquivo3" name="arquivo3" ></td>
      </tr>       
      <tr>
        <td>Arquivo 4</td>
        <td><input type="file" id="arquivo4" name="arquivo4" ></td>
      </tr>       
      <tr>
        <td>Arquivo 5</td>
        <td><input type="file" id="arquivo5" name="arquivo5" ></td>
      </tr>       
      <tr>
        <td></td>
        <td><input type="submit" value="enviar"></td>
      </tr>
    </table>    
  </form>
</p> 
