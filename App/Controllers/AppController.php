<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {

	public function timeline() {

		$this->validaAutenticacao();

		$tweet = Container::getModel('Tweet');
		$tweet->__set('id_usuarios', $_SESSION['id']);

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		//Variaveis de páginação
		$total_tweets_pagina = 10;

		//if ($_GET['pagina']) ? $this->view->deslocamento = $_GET['pagina'] : $this->view->deslocamento = 0;
		$this->view->pagina = (isset($_GET['pagina'])) ? $_GET['pagina'] : 1;
		$deslocamento = ($this->view->pagina - 1) * $total_tweets_pagina;

		//$this->view->tweets = $tweet->getAll();
		$this->view->tweets = $tweet->getPorPagina($total_tweets_pagina, $deslocamento);
		$total_tweets = $tweet->getTotalTweets();
		$this->view->total_paginas = ceil($total_tweets['getTotalTweets'] / $total_tweets_pagina);

		$this->view->nome = $usuario->getInfoUsuario();
		$this->view->total_tweets = $usuario->getTotaltweets();
		$this->view->seguindo = $usuario->getTotalSeguindo();
		$this->view->seguidores = $usuario->getTotalSeguidores();

		$this->render('timeline');
	}

	public function tweet() {

		$this->validaAutenticacao();

		$tweet = Container::getModel('Tweet');

		$tweet->__set('tweet', $_POST['tweet']);
		$tweet->__set('id_usuarios', $_SESSION['id']);

		$tweet->salvar();

		header('Location: /timeline');

		
	}

	public function validaAutenticacao() {

		session_start();

		if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			header('Location: /?login=erro');
		}

		
	}

	public function quemSeguir() {

		$this->validaAutenticacao();

		$pesquisar = isset($_GET['pesquisar']) ? $_GET['pesquisar'] : '';
		$_SESSION['pesquisar'] = $pesquisar;

		$usuarios = array();

		if($pesquisar != '') {

			$usuario = Container::getModel('Usuario');
			$usuario->__set('nome', $pesquisar);
			$usuario->__set('id', $_SESSION['id']);
			$usuarios = $usuario->getAll();
		}

		$this->view->usuarios = $usuarios;

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

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
		$usuario->__set('id', $_SESSION['id']);

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