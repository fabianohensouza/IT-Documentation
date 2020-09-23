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

		$routes['authenticate'] = array(
			'route' => '/authenticate',
			'controller' => 'AuthController',
			'action' => 'authenticate'
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

		$routes['resetar_senha'] = array(
			'route' => '/resetar_senha',
			'controller' => 'AppController',
			'action' => 'resetarSenha'
		);

		$routes['senha_alterar'] = array(
			'route' => '/senha_alterar',
			'controller' => 'AppController',
			'action' => 'senhaAlterar'
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

		$routes['pas'] = array(
			'route' => '/pas',
			'controller' => 'AppController',
			'action' => 'pas'
		);

		$routes['pas-adicionar'] = array(
			'route' => '/pas-adicionar',
			'controller' => 'AppController',
			'action' => 'pasAdicionar'
		);

		$routes['pa_alterar'] = array(
			'route' => '/pa_alterar',
			'controller' => 'AppController',
			'action' => 'paAlterar'
		);

		$routes['rateio'] = array(
			'route' => '/rateio',
			'controller' => 'AppController',
			'action' => 'rateio'
		);

		$this->setRoutes($routes);
	}

}

?>