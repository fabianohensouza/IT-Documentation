<?php

namespace App;

class Connection {

	public static function getDb() {
		try {

			$conn = new \PDO(
				"mysql:host=127.0.0.1;dbname=saic;charset=utf8",
				"root",
				"palio147" 
			);

			return $conn;

		} catch (\PDOException $e) {
			echo 'Error message: ' . $e->getMessage();
		}
	}
}

?>