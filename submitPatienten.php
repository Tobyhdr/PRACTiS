<?php

	session_start();

	require_once("connection.php");
	
	if (!$_SESSION['id']) {
	
		header("Location:denied.html");
	
	}
	
	if ($_GET["logout"]==1 AND $_SESSION['id']) {
	
		session_destroy();
		
	}
	
	if ($_POST['submit0']) {
	
		$PATID = "";
		$_SESSION['id'] = "1";
		$_POST=array();
	
	}
		
	global $PATID;
	global $pastVisits1;

	if ($_POST['submit1']) {
	
		if (!$_POST['Nachname']) $error="<br />Bitte geben Sie einen Nachnamen ein.";
		
		if (!$_POST['Vorname']) $error.="<br />Bitte geben Sie einen Vornamen ein.";
		
		if (!$_POST['Geburtstag']) $error.="<br />Bitte geben Sie den Geburtstag ein.";
		
		if ($error) $error='<strong>Es gab Problem(e) beim Eingeben:</strong>'.$error.'<br >Nachname, Vorname und Geburtstag sind erforderlich. Bitte korrigieren.';
	
		// elseif (!$PATID) 
		elseif ($_SESSION['id'] < 5) {
	
			//Existing patient being selected? If yes, set $PATID and assign to $_SESSION...
			
			//$success='In the session-id less-than-5 loop, checking Nn, Vn and GbsTag ...<br>';
	
			$query = "SELECT * FROM `Patienten` WHERE `Nachname` = '".mysqli_real_escape_string($link, $_POST['Nachname'])."'
			AND
			`Vorname` = '".mysqli_real_escape_string($link, $_POST['Vorname'])."'
			AND
			`Geburtstag` = '".mysqli_real_escape_string($link, $_POST['Geburtstag'])."'";
	
			$result = mysqli_query($link, $query);
	
			if (!$result) die($link->error);

			if ($row = mysqli_fetch_array($result)) {
			//Existing patient, set SESSION-ID!!!
		
				$PATID = $row[13];
				$_SESSION['id'] = $row[13];

				//Gather past visit data from PatBesuch table...
				$query = "SELECT * FROM `PatBesuch` WHERE `PatID` = " . $PATID . " ORDER BY `BesDatum` DESC";
			
				$result1 = mysqli_query($link, $query);
					
				if (!$result1) die($link->error);

				$rows = $result1->num_rows;
				//calculate number of headings for concertina...
				if ($rows < 4 && $rows > 0) {
				$nu_headings = 1;
				} else {
					$remainder = $rows % 4;
					if ($remainder > 0) {
						$nu_headings = floor(1 + $rows / 4);
					} else {
						$nu_headings = $rows / 4;
					}
				}

				//Build arrays of visit data
				while( $r = $result1->fetch_array(MYSQLI_ASSOC) ) {
					$datesUnformatted[] = $r['BesDatum'];
					$anamnesen[] = $r['Anamnese'];
					$behandlungen[] = $r['Behandlung'];
				}

				$count = count($datesUnformatted) -1;

				for ($i = 0 ; $i <= $count ; ++$i) {
					$yearPost = substr($datesUnformatted[$i], 0, -6);
		   			$monthPost = substr($datesUnformatted[$i], 5, -3);
		   			$dayPost = substr($datesUnformatted[$i], 8);
		   			$dates[] = $dayPost . '.' . $monthPost . '.' . $yearPost;
				}

				//Add content from recorded data to created headings...
				if ($rows == 1) {
					$heading = '<h4>Besuch am ' . $dates[0] . '</h4><p>';
					$pastVisits1 = $heading;
					$pastVisits1 .= '<strong>' . $dates[0] . '</strong><br><em>Anamnese: </em>' . $anamnesen[0] . '<br><br><em>Behandlung: </em>' . $behandlungen[0] . '</p>';
				} else if ($rows > 1 && $rows < 4) {
					$last = reset($dates);
					$first = end($dates);
					$heading = '<h4>Besuche vom ' . $last . ' bis ' . $first . '</h4><p>';
					$pastVisits1 = $heading;
					for ($j = 0 ; $j < $rows ; ++$j) {
						$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
					}
					$pastVisits1 .= '</p>';
				} else {
					$lastLoop = count($dates) -1;
					for ($i = 0 ; $i < $nu_headings ; ++$i) {
						//Build array of headings for concertina
						if ($i == $nu_headings - 1) {
							if ($remainder == 1) {
								$headings[] = '<h4>Besuch am ' . $dates[$lastLoop] . '</h4><p>';
								$pastVisits1 .= $headings[$i];
								$pastVisits1 .= '<strong>' . $dates[$lastLoop] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$lastLoop] . '<br><br><em>Behandlung: </em>' . $behandlungen[$lastLoop] . '</p>';
							} else {
								$newest = $i + (3 * $i);
								$headings[] = '<h4>Besuche vom ' . $dates[$newest] . ' bis ' . $dates[$lastLoop] . '</h4><p>';
								$pastVisits1 .= $headings[$i];

								for ($j = $newest ; $j <= $lastLoop ; ++$j) {
									$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
								}
								$pastVisits1 .= '</p>';
							}
						} else {
							$newest = $i + (3 * $i);
							$oldest = $newest + 3;
							$headings[] = '<h4>Besuche vom ' . $dates[$newest] . ' bis ' . $dates[$oldest] . '</h4><p>';
							$pastVisits1 .= $headings[$i];

							for ($j = $newest ; $j < $newest + 4 ; ++$j) {
								$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
							}
							$pastVisits1 .= '</p>';
						}
					}
						
				}

				//$success.='PatID zu SessionID gesetzt für Patient: ' . $row[0] . ', ' . $row[1] . ', ' . $row[4] . '.<br>';
				
				//Existing patient: update changed fields here that are not surname, first name, Geburtstag, Erstbesuch, PatID ...
				
				if (!($row[2] == $_POST['Anrede'])) {
		
					$query = "UPDATE `Patienten` SET
					`Anrede` = '".mysqli_real_escape_string($link, $_POST['Anrede'])."' WHERE `PatID`='".$_SESSION['id']."'";
			
					$result = mysqli_query($link, $query);
			
					if (!$result) die($link->error);
			
					$success.='Patientenanrede wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

					$PATID=$row[13];
					$_SESSION['id']=$row[13];
				
				}
			
				if (!($row[5] == $_POST['Anschrift'])) {
		
					$query = "UPDATE `Patienten` SET
					`Anschrift` = '".mysqli_real_escape_string($link, $_POST['Anschrift'])."' WHERE `PatID`='".$_SESSION['id']."'";
			
					$result = mysqli_query($link, $query);
			
					if (!$result) die($link->error);
			
					$success.='Patientenanschrift wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

					$PATID=$row[13];
					$_SESSION['id']=$row[13];
				
				}
			
				if (!($row[6] == $_POST['Tel'])) {
		
					$query = "UPDATE `Patienten` SET
					`Tel` = '".mysqli_real_escape_string($link, $_POST['Tel'])."' WHERE `PatID`='".$_SESSION['id']."'";
			
					$result = mysqli_query($link, $query);
			
					if (!$result) die($link->error);
			
					$success.='Telefonnummer wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

					$PATID=$row[13];
					$_SESSION['id']=$row[13];
				
				}
			
				if (!($row[7] == $_POST['Handy'])) {
		
					$query = "UPDATE `Patienten` SET
					`Handy` = '".mysqli_real_escape_string($link, $_POST['Handy'])."' WHERE `PatID`='".$_SESSION['id']."'";
			
					$result = mysqli_query($link, $query);
			
					if (!$result) die($link->error);
			
					$success.='Handynummer wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

					$PATID=$row[13];
					$_SESSION['id']=$row[13];
				
				}
			
				if (!($row[8] == $_POST['Email'])) {
		
					$query = "UPDATE `Patienten` SET
					`Email` = '".mysqli_real_escape_string($link, $_POST['Email'])."' WHERE `PatID`='".$_SESSION['id']."'";
			
					$result = mysqli_query($link, $query);
			
					if (!$result) die($link->error);
			
					$success.='Email wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

					$PATID=$row[13];
					$_SESSION['id']=$row[13];
				
				}
				
				if (!($row[14] == $_POST['Bemerkung'])) {
		
					$query = "UPDATE `Patienten` SET
					`Bemerkung` = '".mysqli_real_escape_string($link, $_POST['Bemerkung'])."' WHERE `PatID`='".$_SESSION['id']."'";
			
					$result = mysqli_query($link, $query);
			
					if (!$result) die($link->error);
			
					$success.='Bemerkung wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

					$PATID=$row[13];
					$_SESSION['id']=$row[13];
				
				}
				
				if (!($row[15] == $_POST['Taetigkeit'])) {
		
					$query = "UPDATE `Patienten` SET
					`Taetigkeit` = '".mysqli_real_escape_string($link, $_POST['Taetigkeit'])."' WHERE `PatID`='".$_SESSION['id']."'";
			
					$result = mysqli_query($link, $query);
			
					if (!$result) die($link->error);
			
					$success.='Taetigkeit wurde für '. $row[0]. ', ' . $row[1] . ', ' . $row[4] . ' erfolgreich geändert.<br>';

					$PATID=$row[13];
					$_SESSION['id']=$row[13];
				
				}
				
				if ($_POST['BesDatum'] != "") {
						
					$_POST['BesDatum'] = "";
					$_POST['Anamnese'] = "";
					$_POST['Behandlung'] = "";
						
				}
				
			} else { //New patient!! Add main details (and check all fields??)...
				
				//$success.='In less-than-5 loop for new patient, inserting data<br>';
			
				$query = "INSERT INTO `Patienten` (`Nachname`,`Vorname`,`Anrede`,`Email`,`Tel`,`Handy`,`AnmDatum`,`Geburtstag`,`Anschrift`,`Bemerkung`,`Taetigkeit`) 
				VALUES 
				('".mysqli_real_escape_string($link, $_POST['Nachname'])."','".mysqli_real_escape_string($link, $_POST['Vorname'])."','".mysqli_real_escape_string($link, $_POST['Anrede'])."','".mysqli_real_escape_string($link, $_POST['Email'])."','".mysqli_real_escape_string($link, $_POST['Tel'])."','".mysqli_real_escape_string($link, $_POST['Handy'])."','".mysqli_real_escape_string($link, $_POST['AnmDatum'])."','".mysqli_real_escape_string($link, $_POST['Geburtstag'])."','".mysqli_real_escape_string($link, $_POST['Anschrift'])."','".mysqli_real_escape_string($link, $_POST['Bemerkung'])."','".mysqli_real_escape_string($link, $_POST['Taetigkeit'])."')";
				
				$result = mysqli_query($link, $query);
				
				if (!$result) die($link->error);
			
				//Set new SESSION id from just entered data...
				$query = "SELECT * FROM `Patienten` WHERE `Nachname` = '".mysqli_real_escape_string($link, $_POST['Nachname'])."'
				AND
				`Vorname` = '".mysqli_real_escape_string($link, $_POST['Vorname'])."'
				AND
				`Geburtstag` = '".mysqli_real_escape_string($link, $_POST['Geburtstag'])."'";
	
				$result = mysqli_query($link, $query);
	
				if (!$result) die($link->error);

				if ($row = mysqli_fetch_array($result)) {
		
					$PATID = $row[13];
					$_SESSION['id']=$row[13];
					//$success.='PatientID zu SessionID gesetzt für Patient: ' . $row[0] . ', ' . $row[1] . ', ' . $row[4] . '.<br>';
					if ($_POST['BesDatum'] != "") {
						
						$_POST['BesDatum'] = "";
						$_POST['Anamnese'] = "";
						$_POST['Behandlung'] = "";
					
					}
						
				}
				
				$success.='Neue Patientendaten erfolgreich eingegeben.<br>';
			
			}
			
		}
		
		elseif ($_SESSION['id'] > 5) {
		
			//First check if SessionID and PatID are the same...
			
			if (($_POST['PatID'] == $_SESSION['id']) || ($PATID == $_SESSION['id'])) {
			
				//$success.='Session ID greater than 5 and SessionID is correct for PatID.<br>';
				
				//Update Nachname here IF new surname is required...
		
				$query = "SELECT * FROM `Patienten` WHERE `PatID` = '".$_SESSION['id']."'"; //Select correct patient
			
				$result = mysqli_query($link, $query);
	
				if (!$result) die($link->error);

				if ($row = mysqli_fetch_array($result)) {
			
					if ($_POST['Geburtstag'] == $row['4'] && $_POST['Vorname'] == $row['1'] && $_POST['Nachname'] != $row['0']) { 
					//Existing patient with new Nachname!!! Update Nachname if different...
		
						$query = "UPDATE `Patienten` SET
						`Nachname` = '".mysqli_real_escape_string($link, $_POST['Nachname'])."' WHERE `PatID`='".$_SESSION['id']."'";
			
						$result = mysqli_query($link, $query);
			
						if (!$result) die($link->error);
			
						$success.='Patientennachname erfolgreich geändert für Patient: ' . $row[0] . ', ' . $row[1] . ', ' . $row[4] . '.<br>';

						$PATID=$row[13];
						$_SESSION['id']=$row[13];
						
					} else {
			
						$query = "SELECT * FROM `Patienten` 
						WHERE 
						`Nachname` = '".$_POST['Nachname']."' 
						&& 
						`Vorname` = '".$_POST['Vorname']."' 
						&& 
						`Geburtstag` = '".$_POST['Geburtstag']."'"; 
			
						$result = mysqli_query($link, $query);
	
						if (!$result) die($link->error);

						if ($row = mysqli_fetch_array($result)) {
						//Existing patient!! Make any needed changes...
					
							//$success.='Existing patient in greater-than-5 loop with PATID and/or POST[PatID] set, will now update rows as required by any changes...<br>';
					
							if (!($row[2] == $_POST['Anrede'])) {
		
								$query = "UPDATE `Patienten` SET
								`Anrede` = '".mysqli_real_escape_string($link, $_POST['Anrede'])."' WHERE `PatID`='".$_SESSION['id']."'";
			
								$result = mysqli_query($link, $query);
			
								if (!$result) die($link->error);
			
								$success.='Patientenanrede wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

								$PATID=$row[13];
								$_SESSION['id']=$row[13];
				
							}
			
							if (!($row[5] == $_POST['Anschrift'])) {
		
								$query = "UPDATE `Patienten` SET
								`Anschrift` = '".mysqli_real_escape_string($link, $_POST['Anschrift'])."' WHERE `PatID`='".$_SESSION['id']."'";
			
								$result = mysqli_query($link, $query);
			
								if (!$result) die($link->error);
			
								$success.='Patientenanschrift wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

								$PATID=$row[13];
								$_SESSION['id']=$row[13];
				
							}
			
							if (!($row[6] == $_POST['Tel'])) {
		
								$query = "UPDATE `Patienten` SET
								`Tel` = '".mysqli_real_escape_string($link, $_POST['Tel'])."' WHERE `PatID`='".$_SESSION['id']."'";
			
								$result = mysqli_query($link, $query);
			
								if (!$result) die($link->error);
			
								$success.='Telefonnummer wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

								$PATID=$row[13];
								$_SESSION['id']=$row[13];
				
							}
			
							if (!($row[7] == $_POST['Handy'])) {
		
								$query = "UPDATE `Patienten` SET
								`Handy` = '".mysqli_real_escape_string($link, $_POST['Handy'])."' WHERE `PatID`='".$_SESSION['id']."'";
			
								$result = mysqli_query($link, $query);
			
								if (!$result) die($link->error);
			
								$success.='Handynummer wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

								$PATID=$row[13];
								$_SESSION['id']=$row[13];
				
							}
			
							if (!($row[8] == $_POST['Email'])) {
		
								$query = "UPDATE `Patienten` SET
								`Email` = '".mysqli_real_escape_string($link, $_POST['Email'])."' WHERE `PatID`='".$_SESSION['id']."'";
			
								$result = mysqli_query($link, $query);
			
								if (!$result) die($link->error);
			
								$success.='Email wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

								$PATID=$row[13];
								$_SESSION['id']=$row[13];
				
							}
				
							if (!($row[14] == $_POST['Bemerkung'])) {
		
								$query = "UPDATE `Patienten` SET
								`Bemerkung` = '".mysqli_real_escape_string($link, $_POST['Bemerkung'])."' WHERE `PatID`='".$_SESSION['id']."'";
			
								$result = mysqli_query($link, $query);
			
								if (!$result) die($link->error);
			
								$success.='Bemerkung wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

								$PATID=$row[13];
								$_SESSION['id']=$row[13];
				
							}
				
							if (!($row[15] == $_POST['Taetigkeit'])) {
		
								$query = "UPDATE `Patienten` SET
								`Taetigkeit` = '".mysqli_real_escape_string($link, $_POST['Taetigkeit'])."' WHERE `PatID`='".$_SESSION['id']."'";
			
								$result = mysqli_query($link, $query);
			
								if (!$result) die($link->error);
			
								$success.='Tätigkeit wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

								$PATID=$row[13];
								$_SESSION['id']=$row[13];
				
							}
			
							$PATID=$row[13];
							$_SESSION['id']=$row[13];

							//Gather past visit data from PatBesuch table...
							$query = "SELECT * FROM `PatBesuch` WHERE `PatID` = " . $PATID . " ORDER BY `BesDatum` DESC";
						
							$result1 = mysqli_query($link, $query);
								
							if (!$result1) die($link->error);

							$rows = $result1->num_rows;
							//calculate number of headings for concertina...
							if ($rows < 4 && $rows > 0) {
							$nu_headings = 1;
							} else {
								$remainder = $rows % 4;
								if ($remainder > 0) {
									$nu_headings = floor(1 + $rows / 4);
								} else {
									$nu_headings = $rows / 4;
								}
							}

							//Build arrays of visit data
							while( $r = $result1->fetch_array(MYSQLI_ASSOC) ) {
								$datesUnformatted[] = $r['BesDatum'];
								$anamnesen[] = $r['Anamnese'];
								$behandlungen[] = $r['Behandlung'];
							}

							$count = count($datesUnformatted) -1;

							for ($i = 0 ; $i <= $count ; ++$i) {
								$yearPost = substr($datesUnformatted[$i], 0, -6);
					   			$monthPost = substr($datesUnformatted[$i], 5, -3);
					   			$dayPost = substr($datesUnformatted[$i], 8);
					   			$dates[] = $dayPost . '.' . $monthPost . '.' . $yearPost;
							}

							//Add content from recorded data to created headings...
							if ($rows == 1) {
								$heading = '<h4>Besuch am ' . $dates[0] . '</h4><p>';
								$pastVisits1 = $heading;
								$pastVisits1 .= '<strong>' . $dates[0] . '</strong><br><em>Anamnese: </em>' . $anamnesen[0] . '<br><br><em>Behandlung: </em>' . $behandlungen[0] . '</p>';
							} else if ($rows > 1 && $rows < 4) {
								$last = reset($dates);
								$first = end($dates);
								$heading = '<h4>Besuche vom ' . $last . ' bis ' . $first . '</h4><p>';
								$pastVisits1 = $heading;
								for ($j = 0 ; $j < $rows ; ++$j) {
									$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
								}
								$pastVisits1 .= '</p>';
							} else {
								$lastLoop = count($dates) -1;
								for ($i = 0 ; $i < $nu_headings ; ++$i) {
									//Build array of headings for concertina
									if ($i == $nu_headings - 1) {
										if ($remainder == 1) {
											$headings[] = '<h4>Besuch am ' . $dates[$lastLoop] . '</h4><p>';
											$pastVisits1 .= $headings[$i];
											$pastVisits1 .= '<strong>' . $dates[$lastLoop] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$lastLoop] . '<br><br><em>Behandlung: </em>' . $behandlungen[$lastLoop] . '</p>';
										} else {
											$newest = $i + (3 * $i);
											$headings[] = '<h4>Besuche vom ' . $dates[$newest] . ' bis ' . $dates[$lastLoop] . '</h4><p>';
											$pastVisits1 .= $headings[$i];

											for ($j = $newest ; $j <= $lastLoop ; ++$j) {
												$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
											}
											$pastVisits1 .= '</p>';
										}
									} else {
										$newest = $i + (3 * $i);
										$oldest = $newest + 3;
										$headings[] = '<h4>Besuche vom ' . $dates[$newest] . ' bis ' . $dates[$oldest] . '</h4><p>';
										$pastVisits1 .= $headings[$i];

										for ($j = $newest ; $j < $newest + 4 ; ++$j) {
											$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
										}
										$pastVisits1 .= '</p>';
									}
								}
									
							}
							//$success.='SessionID wurde zu PatID für ' . $row[0] . ', ' . $row[1] . ', '. $row[4] . ' gesetzt.<br>';
							
							if ($_POST['BesDatum'] != "") {
						
								$_POST['BesDatum'] = "";
								$_POST['Anamnese'] = "";
								$_POST['Behandlung'] = "";
						
							}
					
						} else {
						//New patient!!
							
							//$success.='New patient in greater-than-5 loop where PATID and/or POST[PatID] are set equal to SessionID due to previous retrieval of existing patient data but Nachname, Vorname and Geburtstag are new. Adding new patient...<br>';
							
							$query = "INSERT INTO `Patienten` (`Nachname`,`Vorname`,`Anrede`,`Email`,`Tel`,`Handy`,`AnmDatum`,`Geburtstag`,`Anschrift`,`Bemerkung`,`Taetigkeit`) 
							VALUES 
							('".mysqli_real_escape_string($link, $_POST['Nachname'])."','".mysqli_real_escape_string($link, $_POST['Vorname'])."','".mysqli_real_escape_string($link, $_POST['Anrede'])."','".mysqli_real_escape_string($link, $_POST['Email'])."','".mysqli_real_escape_string($link, $_POST['Tel'])."','".mysqli_real_escape_string($link, $_POST['Handy'])."','".mysqli_real_escape_string($link, $_POST['AnmDatum'])."','".mysqli_real_escape_string($link, $_POST['Geburtstag'])."','".mysqli_real_escape_string($link, $_POST['Anschrift'])."','".mysqli_real_escape_string($link, $_POST['Bemerkung'])."','".mysqli_real_escape_string($link, $_POST['Taetigkeit'])."')";
				
							$result = mysqli_query($link, $query);
				
							if (!$result) die($link->error);
			
							//Set new SESSION id from just entered data...
							$query = "SELECT * FROM `Patienten` WHERE `Nachname` = '".mysqli_real_escape_string($link, $_POST['Nachname'])."'
							AND
							`Vorname` = '".mysqli_real_escape_string($link, $_POST['Vorname'])."'
							AND
							`Geburtstag` = '".mysqli_real_escape_string($link, $_POST['Geburtstag'])."'";
	
							$result = mysqli_query($link, $query);
	
							if (!$result) die($link->error);

							if ($row = mysqli_fetch_array($result)) {
		
								$PATID = $row[13];
								$_SESSION['id']=$row[13];
								//$success.='PatID zu SessionID gesetzt für Patient: ' . $row[0] . ', ' . $row[1] . ', '. $row[4] . '.<br>';
								if ($_POST['BesDatum'] != "") {
						
									$_POST['BesDatum'] = "";
									$_POST['Anamnese'] = "";
									$_POST['Behandlung'] = "";
						
								}
						
							}
				
							$success.='Neue Patientendaten erfolgreich eingegeben.<br>';
						
						}
						
					} 
					
				}
				
			} else {
			//New patient? $PATID and $_POST['PatID'] maybe be null/undefined or different to SessionID. Check current Nachname, Vorname and Geburtstag against $_SESSION['id']
							
				//$success.='In greater-than-5 loop (for new patient?) but PATID and POST_[PatID] empty or different to SessionID, checking DB PatID against SessionID...<br>';
				
				$query = "SELECT * FROM `Patienten` WHERE `Nachname` = '".$_POST['Nachname']."' && `Vorname` = '".$_POST['Vorname']."' && `Geburtstag` = '".$_POST['Geburtstag']."'";
				
				$result = mysqli_query($link, $query);
				
				if (!$result) die($link->error);
				
				if ($row = mysqli_fetch_array($result)) {
				//Existing patient, else $row would be empty (not sure this is possible, but just in case)
				
					//$success.='<br>Have just fetched PatID from DB for '. $_POST['Nachname']. ', '. $_POST['Vorname']. ' born on '. $_POST['Geburtstag']. ' in greater-than-5 loop with empty PATID and/or empty _POST[PatID] or both are different to SessionID. Result is: ' . $row[13] . '. Checking against SessionID...<br>';
					
					if ($_SESSION['id'] != $row[13]) {
					
						//$success.='PatID empty or different to SessionID, SessionID greater than 5 but SessionID not yet set to DB PatID. Setting SessionID to $rowID and, if there is an entry to change, changing it... <br>';
						
						$PATID=$row[13];
						$_SESSION['id']=$row[13];

						//Gather past visit data from PatBesuch table...
						$query = "SELECT * FROM `PatBesuch` WHERE `PatID` = " . $PATID . " ORDER BY `BesDatum` DESC";
					
						$result1 = mysqli_query($link, $query);
							
						if (!$result1) die($link->error);

						$rows = $result1->num_rows;
						//calculate number of headings for concertina...
						if ($rows < 4 && $rows > 0) {
						$nu_headings = 1;
						} else {
							$remainder = $rows % 4;
							if ($remainder > 0) {
								$nu_headings = floor(1 + $rows / 4);
							} else {
								$nu_headings = $rows / 4;
							}
						}

						//Build arrays of visit data
						while( $r = $result1->fetch_array(MYSQLI_ASSOC) ) {
							$datesUnformatted[] = $r['BesDatum'];
							$anamnesen[] = $r['Anamnese'];
							$behandlungen[] = $r['Behandlung'];
						}

						$count = count($datesUnformatted) -1;

						for ($i = 0 ; $i <= $count ; ++$i) {
							$yearPost = substr($datesUnformatted[$i], 0, -6);
				   			$monthPost = substr($datesUnformatted[$i], 5, -3);
				   			$dayPost = substr($datesUnformatted[$i], 8);
				   			$dates[] = $dayPost . '.' . $monthPost . '.' . $yearPost;
						}

						//Add content from recorded data to created headings...
						if ($rows == 1) {
							$heading = '<h4>Besuch am ' . $dates[0] . '</h4><p>';
							$pastVisits1 = $heading;
							$pastVisits1 .= '<strong>' . $dates[0] . '</strong><br><em>Anamnese: </em>' . $anamnesen[0] . '<br><br><em>Behandlung: </em>' . $behandlungen[0] . '</p>';
						} else if ($rows > 1 && $rows < 4) {
							$last = reset($dates);
							$first = end($dates);
							$heading = '<h4>Besuche vom ' . $last . ' bis ' . $first . '</h4><p>';
							$pastVisits1 = $heading;
							for ($j = 0 ; $j < $rows ; ++$j) {
								$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
							}
							$pastVisits1 .= '</p>';
						} else {
							$lastLoop = count($dates) -1;
							for ($i = 0 ; $i < $nu_headings ; ++$i) {
								//Build array of headings for concertina
								if ($i == $nu_headings - 1) {
									if ($remainder == 1) {
										$headings[] = '<h4>Besuch am ' . $dates[$lastLoop] . '</h4><p>';
										$pastVisits1 .= $headings[$i];
										$pastVisits1 .= '<strong>' . $dates[$lastLoop] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$lastLoop] . '<br><br><em>Behandlung: </em>' . $behandlungen[$lastLoop] . '</p>';
									} else {
										$newest = $i + (3 * $i);
										$headings[] = '<h4>Besuche vom ' . $dates[$newest] . ' bis ' . $dates[$lastLoop] . '</h4><p>';
										$pastVisits1 .= $headings[$i];

										for ($j = $newest ; $j <= $lastLoop ; ++$j) {
											$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
										}
										$pastVisits1 .= '</p>';
									}
								} else {
									$newest = $i + (3 * $i);
									$oldest = $newest + 3;
									$headings[] = '<h4>Besuche vom ' . $dates[$newest] . ' bis ' . $dates[$oldest] . '</h4><p>';
									$pastVisits1 .= $headings[$i];

									for ($j = $newest ; $j < $newest + 4 ; ++$j) {
										$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
									}
									$pastVisits1 .= '</p>';
								}
							}
								
						}
						//$success.='SessionID wurde zu PatID für ' . $_POST['Nachname'] . ', ' . $_POST['Vorname'] . ', ' . $_POST['Geburtstag']. ' gesetzt.<br>';
				
						if (!($row[2] == $_POST['Anrede'])) {
		
						$query = "UPDATE `Patienten` SET
						`Anrede` = '".mysqli_real_escape_string($link, $_POST['Anrede'])."' WHERE `PatID`='".$_SESSION['id']."'";
			
						$result = mysqli_query($link, $query);
			
						if (!$result) die($link->error);
			
						$success.='<br>Patientenanrede wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';
				
						}
			
						if (!($row[5] == $_POST['Anschrift'])) {
		
							$query = "UPDATE `Patienten` SET
							`Anschrift` = '".mysqli_real_escape_string($link, $_POST['Anschrift'])."' WHERE `PatID`='".$_SESSION['id']."'";
				
							$result = mysqli_query($link, $query);
				
							if (!$result) die($link->error);
				
							$success.='<br>Patientenanschrift wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

							$PATID=$row[13];
							$_SESSION['id']=$row[13];
				
						}
			
						if (!($row[6] == $_POST['Tel'])) {
		
							$query = "UPDATE `Patienten` SET
							`Tel` = '".mysqli_real_escape_string($link, $_POST['Tel'])."' WHERE `PatID`='".$_SESSION['id']."'";
				
							$result = mysqli_query($link, $query);
				
							if (!$result) die($link->error);
				
							$success.='<br>Telefonnummer wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

							$PATID=$row[13];
							$_SESSION['id']=$row[13];
				
						}
				
						if (!($row[7] == $_POST['Handy'])) {
		
							$query = "UPDATE `Patienten` SET
							`Handy` = '".mysqli_real_escape_string($link, $_POST['Handy'])."' WHERE `PatID`='".$_SESSION['id']."'";
				
							$result = mysqli_query($link, $query);
				
							if (!$result) die($link->error);
				
							$success.='<br>Handynummer wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

							$PATID=$row[13];
							$_SESSION['id']=$row[13];
				
						}
			
						if (!($row[8] == $_POST['Email'])) {
		
							$query = "UPDATE `Patienten` SET
							`Email` = '".mysqli_real_escape_string($link, $_POST['Email'])."' WHERE `PatID`='".$_SESSION['id']."'";
				
							$result = mysqli_query($link, $query);
				
							if (!$result) die($link->error);
				
							$success.='<br>Email wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

							$PATID=$row[13];
							$_SESSION['id']=$row[13];
				
						}
				
						if (!($row[14] == $_POST['Bemerkung'])) {
		
							$query = "UPDATE `Patienten` SET
							`Bemerkung` = '".mysqli_real_escape_string($link, $_POST['Bemerkung'])."' WHERE `PatID`='".$_SESSION['id']."'";
				
							$result = mysqli_query($link, $query);
				
							if (!$result) die($link->error);
				
							$success.='<br>Bemerkung wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

							$PATID=$row[13];
							$_SESSION['id']=$row[13];
				
						}
				
						if (!($row[15] == $_POST['Taetigkeit'])) {
		
							$query = "UPDATE `Patienten` SET
							`Taetigkeit` = '".mysqli_real_escape_string($link, $_POST['Taetigkeit'])."' WHERE `PatID`='".$_SESSION['id']."'";
				
							$result = mysqli_query($link, $query);
				
							if (!$result) die($link->error);
				
							$success.='<br>Tätigkeit wurde für '. $row[0]. ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

							$PATID=$row[13];
							$_SESSION['id']=$row[13];
				
						}
						
						if ($_POST['BesDatum'] != "") {
						
							$_POST['BesDatum'] = "";
							$_POST['Anamnese'] = "";
							$_POST['Behandlung'] = "";
						
						}
					
					}
				
				} else {
		
				//Must be new patient because $row is empty, enter data...
				
					//$success.='In greater-than-5 loop with empty PATID and _POST[PatID] or different to SessionID, and search of DB for Nachname, Vorname and Geburtstag yieled nothing, hence new patient. Entering new patient...<br>';
			
					$query = "INSERT INTO `Patienten` (`Nachname`,`Vorname`,`Anrede`,`Email`,`Tel`,`Handy`,`AnmDatum`,`Geburtstag`,`Anschrift`,`Bemerkung`,`Taetigkeit`) 
					VALUES 
					('".mysqli_real_escape_string($link, $_POST['Nachname'])."','".mysqli_real_escape_string($link, $_POST['Vorname'])."','".mysqli_real_escape_string($link, $_POST['Anrede'])."','".mysqli_real_escape_string($link, $_POST['Email'])."','".mysqli_real_escape_string($link, $_POST['Tel'])."','".mysqli_real_escape_string($link, $_POST['Handy'])."','".mysqli_real_escape_string($link, $_POST['AnmDatum'])."','".mysqli_real_escape_string($link, $_POST['Geburtstag'])."','".mysqli_real_escape_string($link, $_POST['Anschrift'])."','".mysqli_real_escape_string($link, $_POST['Bemerkung'])."','".mysqli_real_escape_string($link, $_POST['Taetigkeit'])."')";
				
					$result = mysqli_query($link, $query);
				
					if (!$result) die($link->error);
			
					//Set new SESSION id from just entered data and clear dynData area if populated...
					$query = "SELECT * FROM `Patienten` WHERE `Nachname` = '".mysqli_real_escape_string($link, $_POST['Nachname'])."'
					AND
					`Vorname` = '".mysqli_real_escape_string($link, $_POST['Vorname'])."'
					AND
					`Geburtstag` = '".mysqli_real_escape_string($link, $_POST['Geburtstag'])."'";
	
					$result = mysqli_query($link, $query);
	
					if (!$result) die($link->error);

					if ($row = mysqli_fetch_array($result)) {
		
						$PATID = $row[13];
						$_SESSION['id']=$row[13];
						$success.='PatID zu SessionID gesetzt für Patient: ' . $row[0] . ', ' . $row[1] . ', '. $row[4] . '.<br>';
						if ($_POST['BesDatum'] != "") {
						
							$_POST['BesDatum'] = "";
							$_POST['Anamnese'] = "";
							$_POST['Behandlung'] = "";
						
						}
						
						
					}
				
					$success.='Neue Patientendaten erfolgreich eingegeben.<br>';
					
				}
							
			}
		
		} //End >5 loop
	
	}
	
	//Second data-submission area

	if ($_POST['submit2']) {
	
		if (!$_POST['Hauptbeschw'] AND !$_POST['Kontraind'] AND !$_POST['Diagnose']) $error="<br>Alle Felder sind leer! Es gibt nichts einzugeben!";
		
		if ($error) $error='<strong>Es gab Problem(e) beim Eingeben:</strong>'.$error.'<br>Bitte korrigieren.';
		
		elseif ($_POST['PatID'] == $_SESSION['id']) {
	
			$query = "UPDATE `Patienten` SET 
			`Hauptbeschw` = '".mysqli_real_escape_string($link, $_POST['Hauptbeschw'])."', 
			`Kontraind` = '".mysqli_real_escape_string($link, $_POST['Kontraind'])."', 
			`Diagnose` = '".mysqli_real_escape_string($link, $_POST['Diagnose'])."' 
			WHERE `PatID`='".$_SESSION['id']."'";
				
			$result = mysqli_query($link, $query);
		
			if (!$result) die($link->error);
			
			$PATID = $_SESSION['id'];

			//Gather past visit data from PatBesuch table...
			$query = "SELECT * FROM `PatBesuch` WHERE `PatID` = " . $PATID . " ORDER BY `BesDatum` DESC";
		
			$result1 = mysqli_query($link, $query);
				
			if (!$result1) die($link->error);

			$rows = $result1->num_rows;
			//calculate number of headings for concertina...
			if ($rows < 4 && $rows > 0) {
			$nu_headings = 1;
			} else {
				$remainder = $rows % 4;
				if ($remainder > 0) {
					$nu_headings = floor(1 + $rows / 4);
				} else {
					$nu_headings = $rows / 4;
				}
			}

			//Build arrays of visit data
			while( $r = $result1->fetch_array(MYSQLI_ASSOC) ) {
				$datesUnformatted[] = $r['BesDatum'];
				$anamnesen[] = $r['Anamnese'];
				$behandlungen[] = $r['Behandlung'];
			}

			$count = count($datesUnformatted) -1;

			for ($i = 0 ; $i <= $count ; ++$i) {
				$yearPost = substr($datesUnformatted[$i], 0, -6);
	   			$monthPost = substr($datesUnformatted[$i], 5, -3);
	   			$dayPost = substr($datesUnformatted[$i], 8);
	   			$dates[] = $dayPost . '.' . $monthPost . '.' . $yearPost;
			}

			//Add content from recorded data to created headings...
			if ($rows == 1) {
				$heading = '<h4>Besuch am ' . $dates[0] . '</h4><p>';
				$pastVisits1 = $heading;
				$pastVisits1 .= '<strong>' . $dates[0] . '</strong><br><em>Anamnese: </em>' . $anamnesen[0] . '<br><br><em>Behandlung: </em>' . $behandlungen[0] . '</p>';
			} else if ($rows > 1 && $rows < 4) {
				$last = reset($dates);
				$first = end($dates);
				$heading = '<h4>Besuche vom ' . $last . ' bis ' . $first . '</h4><p>';
				$pastVisits1 = $heading;
				for ($j = 0 ; $j < $rows ; ++$j) {
					$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
				}
				$pastVisits1 .= '</p>';
			} else {
				$lastLoop = count($dates) -1;
				for ($i = 0 ; $i < $nu_headings ; ++$i) {
					//Build array of headings for concertina
					if ($i == $nu_headings - 1) {
						if ($remainder == 1) {
							$headings[] = '<h4>Besuch am ' . $dates[$lastLoop] . '</h4><p>';
							$pastVisits1 .= $headings[$i];
							$pastVisits1 .= '<strong>' . $dates[$lastLoop] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$lastLoop] . '<br><br><em>Behandlung: </em>' . $behandlungen[$lastLoop] . '</p>';
						} else {
							$newest = $i + (3 * $i);
							$headings[] = '<h4>Besuche vom ' . $dates[$newest] . ' bis ' . $dates[$lastLoop] . '</h4><p>';
							$pastVisits1 .= $headings[$i];

							for ($j = $newest ; $j <= $lastLoop ; ++$j) {
								$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
							}
							$pastVisits1 .= '</p>';
						}
					} else {
						$newest = $i + (3 * $i);
						$oldest = $newest + 3;
						$headings[] = '<h4>Besuche vom ' . $dates[$newest] . ' bis ' . $dates[$oldest] . '</h4><p>';
						$pastVisits1 .= $headings[$i];

						for ($j = $newest ; $j < $newest + 4 ; ++$j) {
							$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
						}
						$pastVisits1 .= '</p>';
					}
				}
					
			}
		
			$success.='Patientenzustand zu Patient ' . $_POST['Nachname'] . ', ' . $_POST['Vorname'] . ', ' . $_POST['Geburtstag'] . ' erfolgreich eingegeben/updated.';
		
		} else {
		
			$error.='Patient ID nicht richtig gesetzt für Hauptbeschwerden Bereich. <br>Keine Daten eingegeben! <br>Bitte die Session ID korrekt einsetzen (Patientennachname suchen, dann auf obersten blauen Knopf klicken).';
		
		}
	}
	
	if ($_POST['submit3']) {
	
		if (!$_POST['BesDatum']) $error="<br>Bitte ein Besuchsdatum eingeben!";
	
		if (!$_POST['Anamnese'] AND !$_POST['Behandlung']) $error.="<br>Textfelder sind leer! Es gibt nichts zum Besuchsdatum einzugeben!";
		
		if ($error) {
			$error='<strong>Es gab Problem(e) beim Eingeben:</strong>'.$error.'<br>Bitte korrigieren.';
			
		if ($_SESSION['id'] > 5) {
			$PATID = $_SESSION['id'];
		}
		
		} elseif ($_POST['PatID'] == $_SESSION['id']) {
		
			$query = "SELECT `BesDatum` from `PatBesuch` WHERE `PatID` = '".$_POST['PatID']."' && `BesDatum` = '".$_POST['BesDatum']."'";
			
			$result = mysqli_query($link, $query);
			
			$rows = $result->num_rows;
			
			if ($rows == 1) {
				
				//Update existing visit data here
				$query = "UPDATE `PatBesuch` SET  
				`Anamnese` = '".mysqli_real_escape_string($link, $_POST['Anamnese'])."',
				`Behandlung` = '".mysqli_real_escape_string($link, $_POST['Behandlung'])."'
				WHERE `PatID` = '".$_SESSION['id']."' && `BesDatum` = '".$_POST['BesDatum']."'";
				
				$result = mysqli_query($link, $query);
		
				if (!$result) die($link->error);
			
				if ($_SESSION['id'] > 5) {
					$PATID = $_SESSION['id'];
				}

				//Gather past visit data from PatBesuch table...
				$query = "SELECT * FROM `PatBesuch` WHERE `PatID` = " . $PATID . " ORDER BY `BesDatum` DESC";
			
				$result1 = mysqli_query($link, $query);
					
				if (!$result1) die($link->error);

				$rows = $result1->num_rows;
				//calculate number of headings for concertina...
				if ($rows < 4 && $rows > 0) {
				$nu_headings = 1;
				} else {
					$remainder = $rows % 4;
					if ($remainder > 0) {
						$nu_headings = floor(1 + $rows / 4);
					} else {
						$nu_headings = $rows / 4;
					}
				}

				//Build arrays of visit data
				while( $r = $result1->fetch_array(MYSQLI_ASSOC) ) {
				$datesUnformatted[] = $r['BesDatum'];
				$anamnesen[] = $r['Anamnese'];
				$behandlungen[] = $r['Behandlung'];
				}

				$count = count($datesUnformatted) -1;

				for ($i = 0 ; $i <= $count ; ++$i) {
					$yearPost = substr($datesUnformatted[$i], 0, -6);
		   			$monthPost = substr($datesUnformatted[$i], 5, -3);
		   			$dayPost = substr($datesUnformatted[$i], 8);
		   			$dates[] = $dayPost . '.' . $monthPost . '.' . $yearPost;
				}

				//Add content from recorded data to created headings...
				if ($rows == 1) {
					$heading = '<h4>Besuch am ' . $dates[0] . '</h4><p>';
					$pastVisits1 = $heading;
					$pastVisits1 .= '<strong>' . $dates[0] . '</strong><br><em>Anamnese: </em>' . $anamnesen[0] . '<br><br><em>Behandlung: </em>' . $behandlungen[0] . '</p>';
				} else if ($rows > 1 && $rows < 4) {
					$last = reset($dates);
					$first = end($dates);
					$heading = '<h4>Besuche vom ' . $last . ' bis ' . $first . '</h4><p>';
					$pastVisits1 = $heading;
					for ($j = 0 ; $j < $rows ; ++$j) {
						$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
					}
					$pastVisits1 .= '</p>';
				} else {
					$lastLoop = count($dates) -1;
					for ($i = 0 ; $i < $nu_headings ; ++$i) {
						//Build array of headings for concertina
						if ($i == $nu_headings - 1) {
							if ($remainder == 1) {
								$headings[] = '<h4>Besuch am ' . $dates[$lastLoop] . '</h4><p>';
								$pastVisits1 .= $headings[$i];
								$pastVisits1 .= '<strong>' . $dates[$lastLoop] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$lastLoop] . '<br><br><em>Behandlung: </em>' . $behandlungen[$lastLoop] . '</p>';
							} else {
								$newest = $i + (3 * $i);
								$headings[] = '<h4>Besuche vom ' . $dates[$newest] . ' bis ' . $dates[$lastLoop] . '</h4><p>';
								$pastVisits1 .= $headings[$i];

								for ($j = $newest ; $j <= $lastLoop ; ++$j) {
									$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
								}
								$pastVisits1 .= '</p>';
							}
						} else {
							$newest = $i + (3 * $i);
							$oldest = $newest + 3;
							$headings[] = '<h4>Besuche vom ' . $dates[$newest] . ' bis ' . $dates[$oldest] . '</h4><p>';
							$pastVisits1 .= $headings[$i];

							for ($j = $newest ; $j < $newest + 4 ; ++$j) {
								$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
							}
							$pastVisits1 .= '</p>';
						}
					}
						
				}
		
				$success.='Patientenbesuchsdaten zu Patient ' . $_POST['Nachname'] . ', ' . $_POST['Vorname'] . ', ' . $_POST['Geburtstag'] . ' erfolgreich geändert/updated zum existierenden Besuchsdatum ' . $_POST['BesDatum'] . '.';
				
			} else {
	
				$query = "INSERT INTO `PatBesuch` (`BesDatum`,`Anamnese`,`Behandlung`,`PatID`) 
				VALUES 
				('".mysqli_real_escape_string($link, $_POST['BesDatum'])."','".mysqli_real_escape_string($link, $_POST['Anamnese'])."','".mysqli_real_escape_string($link, $_POST['Behandlung'])."','".mysqli_real_escape_string($link, $_SESSION['id'])."')";
				
				$result = mysqli_query($link, $query);
		
				if (!$result) die($link->error);
			
				if ($_SESSION['id'] > 5) {
					$PATID = $_SESSION['id'];
				}

				//Gather past visit data from PatBesuch table...
				$query = "SELECT * FROM `PatBesuch` WHERE `PatID` = " . $PATID . " ORDER BY `BesDatum` DESC";
			
				$result1 = mysqli_query($link, $query);
					
				if (!$result1) die($link->error);

				$rows = $result1->num_rows;
				//calculate number of headings for concertina...
				if ($rows < 4 && $rows > 0) {
				$nu_headings = 1;
				} else {
					$remainder = $rows % 4;
					if ($remainder > 0) {
						$nu_headings = floor(1 + $rows / 4);
					} else {
						$nu_headings = $rows / 4;
					}
				}

				//Build arrays of visit data
				while( $r = $result1->fetch_array(MYSQLI_ASSOC) ) {
				$datesUnformatted[] = $r['BesDatum'];
				$anamnesen[] = $r['Anamnese'];
				$behandlungen[] = $r['Behandlung'];
				}

				$count = count($datesUnformatted) -1;

				for ($i = 0 ; $i <= $count ; ++$i) {
					$yearPost = substr($datesUnformatted[$i], 0, -6);
		   			$monthPost = substr($datesUnformatted[$i], 5, -3);
		   			$dayPost = substr($datesUnformatted[$i], 8);
		   			$dates[] = $dayPost . '.' . $monthPost . '.' . $yearPost;
				}

				//Add content from recorded data to created headings...
				if ($rows == 1) {
					$heading = '<h4>Besuch am ' . $dates[0] . '</h4><p>';
					$pastVisits1 = $heading;
					$pastVisits1 .= '<strong>' . $dates[0] . '</strong><br><em>Anamnese: </em>' . $anamnesen[0] . '<br><br><em>Behandlung: </em>' . $behandlungen[0] . '</p>';
				} else if ($rows > 1 && $rows < 4) {
					$last = reset($dates);
					$first = end($dates);
					$heading = '<h4>Besuche vom ' . $last . ' bis ' . $first . '</h4><p>';
					$pastVisits1 = $heading;
					for ($j = 0 ; $j < $rows ; ++$j) {
						$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
					}
					$pastVisits1 .= '</p>';
				} else {
					$lastLoop = count($dates) -1;
					for ($i = 0 ; $i < $nu_headings ; ++$i) {
						//Build array of headings for concertina
						if ($i == $nu_headings - 1) {
							if ($remainder == 1) {
								$headings[] = '<h4>Besuch am ' . $dates[$lastLoop] . '</h4><p>';
								$pastVisits1 .= $headings[$i];
								$pastVisits1 .= '<strong>' . $dates[$lastLoop] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$lastLoop] . '<br><br><em>Behandlung: </em>' . $behandlungen[$lastLoop] . '</p>';
							} else {
								$newest = $i + (3 * $i);
								$headings[] = '<h4>Besuche vom ' . $dates[$newest] . ' bis ' . $dates[$lastLoop] . '</h4><p>';
								$pastVisits1 .= $headings[$i];

								for ($j = $newest ; $j <= $lastLoop ; ++$j) {
									$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
								}
								$pastVisits1 .= '</p>';
							}
						} else {
							$newest = $i + (3 * $i);
							$oldest = $newest + 3;
							$headings[] = '<h4>Besuche vom ' . $dates[$newest] . ' bis ' . $dates[$oldest] . '</h4><p>';
							$pastVisits1 .= $headings[$i];

							for ($j = $newest ; $j < $newest + 4 ; ++$j) {
								$pastVisits1 .= '<strong>' . $dates[$j] . '</strong><br><em>Anamnese: </em>' . $anamnesen[$j] . '<br><br><em>Behandlung: </em>' . $behandlungen[$j] . '<br><br>';
							}
							$pastVisits1 .= '</p>';
						}
					}
						
				}
		
				$success.='Patientenbesuchsdaten zu ' . $_POST['Nachname'] . ', ' . $_POST['Vorname'] . ', ' . $_POST['Geburtstag'] . ' erfolgreich eingegeben zum neuen Besuchsdatum ' . $_POST['BesDatum'] . '.';
			
			}
		
		} else {
		
			$error.='Patient ID nicht richtig gesetzt für Besuchsdatum. <br>Keine Daten eingegeben! <br>Bitte die Session ID korrekt einsetzen (Patientennachname suchen, dann auf obersten blauen Knopf klicken).';
			
		}
	}

?>