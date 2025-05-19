<?php
	if(!session_id()){ session_start(); }	
	if(!class_exists('Utils')) require 'Utils.php';	
	if(!class_exists('Sql')) require 'Sql.php';

	class Paciente{
		private $id;
		private $nome;
		private $email;
		private $senha;
		private $cpf;
		private $celular;
		private $data_cadastro;

		public function __construct($id=null){
			$Sql = new Sql();

			$data = $id!=null && $id>0 ? $Sql->select1("SELECT * FROM sci_pacientes WHERE codPac = {$id} ORDER BY 1 DESC LIMIT 1;") : array();
			foreach(($data!=null ? $data : array()) as $key => $val){
				switch($key){
					case 'codPac':{
						$this->id = $val;
						break;
					}
					case 'nome':{
						$this->nome = $val;
						break;
					}
					case 'email':{
						$this->email = $val;
						break;
					}
					case 'senha':{
						$this->senha = $val;
						break;
					}
					case 'cpf':{
						$this->cpf = $val;
						break;
					}
					case 'celular':{
						$this->celular = $val;
						break;
					}
					case 'data_cadastro':{
						$this->data_cadastro = $val;
						break;
					}
				}
			}
		}

		static function getURL($route=''){
			require dirname(__FILE__).DIRECTORY_SEPARATOR.'Config.php';

			$URL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://');
			$HOST = substr($Config['URL'], strpos($Config['URL'], '://')+3);

			return $URL.$HOST.$route;
		}
		static function getPATH(){
			return dirname(dirname(dirname(__FILE__)));
		}

		static function login($origemFile, $login, $senha, $return=false){
			$Sql = new Sql();

			$login = Utils::soNumeros($login);
			$cPanel = Paciente::getURL('panel');

			if($login!='' && $senha!=''){
				if(Utils::isCPF($login)){
					$senha = Utils::antiSQL($senha);

					$querySql = "SELECT * FROM sci_pacientes WHERE codPac>0 AND cpf = '{$login}' AND senha = md5('{$senha}');";
					$rs = $Sql->select1($querySql);
					if(!empty($rs)){
						$_SESSION['SCI_UID'] = $rs['codPac'];
						$_SESSION['Login'] = $rs['cpf'];
						$_SESSION['SCI_Secret'] = md5($rs['senha']);

						return true;
					} else{
						$msgError = "Login ou senha incorretos !";
						setCookie("erro",$msgError);
						if(!$return) header("Location: {$cPanel}/Login.php");
					}
				} else{
					$msgError = "Login inválido !";
					setCookie("erro",$msgError);
					if(!$return) header("Location: {$cPanel}/Login.php");
				}
			} else{
				$msgError = "Login ou senha inválidos.";
				setCookie("erro",$msgError);
				if(!$return) header("Location: {$cPanel}/Login.php");
			}
		}

		static function auth($origemFile, $visitante=false){
			$UID = isset($_SESSION['SCI_UID']) && !empty($_SESSION['SCI_UID']) ? $_SESSION['SCI_UID'] : null;
			$user = $UID!=null ? new Paciente($UID) : array();
			$cPanel = Paciente::getURL('panel');

			if(!empty($user)){
				if(isset($_SESSION['SCI_Secret']) && $_SESSION['SCI_Secret'] === md5($user->getSenha())){
					if(in_array(substr(basename(dirname($origemFile)),0,7), array('panel','classes','univesp'))){
						return $user;
					}
				}else{
					setCookie("erro","Sua senha foi alterada. Faça seu login!");
					if(!$visitante) header("Location: {$cPanel}/Login.php");
				}
			} else{
			   setCookie("erro",'Bem-vindo !');
			   if(!$visitante) header("Location: {$cPanel}/Login.php");
			}
		}

		public function alterarSenha($data, $manterSessao=false){
			$Sql = new Sql();

			$senha1 = isset($data['password1']) ? md5($data['password1']) : null;
			$senha2 = isset($data['password2']) ? md5($data['password2']) : null;
			$senhaAtual = isset($data['password']) ? md5($data['password']) : null;

			if($senha1!=null && $senha1 == $senha2){
				if($this->getSenha() == $senhaAtual){
					$id = $this->getId();

					$querySql ="UPDATE sci_pacientes SET senha = '{$senha1}' WHERE codPac = {$id}";

					$rs = $Sql->update($querySql);

					if($rs && $manterSessao) $_SESSION['SCI_Secret'] = md5($senha1);
					return $rs;
				}
				else return 'Senha antiga não confere.';
			}
			else return 'As novas senhas devem ser iguais.';
		}

		public function getId(){
				return $this->id;
		}

		public function setId($id){
				$this->id = $id;
		}

		public function getNome(){
				return $this->nome;
		}

		public function setNome($nome){
				$this->nome = $nome;
		}

		public function getEmail(){
				return $this->email;
		}

		public function setEmail($email){
				$this->email = $email;
		}
		
		public function getSenha(){
				return $this->senha;
		}

		public function setSenha($senha){
				$this->senha = $senha;
		}

		public function getCpf(){
				return $this->cpf;
		}

		public function setCpf($cpf){
				$this->cpf = $cpf;
		}

		public function getCelular(){
				return $this->celular;
		}

		public function setCelular($celular){
				$this->celular = $celular;
		}

		public function getDataCadastro(){
				return $this->data_cadastro;
		}

		public function setDataCadastro($data){
				$this->data_cadastro = $data;
		}
	}
switch($_SERVER['REQUEST_METHOD']){
    case 'PUT':{}
	case 'POST':{
		$arrResponse =  array('rs'=>false, 'msg'=>'');
		$params = isset($_GET) &&$_GET!=null && !empty($_GET) ? $_GET : array();

		if(isset($params['key']) && $params['key'] = 'PJI310'){
			$u = Paciente::auth(__FILE__, true);
			if(!empty($u)){
				$rs = false; 	$err = false;

				switch($params['a']){
					case 'alteraSenha':
						$rs = $u->alterarSenha(isset($_POST) &&$_POST!=null && !empty($_POST) ? $_POST : array(), true);
						break;
					default:
						$err = true;
				}

				$arrResponse['rs'] = is_bool($rs) && $rs===true;
				$arrResponse['msg'] = is_string($rs) ? $rs : ($arrResponse['rs'] ? 'Salvo com Sucesso !' : 'Error: User');
			}
			else{ $arrResponse['rs'] = -1; }
			
			if(!$err) echo json_encode($arrResponse, JSON_NUMERIC_CHECK);
		}
		break;
	}
	default:{}
}
?>
