<?php
	require dirname(__FILE__).DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Utils.php';
	require dirname(__FILE__).DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Agente.php';
	require dirname(__FILE__).DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Paciente.php';

$_RECV = Utils::receiveAjaxData('GET');

if(isset($_RECV['key']) && $_RECV['key']=='PJI310'){
	$acao = isset($_RECV['a']) && $_RECV['a']!='' ? $_RECV['a'] : null;
	$arrReturn = array('rs'=>false, 'msg'=>'');
	$err=false;

	switch($acao){
		case 'wsLogin':{
			$login = isset($_RECV['login']) && $_RECV['login']!='' ? $_RECV['login'] : null;
			$senha = isset($_RECV['password']) && $_RECV['password']!='' ? $_RECV['password'] : null;
			$tipo = isset($_RECV['tipo']) && $_RECV['tipo']!='' ? $_RECV['tipo'] : null;

			if($login!=null && $senha!=null && $tipo!=null){
				if($tipo=='Servidor'){
					$rsLogin = Agente::login(__FILE__, $login, $senha, true);
					$msgErro = is_string($rsLogin) ? $rsLogin : "Senha ou login incorretos !";

					if(is_bool($rsLogin) && $rsLogin===true){
						$agente = Agente::auth(__FILE__, true);

						$arrReturn['rs'] = 'OK';
						$data = array('id'=>$agente['codUser'],'tipo'=>'A','nome'=>$agente['nome'], 'data_cadastro'=>date('d/m/Y', strtotime($agente['data_cadastro'])));
						$arrReturn['data'] = json_encode($data,JSON_NUMERIC_CHECK);
					}
					else{	$arrReturn['msg'] = $msgErro;	}
				}
				if($tipo=='Paciente'){
					$rsLogin = Paciente::login(__FILE__, $login, $senha, true);
					$msgErro = is_string($rsLogin) ? $rsLogin : "Senha ou login incorretos !";

					if(is_bool($rsLogin) && $rsLogin===true){
						$paciente = Paciente::auth(__FILE__, true);

						$arrReturn['rs'] = 'OK';
						$data = array('id'=>$paciente['codPac'],'tipo'=>'P','nome'=>$paciente['nome'], 'data_cadastro'=>date('d/m/Y', strtotime($paciente['data_cadastro'])));
						$arrReturn['data'] = json_encode($data,JSON_NUMERIC_CHECK);
					}
					else{	$arrReturn['msg'] = $msgErro;	}
				} else{	$arrReturn['msg'] = "Erro no tipo de login.";	}
			} else{
				$arrReturn['msg'] = 'Preencha todos os campos !';
			}

			break;
		}

		default:{ $arrReturn['msg']='Ação Desconhecida'; }
	}

	if(!$err) echo json_encode($arrReturn);
}
?>