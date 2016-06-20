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
		$_POST=array();
	
	}
		
	global $PATID;
	global $pastVisits1;

	if ($_POST['submit1']) {
	
		if (!$_POST['Nachname']) $error = "<br>Bitte geben Sie einen Nachnamen ein.";
		
		if (!$_POST['Vorname']) $error .= "<br>Bitte geben Sie einen Vornamen ein.";
		
		if (!$_POST['Geburtstag']) $error .= "<br>Bitte geben Sie den Geburtstag ein.";
		
		if ($error) {

			$error = '<strong>Es gab Problem(e) beim Eingeben:</strong>' . $error . '<br>Nachname, Vorname und Geburtstag sind erforderlich. Bitte korrigieren.';

		} elseif (empty($_POST['PatID'])) {
			//New patient?

			$query = "SELECT * FROM `Patienten` WHERE `Nachname` = '" . $_POST['Nachname'] . "' AND `Vorname` = '" . $_POST['Vorname'] . "' AND `Geburtstag` = '" . $_POST['Geburtstag'] . "'";

			$result = mysqli_query($link, $query);
	
			if (!$result) $error = die($link->error);

			$rows = $result->num_rows;

			if ($rows == 1) {

				$error = '<br>Einen Patient mit dem Nachname, Vorname und Geburtstag gibt es schon. Wenn die Daten stimmen, bitte einen zweiten Vorname im Vornamenfeld hinzufügen, damit der Patient als Neupatient hinzugefügt werden kann.';

			} else { //New patient, add data...

				$query = "INSERT INTO `Patienten` (`Nachname`,`Vorname`,`Anrede`,`Email`,`Tel`,`Handy`,`AnmDatum`,`Geburtstag`,`Anschrift`,`Bemerkung`,`Taetigkeit`) 
				VALUES 
				('" . mysqli_real_escape_string($link, $_POST['Nachname']) . "', '" . mysqli_real_escape_string($link, $_POST['Vorname']) . "', '" . mysqli_real_escape_string($link, $_POST['Anrede']) . "', '" . mysqli_real_escape_string($link, $_POST['Email']) . "', '" . mysqli_real_escape_string($link, $_POST['Tel']) . "', '" . mysqli_real_escape_string($link, $_POST['Handy']) . "', '" . mysqli_real_escape_string($link, $_POST['AnmDatum']) . "', '" . mysqli_real_escape_string($link, $_POST['Geburtstag']) . "', '" . mysqli_real_escape_string($link, $_POST['Anschrift']) . "', '" . mysqli_real_escape_string($link, $_POST['Bemerkung']) . "', '" . mysqli_real_escape_string($link, $_POST['Taetigkeit']) . "')";

				$result = mysqli_query($link, $query);
	
				if (!$result) $error = die($link->error);

				//Fetch new PatId from just entered data...
				$query = "SELECT * FROM `Patienten` WHERE `Nachname` = '" . $_POST['Nachname'] . "'
				AND
				`Vorname` = '" . $_POST['Vorname'] . "'
				AND
				`Geburtstag` = '" . $_POST['Geburtstag'] . "'";

				$result = mysqli_query($link, $query);
	
				if (!$result) $error = die($link->error);

				if ($row = mysqli_fetch_array($result)) {
					$PATID = $row[13];
					$success.='Neue Patientendaten mit PatientenID ' . $PATID . ' erfolgreich eingegeben.<br>';
					$_POST['BesDatum'] = "";
					$_POST['Anamnese'] = "";
					$_POST['Behandlung'] = "";
				}

			} 

		} else {
			//Existing patient, check then amend data that needs amending...

			$query = "SELECT * FROM `Patienten` WHERE `PatID` = '" . $_POST['PatID'] . "'";

			$result = mysqli_query($link, $query);
	
			if (!$result) $error = die($link->error);

			if ($row = mysqli_fetch_array($result)) {

				if ($_POST['Geburtstag'] != $row[4] && $_POST['Nachname'] != $row[0] && $_POST['Vorname'] != $row[1]) {

					$error = '<strong><span style="color:#D20202">!! ACHTUNG !!</span></strong><br>Die von Ihnen eingegebenen Nachname, Vorname und Geburtstag stimmen mit den in der Patientendatenbank vorhandenen Daten nicht überein. Haben Sie sich eine neue bzw. die richtige PatientenID eingeholt?';

					$PATID = $row[13];

				} else {

					$PATID = $row[13];

					//Existing patient, so gather and format past visit data from PatBesuch table...

					$query = "SELECT * FROM `PatBesuch` WHERE `PatID` = " . $PATID . " ORDER BY `BesDatum` DESC";
				
					$result1 = mysqli_query($link, $query);
	
					if (!$result1) $error = die($link->error);

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

					//Make any needed changes and give detailed information on each change in case an error was made...
					if ($row[0] != $_POST['Nachname']) {
		
						$Nachname = $row[0];
						$query = "UPDATE `Patienten` SET
						`Nachname` = '" . mysqli_real_escape_string($link, $_POST['Nachname']) . "' WHERE `PatID` = '" . $_POST['PatID'] . "'";

						$result = mysqli_query($link, $query);
	
						if (!$result) $error = die($link->error);

						$success.='<strong><span style="color:#D20202">Nachname</span></strong> wurde von <em>' . $Nachname . '</em> auf <em>' . $_POST['Nachname'] . '</em> für ' . $_POST['Nachname'] . ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

						$PATID = $row[13];
		
					} 

					if ($row[1] != $_POST['Vorname']) {
			
						$Vorname = $row[1];
						$query = "UPDATE `Patienten` SET
						`Vorname` = '" . mysqli_real_escape_string($link, $_POST['Vorname']) . "' WHERE `PatID` = '" . $_POST['PatID'] . "'";

						$result = mysqli_query($link, $query);
	
						if (!$result) $error = die($link->error);

						$success.='<strong><span style="color:#D20202">Vorname</span></strong> wurde von <em>' . $Vorname . '</em> auf <em>' . $_POST['Vorname'] . '</em> für ' . $row[0] . ', ' . $_POST['Vorname'] . ', '. $row[4] . ' erfolgreich geändert.<br>';

						$PATID = $row[13];
		
					}

					if ($row[2] != $_POST['Anrede']) {
			
						$Anrede = $row[2];
						$query = "UPDATE `Patienten` SET
						`Anrede` = '" . mysqli_real_escape_string($link, $_POST['Anrede']) . "' WHERE `PatID` = '" . $_POST['PatID'] . "'";

						$result = mysqli_query($link, $query);
	
						if (!$result) $error = die($link->error);

						$success.='<strong><span style="color:#D20202">Anrede</span></strong> wurde von <em>' . $Anrede . '</em> auf <em>' . $_POST['Anrede'] . '</em> für ' . $row[0] . ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

						$PATID = $row[13];
		
					}

					if ($row[3] != $_POST['AnmDatum']) {
			
						$AnmDatum = $row[3];
						$query = "UPDATE `Patienten` SET
						`AnmDatum` = '" . mysqli_real_escape_string($link, $_POST['AnmDatum']) . "' WHERE `PatID` = '" . $_POST['PatID'] . "'";

						$result = mysqli_query($link, $query);
	
						if (!$result) $error = die($link->error);

						$success.='<strong><span style="color:#D20202">Erstbesuch</span></strong> wurde von <em>' . $AnmDatum . '</em> auf <em>' . $_POST['AnmDatum'] . '</em> für ' . $row[0] . ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

						$PATID = $row[13];
		
					}

					if ($row[4] != $_POST['Geburtstag']) {
			
						$Geburtstag = $row[4];
						$query = "UPDATE `Patienten` SET
						`Geburtstag` = '" . mysqli_real_escape_string($link, $_POST['Geburtstag']) . "' WHERE `PatID` = '" . $_POST['PatID'] . "'";

						$result = mysqli_query($link, $query);
	
						if (!$result) $error = die($link->error);

						$success.='<strong><span style="color:#D20202">Geburtstag</span></strong> wurde von <em>' . $Geburtstag . '</em> auf <em>' . $_POST['Geburtstag'] . '</em> für ' . $row[0] . ', ' . $row[1] . ', '. $_POST['Geburtstag'] . ' erfolgreich geändert.<br>';

						$PATID = $row[13];
		
					}

					if ($row[5] != $_POST['Anschrift']) {
			
						$Anschrift = $row[5];
						$query = "UPDATE `Patienten` SET
						`Anschrift` = '" . mysqli_real_escape_string($link, $_POST['Anschrift']) . "' WHERE `PatID` = '" . $_POST['PatID'] . "'";

						$result = mysqli_query($link, $query);
	
						if (!$result) $error = die($link->error);

						$success.='<strong><span style="color:#D20202">Anschrift</span></strong> wurde von <em>' . $Anschrift . '</em> auf <em>' . $_POST['Anschrift'] . '</em> für ' . $row[0] . ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

						$PATID = $row[13];

					}

					if ($row[6] != $_POST['Tel']) {
			
						$Tel = $row[6];
						$query = "UPDATE `Patienten` SET
						`Tel` = '" . mysqli_real_escape_string($link, $_POST['Tel']) . "' WHERE `PatID` = '" . $_POST['PatID'] . "'";

						$result = mysqli_query($link, $query);
	
						if (!$result) $error = die($link->error);

						$success.='<strong><span style="color:#D20202">Telefonnummer</span></strong> wurde von <em>' . $Tel . '</em> auf <em>' . $_POST['Tel'] . '</em> für ' . $row[0] . ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

						$PATID = $row[13];

					}

					if ($row[7] != $_POST['Handy']) {
			
						$Handy = $row[7];
						$query = "UPDATE `Patienten` SET
						`Handy` = '" . mysqli_real_escape_string($link, $_POST['Handy']) . "' WHERE `PatID` = '" . $_POST['PatID'] . "'";

						$result = mysqli_query($link, $query);
	
						if (!$result) $error = die($link->error);

						$success.='<strong><span style="color:#D20202">Handynummer</span></strong> wurde von <em>' . $Handy . '</em> auf <em>' . $_POST['Handy'] . '</em> für ' . $row[0] . ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

						$PATID = $row[13];

					}

					if ($row[8] != $_POST['Email']) {
			
						$Email = $row[8];
						$query = "UPDATE `Patienten` SET
						`Email` = '" . mysqli_real_escape_string($link, $_POST['Email']) . "' WHERE `PatID` = '" . $_POST['PatID'] . "'";

						$result = mysqli_query($link, $query);
	
						if (!$result) $error = die($link->error);

						$success.='<strong><span style="color:#D20202">Email</span></strong> wurde von <em>' . $Email . '</em> auf <em>' . $_POST['Email'] . '</em> für ' . $row[0] . ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

						$PATID = $row[13];

					}

					if ($row[14] != $_POST['Bemerkung']) {
			
						$Bemerkung = $row[14];
						$query = "UPDATE `Patienten` SET
						`Bemerkung` = '" . mysqli_real_escape_string($link, $_POST['Bemerkung']) . "' WHERE `PatID` = '" . $_POST['PatID'] . "'";

						$result = mysqli_query($link, $query);
	
						if (!$result) $error = die($link->error);

						$success.='<strong><span style="color:#D20202">Bemerkung</span></strong> wurde von <em>' . $Bemerkung . '</em> auf <em>' . $_POST['Bemerkung'] . '</em> für ' . $row[0] . ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

						$PATID = $row[13];

					}

					if ($row[15] != $_POST['Taetigkeit']) {
			
						$Taetigkeit = $row[15];
						$query = "UPDATE `Patienten` SET
						`Taetigkeit` = '" . mysqli_real_escape_string($link, $_POST['Taetigkeit']) . "' WHERE `PatID` = '" . $_POST['PatID'] . "'";

						$result = mysqli_query($link, $query);
	
						if (!$result) $error = die($link->error);

						$success.='<strong><span style="color:#D20202">Taetigkeit</span></strong> wurde von <em>' . $Taetigkeit . '</em> auf <em>' . $_POST['Taetigkeit'] . '</em> für ' . $row[0] . ', ' . $row[1] . ', '. $row[4] . ' erfolgreich geändert.<br>';

						$PATID = $row[13];

					}

				}

			}

		} 

	}

	if ($_POST['submit2']) {
	
		if (!$_POST['Hauptbeschw'] && !$_POST['Kontraind'] && !$_POST['Diagnose']) $error="<br>Alle Felder sind leer! Es gibt nichts einzugeben!<br>";
		
		if ($_POST['PatID'] == "") {

			$error .= 'PatientID nicht gesetzt: Daten können nicht korrekt in die Datenbank eingegeben werden!';

		} else {

			$PATID = $_POST['PatID'];

			$query = "UPDATE `Patienten` SET 
			`Hauptbeschw` = '" . mysqli_real_escape_string($link, $_POST['Hauptbeschw']) . "', 
			`Kontraind` = '" . mysqli_real_escape_string($link, $_POST['Kontraind']) . "', 
			`Diagnose` = '" . mysqli_real_escape_string($link, $_POST['Diagnose']) . "' 
			WHERE `PatID`='" . $_POST['PatID'] . "'";

			$result = mysqli_query($link, $query);
	
			if (!$result) $error = die($link->error);

			$success.='Patientenzustand zu Patient ' . $_POST['Nachname'] . ', ' . $_POST['Vorname'] . ', ' . $_POST['Geburtstag'] . ' erfolgreich eingegeben/updated.';

			//Existing patient, so gather and format past visit data from PatBesuch table...
			$query = "SELECT * FROM `PatBesuch` WHERE `PatID` = " . $PATID . " ORDER BY `BesDatum` DESC";
		
			$result1 = mysqli_query($link, $query);
	
			if (!$result1) $error = die($link->error);

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

		}

	}

	if ($_POST['submit3']) {

		$PATID = $_POST['PatID'];

		if (!$_POST['Anamnese'] && !$_POST['Behandlung']) {
			$error="<br>Beide Felder sind leer! Es gibt nichts einzugeben!<br>";
		} elseif (!$_POST['Anamnese'] && $_POST['Behandlung'] && $_POST['BesDatum']) {

			$query = "SELECT `Anamnese` from `PatBesuch` WHERE `PatID` = " . $PATID . " && `BesDatum` = '" . $_POST['BesDatum'] . "'";

			$result = mysqli_query($link, $query);
	
			if (!$result) $error = die($link->error);

			$rows = $result->num_rows;
			
			if ($row = mysqli_fetch_array($result)) {

				$_POST['Anamnese'] = $row[0];
				$error.="<br>Das Anamnesefeld beinhaltet keine Daten, Daten gibt es aber im Behandlungsfeld. Ihre alten Anamnesedaten werden von der Datenbank importiert, damit Sie die komplette Besuchsdaten richtig abspeichern können. Wenn Sie mit Ihren Daten nun zufrieden sind, bitte nochmal abspeichern.";

				//Existing patient, so gather and format past visit data from PatBesuch table...
				$query = "SELECT * FROM `PatBesuch` WHERE `PatID` = " . $PATID . " ORDER BY `BesDatum` DESC";
			
				$result1 = mysqli_query($link, $query);
	
				if (!$result1) $error = die($link->error);

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

			} else {
				$error .= "Anamnese darf nicht leer sein, wenn das Behandlungsfeld Daten beinhaltet. Bitte Text zur Anamnese hinzufügen (z.B.: <em>Keine</em>).";
			}

		} elseif ($_POST['PatID'] == "") {

			$error .= 'PatientID nicht gesetzt: Ohne PatientID können Daten nicht korrekt in die Datenbank eingegeben werden!';

		} else {

			$PATID = $_POST['PatID'];

			//Check date corresponds to existing past visit...
			$query = "SELECT `BesDatum` from `PatBesuch` WHERE `PatID` = " . $PATID . " && `BesDatum` = '" . $_POST['BesDatum'] . "'";

			$result = mysqli_query($link, $query);
	
			if (!$result) $error = die($link->error);

			$rows = $result->num_rows;
			
			if ($rows == 1) {
				
				//Update existing visit data here
				$query = "UPDATE `PatBesuch` SET  
				`Anamnese` = '" . mysqli_real_escape_string($link, $_POST['Anamnese']) . "',
				`Behandlung` = '" . mysqli_real_escape_string($link, $_POST['Behandlung']) . "'
				WHERE `PatID` = '" . $_POST['PatID'] . "' && `BesDatum` = '".$_POST['BesDatum']."'";

				$result = mysqli_query($link, $query);
	
				if (!$result) $error = die($link->error);

				$success.='Patientenbesuchsdaten zu Patient ' . $_POST['Nachname'] . ', ' . $_POST['Vorname'] . ', ' . $_POST['Geburtstag'] . ' erfolgreich geändert/updated zum existierenden Besuchsdatum ' . $_POST['BesDatum'] . '.';

				//Existing patient, so gather and format past visit data from PatBesuch table...
				$query = "SELECT * FROM `PatBesuch` WHERE `PatID` = " . $PATID . " ORDER BY `BesDatum` DESC";
			
				$result1 = mysqli_query($link, $query);
	
				if (!$result1) $error = die($link->error);

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

			} else {
				//New entry for this patient...

				$PATID = $_POST['PatID'];

				$query = "INSERT INTO `PatBesuch` (`BesDatum`,`Anamnese`,`Behandlung`,`PatID`) 
				VALUES 
				('" . mysqli_real_escape_string($link, $_POST['BesDatum']) . "', '" . mysqli_real_escape_string($link, $_POST['Anamnese']) . "', '" . mysqli_real_escape_string($link, $_POST['Behandlung']) . "', '" . mysqli_real_escape_string($link, $_POST['PatID']) . "')";

				$result = mysqli_query($link, $query);
	
				if (!$result) $error = die($link->error);

				$success.='Patientenbesuchsdaten zu ' . $_POST['Nachname'] . ', ' . $_POST['Vorname'] . ', ' . $_POST['Geburtstag'] . ' erfolgreich eingegeben zum neuen Besuchsdatum ' . $_POST['BesDatum'] . '.';

				//Now gather and format new visit data from PatBesuch table...
				$query = "SELECT * FROM `PatBesuch` WHERE `PatID` = " . $PATID . " ORDER BY `BesDatum` DESC";
			
				$result1 = mysqli_query($link, $query);
	
				if (!$result1) $error = die($link->error);

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

			}

		}

	}

?>