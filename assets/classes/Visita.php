<?php
	if(!session_id()){ session_start(); }	
	if(!class_exists('Utils')) require 'Utils.php';	
	if(!class_exists('Sql')) require 'Sql.php';
	if(!class_exists('Agente')) require 'Agente.php';

	class Visita{
		private $id;
		private $status;
		private $data_visita;
		private $data_cadastro;

		public function __construct($id=null){
			$Sql = new Sql();

			$data = $id!=null && $id>0 ? $Sql->select1("SELECT * FROM sci_visitas WHERE fkPaciente = {$id} ORDER BY data_visita ASC;") : array();
			foreach(($data!=null ? $data : array()) as $key => $val){
				switch($key){
					case 'codVis':{
						$this->id = $val;
						break;
					}
					case 'status':{
						$this->status = $val;
						break;
					}
					case 'data_visita':{
						$this->data_visita = $val;
						break;
					}
					case 'data_cadastro':{
						$this->data_cadastro = $val;
						break;
					}
				}
			}
		}

		public function apagarVisita($agente, $data){
			if($agente!=null && $agente->getPerfil()!=''){				
				$id = isset($_RECV['id']) && $_RECV['id']!='' ? intval($_RECV['id']) : 0;

				if($id>0){
					$Sql = new Sql();
					
					$querySql ="DELETE FROM sci_visitas WHERE codVis = {$id}";

					return $Sql->update($querySql);
				}
				else return "ID Invalido !";
			}
			else return "Não tem permissão !";
		}

		public function alterarDados($agente, $data){
			if($agente!=null && $agente->getPerfil()!=''){				
				$id = isset($_RECV['id']) && $_RECV['id']!='' ? intval($_RECV['id']) : 0;

				if($id>0){
					$Sql = new Sql();

					$status = isset($data['status']) ? strtoupper($data['status']) : null;
					$data_visita = isset($data['data_visita']) && $data['data_visita']!='' ? trim($data['data_visita']) : null;

					$whereAdd ='';			
					$whereAdd.= $status!=null ? ", status = '{$status}'" : '';
					$whereAdd.= $data_visita!=null ? ", data_visita = '{$data_visita}'" : '';

					$querySql ="UPDATE sci_visitas SET data_cadastro = data_cadastro {$whereAdd} WHERE codVis = {$id}";

					return $whereAdd!='' ? $Sql->update($querySql) : true;
				}
				else return "ID Invalido !";
			}
			else return "Não tem permissão !";
		}

		public static function cadastrarVisita($agente, $data){
			if($agente!=null && $agente->getPerfil()!=''){
				$Sql = new Sql();

				$arr=array();
				$arr['status'] = 'CRIADA';
				$arr['data_visita'] = isset($data['data_visita']) && $data['data_visita']!='' ? trim($data['data_visita']) : null;

				$arr['fkPaciente'] = isset($data['paciente']) ? intval($data['paciente']) : null;
				$arr['fkAgente'] = $agente->getId();

				if($arr['data_visita']!=null && $arr['fkPaciente']!=null && $arr['fkAgente']!=null){

					return $Sql->newInstance('sci_visitas', $arr);
				}
				else return "Todos os campos são obrigatórios !";
			}
			else return "Não tem permissão !";
		}

		public static function getVisita($id=null){
			$Sql = new Sql();
			$id = $id!=null && $id!='' ? $id : 0;

			if($id>0){
				$querySql ="SELECT * FROM sci_visitas WHERE fkPaciente = {$id} ORDER BY codVis DESC;";

				return $Sql->select1($querySql);
			}
			else return "ID inválido !";
		}

		public static function listarVisitas(){
			$Sql = new Sql();

			$querySql ="SELECT * FROM sci_visitas WHERE codVis>0 ORDER BY codVis DESC;";
			return $Sql->select($querySql);
		}

		public function getId(){
				return $this->id;
		}

		public function setId($id){
				$this->id = $id;
		}

		public function getStatus(){
				return $this->status;
		}

		public function setStatus($status){
				$this->status = $status;
		}

		public function getDataVisita(){
				return $this->data_visita;
		}

		public function setDataVisita($data){
				$this->data_visita = $data;
		}

		public function getDataCadastro(){
				return $this->data_cadastro;
		}

		public function setDataCadastro($data){
				$this->data_cadastro = $data;
		}
	}
switch($_SERVER['REQUEST_METHOD']){
    case 'DELETE':{
		$arrResponse =  array('rs'=>false, 'msg'=>'');
		$_RECV = Utils::receiveAjaxData('DELETE');

		if(isset($_RECV['key']) && $_RECV['key'] == 'PJI310'){
			$id = isset($_RECV['idAgente']) && $_RECV['idAgente']!='' ? intval($_RECV['idAgente']) : 0;
			$u = new Agente($id);

			if(!empty($u)){
				$rs = Visita::apagarVisita($u, $_RECV);

				$arrResponse['rs'] = $rs===true;
				$arrResponse['msg'] = is_string($rs) ? $rs : ($arrResponse['rs'] ? "Apagado com Sucesso!" : "Erro ao tentar apagar.");
			}
			else{
				$arrResponse['rs'] = -1;
				$arrResponse['msg'] = "Não atenticado ! Faça seu Login.";
			}

			echo json_encode($arrResponse, JSON_NUMERIC_CHECK);
		}
		break;
	}
	case 'PUT':{
		$arrResponse =  array('rs'=>false, 'msg'=>'');
		$_RECV = Utils::receiveAjaxData('PUT');

		if(isset($_RECV['key']) && $_RECV['key'] == 'PJI310'){
			$id = isset($_RECV['idAgente']) && $_RECV['idAgente']!='' ? intval($_RECV['idAgente']) : 0;
			$u = new Agente($id);

			if(!empty($u)){
				$rs = Visita::alterarDados($u, $_RECV);

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

			$rs = $id > 0 ? Visita::getVisita($id) : Visita::listarVisitas();

			echo json_encode($rs, JSON_NUMERIC_CHECK);
		}
		break;
	}
	case 'POST':{
		$arrResponse =  array('rs'=>false, 'msg'=>'');
		$_RECV = Utils::receiveAjaxData('POST');

		if(isset($_RECV['key']) && $_RECV['key'] == 'PJI310'){
			$id = isset($_RECV['idAgente']) && $_RECV['idAgente']!='' ? intval($_RECV['idAgente']) : 0;
			$u = new Agente($id);

			if(!empty($u)){
				$rs = Visita::cadastrarVisita($u, $_RECV);

				$arrResponse['rs'] = $rs===true;
				$arrResponse['msg'] = is_string($rs) ? $rs : ($arrResponse['rs'] ? "Cadastrado com Sucesso!" : "Erro ao tentar cadastrar.");
			}
			else{
				$arrResponse['rs'] = -1;
				$arrResponse['msg'] = "Não atenticado ! Faça seu Login.";
			}
			
			echo json_encode($arrResponse, JSON_NUMERIC_CHECK);
		}
		break;
	}
	default:{}
}
?>
