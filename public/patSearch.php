<?php

	session_start();

	require_once("con.php");
	
	if (!$_SESSION['id']) {
	
		header("Location:denied.html");
	
	}
	
	// if the 'term' variable is not sent with the request, exit
	if ( !isset($_REQUEST['term']) )
	exit;
	$doctor = $_REQUEST['postDoctor'];

	$conn = new mysqli($hn, $un, $pw, $db);
	if ($conn->connect_error) die($conn->connect_error);

	$query = "SELECT * FROM `scheduler_" . $doctor . "` WHERE `Name` LIKE '%" . stripslashes($_REQUEST['term']) . "%' ORDER BY `Date` desc limit 0,20";
		
	$result = $conn->query($query);
		
	if (!$result) die($conn->error);

	$data = array();
	
	if ( $result && mysqli_num_rows($result) ) {

		while( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
			$data[] = array(
				'value' => $row['Name'] . ', ' . $row['Date'],
				'label' => $row['Name'] . ', ' . $row['Date']
			);
		}
		
	}
 
	// jQuery wants JSON data
	echo json_encode($data);
	flush();
	
?>