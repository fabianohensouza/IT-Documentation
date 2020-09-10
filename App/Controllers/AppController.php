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
	
	public function usuariosAdicionar() {

		$this->validaAutenticacao();

		if($_SESSION['permissao'] == 'Administrador') {
			
			$usuarios = Container::getModel('Usuario');
			$cooperativas = Container::getModel('Cooperativa');

			if(isset($_GET['id'])) {

				$usuarios->__set('id_usuario', $_GET['id']);
				$this->view->usuarios = $usuarios->usuarioPorId();
				
			}    

			$this->view->cooperativas = $cooperativas->codigoCooperativas();

			$this->render('usuarios-adicionar.phtml');
		}

		$this->render('main.phtml');
	}
	
	public function usuarioAlterar() {

		$this->validaAutenticacao();

		if($_SESSION['permissao'] == 'Administrador') {

			$usuarios = Container::getModel('Usuario');

			$usuarios->__set('id_usuario', $_POST['id_usuario']);
			$usuarios->__set('email', $_POST['email']);
			$usuarios->__set('nome', $_POST['nome']);
			$usuarios->__set('login', $_POST['login']);
			$usuarios->__set('senha', md5($_POST['senha']));
			$usuarios->__set('cooperativa', $_POST['cooperativa']);
			$usuarios->__set('equipe', $_POST['equipe']);
			$usuarios->__set('permissao', $_POST['permissao']);

			if($usuarios->validarCadastro()) {
				
				if(count($usuarios->usuarioPorEmail()) == 0) {

					$usuarios->alterarUsuario('inserir');

					header('Location: /usuarios?mensagem=sucesso');
					
				} elseif($_GET['acao'] == 'alterar') {

					$usuarios->alterarUsuario('alterar');

					header('Location: /usuarios?mensagem=alterado');
					
				} elseif($_GET['acao'] == 'deletar') {

					$usuarios->alterarUsuario('deletar');

					header('Location: /usuarios?mensagem=deletado');
					
				} else {
	
					$this->view->usuarios = array(
						'mensagem' => 'duplicado',
						'id_usuario' => $_POST['id_usuario'],
						'nome' => $_POST['nome'],
						'email' => $_POST['email'],
						'login' => $_POST['login'],
						'senha' => $_POST['senha'],
						'cooperativa' => $_POST['cooperativa'],
						'equipe' => $_POST['equipe'],
						'permissao' => $_POST['permissao']
					);
					
					$this->render('usuarios-adicionar.phtml');
				}
	
			} else {
	
				$this->view->usuarios = array(
					'mensagem' => 'erro',
					'id_usuario' => $_POST['id_usuario'],
					'nome' => $_POST['nome'],
					'email' => $_POST['email'],
					'login' => $_POST['login'],
					'senha' => $_POST['senha'],
					'cooperativa' => $_POST['cooperativa'],
					'equipe' => $_POST['equipe'],
					'permissao' => $_POST['permissao']
				);
				
				$this->render('usuarios-adicionar.phtml');
			}

		}

	}
	
	public function cooperativasAdicionar() {

		$this->validaAutenticacao();

		if($_SESSION['permissao'] == 'Administrador') {
			
			$cooperativas = Container::getModel('Cooperativa'); 

			if(isset($_GET['id'])) {

				$cooperativas->__set('codigo_coop', $_GET['id']);
				$this->view->cooperativas = $cooperativas->CoopPorCod();
				
			} 

			//$this->view->cidades = $cooperativas->cidadesMG();
			$this->view->cooperativas['cidades'] = $cooperativas->cidadesMG();

			$this->render('cooperativas-adicionar.phtml');
		}

		$this->render('main.phtml');
	}
	
	public function cooperativas() {

		$this->validaAutenticacao();

		$cooperativas = Container::getModel('Cooperativa');
		$this->view->cooperativas = $cooperativas->todasCooperativas();

		$this->render('cooperativas.phtml');
	}
	
	public function cooperativaAlterar() {

		$this->validaAutenticacao();

		if($_SESSION['permissao'] == 'Administrador') {

			$usuarios = Container::getModel('Cooperativa');

			$usuarios->__set('codigo_coop', $_POST['codigo_coop']);
			$usuarios->__set('nome', $_POST['nome']);
			$usuarios->__set('nome_cidade', $_POST['nome_cidade']);
			$usuarios->__set('infracredis', $_POST['infracredis']);
			$usuarios->__set('resp_ic', $_POST['resp_ic']);
			$usuarios->__set('qtd_usuarios', $_POST['qtd_usuarios']);
			$usuarios->__set('qtd_equip', $_POST['qtd_equip']);
			$usuarios->__set('adesao', $_POST['adesao']);

			if($usuarios->validarCadastro()) {
				
				if(count($usuarios->usuarioPorEmail()) == 0) {

					$usuarios->alterarUsuario('inserir');

					header('Location: /usuarios?mensagem=sucesso');
					
				} elseif($_GET['acao'] == 'alterar') {

					$usuarios->alterarUsuario('alterar');

					header('Location: /usuarios?mensagem=alterado');
					
				} elseif($_GET['acao'] == 'deletar') {

					$usuarios->alterarUsuario('deletar');

					header('Location: /usuarios?mensagem=deletado');
					
				} else {
	
					$this->view->usuarios = array(
						'mensagem' => 'duplicado',
						'id_usuario' => $_POST['id_usuario'],
						'nome' => $_POST['nome'],
						'email' => $_POST['email'],
						'login' => $_POST['login'],
						'senha' => $_POST['senha'],
						'cooperativa' => $_POST['cooperativa'],
						'equipe' => $_POST['equipe'],
						'permissao' => $_POST['permissao']
					);
					
					$this->render('usuarios-adicionar.phtml');
				}
	
			} else {
	
				$this->view->usuarios = array(
					'mensagem' => 'erro',
					'id_usuario' => $_POST['id_usuario'],
					'nome' => $_POST['nome'],
					'email' => $_POST['email'],
					'login' => $_POST['login'],
					'senha' => $_POST['senha'],
					'cooperativa' => $_POST['cooperativa'],
					'equipe' => $_POST['equipe'],
					'permissao' => $_POST['permissao']
				);
				
				$this->render('usuarios-adicionar.phtml');
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