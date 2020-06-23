<?php

namespace App\Models;

use MF\Model\Model;

class Tweet extends Model {

	private $id;
	private $id_usuarios;
	private $tweet;
	private $data;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

	//Salvar
	public function salvar() {

		$query = "insert into tweets(id_usuarios, tweet)values(:id_usuarios, :tweet)";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuarios', $this->__get('id_usuarios'));
		$stmt->bindValue(':tweet', $this->__get('tweet'));
		$stmt->execute();

		return $this;
	}

	//Recuperar usuário por e-mail
	public function getAll() {
		$query = "select 
					t.id, 
					t.id_usuarios, 
					t.tweet, 
					u.nome, 
					DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data
				  from 
					tweets as t
					left join usuarios as u on (t.id_usuarios = u.id)
				  where 
					t.id_usuarios = :id_usuarios
					or t.id_usuarios in (SELECT `id_usuario_seguindo` FROM `usuarios_seguidores` WHERE id_usuario = :id_usuarios)
				  order by 
				    t.data desc";
				    					
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuarios', $this->__get('id_usuarios'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

	public function getPorPagina($limit, $offset) {
		$query = "select 
					t.id, 
					t.id_usuarios, 
					t.tweet, 
					u.nome, 
					DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data
				  from 
					tweets as t
					left join usuarios as u on (t.id_usuarios = u.id)
				  where 
					t.id_usuarios = :id_usuarios
					or t.id_usuarios in (SELECT `id_usuario_seguindo` FROM `usuarios_seguidores` WHERE id_usuario = :id_usuarios)
				  order by 
				    t.data desc
				  limit
				  	$limit
				  offset
				  	$offset";
				    					
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuarios', $this->__get('id_usuarios'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

	public function getTotalTweets() {
		$query = "SELECT 
					count(`tweet`) AS getTotalTweets
				  FROM 
				  	tweets 
				  WHERE
				  	`id_usuarios` = :id_usuarios";
				    					
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_usuarios', $this->__get('id_usuarios'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);

	}


}

?>