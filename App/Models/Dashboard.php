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

	public function getTotalTweets() {
		$query = "SELECT 
                    count(`codigo_coop`) AS getTotalCoops
                FROM 
                    cooperativas 
                WHERE
                    `infracredis` = TRUE;
                SELECT 
					count(`codigo_pa`) AS getTotalPas
				FROM 
				  	pas;";
				    					
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);

	}


}

?>