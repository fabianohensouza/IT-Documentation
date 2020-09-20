<?php

namespace App\Models;

use MF\Model\Model;

class Pa extends Model {

	private $id_pa;
    private $codigo_pa;
    private $coop;
    private $nome_cidade;
    private $tipo_pa;
    private $firewall;
    private $link_x0;
    private $link_x1;
    private $link_x2;
    private $link_x3;
    private $link_x4;
    private $link_x5;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

	//Recuperar todos usuários
	public function todosPas() {
		$query = "SELECT 
					* 
				  FROM 
				  	pas";

		$stmt = $this->db->prepare($query);;
		$stmt->execute();

		$pas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		
		return $pas;
	
	}

	public function codigoPas() {
		$query = "SELECT id_pa 
				  FROM pas";

		$stmt = $this->db->prepare($query);;
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	
	}

	public function paPorId() {

		$query = "SELECT * FROM pas WHERE id_pa = :id_pa";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_pa', $this->__get('id_pa'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);

	}

	public function cidadesMG() {
		$query = "SELECT nome_cidade 
				  FROM cidades_mg";

		$stmt = $this->db->prepare($query);;
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	
	}
	
	public function alterarPa($acao) {

		if ($acao == 'inserir') {
			$query = "INSERT INTO pas
							(id_pa, coop, codigo_pa, nome_cidade, tipo_pa, firewall, link_x0, link_x1, link_x2, link_x3, link_x4, link_x5)
						VALUES
							(:id_pa, :coop, :codigo_pa, :nome_cidade, :tipo_pa, :firewall, :link_x0, :link_x1, :link_x2, :link_x3, :link_x4, :link_x5)";
		} elseif ($acao == 'alterar') {
			$query = "UPDATE 
						pas
					SET
						id_pa = :id_pa, coop = :coop, codigo_pa = :codigo_pa, nome_cidade = :nome_cidade, tipo_pa = :tipo_pa, firewall = :firewall, link_x0 = :link_x0, link_x1 = :link_x1, link_x2 = :link_x2, link_x3 = :link_x3, link_x4 = :link_x4, link_x5 = :link_x5
					WHERE
						id_pa = :id_pa";
		} elseif ($acao == 'deletar') {
			$query = "DELETE FROM 
						pas
					WHERE
						id_pa = :id_pa";
		}

		$stmt = $this->db->prepare($query);
		
		$stmt->bindValue(':id_pa', $this->__get('id_pa'));
		$stmt->bindValue(':coop', $this->__get('coop')); 
		$stmt->bindValue(':codigo_pa', $this->__get('codigo_pa'));
		$stmt->bindValue(':nome_cidade', $this->__get('nome_cidade'));
		$stmt->bindValue(':tipo_pa', $this->__get('tipo_pa'));
		$stmt->bindValue(':firewall', $this->__get('firewall'));
		for($i=0; $i <= 5; $i++) { 

			$bind = ":link_x" . $i;
			$idx = "link_x" . $i;
			$stmt->bindValue( $bind, $this->__get($idx));
			
		}
	
		$stmt->execute();

		return $this;

	}


}

?>