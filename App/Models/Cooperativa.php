<?php

namespace App\Models;

use MF\Model\Model;

class Cooperativa extends Model {

	private $codigo_coop;
	private $nome;
	private $nome_cidade;
	private $infracredis;
	private $resp_ti;
	private $diretoria;
	private $resp_ic;
	private $qtd_usuarios;
	private $qtd_equip;
	private $adesao;

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

	public function codigoCooperativas() {
		$query = "SELECT codigo_coop 
				  FROM cooperativas";

		$stmt = $this->db->prepare($query);;
		$stmt->execute();

		
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	
	}

	public function cidadesMG() {
		$query = "SELECT nome_cidade 
				  FROM cidades_mg";

		$stmt = $this->db->prepare($query);;
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	
	}

	public function CoopPorCod() {

		$query = "SELECT * FROM cooperativas WHERE codigo_coop = :codigo_coop";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':codigo_coop', $this->__get('codigo_coop'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);

	}
	
	public function alterarCooperativa($acao) {

		if ($acao == 'inserir') {
			$query = "INSERT INTO cooperativas
							(codigo_coop, nome, nome_cidade, infracredis, resp_ti, diretoria, resp_ic, qtd_usuarios, qtd_equip, adesao)
						VALUES
							(:codigo_coop, :nome, :nome_cidade, :infracredis, :resp_ti, :diretoria, :resp_ic, :qtd_usuarios, :qtd_equip, :adesao)";
		} elseif ($acao == 'alterar') {
			$query = "UPDATE 
						cooperativas
					SET
					codigo_coop = :codigo_coop, nome = :nome, nome_cidade = :nome_cidade, infracredis = :infracredis, resp_ti = :resp_ti, diretoria = :diretoria, resp_ic = :resp_ic, qtd_usuarios = :qtd_usuarios, qtd_equip = :qtd_equip, adesao = :adesao
					WHERE
						codigo_coop = :codigo_coop";
		} elseif ($acao == 'deletar') {
			$query = "DELETE FROM 
						cooperativas
					WHERE
						codigo_coop = :codigo_coop";
		}

		$stmt = $this->db->prepare($query);
		
		$stmt->bindValue(':codigo_coop', $this->__get('codigo_coop')); 
		$stmt->bindValue(':nome', $this->__get('nome'));
		$stmt->bindValue(':resp_ic', $this->__get('resp_ic'));
		$stmt->bindValue(':infracredis', $this->__get('infracredis'));
		$stmt->bindValue(':nome_cidade', $this->__get('nome_cidade'));
		$stmt->bindValue(':qtd_usuarios', $this->__get('qtd_usuarios'));
		$stmt->bindValue(':qtd_equip', $this->__get('qtd_equip'));
		$stmt->bindValue(':adesao', $this->__get('adesao'));
		$stmt->bindValue(':diretoria', $this->__get('diretoria'));
		$stmt->bindValue(':resp_ti', $this->__get('resp_ti'));
	
		$stmt->execute();

		return $this;
	}


}

?>