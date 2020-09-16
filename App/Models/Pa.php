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

		/*if ($acao == 'inserir') {
			$query = "INSERT INTO pas
							(id_pa, codigo_pa, coop, nome_cidade, tipo_pa, firewall, link_x0, link_x1, link_x2, link_x3, link_x4, link_x5)
						VALUES
							(:id_pa, :codigo_pa, :coop, :nome_cidade, :tipo_pa, :firewall, :link_x0, :link_x1, :link_x2, :link_x3, :link_x4, :link_x5)";
		} elseif ($acao == 'alterar') {
			$query = "UPDATE 
						pas
					SET
					id_pa = :id_pa, codigo_pa = :codigo_pa, coop = :coop, nome_cidade = :nome_cidade, tipo_pa = :tipo_pa, firewall = :firewall, link_x0 = :link_x0, link_x1 = :link_x1, link_x2 = :link_x2, link_x3 = :link_x3, link_x4 = :link_x4, link_x5 = :link_x5
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
		$stmt->bindValue(':codigo_pa', $this->__get('codigo_pa'));
		$stmt->bindValue(':coop', $this->__get('resp_ic'));
		$stmt->bindValue(':nome_cidade', $this->__get('nome_cidade'));
		$stmt->bindValue(':tipo_pa', $this->__get('tipo_pa'));
		$stmt->bindValue(':firewall', $this->__get('firewall'));
		$stmt->bindValue(':link_x0', $this->__get('link_x0'));
		$stmt->bindValue(':link_x1', $this->__get('link_x1'));
		$stmt->bindValue(':link_x2', $this->__get('link_x2'));
		$stmt->bindValue(':link_x3', $this->__get('link_x3'));
		$stmt->bindValue(':link_x4', $this->__get('link_x4'));
		$stmt->bindValue(':link_x5', $this->__get('link_x5'));
	
		$stmt->execute();

		return $this;*/

		echo $acao . "<br>";
		echo $this->__get('id_pa') . "<br>";
		echo $this->__get('codigo_pa'). "<br>";
		echo $this->__get('resp_ic'). "<br>";
		echo $this->__get('nome_cidade'). "<br>";
		echo $this->__get('tipo_pa'). "<br>";
		echo $this->__get('firewall'). "<br>";
		echo $this->__get('link_x0'). "<br>";
		echo $this->__get('link_x1'). "<br>";
		echo $this->__get('link_x2'). "<br>";
		echo $this->__get('link_x3'). "<br>";
		echo $this->__get('link_x4'). "<br>";
		echo $this->__get('link_x5'). "<br>";

	}


}

?>