<?php

namespace App\Models;

use MF\Model\Model;

class Dashboard extends Model {

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

	public function getInfoDashboard() {
		$query = "SELECT (SELECT COUNT(`codigo_coop`) FROM  cooperativas WHERE `infracredis` = TRUE) AS totalCoops,
		(SELECT COUNT(`codigo_pa`) FROM pas) AS totalPas,
		(SELECT SUM(`qtd_usuarios`) FROM  cooperativas WHERE `infracredis` = TRUE) AS totalUsuarios,
		(SELECT SUM(`qtd_equip`) FROM cooperativas WHERE `infracredis` = TRUE) AS totalEquip,
		(SELECT COUNT (`codigo_servidor`) FROM servidores WHERE `status_servidor` = "Produção") AS totalServidores,
		(SELECT COUNT(`firewall`) FROM  pas) AS totalFirewalls,
		(SELECT COUNT(`id_usuario`) FROM usuarios WHERE `equipe` = "Infra-Credis") AS totalIc,
		(SELECT COUNT(`id_relatorio`) FROM relatorios) AS totalRelatorios,
		(SELECT COUNT(`id_visita`) FROM visitas) AS totalVisitas;";
				    					
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);

	}


}

?>