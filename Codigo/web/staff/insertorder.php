<?php
	include("../functions.php");

	if((!isset($_SESSION['uid']) && !isset($_SESSION['username']) && isset($_SESSION['user_level'])) ) {
		header("Location: login.php");
		exit();
	}

	if($_SESSION['user_level'] != "staff") {
		header("Location: login.php");
		exit();
	}

	if (isset($_POST['sentorder'])) {

		if (isset($_POST['itemID']) && isset($_POST['itemqty'])) {

			$arrItemID = $_POST['itemID'];
			$arrItemQty = $_POST['itemqty'];			

			//check pair of the array have same element number
			if (count($arrItemID) == count($arrItemQty)) {				
				$arrlength = count($arrItemID);

				//add new id
				$currentOrderID = getLastID("orderID","tbl_order") + 1;

				if (insertOrderQuery($currentOrderID) && insertOrderDetails($currentOrderID, $arrItemID, $arrItemQty)) {
					updateTotal($currentOrderID);

					//completed insert current order
					header("Location: index.php");
					exit();
				} else {
					echo "Something went wrong.";
				}
			} else {
				echo "xD";
			}
		}
	}

	function insertOrderDetails($orderID, $arrItemID, $arrItemQty) {
		global $sqlconnection;

		$success = true;

		for ($i=0; $i < count($arrItemID); $i++) { 
			$itemID = $arrItemID[$i];
			$quantity = $arrItemQty[$i];

			$addOrderDetailQuery = "INSERT INTO tbl_orderdetail (orderID ,itemID ,quantity) VALUES ('{$orderID}', '{$itemID}' ,{$quantity})";

			if ($sqlconnection->query($addOrderDetailQuery) !== TRUE) {
				$success = false;
				echo "Something went wrong with order detail insertion.";
				echo $sqlconnection->error;
				break;
			}
		}

		return $success;
	}

	function insertOrderQuery($orderID) {
		global $sqlconnection;
		$addOrderQuery = "INSERT INTO tbl_order (orderID, status, order_date, total) VALUES ('{$orderID}', 'Esperando', CURDATE(), 0.00)";

		if ($sqlconnection->query($addOrderQuery) === TRUE) {
			return true;
		} else {
			echo "Something went wrong with order insertion.";
			echo $sqlconnection->error;
			return false;
		}
	}
?>