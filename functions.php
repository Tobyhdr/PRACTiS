<?php

	function generate_pastVisit_data($PATID) {

		global $link;
		$link = mysqli_connect("localhost", "root", "JhKM^1qayXSW8297", "chiroyoga");

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

		return $pastVisits1;

	}

?>