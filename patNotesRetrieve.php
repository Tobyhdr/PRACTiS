<?php

	session_start();

	require_once("con.php");
	
	if (!$_SESSION['id']) {
	
		header("Location:denied.html");
	
	}
	
	if ($_GET["logout"]==1 AND $_SESSION['id']) {
	
		session_destroy();
		
	}
	
	global $yearPost;
	global $monthPost;
	global $dayPost;
	
	$conn = new mysqli($hn, $un, $pw, $db);
	if ($conn->connect_error) die($conn->connect_error);
	
	if ($_POST['data']) {
	
		$data = json_decode(stripslashes($_POST['data']));
		$yearPost = substr($data[0], 6);
   		$monthPost = substr($data[0], 3, -5);
   		$dayPost = substr($data[0], 0, -8);
   		$date = $yearPost . '-' . $monthPost . '-' . $dayPost;
		
		$query = "SELECT * FROM `scheduler_jmu` WHERE `Date` LIKE '" . $date . "%' ORDER BY `Date` asc";
		
		$result = $conn->query($query);
		
		if (!$result) die($conn->error);
		
		$rows = $result->num_rows;
		
		if ($rows >= 1) {
		
			$retrieved = '<table id="myTable"><thead><tr><th>Zeit</th><th>Name</th></tr></thead><tbody>';
			
			$j = 0;
			$r = 1;

			for ($i = 8 ; $i <= 20 ; ++$i) {
				for ($l = 0 ; $l <= 4 ; ++$l) {
					
					$result->data_seek($j);
					$row = $result->fetch_array(MYSQLI_ASSOC);
					$time = substr($row['Date'], 11, -3);
					
					$time1 = $i . ':' . $l . '0';
					
					if ($i < 10) {
						$time2 = '0' . $time1;
					} else {
						$time2 = $time1;
					}
					
					++$l;
				if ($time == $time2) {
					$type = $row['Typ'];
					if ($type == "") {
						$retrieved .= '<td id="time">' . $time . '</td><td><input type="text" class="name Nichts" id="row' . $r . 'name" style="width: 300px;" value="' . $row['Name'] . '"></td></tr>';
					} else {
						$retrieved .= '<td id="time">' . $time . '</td><td><input type="text" class="name ' . $type . '" id="row' . $r . 'name" style="width: 300px;" value="' . $row['Name'] . '"</td></tr>';
					}
					++$j;
				} else {
					$retrieved .= '<td id="time">' . $time2 . '</td><td><input type="text" class="name Nichts" id="row' . $r . 'name" style="width: 300px;" value=""></td></tr>';
				}
			++$r;
			}
		
			}
			
			$retrieved .= '</tbody></table>';
			
			echo $retrieved;
	
		}
		
	}
	
?>