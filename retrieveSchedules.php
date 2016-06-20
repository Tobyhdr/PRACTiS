<?php

	session_start();

	require_once("con.php");
	
	if (!$_SESSION['id']) {
	
		header("Location:denied.html");
	
	}
	
	$conn = new mysqli($hn, $un, $pw, $db);
	if ($conn->connect_error) die($conn->connect_error);
	
	global $yearPost;
	global $monthPost;
	global $dayPost;
	global $doctor;
	global $error;
	
	if ($_POST['Müller'] || $_POST['Who']) {

    if ($_POST['Müller']) {
      $doctor = "jmu";
    } else {
      $doctor = "xpa";
    }

    $key = date('Y-m-d');

    $yearPost = substr($key, 0, -6);
    $monthPost = substr($key, 5, -3);
    $dayPost = substr($key, 8);
    $schedDate1 = $dayPost . '.' . $monthPost . '.' . $yearPost;
    $schedDate2 = $yearPost . '-' . $monthPost . '-' . $dayPost;
    $_GET['year'] = $yearPost;
    $_GET['month'] = $monthPost;
    
    $nameDay = date("l", mktime(0, 0, 0, $monthPost, $dayPost, $yearPost));
    $mydate = strtotime($schedDate1);
    $schedDate = $nameDay . ' ' . date('j F Y', $mydate);

    $query = "SELECT * FROM `scheduler_" . $doctor . "` WHERE `Date` LIKE '". $key . "%' ORDER BY `Date` asc";
    
    $result = $conn->query($query);
    
    if (!$result) die($conn->error);
    
    $rows = $result->num_rows;
    for ($i = 0 ; $i <= $rows ; ++$i) {
      $row = $result->fetch_array(MYSQLI_ASSOC);
      if ($row['Date'] == $schedDate2 . ' 23:00:00') {
        $comment = $row['Comment'];
        $kommentar = '<div id="kommentar">
				<span style="display:inline-block">
				<label for="Kommentar" style="display:block;">Kommentar</label>
				<textarea rows="8" cols="32">' . $comment . '</textarea>
				</span>
			  </div>';
	    } else {
			  $kommentar = '<div id="kommentar">
				<span style="display:inline-block">
					<label for="Kommentar" style="display:block;">Kommentar</label>
					<textarea rows="8" cols="32">' . $comment . '</textarea>
				</span>
			  </div>';
      }
    }
	    
    $retrieved = '<table id="myTable"><thead><tr><th>Zeit</th><th>Name</th><th>Ersch.</th><th>Bar</th><th>EC</th><th>Überw.</th><th>Typ</th><th>n. bez.</th></tr></thead><tbody>';
    //$rows = $result->num_rows;
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
            $retrieved .= '<td id="time">' . $time . '</td><td><input type="text" class="name Nichts" id="row' . $r . 'name" style="width: 300px;" value="' . $row['Name'] . '"></td><td><input type="text" class="number numberErsch" id="row' . $r . 'erschienen" style="width: 50px;" value="' . $row['Ersch'] . '"></td><td><input type="text" class="number numberBar" id="row' . $r . 'bar" style="width: 50px;" value="' . $row['Bar'] . '"></td><td><input type="text" class="number numberEC" id="row' . $r . 'EC" style="width: 50px;" value="' . $row['EC'] . '"></td><td><input type="text" class="number numberÜber" id="row' . $r . 'Überweisung" style="width: 50px;" value="' . $row['Ueberw'] . '"></td><td><select size="1" id="row' . $r . 'label">';
          } else {
            $retrieved .= '<td id="time">' . $time . '</td><td><input type="text" class="name ' . $type . '" id="row' . $r . 'name" style="width: 300px;" value="' . $row['Name'] . '"></td><td><input type="text" class="number numberErsch" id="row' . $r . 'erschienen" style="width: 50px;" value="' . $row['Ersch'] . '"></td><td><input type="text" class="number numberBar" id="row' . $r . 'bar" style="width: 50px;" value="' . $row['Bar'] . '"></td><td><input type="text" class="number numberEC" id="row' . $r . 'EC" style="width: 50px;" value="' . $row['EC'] . '"></td><td><input type="text" class="number numberÜber" id="row' . $r . 'Überweisung" style="width: 50px;" value="' . $row['Ueberw'] . '"></td><td><select size="1" id="row' . $r . 'label">';
          }
          if ($type == "") {
            $retrieved .= '<option value="0" selected="selected">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2">NeupatientTBC</option>
            <option value="3">Patient</option>
            <option value="4">PatientTBC</option>
            <option value="5">Pause</option>
            <option value="6">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "Nichts") {
            $retrieved .= '<option value="0" selected="selected">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2">NeupatientTBC</option>
            <option value="3">Patient</option>
            <option value="4">PatientTBC</option>
            <option value="5">Pause</option>
            <option value="6">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "Neupatient") {
            $retrieved .= '<option value="0">Nichts</option>
            <option value="1" selected="selected">Neupatient</option>
            <option value="2">NeupatientTBC</option>
            <option value="3">Patient</option>
            <option value="4">PatientTBC</option>
            <option value="5">Pause</option>
            <option value="6">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "NeupatientTBC") {
            $retrieved .= '<option value="0">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2" selected="selected">NeupatientTBC</option>
            <option value="3">Patient</option>
            <option value="4">PatientTBC</option
            ><option value="5">Pause</option>
            <option value="6">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "Patient") {
            $retrieved .= '<option value="0">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2">NeupatientTBC</option>
            <option value="3" selected="selected">Patient</option>
            <option value="4">PatientTBC</option>
            <option value="5">Pause</option>
            <option value="6">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "PatientTBC") {
            $retrieved .= '<option value="0">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2">NeupatientTBC</option>
            <option value="3">Patient</option>
            <option value="4" selected="selected">PatientTBC</option>
            <option value="5">Pause</option>
            <option value="6">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "Pause") {
            $retrieved .= '<option value="0">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2">Neupatient-TBC</option>
            <option value="3">Patient</option>
            <option value="4">PatientTBC</option>
            <option value="5" selected="selected">Pause</option>
            <option value="6">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "Puffer") {
            $retrieved .= '<option value="0">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2">NeupatientTBC</option>
            <option value="3">Patient</option>
            <option value="4">PatientTBC</option>
            <option value="5">Pause</option>
            <option value="6" selected="selected">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "ChiroYogaEvent") {
            $retrieved .= '<option value="0">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2">NeupatientTBC</option>
            <option value="3">Patient</option>
            <option value="4">PatientTBC</option>
            <option value="5">Pause</option>
            <option value="6">Puffer</option>
            <option value="7" selected="selected">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "PersonalEvent") {
            $retrieved .= '<option value="0">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2">NeupatientTBC</option>
            <option value="3">Patient</option>
            <option value="4">PatientTBC</option>
            <option value="5">Pause</option>
            <option value="6">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8" selected="selected">PersonalEvent</option>
            </select></td>';
          }
          if ($row['Nicht_bezahlt'] == 1) {
            $retrieved .= '<td><input type="text" id="row' . $r . 'bezahlt" style="width: 40px; text-align:center;" value="1" checked="checked"></td></tr>';
          } else {
            $retrieved .= '<td><input type="text" id="row' . $r . 'bezahlt" style="width: 40px; text-align:center;" value=""></td></tr>';
          }
          ++$j;
        } else {
          $retrieved .= '<td id="time">' . $time2 . '</td><td><input type="text" class="name Nichts" id="row' . $r . 'name" style="width: 300px;" value=""></td><td><input type="text" class="number numberErsch" id="row' . $r . 'erschienen" style="width: 50px;" value=""></td><td><input type="text" class="number numberBar" id="row' . $r . 'bar" style="width: 50px;" value=""></td><td><input type="text" class="number numberEC" id="row' . $r . 'EC" style="width: 50px;" value=""></td><td><input type="text" class="number numberÜber" id="row' . $r . 'Überweisung" style="width: 50px;" value=""></td><td><select size="1" id="row' . $r . 'label">
          <option value="0" selected="selected">Nichts</option>
          <option value="1">Neupatient</option>
          <option value="2">NeupatientTBC</option>
          <option value="3">Patient</option>
          <option value="4">PatientTBC</option>
          <option value="5">Pause</option>
          <option value="6">Puffer</option>
          <option value="7">ChiroYogaEvent</option>
          <option value="8">PersonalEvent</option>
          </select></td><td><input type="text" id="row' . $r . 'bezahlt" style="width: 40px; text-align:center;" value=""></td></tr>';
        }
      ++$r;
      }
    }
    
    $retrieved .= '<tr><td class="totals">Summen</td><td class="totals"></td><td class="number totals" id="erschienen" style="width: 50px;"></td><td class="number totals" id="bar" style="width: 50px;"></td><td class="number totals" id="EC" style="width: 50px;"></td><td class="number totals" id="Überwiesen" style="width: 50px;"></td><td class="number totals" id="dayTotal"></td></tbody></table>';

  } else if ($_POST['blockBook']) {

    $doctor = $_POST['doctor'];
    $yearPost = substr($_POST['From'], 0, -12);
    $currentYear = $yearPost;
    $monthPost = substr($_POST['From'], 5, -9);
    $dayPost = substr($_POST['From'], 8, -6);
    $schedDate1 = $dayPost . '.' . $monthPost . '.' . $yearPost;
    $schedDate2 = $yearPost . '-' . $monthPost . '-' . $dayPost;
    $_GET['year'] = $yearPost;
    $_GET['month'] = $monthPost;
    $Type = $_POST['bbType'];
    $Description = $_POST['Description'];

    if ($Type == "Urlaub") {
    	$Type = "PersonalEvent";
    	$Description = "–––  U R L A U B  –––";
    }

    $untilYear = substr($_POST['Until'], 0, -12);
    $untilMonth = substr($_POST['Until'], 5, -9);
    $untilDay = substr($_POST['Until'], 8, -6);
    $fromTime = substr($_POST['From'], 11);
    $untilTime = substr($_POST['Until'], 11);
    $fromHr = substr($fromTime, 0, -3);
    $fromMin = substr($fromTime, 3);
    $untilHr = substr($untilTime, 0, -3);
    $untilMin = substr($untilTime, 3);
    $untMin = substr($untilMin, 0, -1);

    $monthNumDays = date('t', strtotime($yearPost . '-' . $monthPost . '-01'));
    $numDays = 0;

    if ($untilDay < 10) {
    	$untDay = substr($untilDay, 1);
    } else {
    	$untDay = $untilDay;
    }

    if ($monthPost < 10) {
			$monStart = substr($monthPost, 1);
		} else {
			$monStart = $monthPost;
		}

		if ($untilMonth < 10) {
			$monEnd = substr($untilMonth, 1);
		} else {
		  $monEnd = $untilMonth;
		}

		if ($fromHr < 10) {
      $hour = substr($fromHr, 1);
    } else {
      $hour = $fromHr;
    }

    if ($untilHr < 10) {
      $untHour = substr($untilHr, 1);
    } else {
      $untHour = $untilHr;
    }

    if ($untilMin == "00" && $untHour > $hour) {
    	--$untHour;
    }

    if ($fromMin < 20) {
      $min = 0;
    } else if ($fromMin > 19 && $fromMin < 40) {
      $min = 2;
    } else {
      $min = 4;
    }

    if ($_POST['From'] == "" || $_POST['Until'] == "") {
    	$error = 'Bitte gültigen Daten eingeben.';
    	$key = date('Y-m-d');
	    $yearPost = substr($key, 0, -6);
	    $monthPost = substr($key, 5, -3);
	    $dayPost = substr($key, 8);
	    $schedDate1 = $dayPost . '.' . $monthPost . '.' . $yearPost;
	    $schedDate2 = $yearPost . '-' . $monthPost . '-' . $dayPost;
	    $_GET['year'] = $yearPost;
	    $_GET['month'] = $monthPost;
    } else if ($yearPost > $untilYear) {
    	$error = '<em>Bis</em>-Datum liegt vor <em>Von</em>-Datum. Bitte korrigieren.';
    } else if ($monthPost > $untilMonth && $yearPost >= $untilYear) {
    	$error = '<em>Bis</em>-Datum liegt vor <em>Von</em>-Datum. Bitte korrigieren.';
    } else if ($dayPost > $untilDay && $monthPost >= $untilMonth && $yearPost >= $untilYear) {
    	$error = '<em>Bis</em>-Datum liegt vor <em>Von</em>-Datum. Bitte korrigieren.';
    } else if ($_POST['From'] == $_POST['Until']) {
    	$error = '<em>Bis</em>-Datum ist genau gleich <em>Von</em>-Datum. Bitte korrigieren.';
    } else if ($monthPost === $untilMonth && $dayPost === $untilDay && $yearPost === $untilYear && $_POST['From'] != $_POST['Until']) {
	    $numDays = 1;
	    $numMonths = 1;
	  } else if ($monthPost === $untilMonth && $yearPost === $untilYear) {
	    $numDays = ($untilDay - $dayPost) + 1;
	    $numMonths = 1;
	  } else if (($untilMonth - $monthPost) != 0 && $yearPost === $untilYear) {
	    $numMonths = ($untilMonth - $monthPost) + 1;
	    //$numDays = (($monthNumDays - $dayPost) + 1) + $untilDay;
	    if ($numMonths >= 2) {
	      for ($m = $monStart ; $m <= $monEnd ; ++$m) {
	        if ($m < 10) {
	          $monthNumDays = date('t', strtotime($yearPost . '-0' . $m . '-01'));
	          $monthDays[] = $monthNumDays;
	        } else {
	          $monthNumDays = date('t', strtotime($yearPost . '-' . $m . '-01'));
	          $monthDays[] = $monthNumDays;
	        }
	      }
	    }
	  } else if ($yearPost < $untilYear) {
	    //Calculate number of days for this condition...
	    for ($y = $yearPost ; $y <= $untilYear ; ++$y) {
	      if ($y === $yearPost) {
	        for ($m = $monStart ; $m <= 12 ; ++$m) {
	          if ($m < 10) {
	            $monthNumDays = date('t', strtotime($currentYear . '-0' . $m . '-01'));
	            $monthDays[] = $monthNumDays;
	          } else {
	            $monthNumDays = date('t', strtotime($currentYear . '-' . $m . '-01'));
	            $monthDays[] = $monthNumDays;
	          }
	        }
	        ++$y;
	      } 
	      if (($untilYear - $y) >= 1) {
	        for ($n = 1 ; $n <= 12 ; ++$n) {
	          if ($n < 10) {
	            $monthNumDays = date('t', strtotime($currentYear . '-0' . $n . '-01'));
	            $monthDays[] = $monthNumDays;
	          } else {
	            $monthNumDays = date('t', strtotime($currentYear . '-' . $n . '-01'));
	            $monthDays[] = $monthNumDays;
	          }
	        }
	      } 
      }
      if ($y > $untilYear) {
        for ($o = 1 ; $o <= $monEnd ; ++$o) {
          if ($o < 10) {
            $monthNumDays = date('t', strtotime($currentYear . '-0' . $o . '-01'));
            $monthDays[] = $monthNumDays;
          } else {
            $monthNumDays = date('t', strtotime($currentYear . '-' . $o . '-01'));
            $monthDays[] = $monthNumDays;
          }
        }
      }
	  }

	  //Step through array and add all days of complete months plus selected days of partial months
	  $count = count($monthDays) -1;
	  if ($count > 0) {
	    for ($j = 0 ; $j <= $count ; ++$j) {
	      if ($j === 0) {
	        $numDays += ($monthDays[$j] - $dayPost) +1;
	      } else if ($j > 0 && $j < $count) {
	        $numDays += $monthDays[$j];
	      } else if ($j === $count) {
	        $numDays += $untilDay;
	      }
	    }
	  }

	  if ($error == "") {

		  if ($dayPost < 10) {
		    $day = substr($dayPost, 1);
		  } else {
		    $day = $dayPost;
		  }

		  if ($monthPost < 10) {
		    $month = substr($monthPost, 1);
		  } else {
		    $month = $monthPost;
		  }

		  for ($d = 1 ; $d <= $numDays ; ++$d) {

		  	if ($month < 10) {
		      $monthNumDays = date('t', strtotime($currentYear . '-0' . $month . '-01'));
		    } else {
		      $monthNumDays = date('t', strtotime($currentYear . '-' . $month . '-01'));
		    }

		    if ($day < 10 && $month < 10) {
		      $date = $currentYear . '-' . '0' . $month . '-' . '0' . $day;
		    } else if ($day < 10 && $month > 9){
		      $date = $currentYear . '-' . $month . '-' . '0' . $day;
		    } else if ($day > 9 && $month < 10){
		      $date = $currentYear . '-' . '0' . $month . '-' . $day;
		    } else {
		      $date = $currentYear . '-' . $month . '-' . $day;
		    }

		    $query = "SELECT * FROM `scheduler_" . $doctor . "` WHERE `Date` LIKE '". $date . "%' && `Typ` != 'Nichts' && `Typ` != 'Pause' && `Typ` != ''";
			  $result = $conn->query($query);

			  if (!$result) die($conn->error);

			  $rows = $result->num_rows;

			  //Reset $min here????

			  //Generate error message here as needed...
			  if ($rows >= 1 && $Type != "UrlaubLoeschen" && $Type != "ChiroYogaEventLoeschen") {
		      for ($j = 1 ; $j <= $rows ; ++$j) {
		        $row = $result->fetch_array(MYSQLI_ASSOC);
		        $time = substr($row['Date'], 11, -3);
		        //CHECK AGAINST GIVEN TIME FRAME HERE; bookings outside specified time frame irrelevant...
		        for ($h = $hour ; $h <= $untHour ; ++$h) {
		          if ($h > $hour) {
		            $min = 0;
		          }
		          for ($m = $min ; $m <= 4 ; ++$m) {

		            if ($h < 10) {
		              $time1 = '0' . $h . ':' . $m . '0';
		            } else {
		              $time1 = $h . ':' . $m . '0';
		            }

		            if ($time == $time1) {
		              $error .= $row['Typ'] . ' um ' . $time . ' am ' . $day . '.' . $month . '.' . $yearPost . '<br>';
		            }

		            if ($h == $untHour && $m >= $untMin) {
		            	break; //Stop here if untilMin is reached: don't complete all of untilHour
		            } else {
		            	++$m;
		            }

		          }
		        }
		      }
		    }

		    ++$day;

			  if ($day > $monthNumDays) {
			  	$day = 1;
			  	++$month;
			  	if ($month === 13) {
			  		$month = 1;
			  		++$currentYear;
			  	}
			  }

		  }

		  if ($error != "") {
		  	$error = '<strong>ACHTUNG!</strong> Gebuchten Termine wurden binnen Zeitrahmen gefunden:<br><br>' . $error . '<br>Alle Termine bitte umbuchen/löschen, dann veruschen Sie Ihr Blockbuchen erneut.';
		  } else { //The way is clear, let's write entries to the db...

		  	if ($dayPost < 10) { //Reset $day
			    $day = substr($dayPost, 1);
			  } else {
			    $day = $dayPost;
			  }

			  if ($monthPost < 10) { //Reset $month
			    $month = substr($monthPost, 1);
			  } else {
			    $month = $monthPost;
			  }

			  //Reset $currentYear
			  $currentYear = $yearPost;

		  	for ($d = 1 ; $d <= $numDays ; ++$d) {

			  	if ($month < 10) {
			      $monthNumDays = date('t', strtotime($currentYear . '-0' . $month . '-01'));
			    } else {
			      $monthNumDays = date('t', strtotime($currentYear . '-' . $month . '-01'));
			    }

			    if ($day < 10 && $month < 10) {
			      $date = $currentYear . '-' . '0' . $month . '-' . '0' . $day;
			    } else if ($day < 10 && $month > 9){
			      $date = $currentYear . '-' . $month . '-' . '0' . $day;
			    } else if ($day > 9 && $month < 10){
			      $date = $currentYear . '-' . '0' . $month . '-' . $day;
			    } else {
			      $date = $currentYear . '-' . $month . '-' . $day;
			    }

			    //First clear any EMPTY ("Nichts") entries before writing...
			    $query = "DELETE FROM `scheduler_" . $doctor . "` WHERE `Date` LIKE '". $date . "%' && `Typ` = 'Nichts'";
			    $result = $conn->query($query);

			    if (!$result) die($conn->error);

			    $rows = $result->num_rows;

			    //Then clear any FULLY EMPTY entries before writing...
          $query = "DELETE FROM `scheduler_" . $doctor . "` WHERE `Typ` = '' && `Comment` = ''";
          $result = $conn->query($query);

          if (!$result) die($conn->error);

          $rows = $result->num_rows;

			    if ($fromMin < 20) { //reset $min...
			      $min = "0";
			    } else if ($fromMin > 19 && $fromMin < 40) {
			      $min = "2";
			    } else {
			      $min = "4";
			    }

			    //It's ok to verwrite existing Pausen...
			    for ($h = $hour ; $h <= $untHour ; ++$h) {
			      if ($h > $hour) { $min = "0"; }
			      for ($m = $min ; $m <= 4 ; ++$m) {
			        if ($h < 10) {
			          $time1 = '0' . $h . ':' . $m . '0';
			        } else {
			          $time1 = $h . ':' . $m . '0';
			        }
			        $dateTime = $date . ' ' . $time1 . ':00';
			        if ($h == $untHour && $m >= $untMin && $untilMin != "00") {
			        	break;
			        }
			        if ($Type == "UrlaubLoeschen") {
			        	$query = "DELETE from `scheduler_" . $doctor . "` WHERE `Date` = '". $dateTime . "' && `Name` LIKE '%U R L A U B%'";
			        } else if ($Type == "ChiroYogaEventLoeschen") {
			        	$query = "DELETE from `scheduler_" . $doctor . "` WHERE `Date` = '". $dateTime . "' && `Typ` = 'ChiroYogaEvent'";
			        } else {
			        	$query = "SELECT * from `scheduler_" . $doctor . "` WHERE `Date` = '". $dateTime . "' && `Typ` = 'Pause'";
			        }

			        $result = $conn->query($query);

			        if (!$result) die($conn->error);

			        $rows = $result->num_rows;

			        if ($rows < 1 && $Type != "UrlaubLoeschen" && $Type != "ChiroYogaEventLoeschen") {
			          $query = "INSERT INTO `scheduler_" . $doctor . "` (`Date`,`Typ`,`Name`) 
			          VALUES 
			          ('" . $dateTime . "','" . $Type . "', '" . $Description . "')";

			          $result = $conn->query($query);

			          if (!$result) die($conn->error);

			        } else if ($rows >= 1 && $Type != "UrlaubLoeschen" && $Type != "ChiroYogaEventLoeschen") {
			        	$query = "UPDATE `scheduler_" . $doctor . "` SET 
			        	`Date` = '" . $dateTime . "',
			        	`Typ` = '" . $Type . "',
			        	`Name` = '" . $Description . "'
			        	WHERE `Date` = '" . $dateTime . "'";

			          $result = $conn->query($query);

			          if (!$result) die($conn->error);

			        }
			        ++$m;
			      }
			    }

			    ++$day;

			    if ($day > $monthNumDays) {
				  	$day = 1;
				  	++$month;
				  	if ($month === 13) {
				  		$month = 1;
				  		++$currentYear;
				  	}
				  }
				}

		  }

		}
    
    //----- Now draw scheduler table -----
    $nameDay = date("l", mktime(0, 0, 0, $monthPost, $dayPost, $yearPost));
    $mydate = strtotime($schedDate1);
    $schedDate = $nameDay . ' ' . date('j F Y', $mydate);

    $query = "SELECT * FROM `scheduler_" . $doctor . "` WHERE `Date` LIKE '". $schedDate2 . "%' ORDER BY `Date` asc";
    
    $result = $conn->query($query);
    
    if (!$result) die($conn->error);
    
    $rows = $result->num_rows;
    for ($i = 0 ; $i <= $rows ; ++$i) {
      $row = $result->fetch_array(MYSQLI_ASSOC);
      if ($row['Date'] == $schedDate2 . ' 23:00:00') {
        $comment = $row['Comment'];
        $kommentar = '<div id="kommentar">
				<span style="display:inline-block">
					<label for="Kommentar" style="display:block;">Kommentar</label>
					<textarea rows="8" cols="32">' . $comment . '</textarea>
				</span>
			  </div>';
	    } else {
			  $kommentar = '<div id="kommentar">
				<span style="display:inline-block">
				<label for="Kommentar" style="display:block;">Kommentar</label>
				<textarea rows="8" cols="32">' . $comment . '</textarea>
				</span>
			  </div>';
	    }
    }
    
    $retrieved = '<table id="myTable"><thead><tr><th>Zeit</th><th>Name</th><th>Ersch.</th><th>Bar</th><th>EC</th><th>Überw.</th><th>Typ</th><th>n. bez.</th></tr></thead><tbody>';
    //$rows = $result->num_rows;
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
            $retrieved .= '<td id="time">' . $time . '</td><td><input type="text" class="name Nichts" id="row' . $r . 'name" style="width: 300px;" value="' . $row['Name'] . '"></td><td><input type="text" class="number numberErsch" id="row' . $r . 'erschienen" style="width: 50px;" value="' . $row['Ersch'] . '"></td><td><input type="text" class="number numberBar" id="row' . $r . 'bar" style="width: 50px;" value="' . $row['Bar'] . '"></td><td><input type="text" class="number numberEC" id="row' . $r . 'EC" style="width: 50px;" value="' . $row['EC'] . '"></td><td><input type="text" class="number numberÜber" id="row' . $r . 'Überweisung" style="width: 50px;" value="' . $row['Ueberw'] . '"></td><td><select size="1" id="row' . $r . 'label">';
          } else {
            $retrieved .= '<td id="time">' . $time . '</td><td><input type="text" class="name ' . $type . '" id="row' . $r . 'name" style="width: 300px;" value="' . $row['Name'] . '"></td><td><input type="text" class="number numberErsch" id="row' . $r . 'erschienen" style="width: 50px;" value="' . $row['Ersch'] . '"></td><td><input type="text" class="number numberBar" id="row' . $r . 'bar" style="width: 50px;" value="' . $row['Bar'] . '"></td><td><input type="text" class="number numberEC" id="row' . $r . 'EC" style="width: 50px;" value="' . $row['EC'] . '"></td><td><input type="text" class="number numberÜber" id="row' . $r . 'Überweisung" style="width: 50px;" value="' . $row['Ueberw'] . '"></td><td><select size="1" id="row' . $r . 'label">';
          }
          if ($type == "") {
            $retrieved .= '<option value="0" selected="selected">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2">NeupatientTBC</option>
            <option value="3">Patient</option>
            <option value="4">PatientTBC</option>
            <option value="5">Pause</option>
            <option value="6">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "Nichts") {
            $retrieved .= '<option value="0" selected="selected">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2">NeupatientTBC</option>
            <option value="3">Patient</option>
            <option value="4">PatientTBC</option>
            <option value="5">Pause</option>
            <option value="6">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "Neupatient") {
            $retrieved .= '<option value="0">Nichts</option>
            <option value="1" selected="selected">Neupatient</option>
            <option value="2">NeupatientTBC</option>
            <option value="3">Patient</option>
            <option value="4">PatientTBC</option>
            <option value="5">Pause</option>
            <option value="6">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "NeupatientTBC") {
            $retrieved .= '<option value="0">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2" selected="selected">NeupatientTBC</option>
            <option value="3">Patient</option>
            <option value="4">PatientTBC</option
            ><option value="5">Pause</option>
            <option value="6">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "Patient") {
            $retrieved .= '<option value="0">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2">NeupatientTBC</option>
            <option value="3" selected="selected">Patient</option>
            <option value="4">PatientTBC</option>
            <option value="5">Pause</option>
            <option value="6">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "PatientTBC") {
            $retrieved .= '<option value="0">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2">NeupatientTBC</option>
            <option value="3">Patient</option>
            <option value="4" selected="selected">PatientTBC</option>
            <option value="5">Pause</option>
            <option value="6">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "Pause") {
            $retrieved .= '<option value="0">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2">Neupatient-TBC</option>
            <option value="3">Patient</option>
            <option value="4">PatientTBC</option>
            <option value="5" selected="selected">Pause</option>
            <option value="6">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "Puffer") {
            $retrieved .= '<option value="0">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2">NeupatientTBC</option>
            <option value="3">Patient</option>
            <option value="4">PatientTBC</option>
            <option value="5">Pause</option>
            <option value="6" selected="selected">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "ChiroYogaEvent") {
            $retrieved .= '<option value="0">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2">NeupatientTBC</option>
            <option value="3">Patient</option>
            <option value="4">PatientTBC</option>
            <option value="5">Pause</option>
            <option value="6">Puffer</option>
            <option value="7" selected="selected">ChiroYogaEvent</option>
            <option value="8">PersonalEvent</option>
            </select></td>';
          } else if ($type == "PersonalEvent") {
            $retrieved .= '<option value="0">Nichts</option>
            <option value="1">Neupatient</option>
            <option value="2">NeupatientTBC</option>
            <option value="3">Patient</option>
            <option value="4">PatientTBC</option>
            <option value="5">Pause</option>
            <option value="6">Puffer</option>
            <option value="7">ChiroYogaEvent</option>
            <option value="8" selected="selected">PersonalEvent</option>
            </select></td>';
          }
          if ($row['Nicht_bezahlt'] == 1) {
            $retrieved .= '<td><input type="text" id="row' . $r . 'bezahlt" style="width: 40px; text-align:center;" value="1" checked="checked"></td></tr>';
          } else {
            $retrieved .= '<td><input type="text" id="row' . $r . 'bezahlt" style="width: 40px; text-align:center;" value=""></td></tr>';
          }
          ++$j;
        } else {
          $retrieved .= '<td id="time">' . $time2 . '</td><td><input type="text" class="name Nichts" id="row' . $r . 'name" style="width: 300px;" value=""></td><td><input type="text" class="number numberErsch" id="row' . $r . 'erschienen" style="width: 50px;" value=""></td><td><input type="text" class="number numberBar" id="row' . $r . 'bar" style="width: 50px;" value=""></td><td><input type="text" class="number numberEC" id="row' . $r . 'EC" style="width: 50px;" value=""></td><td><input type="text" class="number numberÜber" id="row' . $r . 'Überweisung" style="width: 50px;" value=""></td><td><select size="1" id="row' . $r . 'label">
          <option value="0" selected="selected">Nichts</option>
          <option value="1">Neupatient</option>
          <option value="2">NeupatientTBC</option>
          <option value="3">Patient</option>
          <option value="4">PatientTBC</option>
          <option value="5">Pause</option>
          <option value="6">Puffer</option>
          <option value="7">ChiroYogaEvent</option>
          <option value="8">PersonalEvent</option>
          </select></td><td><input type="text" id="row' . $r . 'bezahlt" style="width: 40px; text-align:center;" value=""></td></tr>';
        }
      ++$r;
      }
    }
    
    $retrieved .= '<tr><td class="totals">Summen</td><td class="totals"></td><td class="number totals" id="erschienen" style="width: 50px;"></td><td class="number totals" id="bar" style="width: 50px;"></td><td class="number totals" id="EC" style="width: 50px;"></td><td class="number totals" id="Überwiesen" style="width: 50px;"></td><td class="number totals" id="dayTotal"></td></tbody></table>';

  } else if ($_POST) {
	
		foreach($_POST as $key => $value) {
   			/*Fetch data from submit to create h2 heading (date or Heute) 2016-05-02_xpa*/
   			$yearPost = substr($key, 0, -10);
   			$monthPost = substr($key, 5, -7);
   			$dayPost = substr($key, 8, -4);
   			$doctor = substr($key, 11);
   			$schedDate1 = $dayPost . '.' . $monthPost . '.' . $yearPost;
   			$schedDate2 = $yearPost . '-' . $monthPost . '-' . $dayPost;
   			$_GET['year'] = $yearPost;
   			$_GET['month'] = $monthPost;
		}

		echo $dayPost;
		if ($_POST['doctor']) {
			$doctor = $_POST['doctor'];
		}
		
		$nameDay = date("l", mktime(0, 0, 0, $monthPost, $dayPost, $yearPost));
		$mydate = strtotime($schedDate1);
		$schedDate = $nameDay . ' ' . date('j F Y', $mydate);
		
		$query = "SELECT * FROM `scheduler_" . $doctor . "` WHERE `Date` LIKE '". $schedDate2 . "%' ORDER BY `Date` asc";
		
		$result = $conn->query($query);
		
		if (!$result) die($conn->error);
		
		$rows = $result->num_rows;
		for ($i = 0 ; $i <= $rows ; ++$i) {
			$row = $result->fetch_array(MYSQLI_ASSOC);
			if ($row['Date'] == $schedDate2 . ' 23:00:00') {
        $comment = $row['Comment'];
        $kommentar = '<div id="kommentar">
				<span style="display:inline-block">
				<label for="Kommentar" style="display:block;">Kommentar</label>
				<textarea rows="8" cols="32">' . $comment . '</textarea>
				</span>
			  </div>';
		  } else {
			  $kommentar = '<div id="kommentar">
				<span style="display:inline-block">
				<label for="Kommentar" style="display:block;">Kommentar</label>
				<textarea rows="8" cols="32">' . $comment . '</textarea>
				</span>
			  </div>';
	      }
		}
		
		$retrieved = '<table id="myTable"><thead><tr><th>Zeit</th><th>Name</th><th>Ersch.</th><th>Bar</th><th>EC</th><th>Überw.</th><th>Typ</th><th>n. bez.</th></tr></thead><tbody>';
		//$rows = $result->num_rows;
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
						$retrieved .= '<td id="time">' . $time . '</td><td><input type="text" class="name Nichts" id="row' . $r . 'name" style="width: 300px;" value="' . $row['Name'] . '"></td><td><input type="text" class="number numberErsch" id="row' . $r . 'erschienen" style="width: 50px;" value="' . $row['Ersch'] . '"></td><td><input type="text" class="number numberBar" id="row' . $r . 'bar" style="width: 50px;" value="' . $row['Bar'] . '"></td><td><input type="text" class="number numberEC" id="row' . $r . 'EC" style="width: 50px;" value="' . $row['EC'] . '"></td><td><input type="text" class="number numberÜber" id="row' . $r . 'Überweisung" style="width: 50px;" value="' . $row['Ueberw'] . '"></td><td><select size="1" id="row' . $r . 'label">';
					} else {
						$retrieved .= '<td id="time">' . $time . '</td><td><input type="text" class="name ' . $type . '" id="row' . $r . 'name" style="width: 300px;" value="' . $row['Name'] . '"></td><td><input type="text" class="number numberErsch" id="row' . $r . 'erschienen" style="width: 50px;" value="' . $row['Ersch'] . '"></td><td><input type="text" class="number numberBar" id="row' . $r . 'bar" style="width: 50px;" value="' . $row['Bar'] . '"></td><td><input type="text" class="number numberEC" id="row' . $r . 'EC" style="width: 50px;" value="' . $row['EC'] . '"></td><td><input type="text" class="number numberÜber" id="row' . $r . 'Überweisung" style="width: 50px;" value="' . $row['Ueberw'] . '"></td><td><select size="1" id="row' . $r . 'label">';
					}
					if ($type == "") {
						$retrieved .= '<option value="0" selected="selected">Nichts</option>
						<option value="1">Neupatient</option>
						<option value="2">NeupatientTBC</option>
						<option value="3">Patient</option>
						<option value="4">PatientTBC</option>
						<option value="5">Pause</option>
						<option value="6">Puffer</option>
						<option value="7">ChiroYogaEvent</option>
						<option value="8">PersonalEvent</option>
						</select></td>';
					} else if ($type == "Nichts") {
						$retrieved .= '<option value="0" selected="selected">Nichts</option>
						<option value="1">Neupatient</option>
						<option value="2">NeupatientTBC</option>
						<option value="3">Patient</option>
						<option value="4">PatientTBC</option>
						<option value="5">Pause</option>
						<option value="6">Puffer</option>
						<option value="7">ChiroYogaEvent</option>
						<option value="8">PersonalEvent</option>
						</select></td>';
					} else if ($type == "Neupatient") {
						$retrieved .= '<option value="0">Nichts</option>
						<option value="1" selected="selected">Neupatient</option>
						<option value="2">NeupatientTBC</option>
						<option value="3">Patient</option>
						<option value="4">PatientTBC</option>
						<option value="5">Pause</option>
						<option value="6">Puffer</option>
						<option value="7">ChiroYogaEvent</option>
						<option value="8">PersonalEvent</option>
						</select></td>';
					} else if ($type == "NeupatientTBC") {
						$retrieved .= '<option value="0">Nichts</option>
						<option value="1">Neupatient</option>
						<option value="2" selected="selected">NeupatientTBC</option>
						<option value="3">Patient</option>
						<option value="4">PatientTBC</option
						><option value="5">Pause</option>
						<option value="6">Puffer</option>
						<option value="7">ChiroYogaEvent</option>
						<option value="8">PersonalEvent</option>
						</select></td>';
					} else if ($type == "Patient") {
						$retrieved .= '<option value="0">Nichts</option>
						<option value="1">Neupatient</option>
						<option value="2">NeupatientTBC</option>
						<option value="3" selected="selected">Patient</option>
						<option value="4">PatientTBC</option>
						<option value="5">Pause</option>
						<option value="6">Puffer</option>
						<option value="7">ChiroYogaEvent</option>
						<option value="8">PersonalEvent</option>
						</select></td>';
					} else if ($type == "PatientTBC") {
						$retrieved .= '<option value="0">Nichts</option>
						<option value="1">Neupatient</option>
						<option value="2">NeupatientTBC</option>
						<option value="3">Patient</option>
						<option value="4" selected="selected">PatientTBC</option>
						<option value="5">Pause</option>
						<option value="6">Puffer</option>
						<option value="7">ChiroYogaEvent</option>
						<option value="8">PersonalEvent</option>
						</select></td>';
					} else if ($type == "Pause") {
						$retrieved .= '<option value="0">Nichts</option>
						<option value="1">Neupatient</option>
						<option value="2">Neupatient-TBC</option>
						<option value="3">Patient</option>
						<option value="4">PatientTBC</option>
						<option value="5" selected="selected">Pause</option>
						<option value="6">Puffer</option>
						<option value="7">ChiroYogaEvent</option>
						<option value="8">PersonalEvent</option>
						</select></td>';
					} else if ($type == "Puffer") {
						$retrieved .= '<option value="0">Nichts</option>
						<option value="1">Neupatient</option>
						<option value="2">NeupatientTBC</option>
						<option value="3">Patient</option>
						<option value="4">PatientTBC</option>
						<option value="5">Pause</option>
						<option value="6" selected="selected">Puffer</option>
						<option value="7">ChiroYogaEvent</option>
						<option value="8">PersonalEvent</option>
						</select></td>';
					} else if ($type == "ChiroYogaEvent") {
						$retrieved .= '<option value="0">Nichts</option>
						<option value="1">Neupatient</option>
						<option value="2">NeupatientTBC</option>
						<option value="3">Patient</option>
						<option value="4">PatientTBC</option>
						<option value="5">Pause</option>
						<option value="6">Puffer</option>
						<option value="7" selected="selected">ChiroYogaEvent</option>
						<option value="8">PersonalEvent</option>
						</select></td>';
					} else if ($type == "PersonalEvent") {
						$retrieved .= '<option value="0">Nichts</option>
						<option value="1">Neupatient</option>
						<option value="2">NeupatientTBC</option>
						<option value="3">Patient</option>
						<option value="4">PatientTBC</option>
						<option value="5">Pause</option>
						<option value="6">Puffer</option>
						<option value="7">ChiroYogaEvent</option>
						<option value="8" selected="selected">PersonalEvent</option>
						</select></td>';
					}
					if ($row['Nicht_bezahlt'] == 1) {
						$retrieved .= '<td><input type="text" id="row' . $r . 'bezahlt" style="width: 40px; text-align:center;" value="1" checked="checked"></td></tr>';
					} else {
						$retrieved .= '<td><input type="text" id="row' . $r . 'bezahlt" style="width: 40px; text-align:center;" value=""></td></tr>';
					}
					++$j;
				} else {
					$retrieved .= '<td id="time">' . $time2 . '</td><td><input type="text" class="name Nichts" id="row' . $r . 'name" style="width: 300px;" value=""></td><td><input type="text" class="number numberErsch" id="row' . $r . 'erschienen" style="width: 50px;" value=""></td><td><input type="text" class="number numberBar" id="row' . $r . 'bar" style="width: 50px;" value=""></td><td><input type="text" class="number numberEC" id="row' . $r . 'EC" style="width: 50px;" value=""></td><td><input type="text" class="number numberÜber" id="row' . $r . 'Überweisung" style="width: 50px;" value=""></td><td><select size="1" id="row' . $r . 'label">
					<option value="0" selected="selected">Nichts</option>
					<option value="1">Neupatient</option>
					<option value="2">NeupatientTBC</option>
					<option value="3">Patient</option>
					<option value="4">PatientTBC</option>
					<option value="5">Pause</option>
					<option value="6">Puffer</option>
					<option value="7">ChiroYogaEvent</option>
					<option value="8">PersonalEvent</option>
					</select></td><td><input type="text" id="row' . $r . 'bezahlt" style="width: 40px; text-align:center;" value=""></td></tr>';
				}
			++$r;
			}
		}
		
		$retrieved .= '<tr><td class="totals">Summen</td><td class="totals"></td><td class="number totals" id="erschienen" style="width: 50px;"></td><td class="number totals" id="bar" style="width: 50px;"></td><td class="number totals" id="EC" style="width: 50px;"></td><td class="number totals" id="Überwiesen" style="width: 50px;"></td><td class="number totals" id="dayTotal"></td></tbody></table>';
		
	}
	
?>