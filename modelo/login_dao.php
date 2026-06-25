<?php
/*
 * @framework	SiGeM - Sistema Gerenciador de Módulos
 * @copyright	Copyright (C) 2016 Kleyton Fantin, Todos os direitos reservados.
 * @license		GNU General Public License versão 2 ou posterior; leia LICENSE.txt
 *
 */

//Verifica se está dentro do sistema
defined('SIGEM_EXEC') or die;

Class LoginDAO{
	private $banco = null;
	private $funcao = null;
	private $perfil = null;
	
	function __construct($banco){

		$this->funcao = new Funcao();
		//Valida objeto de configuração
		if(get_class($banco) != 'Banco') return false;
		
		//Guarda objeto do banco para conexão
		$this->banco = $banco;
		
		//Verifica se tem acesso a classe de perfil
		$this->funcao->carrega_arquivo('controle','perfil');
	}
	
	public function validar_login($usuario, $senha){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return array(MENSAGEM_ERRO=>'Não foi identificada conexão com o banco de dados');
		//Valida parâmetro
		if(strlen($usuario) == 0) return array(MENSAGEM_PADRAO=>'Informe o usuário');
		if(strlen($senha) == 0) return array(MENSAGEM_PADRAO=>'Informe a senha');
		
		//Monta requisição
		$requisicao = 'select u.id, u.nome as usuario_nome, u.provisoria, p.id as perfil_id, p.nome as perfil_nome from usuario u left join perfil p on p.id = u.perfil_id where u.desativado is null and u.usuario = ? and senha = md5(?)';
		
		//Adiciona parâmetros da requisição
		$parametros = array($usuario, $senha);
		
		//Executa a requisição
		$resultado = $this->banco->query($requisicao, $parametros);
		
		// if(isset($_POST['entrar']) && $_POST['entrar'] == 'Entrar'){
			// if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response']))
			// {
				  // $secret = '6LdMyJ0cAAAAAKS6gIQG8QsCV8iFQKwrnwHqOTPb';
				  // $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
				  // $responseData = json_decode($verifyResponse);
				  // if($responseData->success)
				  // { 
				  // else
				  // {
					// return array(MENSAGEM_ERRO=>'Você é um robô');
				   // }
			 // }else{
				// return array(MENSAGEM_ERRO=>'Favor marcar a opção de ReCaptcha');
			  // }
		   // }
		
		//Verifica o resultado obtido
		if(is_string($resultado)) return array(MENSAGEM_ERRO=>$resultado);
		if(!is_array($resultado)) return array(MENSAGEM_ERRO=>'Não foi possível carregar a informação');
		if(count($resultado) != 1) return array(MENSAGEM_ERRO=>'Usuário e/ou senha inválido(s)');
		
		//Retorna os dados obtidos
		return $resultado[0];
	}
	
	public function permissao($usuario, $senha){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return 'Não foi identificada conexão com o banco de dados';
		//Valida parâmetro
		if(strlen($usuario) == 0) return 'Informe o usuário';
		if(strlen($senha) == 0) return 'Informe a senha';
		
		//Monta requisição
		$requisicao = 'select m.arquivo, p.acao, p.tipo from usuario u left join permissao p on p.perfil_id = u.perfil_id left join modulo m on m.id = p.modulo_id where u.desativado is null and p.desativado is null and m.desativado is null and u.usuario = ? and senha = md5(?) group by m.arquivo, p.acao';
		
		//Adiciona parâmetros da requisição
		$parametros = array($usuario, $senha);
		
		//Executa a requisição
		$resultado = $this->banco->query($requisicao, $parametros);
		
		//Verifica o resultado obtido
		if(is_string($resultado)) return $resultado;
		if(!is_array($resultado)) return 'Não foi possível carregar a informação';
		if(count($resultado) == 0) return 'Nenhuma permissão encontrada';
		if(strlen($resultado[0]['arquivo']) == 0) return 'Nenhuma permissão encontrada';
		
		//Organiza as permissões para um array $permissao[módulo][ação]=tipo;
		//$resultado = $this->organizar_permissoes($resultado);
		$resultado = $this->organizar_permissoes($resultado);
		
		//Retorna os dados obtidos
		return $resultado;
	}
	
	public function trocar_senha($usuario, $senha_atual, $nova_senha){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return 'Não foi identificada conexão com o banco de dados';
		//Valida parâmetro
		if(!is_string($usuario) || strlen($usuario) == 0) return 'Informe o usuário';
		if(!is_string($senha_atual) || strlen($senha_atual) == 0) return 'Informe a senha atual';
		if(!is_string($nova_senha) || strlen($nova_senha) == 0) return 'Informe a nova senha';
		
		//Monta requisição
		$requisicao = 'update usuario u set u.senha = md5(?), u.provisoria = NULL where u.usuario = ? and u.senha = md5(?) ';
		
		//Adiciona parâmetros da requisição
		$parametros = array($nova_senha, $usuario, $senha_atual);
		
		//Executa a requisição
		$resultado = $this->banco->query($requisicao, $parametros);
		
		//Verifica o resultado obtido
		if(is_string($resultado)) return $resultado;
		if(!is_numeric($resultado)) return 'Não foi possível alterar a senha';
		if($resultado == 0 && $senha_atual != $nova_senha) return 'Não foi possível alterar a senha';
		
		//Retorna os dados obtidos
		return true;
	}

public function verificar_token($token) {
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return 'Não foi identificada conexão com o banco de dados';
		
		//Monta requisição
		$requisicao = 'select * from usuario where token = ?;';
		
		//Adiciona parâmetros da requisição
		$parametros = array($token);
		
		//Executa a requisição
		$resultado = (strlen($token) > 0) ? $this->banco->query($requisicao, $parametros) : '';

		
		if(is_array($resultado) && (count($resultado) > 0) && isset($resultado[0]) && $resultado = $resultado[0]){
			$this->banco->query('update usuario set token = null, senha = md5(?) where token = ? ', array($resultado['usuario'], $token));
			return 'Seu acesso foi alterado. Favor clicar em <strong>Login</strong> e acessar usando o seu <strong> CPF como usuário e senha </strong>, após o acesso, podera alterar para um nova senha.';
		}
		
		//Verifica o resultado obtido
		if(is_string($resultado)) return $resultado;
		if(!is_numeric($resultado)) return 'Não foi encontrado o token, verifique o seu e-mail e pode enviar outro pelo esqueci senha';
		
		//Retorna os dados obtidos
		return true;
	}

	public function verificar_email($email, $cpf){
		//Verifica se tem acesso ao banco
		if(is_null($this->banco)) return 'Não foi identificada conexão com o banco de dados';
		
		//Monta requisição
		$requisicao = 'select * from usuario where email = ? and usuario = ?;';
		
		//Adiciona parâmetros da requisição
		$parametros = array($email, $cpf);
		
		//Executa a requisição
		$resultado = (strlen($email) > 0 && strlen($cpf) > 0) ? $this->banco->query($requisicao, $parametros) : '';

		
		if(is_array($resultado) && (count($resultado) > 0) && isset($resultado[0]) && $resultado = $resultado[0]){
			$token = uniqid();
			$str = 'Enviamos uma senha provisória para o seu e-mail ' . $resultado['email'] . ' aos cuidados de ' . $resultado['nome'].'. Favor verificar Caixa de Entrada e Spam<br>'; 
			$this->enviar_email($resultado['nome'], $resultado['email'], $token);
			$this->banco->query('update usuario set senha = md5(?), provisoria = 1 where id = ? ', array($token, $resultado['id']));
			return $str;
		}
		
		//Verifica o resultado obtido
		if(is_string($resultado)) return $resultado;
		if(!is_numeric($resultado)) return 'Não foi encontrado o E-mail na base do Mapa, favor criar chamado informando e-mail para a TIC';
		
		//Retorna os dados obtidos
		return true;
	}

	public function enviar_email($nome, $email, $token) {
		//include_once '/dados_sigem/mapa/PHPMailer-5.2.4/class.phpmailer.php';
		include "/usr/share/php/PHPMailer/PHPMailerAutoload.php";

		$mail = new PHPMailer;

		$mail->IsSMTP();  
		$mail->CharSet = 'UTF-8';                                    // Set mailer to use SMTP
		//$mail->XMailer = ' ';
		$mail->Host = '172.25.104.14';  // Specify main and backup server
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'no-reply@esteio.rs.gov.br';                            // SMTP username
		$mail->Password = "#3/JvIAyu@w~kvF?ofUQqR~DYRJofoZB";                           // SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
		$mail->SMTPOptions = array( 'ssl' => array( 'verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true ) );

		
		$mail->addCustomHeader('MIME-version', "1.0");
                $mail->addCustomHeader('X-Mailer', "PHP/' . phpversion();");  	 	

		$mail->Port = 587; 
		//$mail->SMTPDebug = 2;

		$mail->From = 'no-reply@esteio.rs.gov.br';
		$mail->FromName = 'No-Reply';
		$mail->AddAddress($email, $nome);  // Add a recipient
		//$mail->AddAddress('ellen@example.com');               // Name is optional
		//$mail->AddReplyTo('info@example.com', 'Information');
		//$mail->AddCC('cc@example.com');
		//$mail->AddBCC('bcc@example.com');

		//$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		//$mail->AddAttachment('/var/tmp/file.tar.gz');         // Add attachments
		//$mail->AddAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
		$mail->IsHTML(true);                                  // Set email format to HTML

		$mail->Subject = 'Não Responda - Mensagem automática';
		$mail->Body    = 'Olá, houve uma solicitação de trocar de senha no Sistema <br> A senha provisória é <br> <strong>'.$token.' <br>  <a href="http://portal.esteio.rs.gov.br/mapa/">Retornar ao site </a> <br> Favor inserir o seu CPF como USUÁRIO e a SENHA PROVISÓRIA para posteriormente trocar a sua senha por uma nova . </strong> <br>';
		$mail->AltBody = 'Envio de e-mail automático';

		if(!$mail->Send()) {
		echo 'Não foi possível enviar o e-mail. Ocorreu um erro. ';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
		exit;
		}
	}

	private function organizar_permissoes($array){
		//Inicia o array de objeto
		$permissao = array();
		
		//Valida parâmetro
		if(is_array($array)){
			//Converte arrays em objects
			foreach($array as $registro){
				$permissao[$registro['arquivo']][$registro['acao']] = $registro['tipo'];
			}
		}
		
		return $permissao;
	}


}
?>
