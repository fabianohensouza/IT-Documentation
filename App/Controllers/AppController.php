<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {

	public function main() {

		$this->validaAutenticacao();

		$infoDashboard = Container::getModel('Dashboard');
		$this->view->dashboard = $infoDashboard->getInfoDashboard();

		$this->render('main.phtml');
	}
	
	public function usuarios() {

		$this->validaAutenticacao();

		$usuarios = Container::getModel('Usuario');
		$this->view->usuarios = $usuarios->todosUsuarios();

		$this->render('usuarios.phtml');
	}
	
	public function gerenciaUsuarios() {

		$this->validaAutenticacao();

		if($_SESSION['permissao'] == 'Administrador') {
			
			$usuarios = Container::getModel('Usuario');
			$cooperativas = Container::getModel('Cooperativa');

			$this->view->cooperativas = $cooperativas->codigoCooperativas();

			$this->render('gerencia_usuarios.phtml');
		}

		$this->render('main.phtml');
	}
	
	public function salvarUsuarios() {

		$this->validaAutenticacao();

		if($_SESSION['permissao'] == 'Administrador') {
			
			$usuario = Container::getModel('Usuario');

			$usuario->__set('nome', $_POST['nome']);
			$usuario->__set('email', $_POST['email']);
			$usuario->__set('login', $_POST['login']);
			$usuario->__set('senha', md5($_POST['senha']));
			$usuario->__set('cooperativa', $_POST['cooperativa']);
			$usuario->__set('equipe', $_POST['equipe']);
			$usuario->__set('permissao', $_POST['permissao']);

			if($usuario->validarCadastro()) {
	
				if(count($usuario->getUsuarioPorEmail()) == 0) {
					
					$usuario->salvar();
	
					$this->view->usuarioCadastrado = true;
	
					$this->render('gerencia_usuarios.phtml');
					
				} else {
	
					$this->view->usuario = array(
						'nome' => $_POST['nome'],
						'email' => $_POST['email'],
						'senha' => $_POST['senha']
					);
	
					$this->view->usuarioCadastrado = true;
					
					$this->render('gerencia_usuarios.phtml');
				}
	
			} else {
	
				$this->view->usuario = array(
					'nome' => $_POST['nome'],
					'email' => $_POST['email'],
					'senha' => $_POST['senha']
				);
	
				$this->view->erroCadastro = true;
				
				$this->render('gerencia_usuarios.phtml');
			

			}
		}
	}

	public function rateio() {

		$this->validaAutenticacao();

		$this->render('rateio.phtml');
	}

	public function validaAutenticacao() {

		session_start();

		if(!isset($_SESSION['id_usuario']) || $_SESSION['id_usuario'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			
			header('Location: /?login=erro');
		}
		
	}

	public function components() {

		$this->validaAutenticacao();

		$p = "echo 'Path: '";
		
		$this->render($p);
	}

	public function tweet() {

		$this->validaAutenticacao();

		$tweet = Container::getModel('Tweet');

		$tweet->__set('tweet', $_POST['tweet']);
		$tweet->__set('id_usuarios', $_SESSION['id_usuario']);

		$tweet->salvar();

		header('Location: /timeline');

		
	}

	public function quemSeguir() {

		$this->validaAutenticacao();

		$pesquisar = isset($_GET['pesquisar']) ? $_GET['pesquisar'] : '';
		$_SESSION['pesquisar'] = $pesquisar;

		$usuarios = array();

		if($pesquisar != '') {

			$usuario = Container::getModel('Usuario');
			$usuario->__set('nome', $pesquisar);
			$usuario->__set('id', $_SESSION['id_usuario']);
			$usuarios = $usuario->getAll();
		}

		$this->view->usuarios = $usuarios;

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id_usuario']);

		$this->view->nome = $usuario->getInfoUsuario();
		$this->view->total_tweets = $usuario->getTotaltweets();
		$this->view->seguindo = $usuario->getTotalSeguindo();
		$this->view->seguidores = $usuario->getTotalSeguidores();

		$this->render('quemSeguir');

	}

	public function acao() {

		$this->validaAutenticacao();

		$acao = isset($_GET['acao']) ? $_GET['acao']  : '';
		$id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario']  : '';
		$nome = isset($_SESSION['pesquisar']) ? $_SESSION['pesquisar']  : '';

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id_usuario']);

		if( $acao == 'seguir' ) {

			$usuario->seguirUsuario($id_usuario_seguindo);

		} else if ( $acao == 'deixar_de_seguir' ) {

			$usuario->deixarSeguirUsuario($id_usuario_seguindo);
			
		}

		//header('Location: /quem_seguir');
		header('Location: /quem_seguir?pesquisar='.$nome);
		
	}

}


?>