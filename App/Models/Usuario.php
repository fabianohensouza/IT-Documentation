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
	private $equipe;
	private $equipeic;
	private $senha;
	private $senha_nova1;
	private $senha_nova2;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

	public function todosUsuarios() {
		$query = "SELECT id_usuario, permissao, nome, login, email, cooperativa, equipe FROM usuarios";

		$stmt = $this->db->prepare($query);;
		$stmt->execute();

		
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

	public function todosUsuariosIC() {
		$query = "SELECT 
					nome 
				  FROM 
					usuarios
				  WHERE
				  	equipe = :equipe";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':equipe', "Infra-Credis");
		$stmt->execute();

		
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

	
	public function usuarioPorId() {
		$query = "SELECT * FROM usuarios WHERE id_usuario = :id_usuario";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
		$stmt->execute();

		
		return $stmt->fetch(\PDO::FETCH_ASSOC);

	}

	
	public function alterarUsuario($acao) {

		if ($acao == 'inserir') {
			$query = "INSERT INTO usuarios
							(nome, login, email, cooperativa, permissao, equipe, senha)
						VALUES
							(:nome, :login, :email, :cooperativa, :permissao, :equipe, :senha)";
		} elseif ($acao == 'alterarsemsenha') {
			$query = "UPDATE 
						usuarios
					SET
						nome = :nome , login = :login, email = :email, cooperativa = :cooperativa, permissao = :permissao, equipe = :equipe
					WHERE
						id_usuario = :id_usuario";
		} elseif ($acao == 'alterarcomsenha') {
			$query = "UPDATE 
						usuarios
					SET
						nome = :nome , login = :login, email = :email, cooperativa = :cooperativa, permissao = :permissao, equipe = :equipe, senha = :senha
					WHERE
						id_usuario = :id_usuario";
		} elseif ($acao == 'deletar') {
			$query = "DELETE FROM 
						usuarios
					WHERE
						id_usuario = :id_usuario";
		}

		$stmt = $this->db->prepare($query);
		if ($acao == 'alterar' || $acao == 'deletar') { 
			$stmt->bindValue(':id_usuario', $this->__get('id_usuario')); 
		}
		
		if ($acao == 'inserir' || $acao == 'alterar') { 
			$stmt->bindValue(':nome', $this->__get('nome'));
			$stmt->bindValue(':login', $this->__get('login'));
			$stmt->bindValue(':email', $this->__get('email'));
			$stmt->bindValue(':cooperativa', $this->__get('cooperativa'));
			$stmt->bindValue(':permissao', $this->__get('permissao'));
			$stmt->bindValue(':equipe', $this->__get('equipe'));
			$stmt->bindValue(':senha', $this->__get('senha'));
		}
	
		$stmt->execute();

		return $this;
	}

	
	public function alterarSenha() {

		$query = "UPDATE 
						usuarios
					SET
						senha = :senha
					WHERE
						id_usuario = :id_usuario";
		
		$stmt = $this->db->prepare($query);

		$stmt->bindValue(':id_usuario', $this->__get('id_usuario')); 
		$stmt->bindValue(':senha', $this->__get('senha_nova1'));
		
		$stmt->execute();
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
		$query = "select id_usuario, nome, permissao, cooperativa, login from usuarios where login = :login and senha = :senha";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':login', $this->__get('login'));
		$stmt->bindValue(':senha', $this->__get('senha'));
		$stmt->execute();

		$usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

		if($usuario['id_usuario'] != '' && $usuario['nome'] != '' && $usuario['permissao'] != '') {			
			$this->__set('id_usuario', $usuario['id_usuario']);			
			$this->__set('nome', $usuario['nome']);	
			$this->__set('permissao', $usuario['permissao']);
			$this->__set('cooperativa', $usuario['cooperativa']);
			$this->__set('login', $usuario['login']);
		}

		//return $this;
		return $usuario;

	}

	public function validaSenha() {
		$query = "select count(*) as valida from usuarios where login = :login and senha = :senha";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':login', $this->__get('login'));
		$stmt->bindValue(':senha', $this->__get('senha'));
		$stmt->execute();

		$usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

		return $usuario;

	}

	public function validaUsuario() {
		$query = "select count(*) as usuario from usuarios where login = :login and email = :email";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':login', $this->__get('login'));
		$stmt->bindValue(':email', $this->__get('email'));
		$stmt->execute();

		$usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

		return $usuario;

	}

	//Recuperar usuário por e-mail
	public function usuarioPorEmail() {
		$query = "select nome, email from usuarios where email = :email";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':email', $this->__get('email'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

}

?>