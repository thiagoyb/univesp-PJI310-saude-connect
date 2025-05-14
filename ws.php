<?php
	require dirname(__FILE__).DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'User.php';
	require dirname(__FILE__).DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Cidadao.php';

///$_RECV = isset($_GET) &&$_GET!=null && !empty($_GET) ? $_GET : array();//GET (testes)
$_RECV = isset($_POST) &&$_POST!=null && !empty($_POST) ? $_POST : array();//ou POST

if(isset($_RECV['key']) && $_RECV['key']=='PJI310'){
	$acao = isset($_RECV['a']) && $_RECV['a']!='' ? $_RECV['a'] : null;
	$arrReturn = array('rs'=>false, 'msg'=>'');
	$err=false;

	switch($acao){
		case 'wsLogin':{//OK 13/05/2025
			$login = isset($_RECV['login']) && $_RECV['login']!='' ? $_RECV['login'] : null;
			$senha = isset($_RECV['password']) && $_RECV['password']!='' ? $_RECV['password'] : null;
			$tipo = isset($_RECV['tipo']) && $_RECV['tipo']!='' ? $_RECV['tipo'] : null;

			if($login!=null && $senha!=null && $tipo!=null){
				if($tipo=='Servidor'){
					$rsLogin = User::login(__FILE__, $login, $senha, true);
					$msgErro = is_string($rsLogin) ? $rsLogin : "Senha ou login incorretos !";

					if(is_bool($rsLogin) && $rsLogin===true){
						$agente = User::auth(__FILE__, true);

						$arrReturn['rs'] = 'OK';
						$data = array('id'=>$agente['codUser'],'tipo'=>'S','nome'=>$agente['nome'], 'data_cadastro'=>date('d/m/Y', strtotime($agente['data_cadastro'])));
						$arrReturn['data'] = json_encode($data,JSON_NUMERIC_CHECK);
					}
					else{	$arrReturn['msg'] = $msgErro;	}
				} else{
					$rsLogin = Cidadao::login(__FILE__, $login, $senha, true);
					$msgErro = is_string($rsLogin) ? $rsLogin : "Senha ou login incorretos !";

					if(is_bool($rsLogin) && $rsLogin===true){
						$municipe = Cidadao::auth(__FILE__, true);

						$arrReturn['rs'] = 'OK';
						$data = array('id'=>$municipe['codPac'],'tipo'=>'M','nome'=>$municipe['nome'], 'data_cadastro'=>date('d/m/Y', strtotime($municipe['data_cadastro'])));
						$arrReturn['data'] = json_encode($data,JSON_NUMERIC_CHECK);
					}
					else{	$arrReturn['msg'] = $msgErro;	}
				}
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