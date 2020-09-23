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

	public function stringToArray($string) {

		$finalArray = array();

		$string = str_replace( "'", "", $string );

		$asArr = explode( ',', $string );

		foreach( $asArr as $val ){
			$tmp = explode( '=>', $val );
			$finalArray[ $tmp[0] ] = $tmp[1];
		}
		
		return $finalArray;
	}

	public function arrayToString($array) {

		$finalString = implode(',', array_map(
			function ($v, $k) {
				if(is_array($v)){
					return $k."[]=>".implode("&".$k."[]=>", $v);
				}else{
					return $k."=>".$v;
				}
			}, 
			$array, 
			array_keys($array)
		));
		
		return $finalString;
	}
	
	public function usuarios() {

		$this->validaAutenticacao();

		$usuarios = Container::getModel('Usuario');
		$this->view->usuarios = $usuarios->todosUsuarios();

		$this->render('usuarios.phtml');
	}
	
	public function resetarSenha() {

		$this->validaAutenticacao();

		$this->render('resetar_senha.phtml');
	}
	
	public function senhaAlterar() {

		$this->validaAutenticacao();

		$usuario = Container::getModel('Usuario');

		$usuario->__set('id_usuario', $_SESSION['id_usuario']);
		$usuario->__set('login', $_SESSION['login']);
		$usuario->__set('senha', md5($_POST['senha']));
		$usuario->__set('senha_nova1', md5($_POST['senha_nova1']));
		$usuario->__set('senha_nova2', md5($_POST['senha_nova2']));

		$valida = $usuario->validaSenha();

		if ($valida['valida'] == 0) {

			header('Location: /resetar_senha?mensagem=senhaincorreta');

		} elseif ($_POST['senha_nova1'] != $_POST['senha_nova2']) {

			header('Location: /resetar_senha?mensagem=naoconfere');

		} else {

			$usuario->alterarSenha();
			header('Location: /resetar_senha?mensagem=sucesso');

		}
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

					if($_POST['senha'] == "") {

						$usuarios->alterarUsuario('alterarsemsenha');

					}else {
						
						$usuarios->alterarUsuario('alterarcomsenha');

					}

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
			$usuarios = Container::getModel('Usuario');

			if(isset($_GET['id'])) {

				$cooperativas->__set('codigo_coop', $_GET['id']);
				$this->view->cooperativas = $cooperativas->CoopPorCod();
				
			} 

			//$this->view->cidades = $cooperativas->cidadesMG();
			$this->view->cooperativas['cidades'] = $cooperativas->cidadesMG();
			$this->view->cooperativas['equipeic'] = $usuarios->todosUsuariosIC();

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
			$usuarios->__set('resp_ic', $_POST['resp_ic']);
			$usuarios->__set('infracredis', $_POST['infracredis']);
			$usuarios->__set('nome_cidade', $_POST['nome_cidade']);
			$usuarios->__set('qtd_usuarios', $_POST['qtd_usuarios']);
			$usuarios->__set('qtd_equip', $_POST['qtd_equip']);
			$usuarios->__set('adesao', $_POST['adesao']);
			$usuarios->__set('diretoria', implode(",",$_POST['diretoria']));
			$usuarios->__set('resp_ti', implode(",",$_POST['resp_ti']));

			if(count($usuarios->CoopPorCod()) == 0) {

				$usuarios->alterarCooperativa('inserir');

				header('Location: /cooperativas?mensagem=sucesso');
					
			} elseif($_GET['acao'] == 'alterar') {

				$usuarios->alterarCooperativa('alterar');

				header('Location: /cooperativas?mensagem=alterado');
					
			} elseif($_GET['acao'] == 'deletar') {

				$usuarios->alterarCooperativa('deletar');

				header('Location: /cooperativas?mensagem=deletado');
					
			} else {
	
				$this->view->usuarios = array(
					'mensagem' => 'duplicado',
					'codigo_coop' => $_POST['codigo_coop'],
					'nome' => $_POST['nome'],
					'resp_ic' => $_POST['resp_ic'],
					'infracredis' => $_POST['infracredis'],
					'nome_cidade' => $_POST['nome_cidade'],
					'qtd_usuarios' => $_POST['qtd_usuarios'],
					'qtd_equip' => $_POST['qtd_equip'],
					'adesao' => $_POST['adesao'],
					'diretoria' => $_POST['diretoria'],
					'resp_ti' => $_POST['resp_ti']
			);
					
				$this->render('usuarios-adicionar.phtml');
			}
				
				$this->render('usuarios-adicionar.phtml');
		}

	}
	
	public function pas() {
		
		$this->validaAutenticacao();

		$pas = Container::getModel('Pa');
		$this->view->pas = $pas->todosPas();

		for($i = 0; $i < count($this->view->pas); $i++) {

			for($x = 0; $x <= 5; $x++) {

				$link_x = $this->view->pas[$i]["link_x" . $x];
				if($link_x != "") {
					$this->view->pas[$i]["link_x" . $x] = $this->stringToArray($link_x);
				}
			}
		
		}
		
		$this->render('pas.phtml');
	}
	
	public function pasAdicionar() {

		$this->validaAutenticacao();

		if($_SESSION['permissao'] == 'Administrador') {
			
			$pas = Container::getModel('Pa');
			$cooperativas = Container::getModel('Cooperativa'); 

			if(isset($_GET['id'])) {

				$pas->__set('id_pa', $_GET['id']);
				$this->view->pas = $pas->paPorId();
				
				for($x = 0; $x <= 5; $x++) {

					$link_x = $this->view->pas["link_x" . $x];
					if($link_x != "") {
						$this->view->pas["link_x" . $x] = $this->stringToArray($link_x);
					}
				}
				
			} 

			$this->view->pas['cidades'] = $cooperativas->cidadesMG();

			$this->render('pas-adicionar.phtml');
		}

		$this->render('main.phtml');
	}
	
	public function paAlterar() {

		$this->validaAutenticacao();

		for($i=0; $i <= 5; $i++) { 

			$idx = "link_x" . $i;
			$_POST[$idx] = $this->arrayToString($_POST[$idx]);
			
		}

		if(!isset($_POST['id_pa'])) {
			$_POST['id_pa'] = $_POST['coop'] . $_POST['codigo_pa'];
		}

		if($_SESSION['permissao'] == 'Administrador') {

			$pas = Container::getModel('Pa');

			$pas->__set('id_pa', $_POST['id_pa']);
			$pas->__set('coop', $_POST['coop']);
			$pas->__set('codigo_pa', $_POST['codigo_pa']);
			$pas->__set('nome_cidade', $_POST['nome_cidade']);
			$pas->__set('tipo_pa', $_POST['tipo_pa']);
			$pas->__set('firewall', $_POST['firewall']);

			for($i=0; $i <= 5; $i++) { 
				$pas->__set('link_x' . $i, $_POST['link_x' . $i]);
			}

			echo "<pre>";
			print_r($_POST);
			echo "</pre>";

			if(count($pas->paPorId()) == 0) {

				$pas->alterarPa('inserir');

				header('Location: /pas?mensagem=sucesso');
					
			} elseif($_GET['acao'] == 'alterar') {

				$pas->alterarPa('alterar');

				header('Location: /pas?mensagem=alterado');
					
			} elseif($_GET['acao'] == 'deletar') {

				$pas->alterarPa('deletar');

				header('Location: /pas?mensagem=deletado');
					
			} else {
	
				$this->view->pas = array(
					'mensagem' => 'duplicado',
					'id_pa' => $_POST['id_pa'],
					'coop' => $_POST['coop'],
					'codigo_pa' => $_POST['codigo_pa'],
					'nome_cidade' => $_POST['nome_cidade'],
					'tipo_pa' => $_POST['tipo_pa'],
					'firewall' => $_POST['firewall']
				);
			
			for($i=0; $i <= 5; $i++) { 
					$this->view->pas = array('link_x' . $i => $_POST['link_x' . $i],);
			}
				$this->render('pas-adicionar.phtml');
			}
				
				$this->render('pas-adicionar.phtml');
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