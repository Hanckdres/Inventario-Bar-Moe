<?php	
	require("dbconnection.php");
	session_start();

	// Función para obtener el número de filas de una consulta
	function getNumRowsQuery($query) {
		global $sqlconnection;
		if ($result = $sqlconnection->query($query)) {
			return $result->num_rows;
		} else {
			// Manejo de errores: Devuelve 0 o maneja el error según tu aplicación
			error_log("Error in getNumRowsQuery: " . $sqlconnection->error);
			return 0;
		}
	}

	// Función para obtener filas asociativas de una consulta
	function getFetchAssocQuery($query) {
		global $sqlconnection;
		$data = array();

		if ($result = $sqlconnection->query($query)) {
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$data[] = $row;
			}
			return $data;
		} else {
			// Manejo de errores: Devuelve un array vacío o maneja el error según tu aplicación
			error_log("Error in getFetchAssocQuery: " . $sqlconnection->error);
			return array();
		}
	}

	// Función para obtener el último ID de una tabla
	function getLastID($id, $table) {
		global $sqlconnection;

		$query = "SELECT MAX({$id}) AS {$id} FROM {$table}";

		if ($result = $sqlconnection->query($query)) {
			$res = $result->fetch_array();
			// Si no hay IDs en la tabla, devuelve 0
			return $res[$id] === null ? 0 : $res[$id];
		} else {
			// Manejo de errores: Devuelve null o maneja el error según tu aplicación
			error_log("Error in getLastID: " . $sqlconnection->error);
			return null;
		}
	}

	// Función para contar el número de IDs en una tabla
	function getCountID($idnum, $id, $table) {
		global $sqlconnection;

		$query = "SELECT COUNT({$id}) AS {$id} FROM {$table} WHERE {$id} = {$idnum}";

		if ($result = $sqlconnection->query($query)) {
			$res = $result->fetch_array();
			// Si no hay IDs en la tabla, devuelve 0
			return $res[$id] === null ? 0 : $res[$id];
		} else {
			// Manejo de errores: Devuelve null o maneja el error según tu aplicación
			error_log("Error in getCountID: " . $sqlconnection->error);
			return null;
		}
	}

	// Función para obtener el total de ventas de un pedido específico
	function getSalesTotal($orderID) {
		global $sqlconnection;

		$query = "SELECT total FROM tbl_order WHERE orderID = {$orderID}";

		if ($result = $sqlconnection->query($query)) {
			$res = $result->fetch_array();
			return $res ? $res[0] : null; // Devuelve el total o null si no se encontró
		} else {
			// Manejo de errores: Devuelve null o maneja el error según tu aplicación
			error_log("Error in getSalesTotal: " . $sqlconnection->error);
			return null;
		}
	}

	// Función para obtener el total de ventas acumulado según la duración especificada
	function getSalesGrandTotal($duration) {
		global $sqlconnection;

		$total = 0;

		if ($duration == "ALLTIME") {
			$query = "SELECT SUM(total) AS grandtotal FROM tbl_order";
		} else if (in_array($duration, array("DAY", "MONTH", "WEEK"))) {
			$query = "SELECT SUM(total) AS grandtotal FROM tbl_order 
					  WHERE order_date > DATE_SUB(NOW(), INTERVAL 1 {$duration})";
		} else {
			return null;
		}

		if ($result = $sqlconnection->query($query)) {
			$res = $result->fetch_array();
			return $res ? $res['grandtotal'] : 0; // Devuelve el total acumulado o 0 si no se encontró
		} else {
			// Manejo de errores: Devuelve null o maneja el error según tu aplicación
			error_log("Error in getSalesGrandTotal: " . $sqlconnection->error);
			return null;
		}
	}

	// Función para actualizar el total de un pedido
	function updateTotal($orderID) {
		global $sqlconnection;

		$query = "UPDATE tbl_order o
				  INNER JOIN (
					  SELECT SUM(OD.quantity * MI.price) AS total
					  FROM tbl_order O
					  LEFT JOIN tbl_orderdetail OD ON O.orderID = OD.orderID
					  LEFT JOIN tbl_menuitem MI ON OD.itemID = MI.itemID
					  LEFT JOIN tbl_menu M ON MI.menuID = M.menuID
					  WHERE O.orderID = {$orderID}
				  ) x
				  SET o.total = x.total
				  WHERE o.orderID = {$orderID}";

		if ($sqlconnection->query($query) === TRUE) {
			return true; // Devuelve true si la actualización fue exitosa
		} else {
			// Manejo de errores: Devuelve false o maneja el error según tu aplicación
			error_log("Error in updateTotal: " . $sqlconnection->error);
			return false;
		}
	}
?>