<?php

	session_start();

	require_once("con.php");
	
	if (!$_SESSION['id']) {
	
		header("Location:denied.html");
	
	}
	
	// if the 'term' variable is not sent with the request, exit
	if ( !isset($_REQUEST['term']) )
	exit;

	$conn = new mysqli($hn, $un, $pw, $db);
	if ($conn->connect_error) die($conn->connect_error);

	$query = "SELECT * FROM `Patienten` WHERE `Handy` LIKE '" . stripslashes($_REQUEST['term']) . "%' || `Tel` LIKE '" . stripslashes($_REQUEST['term']) . "%'";
		
	$result = $conn->query($query);
		
	if (!$result) die($conn->error);

	$data = array();
	
	if ( $result && mysqli_num_rows($result) ) {

		while( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
			$data[] = array(
				'value' => $row['Nachname'] . ', ' . $row['Vorname'],
				'label' => $row['Nachname'] . ', ' . $row['Vorname']
			);
		}
		
	}
 
	// jQuery wants JSON data
	echo json_encode($data);
	flush();
	
?>