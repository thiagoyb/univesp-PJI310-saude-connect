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

		public static function cadastrarPaciente($data){
			$Sql = new Sql();

			$arr=array();
			$arr['nome'] = isset($data['nome']) && $data['nome']!='' ? strtoupper($data['nome']) : null;
			$arr['cpf'] = isset($data['cpf']) && Utils::isCPF($data['cpf']) ? Utils::soNumeros($data['cpf']) : null;
			$arr['senha'] = isset($data['senha']) && $data['senha']!='' ? md5($data['senha']) : md5($arr['cpf']);
			$arr['email'] = isset($data['email']) && $data['email']!='' ? strtolower($data['email']) : null;
			$arr['celular'] = isset($data['celular']) ? Utils::soNumeros($data['celular']) : null;
			$arr['cns'] = isset($data['cns']) ? Utils::soNumeros($data['cns']) : '';
			$arr['data_nasc'] = isset($data['data_nasc']) ? trim($data['data_nasc']) : '';
			$arr['sexo'] = isset($data['sexo']) ? strtoupper($data['sexo']) : '';
			$arr['profissao'] = isset($data['profissao']) ? strtoupper($data['profissao']) : null;

			if($arr['nome']!=null && $arr['email']!=null && $arr['cpf']!=null){

				return $Sql->newInstance('sci_pacientes', $arr);
			}
			else return "Todos os campos são obrigatórios !";
		}

		public static function loginPaciente($data){
			$Sql = new Sql();

			$login = isset($data['login']) ? Utils::soNumeros($data['login']) : null;
			$senha = isset($data['senha']) && $data['senha']!='' ? $data['senha'] : null;

			if($login!=null && $senha!=null){
				if(Utils::isCPF($login)){
					$querySql = "SELECT * FROM sci_pacientes WHERE codPac>0 AND cpf = '{$login}' AND senha = md5('{$senha}');";
					$rs = $Sql->select1($querySql);

					return $rs!=null && isset($rs['codPac']) ? $rs['codPac'] : false;
				}
				else return "CPF inválido !";
			}
			else return "Login ou senha inválidos.";
		}

		public static function getPaciente($id=null){
			$Sql = new Sql();
			$id = $id!=null && $id!='' ? $id : 0;

			if($id>0){
				$querySql ="SELECT * FROM sci_pacientes WHERE codPac = {$id} ORDER BY codPac DESC;";

				return $Sql->select1($querySql);
			}
			else return "ID inválido !";
		}

		public static function listarPacientes(){
			$Sql = new Sql();

			$querySql ="SELECT * FROM sci_pacientes WHERE codPac>0 ORDER BY codPac DESC;";
			return $Sql->select($querySql);
		}

		public function alterarDados($data){
			$Sql = new Sql();

			$nome = isset($data['nome']) ? strtoupper($data['nome']) : null;
			$email = isset($data['email']) ? strtolower($data['email']) : null;
			$celular = isset($data['celular']) ? Utils::soNumeros($data['celular']) : null;
			$cns = isset($data['cns']) ? Utils::soNumeros($data['cns']) : '';
			$data_nasc = isset($data['data_nasc']) ? trim($data['data_nasc']) : '';
			$sexo = isset($data['sexo']) ? strtoupper($data['sexo']) : '';
			$profissao = isset($data['profissao']) ? strtoupper($data['profissao']) : null;

			$whereAdd ='';
			$whereAdd.= $nome!=null ? ", nome = '{$nome}'" : '';
			$whereAdd.= $email!=null ? ", email = '{$email}'": '';
			$whereAdd.= $celular!=null ? ", celular = '{$celular}'": '';
			$whereAdd.= $cns!=null ? ", cns = '{$cns}'": '';
			$whereAdd.= $data_nasc!=null ? ", data_nasc = '{$data_nasc}'": '';
			$whereAdd.= $sexo!=null ? ", sexo = '{$sexo}'": '';
			$whereAdd.= $profissao!=null ? ", profissao = '{$profissao}'": '';

			$id = $this->getId();
			$querySql ="UPDATE sci_pacientes SET data_cadastro = data_cadastro {$whereAdd} WHERE codPac = {$id}";

			return $whereAdd!='' ? $Sql->update($querySql) : true;
		}

		public function alterarSenha($data){
			$Sql = new Sql();

			$senha1 = isset($data['senha1']) ? md5($data['senha1']) : null;
			$senha2 = isset($data['senha2']) ? md5($data['senha2']) : null;
			$senhaAtual = isset($data['senha']) ? md5($data['senha']) : null;

			if($senha1!=null && $senha1 == $senha2){
				if($this->getSenha() == $senhaAtual){
					$id = $this->getId();

					$querySql ="UPDATE sci_pacientes SET senha = '{$senha1}' WHERE codPac = {$id}";

					return $Sql->update($querySql);
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
   case 'PUT':{
		$arrResponse =  array('rs'=>false, 'msg'=>'');
		$_RECV = Utils::receiveAjaxData('PUT');

		if(isset($_RECV['key']) && $_RECV['key'] == 'PJI310'){
			$id = isset($_RECV['id']) && $_RECV['id']!='' ? intval($_RECV['id']) : 0;
			$u = new Paciente($id);

			if(!empty($u)){
				$rs = isset($_RECV['senha']) ? $u->alterarSenha($_RECV) : $u->alterarDados($_RECV);

				$arrResponse['rs'] = $rs===true;
				$arrResponse['msg'] = is_string($rs) ? $rs : ($arrResponse['rs'] ? "Salvo com Sucesso!" : "Erro ao tentar salvar.");
			}
			else{
				$arrResponse['rs'] = -1;
				$arrResponse['msg'] = "Não atenticado ! Faça seu Login.";
			}

			echo json_encode($arrResponse, JSON_NUMERIC_CHECK);
		}
		break;
	}
	case 'GET':{
		$arrResponse =  array('rs'=>false, 'msg'=>'');
		$_RECV = Utils::receiveAjaxData('GET');

		if(isset($_RECV['key']) && $_RECV['key'] == 'PJI310'){
			$id = isset($_RECV['id']) && $_RECV['id']!='' ? intval($_RECV['id']) : 0;

			$rs = $id > 0 ? Paciente::getPaciente($id) : Paciente::listarPacientes();

			echo json_encode($rs, JSON_NUMERIC_CHECK);
		}
		break;
	}
	case 'POST':{
		$arrResponse =  array('rs'=>false, 'msg'=>'');
		$_RECV = Utils::receiveAjaxData('POST');

		if(isset($_RECV['key']) && $_RECV['key'] == 'PJI310'){
			$id = isset($_RECV['id']) && $_RECV['id']!='' ? intval($_RECV['id']) : 0;
			$err=false;

			if($id > 0){
				$rs = Paciente::loginPaciente($_RECV);
				$u = $rs!=null && is_numeric($rs) ? new Paciente($rs) : array();
				$data = !empty($u) ? array('id'=>$u->getId(),'tipo'=>'P',$u->getNome(), 'data_cadastro'=>date('d/m/Y', strtotime($u->getDataCadastro()))) : array();

				$arrResponse['rs'] = !empty($u);
				$arrResponse['msg'] = is_string($rs) ? $rs : ($arrResponse['rs'] ? "Login com Sucesso!" : "Erro ao tentar fazer login.");
				$arrResponse['data'] = json_encode($data,JSON_NUMERIC_CHECK);
			}
			else if(!isset($_RECV['id'])){
				$rs = Paciente::cadastrarPaciente($_RECV);

				$arrResponse['rs'] = $rs===true;
				$arrResponse['msg'] = is_string($rs) ? $rs : ($arrResponse['rs'] ? "Cadastrado com Sucesso!" : "Erro ao tentar cadastrar.");
			}
			else{
				$err=true;
			}

			if(!$err) echo json_encode($arrResponse, JSON_NUMERIC_CHECK);
		}
		break;
	}
	default:{}
}
?>