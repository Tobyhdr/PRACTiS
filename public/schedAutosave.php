<?php

	session_start();

	//require_once("con.php");
	require_once("connection.php");
	
	if (!$_SESSION['id']) {
	
		header("Location:denied.html");
	
	}
	
	if ($_GET["logout"]==1 AND $_SESSION['id']) {
	
		session_destroy();
		
	}
	
	if ($conn->connect_error) die($conn->connect_error);
	
	if ($_POST['data']) {
	
		$data = json_decode(stripslashes($_POST['data']));
		$date = $data[0];
		$time = $data[1];
		$content = $data[2];
		$dbRow = $data[3];
		$doctor = $data[4];
		$dateTime = $date . " " . $time . ":00";
		//echo json_encode($dbRow);
		
		$query = "SELECT `". $dbRow ."` FROM `scheduler_" . $doctor . "` WHERE `Date` = '". $dateTime . "'";
		
		$result = mysqli_query($link, $query);
		
		if (!$result) die($conn->error);

		$row = mysqli_fetch_array($result);
		
		$rows = $result->num_rows;
		
		if ($rows == 1 && $dbRow != "Name" && $content == "" && $row[0] == "") {
		
			//do nothing
		
		} else if ($rows == 1) {
		
			$query = "UPDATE `scheduler_" . $doctor . "` SET `". $dbRow ."` = '". mysqli_real_escape_string($link, $content) ."' WHERE `Date` = '". $dateTime ."'";
			
			$result = mysqli_query($link, $query);
		
			if (!$result) die($conn->error);
		
		} else {
			
			$query = "INSERT INTO `scheduler_" . $doctor . "` (`Date`, `". $dbRow ."`) VALUES ('". mysqli_real_escape_string($link, $dateTime) ."','". mysqli_real_escape_string($link, $content) ."')";
			
			$result = mysqli_query($link, $query);
		
			if (!$result) die($conn->error);
		
		}
		
	}
	
	if ($_POST['Typ']) {
	
		$data2 = json_decode(stripslashes($_POST['Typ']));
		$date = $data2[0];
		$time = $data2[1];
		$type = $data2[2];
		$doctor = $data2[3];
		$dateTime = $date . " " . $time . ":00";
		echo json_encode($data2);
		
		$query = "SELECT `Date` FROM `scheduler_" . $doctor . "` WHERE `Date` = '". $dateTime . "'";
		
		$result = mysqli_query($link, $query);
		
		if (!$result) die($conn->error);
		
		$rows = $result->num_rows;
		
		if ($rows == 1) {
		
			$query = "UPDATE `scheduler_" . $doctor . "` SET `Typ` = '". mysqli_real_escape_string($link, $type) ."' WHERE `Date` = '". $dateTime ."'";
			
			$result = mysqli_query($link, $query);
		
			if (!$result) die($conn->error);
		
		} else {
			
			$query = "INSERT INTO `scheduler_" . $doctor . "` (`Date`, `Typ`) VALUES ('". mysqli_real_escape_string($link, $dateTime) ."','". mysqli_real_escape_string($link, $type) ."')";
			
			$result = mysqli_query($link, $query);
		
			if (!$result) die($conn->error);
		
		}
		
	}
	
	if ($_POST['comment']) {

		$data3 = $_POST['comment'];
		$date = $data3[0];
		$time = $data3[1];
		$comment = $data3[2];
		$doctor = $data3[3];
		$dateTime = $date . " " . $time . ":00";
		
		$query = "SELECT `Date` FROM `scheduler_" . $doctor . "` WHERE `Date` = '". $dateTime . "'";
		
		$result = mysqli_query($link, $query);
		
		if (!$result) die($conn->error);
		
		$rows = $result->num_rows;
		
		if ($rows == 1) {
		
			$query = "UPDATE `scheduler_" . $doctor . "` SET `Comment` = '". mysqli_real_escape_string($link, $comment) ."' WHERE `Date` = '". $dateTime ."'";
			
			$result = mysqli_query($link, $query);
		
			if (!$result) die($conn->error);
		
		} else {
			
			$query = "INSERT INTO `scheduler_" . $doctor . "` (`Date`, `Comment`) VALUES ('". mysqli_real_escape_string($link, $dateTime) ."','". mysqli_real_escape_string($link, $comment) ."')";
			
			$result = mysqli_query($link, $query);
		
			if (!$result) die($conn->error);
		
		}
		
	}
	
?>