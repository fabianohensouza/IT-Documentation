<?php

namespace App\Models;

use MF\Model\Model;

class Cooperativa extends Model {

	private $codigo_coop;
	private $nome;
	private $resp_ti;
	private $resp_ic;
	private $qtd_usuarios;
	private $qtd_equip;
	private $infracredis;
	private $adesao;
	private $site_unificado;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

	//Recuperar todos usuários
	public function todasCooperativas() {
		$query = "SELECT 
					* 
				  FROM 
				  	cooperativas";

		$stmt = $this->db->prepare($query);;
		$stmt->execute();

		$cooperativas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		$idx = 0;

		foreach($cooperativas as $cooperativa) {

			$query = "SELECT 
						count(*) AS pas 
					FROM 
						pas 
					WHERE coop = :coop";
				    					
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':coop', $cooperativa['codigo_coop']);
			$stmt->execute();

			$valor = $stmt->fetch(\PDO::FETCH_ASSOC);
			$cooperativas[$idx]['pas'] = $valor['pas'];
			
			$idx++;
		}
		
		return $cooperativas;
	
	}

	//Recuperar todos usuários
	public function codigoCooperativas() {
		$query = "SELECT codigo_coop 
				  FROM cooperativas";

		$stmt = $this->db->prepare($query);;
		$stmt->execute();

		
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	
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