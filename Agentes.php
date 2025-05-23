<?php
	if(!session_id()){ session_start(); }	
	if(!class_exists('Utils')) require './assets/classes/Utils.php';	
	if(!class_exists('Sql')) require './assets/classes/Sql.php';

	class Agentes{
		private $id;
		private $nome;
		private $email;
		private $senha;
		private $posto;
		private $perfil;
		private $data_cadastro;

		public function __construct($id=null){
			$Sql = new Sql();

			$data = $id!=null && $id>0 ? $Sql->select1("SELECT * FROM sci_agente_saude WHERE ativado=1 AND codUser = {$id} ORDER BY 1 DESC LIMIT 1;") : array();
			foreach(($data!=null ? $data : array()) as $key => $val){
				switch($key){
					case 'codUser':{
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
					case 'posto':{
						$this->posto = $val;
						break;
					}
					case 'perfil':{
						$this->perfil = $val;
						break;
					}
					case 'data_cadastro':{
						$this->data_cadastro = $val;
						break;
					}
				}
			}
		}

		public static function cadastrarAgente($data){
			$Sql = new Sql();

			$arr=array();
			$arr['nome'] = isset($data['nome']) && $data['nome']!='' ? strtoupper($data['nome']) : null;
			$arr['cpf'] = isset($data['cpf']) && Utils::isCPF($data['cpf']) ? Utils::soNumeros($data['cpf']) : null;
			$arr['senha'] = isset($data['senha']) && $data['senha']!='' ? md5($data['senha']) : md5($arr['cpf']);
			$arr['email'] = isset($data['email']) && $data['email']!='' ? strtolower($data['email']) : null;
			$arr['perfil'] = isset($data['perfil']) && $data['perfil']!='' ? strtoupper($data['perfil']) : 'ACS';

			if($arr['nome']!=null && $arr['email']!=null && $arr['cpf']!=null){

				return $Sql->newInstance('sci_agente_saude', $arr);
			}
			else return "Todos os campos são obrigatórios !";
		}

		public static function loginAgente($data){
			$Sql = new Sql();

			$login = isset($data['login']) ? Utils::soNumeros($data['login']) : null;
			$senha = isset($data['senha']) && $data['senha']!='' ? $data['senha'] : null;

			if($login!=null && $senha!=null){
				if(Utils::isCPF($login)){
					$querySql = "SELECT * FROM sci_agente_saude WHERE ativado=1 AND cpf = '{$login}' AND senha = md5('{$senha}');";
					$rs = $Sql->select1($querySql);

					return $rs!=null && isset($rs['codUser']) ? $rs['codUser'] : false;
				}
				else return "CPF inválido !";
			}
			else return "Login ou senha inválidos.";
		}

		public static function getAgente($id=null){
			$Sql = new Sql();
			$id = $id!=null && $id!='' ? $id : 0;

			if($id>0){
				$querySql ="SELECT * FROM sci_agente_saude WHERE codUser = {$id} ORDER BY codUser DESC;";

				return $Sql->select($querySql);
			}
			else return "ID inválido !";
		}

		public static function listarAgentes(){
			$Sql = new Sql();

			$querySql ="SELECT * FROM sci_agente_saude WHERE codUser>0 ORDER BY codUser DESC;";
			return $Sql->select($querySql);
		}

		public function alterarSenha($data){
			$Sql = new Sql();

			$senha1 = isset($data['senha1']) ? md5($data['senha1']) : null;
			$senha2 = isset($data['senha2']) ? md5($data['senha2']) : null;
			$senhaAtual = isset($data['senha']) ? md5($data['senha']) : null;

			if($senha1!=null && $senha1 == $senha2){
				if($this->getSenha() == $senhaAtual){
					$id = $this->getId();

					$querySql ="UPDATE sci_agente_saude SET senha = '{$senha1}' WHERE codUser = {$id}";

					return $Sql->update($querySql);
				}
				else return 'Senha antiga não confere.';
			}
			else return 'As novas senhas devem ser iguais.';
		}

		public function alterarDados($data){
			$Sql = new Sql();

			$nome = isset($data['nome']) ? strtoupper($data['nome']) : null;
			$email = isset($data['email']) ? strtolower($data['email']) : null;
			$posto = isset($data['posto']) ? intval($data['posto']) : null;

			$whereAdd ='';
			$whereAdd.= $nome!=null ? ", nome = '{$nome}'" : '';
			$whereAdd.= $email!=null ? ", email = '{$email}'" : '';
			$whereAdd.= $posto!=null ? ", fkPosto = {$posto}" : '';

			$id = $this->getId();
			$querySql ="UPDATE sci_agente_saude SET data_cadastro = data_cadastro {$whereAdd} WHERE codUser = {$id}";

			return $whereAdd!='' ? $Sql->update($querySql) : true;
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

		public function getPosto(){
				return $this->posto;
		}

		public function setPosto($posto){
				$this->posto = $posto;
		}

		public function getPerfil(){
				return $this->perfil;
		}

		public function setPerfil($perfil){
				$this->perfil = $perfil;
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
			$u = new Agentes($id);

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

			$rs = $id > 0 ? Agentes::getAgente($id) : Agentes::listarAgentes();

			if(strpos($_SERVER['REQUEST_URI'],'Agentes.php')!==false) echo json_encode($rs, JSON_NUMERIC_CHECK);
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
				$rs = Agentes::loginAgente($_RECV);
				$u = $rs!=null && is_numeric($rs) ? new Agentes($rs) : array();
				$data = !empty($u) ? array('id'=>$u->getId(),'tipo'=>'A',$u->getNome(), 'data_cadastro'=>date('d/m/Y', strtotime($u->getDataCadastro()))) : array();

				$arrResponse['rs'] = !empty($u);
				$arrResponse['msg'] = is_string($rs) ? $rs : ($arrResponse['rs'] ? "Login com Sucesso!" : "Erro ao tentar fazer login.");
				$arrResponse['data'] = json_encode($data,JSON_NUMERIC_CHECK);
			}
			else if(!isset($_RECV['id'])){
				$rs = Agentes::cadastrarAgente($_RECV);

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