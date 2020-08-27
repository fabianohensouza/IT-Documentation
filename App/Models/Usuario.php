<?php

namespace App\Models;

use MF\Model\Model;

class Usuario extends Model {

	private $id_usuario;
	private $nome;
	private $login;
	private $email;
	private $cooperativa;
	private $permissao;
	private $senha;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

	//Recuperar todos usuários
	public function todosUsuarios() {
		$query = "SELECT id_usuario, permissao, nome, login, email, cooperativa, equipe FROM usuarios";

		$stmt = $this->db->prepare($query);;
		$stmt->execute();

		
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

	//Salvar
	public function salvar() {

		$query = "insert into usuarios
					(nome, login, email, cooperativa, permissao, senha)
				  values
				  	(:nome, :login, :email, :cooperativa, :permissao, :senha)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':nome', $this->__get('nome'));
		$stmt->bindValue(':login', $this->__get('login'));
		$stmt->bindValue(':email', $this->__get('email'));
		$stmt->bindValue(':cooperativa', $this->__get('cooperativa'));
		$stmt->bindValue(':permissao', $this->__get('permissao'));
		$stmt->bindValue(':senha', $this->__get('senha'));
		$stmt->execute();

		return $this;
	}

	//Validar cadastro
	public function validarCadastro() {
		$valido = true;

		if(strlen($this->__get('nome')) < 3 ) {
			$valido = false;
		}

		if(strpos($this->__get('email'), '@') == False ) {
			$valido = false;
		}

		if(strlen($this->__get('email')) < 3) {
			$valido = false;
		}

		if(strlen($this->__get('senha')) < 3 ) {
			$valido = false;
		}

		return $valido;
	}

	//Validar cadastro
	public function authenticate() {
		$query = "select id_usuario, nome, permissao, email, cooperativa from usuarios where login = :login and senha = :senha";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':login', $this->__get('login'));
		$stmt->bindValue(':senha', $this->__get('senha'));
		$stmt->execute();

		$usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

		if($usuario['id_usuario'] != '' && $usuario['nome'] != '' && $usuario['permissao'] != '') {			
			$this->__set('id_usuario', $usuario['id_usuario']);			
			$this->__set('nome', $usuario['nome']);	
			$this->__set('permissao', $usuario['permissao']);
			$this->__set('email', $usuario['email']);
			$this->__set('cooperativa', $usuario['cooperativa']);
		}

		//return $this;

	}

	//Recuperar usuário por e-mail
	public function getUsuarioPorEmail() {
		$query = "select nome, email from usuarios where email = :email";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':email', $this->__get('email'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

	//Seguir usuários
	public function seguirUsuario($id_usuario_seguindo) {

		$query = "insert into usuarios_seguidores(id_usuario, id_usuario_seguindo)
				  values(:id_usuario, :id_usuario_seguindo)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id'));
		$stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
		$stmt->execute();

		return true;

	}

	//Deixar de seguir usuários
	public function deixarSeguirUsuario($id_usuario_seguindo) {
		$query = "delete from 
					usuarios_seguidores
				  where 
				  	id_usuario = :id_usuario and id_usuario_seguindo = :id_usuario_seguindo";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id'));
		$stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
		$stmt->execute();

		return true;

	}

	//Deixar de seguir usuários
	public function excluirTweet($id_usuario_seguindo) {
		$query = "delete from tweets where id_usuarios = :id_usuario and id = :tweet_id";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id'));
		$stmt->bindValue(':tweet_id', $tweet['id']);
		$stmt->execute();

		return true;

	}

	public function getInfoUsuario() {

		$query = "select nome from usuarios where id = :id_usuario";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);

	}

	public function getTotaltweets() {

		$query = "select count(*) as total_tweet from tweets where id_usuarios = :id_usuario";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);

	}

	public function getTotalSeguindo() {

		$query = "select count(*) as total_seguindo from usuarios_seguidores where id_usuario = :id_usuario";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);

	}


	public function getTotalSeguidores() {

		$query = "select count(*) as total_seguidores from usuarios_seguidores where id_usuario_seguindo = :id_usuario";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);

	}

}

?>