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

	$query = "SELECT * FROM `Patienten` WHERE `Nachname` LIKE '" . stripslashes($_REQUEST['term']) . "%' ORDER BY `Nachname` asc limit 0,20";
		
	$result = $conn->query($query);
		
	if (!$result) die($conn->error);

	$data = array();
	
	if ( $result && mysqli_num_rows($result) ) {

		while( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
			if ( $row['Handy'] ) {
				$Phone = $row['Handy'];
			} else if ( $row['Tel'] ) {
				$Phone = $row['Tel'];
			} else {
				$Phone = 'Kein Fon';
			}
			$data[] = array(
				'value' => $row['Anrede'] . ' ' . $row['Nachname'] . ', ' . $row['Vorname'] . ' (' . $Phone . ') [' . $row['PatID'] . ']',
				'label' => $row['Nachname'] . ', ' . $row['Vorname'] . ', ' . $row['Geburtstag'],
				'PatData' => $row['Anrede'] . ' ' . $row['Nachname'] . ', ' . $row['Vorname'] . ' (' . $row['Handy'] . ')'
			);
		}
		
	}
 
	// jQuery wants JSON data
	echo json_encode($data);
	flush();
	
?>