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

		$routes['gerencia_usuarios'] = array(
			'route' => '/gerencia_usuarios',
			'controller' => 'AppController',
			'action' => 'gerenciaUsuarios'
		);

		$routes['salvar_usuario'] = array(
			'route' => '/salvar_usuario',
			'controller' => 'AppController',
			'action' => 'salvarUsuarios'
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