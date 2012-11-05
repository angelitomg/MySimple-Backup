<?php 
	
	/**
	* 
	* MySimple Backup
	*
	* @arquivo: backup.php
	*
	* @autor: Angelito M. Goulart
	*
	* @descricao: arquivo responsavel por gerar o dump da base de dados informada
	* e enviar o arquivo de download para download.
	*
	*/
	
	/**
	* Desativa a exibicao de erros do PHP
	*/
	error_reporting(0);
	
	/**
	* Variavel que ira conter a mensagem com o status da operacao
	*/
	$resultado = '';
	
	/**
	* Variavel que ira conter o dump do banco de dados
	*/
	$dump_str = '';
	
	/**
	* Se o form for postado, realiza o backup.
	*/ 
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	
		/**
		* Obtem os dados do formulario
		*/
		$servidor = (isset($_POST['servidor'])) ? $_POST['servidor'] : '';
		$banco 	  = (isset($_POST['banco'])) 	? $_POST['banco']    : '';
		$usuario  = (isset($_POST['usuario']))  ? $_POST['usuario']  : '';
		$senha    = (isset($_POST['senha']))    ? $_POST['senha']    : '';
		
		/**
		* Verifica o preenchimento dos campos
		*/
		if (empty($servidor) || empty($banco) || empty($usuario)){
			die('Preencha todos os campos corretamente!');
		}
		
		/**
		* Conexao com banco de dados 
		*/
		if (!$conexao = mysqli_connect($servidor, $usuario, $senha, $banco)){
			die('Erro na conexao com o banco de dados!');
		}
		
		
		/**
		* Consulta as tabelas do banco de dados
		*/
		$consulta_tabelas = mysqli_query($conexao, "SHOW TABLES;");
		
		/**
		* String que ira conter o dump do banco de dados
		*/
		$dump_str  = "-- \r\n";
		$dump_str .= "-- MySimple Backup\r\n";
		$dump_str .= "-- Gerado em " . date('d/m/Y H:i:s') . "\r\n";
		$dump_str .= "-- Angelito M. Goulart\r\n";
		$dump_str .= "-- http://angelitomg.com\r\n";
		$dump_str .= "-- \r\n\r\n";
		
		/**
		* Percorre as tabelas encontradas
		*/
		while ($tabela = mysqli_fetch_array($consulta_tabelas)){
		
		
			/**
			* Consulta e percorre a estrutura de cada tabela
			*/
			$consulta_estrutura = mysqli_query($conexao, "SHOW CREATE TABLE {$tabela[0]}");
			
			while ($estrutura = mysqli_fetch_array($consulta_estrutura)){
				$dump_str .= "\r\n";
				$dump_str .= "--\r\n";
				$dump_str .= "-- Estrutura da tabela `{$tabela[0]}`\r\n";
				$dump_str .= "--\r\n\r\n";
				$dump_str .= $estrutura[1] . ";";
				$dump_str .= "\r\n\r\n";
			}	
			
			/**
			* Consulta os dados de cada tabela
			*/
			$consulta_registros = mysqli_query($conexao, "SELECT * FROM {$tabela[0]}");
			
			
			/**
			* Consulta os campos da tabela selecionada
			*/
			$consulta_campos = mysqli_query($conexao, "SHOW COLUMNS FROM {$tabela[0]}");
			
			/**
			* Array que ira conter os campos da tabela 
			*/
			$campos = array();
			
			
			/**
			* Obtem todos os campos da tabela
			*/
			while ($lista_campos = mysqli_fetch_array($consulta_campos)){
				$campos[] = $lista_campos[0];
			}
			
			$dump_str .= "--\r\n";
			$dump_str .= "-- Dados da tabela `{$tabela[0]}`\r\n";
			$dump_str .= "--\r\n\r\n";
			
			/**
			* Percorre cada registro da tabela selecionada
			*/
			while ($registro = mysqli_fetch_assoc($consulta_registros)){
			
				/**
				* Cria a instrucao INSERT para o registro atual
				*/
				$dump_str .= "INSERT INTO {$tabela[0]} (";
				
				/**
				* Obtem o ultimo campo da tabela, para realizar a comparacao
				* e verificar se vai virgula ou parentese apos o nome do campo.
				*/
				$ultimo = end($campos);
				
				/**
				* Insere os campos
				*/
				foreach ($campos as $campo){
					$dump_str .= "`{$campo}`";
					if ($campo <> $ultimo)
						$dump_str .= ", ";
					else
						$dump_str .= ") VALUES (";
				}
				
				/**
				* Obtem o ultimo valor do registro, para verificar se apos
				* o registro deve ir uma virgula ou o fim da instrucao.
				*/
				$chaves = array_keys($registro);
				$ultimo = end($chaves);
				
				/**
				* Insere os valores
				*/
				foreach ($registro as $chave => $valor){
					$dump_str .= "'" . mysqli_real_escape_string($conexao, $valor) . "'";
					if ($chave <> $ultimo)
						$dump_str .= ", ";
					else
						$dump_str .= ");\r\n";
				}
			
			}
			
		}
		
	}
	
	/**
	* Verifica se existe algum conteudo no dump
	*/
	if (!empty($dump_str)){
	
		/**
		* Cria um arquivo temporario e abre para gravacao e leitura
		*/
		$tmp_dir  = sys_get_temp_dir();
		$tmp_name = $tmp_dir . DIRECTORY_SEPARATOR . 'backup-' . date('d-m-Y--H-i-s') . '.sql';
		$tmp_file = fopen($tmp_name, 'a+');
		
		/**
		* Escreve o dump no arquivo
		*/
		fwrite($tmp_file, $dump_str);
		
		/**
		* Cabecalho necessario para enviar o arquivo para download
		*/
		header('Content-type: octet/stream');
		header('Content-disposition: attachment; filename="'.basename($tmp_name).'";');
		header('Content-Length: '.filesize($tmp_name));
		
		/**
		* Fecha o arquivo
		*/ 
		fclose($tmp_file);
		
		/**
		* Envia o arquivo para download
		*/
		readfile($tmp_name);
		
		/**
		* Exclui o arquivo temporario
		*/
		unlink($tmp_name);
		
	}

?>