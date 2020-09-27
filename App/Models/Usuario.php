<?php

namespace App\Models;

use MF\Model\Model;

class Usuario extends Model {

	private $id_usuario;
	private $nome;
	private $usuario;
	private $email;
	private $cooperativa;
	private $permissao;
	private $equipe;
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
		$query = "SELECT id_usuario, permissao, nome, usuario, email, cooperativa, equipe FROM usuarios";

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

		try {
			if ($acao == 'inserir') {
				$query = "INSERT INTO usuarios (nome, usuario, email, cooperativa, permissao, equipe, senha) VALUES (:nome, :usuario, :email, :cooperativa, :permissao, :equipe, :senha)";
				
				echo $query;
								
			} elseif ($acao == 'alterarsemsenha') {
				$query = "UPDATE usuarios SET nome = :nome, usuario = :usuario, email = :email, cooperativa = :cooperativa, permissao = :permissao, equipe = :equipe WHERE id_usuario = :id_usuario";
				
				echo $query;
							
			} elseif ($acao == 'alterarcomsenha') {
				$query = "UPDATE usuarios SET nome = :nome, usuario = :usuario, email = :email, cooperativa = :cooperativa, permissao = :permissao, equipe = :equipe, senha = :senha WHERE id_usuario = :id_usuario";
				
				echo $query;
							
			} elseif ($acao == 'deletar') {
				$query = "DELETE FROM usuarios WHERE id_usuario = :id_usuario";
				
				echo $query;
							
			}

			$stmt = $this->db->prepare($query);
			if ($acao == 'alterar' || $acao == 'deletar') { 
				$stmt->bindValue(':id_usuario', $this->__get('id_usuario')); 
			}
			
			if ($acao == 'inserir' || $acao == 'alterar') { 
				$stmt->bindValue(':nome', $this->__get('nome'));
				$stmt->bindValue(':usuario', $this->__get('usuario'));
				$stmt->bindValue(':email', $this->__get('email'));
				$stmt->bindValue(':cooperativa', $this->__get('cooperativa'));
				$stmt->bindValue(':permissao', $this->__get('permissao'));
				$stmt->bindValue(':equipe', $this->__get('equipe'));
				$stmt->bindValue(':senha', $this->__get('senha'));
			}
		
			$stmt->execute();

			//return $this;

			echo "<pre>";
			print_r($this);
			echo "</pre>";

		} catch (PDOException $e) {

		  echo "DataBase Error: The user could not be added.<br>".$e->getMessage();

		} catch (Exception $e) {
			
		  echo "General Error: The user could not be added.<br>".$e->getMessage();

		}
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

		if(strlen($this->__get('login')) < 3 ) {
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
		$query = "select count(*) as valida from usuarios where usuario = :usuario and senha = :senha";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':usuario', $this->__get('usuario'));
		$stmt->bindValue(':senha', $this->__get('senha'));
		$stmt->execute();

		$usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

		return $usuario;

	}

	public function validaUsuario() {
		$query = "SELECT COUNT(*) as usuario FROM usuarios WHERE usuario = :usuario AND email = :email";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':usuario', $this->__get('usuario'));
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