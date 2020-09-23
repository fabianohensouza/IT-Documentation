<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AuthController extends Action {

	public function authenticate() {

		$usuario = Container::getModel('Usuario');

		$usuario->__set('login', $_POST['login']);
		$usuario->__set('senha', md5($_POST['senha']));

		$usuario->authenticate();

		echo $usuario->__get('id_usuario') . ' / ' . $usuario->__get('nome');

		if($usuario->__get('id_usuario') != '' && $usuario->__get('nome') != '') {
			
			session_start();

			$_SESSION['id_usuario'] = $usuario->__get('id_usuario');
			$_SESSION['nome'] = $usuario->__get('nome');
			$_SESSION['permissao'] = $usuario->__get('permissao');
			$_SESSION['cooperativa'] = $usuario->__get('cooperativa');
			$_SESSION['login'] = $usuario->__get('login');

			header('Location: /main');

		} else {
			header('Location: /?login=erro');
		}

	}

	public function logout() {

		session_start();
		session_destroy();
		header('Location: /');

	}

}


?>