<?php

	session_start();

	require_once("con.php");
	
	if (!$_SESSION['id']) {
	
		header("Location:denied.html");
	
	}

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
			$data[] = array(
				'value' => $row['Nachname'],
				'label' => $row['Nachname'] . ', ' . $row['Vorname'] . ', ' . $row['Geburtstag'],
				'Vorname' => $row['Vorname'],
				'Anrede' => $row['Anrede'],
				'AnmDatum' => $row['AnmDatum'],
				'Geburtstag' => $row['Geburtstag'],
				'Anschrift' => $row['Anschrift'],
				'Tel' => $row['Tel'],
				'Handy' => $row['Handy'],
				'email' => $row['email'],
				'Hauptbeschw' => $row['Hauptbeschw'],
				'Kontraind' => $row['Kontraind'],
				'Diagnose' => $row['Diagnose'],
				'Bemerkung' => $row['Bemerkung'],
				'PatID' => $row['PatID'],
				'Taetigkeit' => $row['Taetigkeit']
			);
		}
		
	}
 
	// jQuery wants JSON data
	echo json_encode($data);
	flush();
	
?>