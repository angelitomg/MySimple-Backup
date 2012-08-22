<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>MySimple Backup</title>	
	<meta http-equiv="content-type" content="text/html;charset=UTF-8" />    
	<link rel="stylesheet" href="css/screen.css" media="screen" />
</head>
<body>

<div id="container">


		<form id="form2" action="backup.php" target="_download" method="post">	
		
			<h3><span>MySimple Backup</span></h3>
		
			<fieldset><legend>MySimple Backup</legend>
				<p class="first">
					<label for="servidor">Servidor</label>
					<input type="text" name="servidor" id="servidor" size="30" />
				</p>
				<p>
					<label for="banco">Banco</label>
					<input type="text" name="banco" id="banco" size="30" />
				</p>
				<p>
					<label for="usuario">Usu&aacute;rio</label>
					<input type="text" name="usuario" id="usuario" size="30" />
				</p>
				<p>
					<label for="senha">Senha</label>
					<input type="password" name="senha" id="senha" size="30" />
				</p>				
				
				<p class="submit"><button type="submit">Backup</button></p>		
							
			</fieldset>					
						
		</form>	
		
</div>

</body>
</html>
