<?php

	session_start();
	
	if ($_GET["logout"]==1 AND $_SESSION['id']) {
	
		session_destroy();
		
		$message="Sie sind nun abgemeldet.";
		
	}
	
	require_once("connection.php");

	if ($_POST['submit']=="➙") {
	
		if ((!$_POST['loginemail']) || (!$_POST['loginpassword'])) {
		
			$error="Bitte geben Sie ein gültige Email und ihre Loginpasswort ein.";
			
		} else {
	
			$query = "SELECT * from `users` WHERE `email`='".mysqli_real_escape_string($link, $_POST['loginemail'])."' AND `password`='".md5(md5($_POST['loginemail']).$_POST['loginpassword'])."' LIMIT 1";
		
			$result = mysqli_query($link, $query);
		
			$row = mysqli_fetch_array($result);
		
			if($row) {
		
				$_SESSION['id']=$row['id'];
			
				header("Location:softwareFront.php");
		
			} else {
		
			$error="Hoppla! Keine solche email-passwort Kombination vorhanden. Bitte versuchen Sie es erneut.";
			
			}
			
		}
	
	}

?>