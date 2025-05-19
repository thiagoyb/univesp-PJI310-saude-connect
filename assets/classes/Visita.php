<?php
	if(!session_id()){ session_start(); }	
	if(!class_exists('Utils')) require 'Utils.php';	
	if(!class_exists('Sql')) require 'Sql.php';

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

		static function getURL($route=''){
			require dirname(__FILE__).DIRECTORY_SEPARATOR.'Config.php';

			$URL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://');
			$HOST = substr($Config['URL'], strpos($Config['URL'], '://')+3);

			return $URL.$HOST.$route;
		}
		static function getPATH(){
			return dirname(dirname(dirname(__FILE__)));
		}

		static function novaVisita($p=null, $data=null, $a=null){
			$Sql = new Sql();

			$p = Utils::soNumeros($p);
			$a = Utils::soNumeros($a);

			if($p!=null && $a!=null && $data!=null){
				if(checkdate(date('m',strtotime($data)), date('d',strtotime($data)), date('Y',strtotime($data)))){
					$querySql = "INSERT INTO sci_visitas (status, data_visita, fkPaciente, fkAgente) VALUES ('CRIADA', '{$data}', {$p}, {$a});";
					$rs = $Sql->query($querySql);
					return $rs ? $rs : "Erro ao salvar paciente.";
				} else{
					$msgError = "Data de Visita inválida !";
					setCookie("erro",$msgError);
				}
			} else{
				$msgError = "Paciente ou data inválida.";
				setCookie("erro",$msgError);
			}
		}

		static function getVisitas($id=null){
			$Sql = new Sql();

			$id = intval(Utils::soNumeros($id));

			if($id!=null && $id>0){
				$querySql = "SELECT * FROM sci_visitas WHERE fkPaciente = {$id} ORDER BY data_visita ASC;";
				
				return $Sql->select($querySql);
			} else{
			   return 'ID invalido !';
			}
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
    case 'PUT':{}
	case 'GET':{
		$arrResponse =  array('rs'=>false, 'msg'=>'');
		$params = isset($_GET) &&$_GET!=null && !empty($_GET) ? $_GET : array();

		if(isset($params['key']) && $params['key'] = 'PJI310'){

			$visitas = Visita::getVisitas(isset($params['id']) ? intval($params['id']) : null);
			$visitas = $visitas!=null ? $visitas : array();

			$arrResponse['rs'] = true;
			$arrResponse['msg'] = 'OK';
			$arrResponse['data'] = json_encode($visitas,JSON_NUMERIC_CHECK);

			echo json_encode($arrResponse, JSON_NUMERIC_CHECK);
		}
		break;
	}
	default:{}
}
?>
