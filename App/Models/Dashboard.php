<?php

namespace App\Models;

use MF\Model\Model;

class Dashboard extends Model {

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

	public function getInfoDashboard() {
		$query = "SELECT 
                    count(`codigo_coop`) AS getTotalCoops
                FROM 
                    cooperativas 
                WHERE
                    `infracredis` = TRUE;
                SELECT 
					count(`codigo_pa`) AS getTotalPas
				FROM 
				    pas;
				SELECT 
                    count(`id_usuario`) AS getTotalRespTi
                FROM 
					usuarios 
                WHERE
					`equipe` = `Cooperativa`;
				SELECT 
					sum (`qtd_equip`) AS getTotalEquip
				FROM 
					cooperativas
                WHERE
                    `infracredis` = TRUE;
				SELECT
					count (`codigo_servidor`) AS getTotalServidores
				FROM 
					servidores
                WHERE
                    `status_servidor` = `Produção`;
                SELECT 
					count(`firewall`) AS getTotalFirewalls
				FROM 
				    pas;
				SELECT 
                    count(`id_usuario`) AS getTotalIc
                FROM 
					usuarios 
                WHERE
					`equipe` = `Infra-Credis`;
                SELECT 
					count(`id_relatorio`) AS getTotalPas
				FROM 
					relatorios;
                SELECT 
					count(`id_visita`) AS getTotalPas
				FROM 
					visitas;";
				    					
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);

	}


}

?>