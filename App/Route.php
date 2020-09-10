<?php

namespace App;

use MF\Init\Bootstrap;

class Route extends Bootstrap {

	protected function initRoutes() {

		$routes['home'] = array(
			'route' => '/',
			'controller' => 'indexController',
			'action' => 'index'
		);

		$routes['logout'] = array(
			'route' => '/logout',
			'controller' => 'AuthController',
			'action' => 'logout'
		);

		$routes['main'] = array(
			'route' => '/main',
			'controller' => 'AppController',
			'action' => 'main'
		);

		$routes['usuarios'] = array(
			'route' => '/usuarios',
			'controller' => 'AppController',
			'action' => 'usuarios'
		);

		$routes['usuarios-adicionar'] = array(
			'route' => '/usuarios-adicionar',
			'controller' => 'AppController',
			'action' => 'usuariosAdicionar'
		);

		$routes['usuario_alterar'] = array(
			'route' => '/usuario_alterar',
			'controller' => 'AppController',
			'action' => 'usuarioAlterar'
		);

		$routes['cooperativas'] = array(
			'route' => '/cooperativas',
			'controller' => 'AppController',
			'action' => 'cooperativas'
		);

		$routes['cooperativas-adicionar'] = array(
			'route' => '/cooperativas-adicionar',
			'controller' => 'AppController',
			'action' => 'cooperativasAdicionar'
		);

		$routes['cooperativa_alterar'] = array(
			'route' => '/cooperativa_alterar',
			'controller' => 'AppController',
			'action' => 'cooperativaAlterar'
		);

		$routes['rateio'] = array(
			'route' => '/rateio',
			'controller' => 'AppController',
			'action' => 'rateio'
		);

		$routes['cadastro'] = array(
			'route' => '/cadastro',
			'controller' => 'indexController',
			'action' => 'cadastro'
		);

		$routes['registrar'] = array(
			'route' => '/registrar',
			'controller' => 'indexController',
			'action' => 'registrar'
		);

		$routes['authenticate'] = array(
			'route' => '/authenticate',
			'controller' => 'AuthController',
			'action' => 'authenticate'
		);

		$routes['tweet'] = array(
			'route' => '/tweet',
			'controller' => 'AppController',
			'action' => 'tweet'
		);

		$routes['quem_seguir'] = array(
			'route' => '/quem_seguir',
			'controller' => 'AppController',
			'action' => 'quemSeguir'
		);

		$routes['acao'] = array(
			'route' => '/acao',
			'controller' => 'AppController',
			'action' => 'acao'
		);

		$this->setRoutes($routes);
	}

}

?>