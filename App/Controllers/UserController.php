<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class UserController extends Action {

	public function usuarios() {

		$this->validaAutenticacao();

		/*$this->view->usuario = array(
			'permissao' => '',
			'nome' => '',
			'login' => '',
			'email' => '',
			'cooperativa' => '',
			'senha' => ''
		);

		$this->view->usuarioCadastrado = false;
		$this->view->erroCadastro = false;*/

		$this->render('usuarios.phtml');
	}

	public function validaAutenticacao() {

		session_start();

		if(!isset($_SESSION['id_usuario']) || $_SESSION['id_usuario'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			
			header('Location: /?login=erro');
		}
		
	}

	public function registrar() {

		$usuario = Container::getModel('Usuario');

		$usuario->__set('nome', $_POST['nome']);
		$usuario->__set('email', $_POST['email']);
		$usuario->__set('senha', md5($_POST['senha']));

		if($usuario->validarCadastro()) {

			if(count($usuario->getUsuarioPorEmail()) == 0) {
				
				$usuario->salvar();

				$this->render('cadastro');
				
			} else {

				$this->view->usuario = array(
					'nome' => $_POST['nome'],
					'email' => $_POST['email'],
					'senha' => $_POST['senha']
				);

				$this->view->usuarioCadastrado = true;
				
				$this->render('inscreverse');
			}

		} else {

			$this->view->usuario = array(
				'nome' => $_POST['nome'],
				'email' => $_POST['email'],
				'senha' => $_POST['senha']
			);

			$this->view->erroCadastro = true;
			
			$this->render('inscreverse');
		}

	}

}


?>