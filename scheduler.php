<?php 

	session_start();

	require_once("connection.php");
	require_once("retrieveSchedules.php");

	if (!$_SESSION['id']) {
	
		header("Location:denied.html");
	
	}
	
	global $heute;
	global $yearH2;
	global $monthH2;
  global $doctor;
  global $doctorName;
	
	if ($_GET['year']) {
		$yearH2 = $_GET['year'];
		$monthH2 = $_GET['month'];
	}
	
	$heute = date('d\.m\.Y');

?>

<!DOCTYPE html>
<html lang="en">
  <head>
	<meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title>Chiro-Yoga | Scheduler</title>
	
	<!-- Bootstrap -->
    <link href="Bootstrap/css/bootstrap.min.css" rel="stylesheet">    

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
     <!--The order of jQuery modules is critical!!!-->
    <script type="text/javascript" src="jQuery/jquery.min.js"></script>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script><!--Needed for modals -->
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css"><!-- Needed for autofill jQuery stuff-->
    <script type="text/javascript" src="jQuery/jquery-ui.min.js"></script><!-- Needed for autofill jQuery stuff-->
	<link rel="stylesheet" type="text/css" href="schedStyles.css" />
	<link rel="stylesheet" type="text/css"
          href="https://fonts.googleapis.com/css?family=Philosopher|Ubuntu+Condensed">

  </head>
	
	<body>
	
	<div class="navbar navbar-default navbar-fixed-top">
  
  		<div class="container">
  	
  			<div class="navbar-header">
  			
  				<a class="navbar-brand pull-left"><img src="images/Practis_logo.png" width="122"></a>
  				
  				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
    			
    				<span class="sr-only">Toggle navigation</span>
    			
    				<span class="icon-bar"></span>
    				<span class="icon-bar"></span>
    				<span class="icon-bar"></span>
    			</button>
    			
    		</div>
    		
    		<div class="collapse navbar-collapse">
  				
  				<ul class="nav navbar-nav">
    			
    				<li> <a href="softwareFront.php"><span class="glyphicon glyphicon-home large" aria-hidden="true"></span></a></li>
    				<li> <a href="scheduler.php"><span class="glyphicon glyphicon-calendar large" style="color:#F11C1C;" aria-hidden="true"></span></a></li>
    				<li> <a href="waitingList.php"><span class="glyphicon glyphicon-hourglass" aria-hidden="true"></span></a></li>
    				<li> <a href="patientNotes.php"><span class="glyphicon glyphicon-pencil large" aria-hidden="true"></span></a></li>
    				<li> <a href="invoiceX.php"><span class="glyphicon glyphicon-usd large" aria-hidden="true"></span></a></li>
    				<li> <a href="email.php"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></a></li>
    				<li> <a href="stats.php"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span></a></li>
    			
    			</ul>
  		
  				<div class="navbar-right">
  					<ul class= "navbar-nav nav">
  			
  						<li><a href="index.php?logout=1"><span class="glyphicon glyphicon-log-out large" aria-hidden="true"></span></a></li>
  			
  					</ul>
  			
  				</div>
  		
  			</div>	
  	
  		</div>
  		
  	</div>
  	
  	<div id="topContainer2">
  	
  	<div id="invisDiv">

      <div>
        <? if (!empty($error)) { ?>
          <div class="modal fade" id="error" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">  &times;</button>
                    <h4 class="modal-title">Fehler</h4>
                  </div>
                  <div class="modal-body">
                    <? echo $error; ?>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Schliessen</button>
                  </div>
              </div>
            </div>
          </div>
        <? } ?>
      </div>

      <form method="post" action="scheduler.php">

      <div id="formBb">

        <div class="form-group" id="doctors">

          <input style="width: 83px; margin-right: 8px" type="submit" name="Müller" value="Dr Müller" class="btn btn-secondary btn-md" />
          <input style="width: 83px;" type="submit" name="Who" value="Dr Who" class="btn btn-secondary btn-md"/>

        </div>

        <?php

          if ($doctor != "") {

          	if ($doctor == "jmu") {
          		$doctorName = "Dr Müller";
              $image = "Mueller.jpg";
          	} else if ($doctor == "xpa") {
          		$doctorName = "Dr Who";
              $image = "Who.png";
          	}

            $blockBuchen = '<h3 style="font-size: 1.08em; text-align:left;">Blockbuchen</h3>';

            $blockBuchen = '<div class="form-group" id="blockBook">

              <p style="font-size: .9em;"><em>Typ</em></p>
              <select name="bbType" style="color:#4A5C5F; font-size: .8em;" size="1">
                <option>ChiroYogaEvent</option>
                <option>ChiroYogaEventLoeschen</option>
                <option>PersonalEvent</option>
                <option>Pause</option>
                <option>Urlaub</option>
                <option>UrlaubLoeschen</option>
              </select>
              <p style="margin-top:5px; font-size: .9em;"><em>Beschreibung</em></p>
              <input type="text" name="Description" style="width:160px; color:#4A5C5F; border:solid .4px gray; background-color:white;"/>
              <p style="margin-top:5px; font-size: .9em;"><em>Von</em></p>
              <input type="datetime-local" name="From" style="width:160px; color:#4A5C5F; border:solid .4px gray; background-color:white;"/>
              <p style="margin-top:5px; font-size: .9em;"><em>Bis</em></p>
              <input type="datetime-local" name="Until" style="width:160px; color:#4A5C5F; border:solid .4px gray; background-color:white; margin-bottom:10px;">
              <input type="submit" name="blockBook" value="Eingeben" class="btn btn-success btn-sm" style="margin-left:95px;"/>

            </div>';

            $portrait = '<div id="portrait">
              <img src="images/' . $image . '" width="65" height="82.3">
            </div>';

            echo $portrait;
            echo $blockBuchen;

          }

        ?>

        <h3 style="font-size:14px;">Aktives Datum: <br><? echo $schedDate; ?>
        <input type="hidden" id="doctorHidden" name="doctor" value="<? echo $doctor; ?>"/>

      </div>
  	
  		<div class="form-group" id="scheduleMain">
  			
  			<? if ($schedDate1 == $heute) {
  			?>
		
				<h2 style="padding-bottom:0; margin-bottom:0;"><span class="glyphicon glyphicon-calendar"></span> Heute</h2>
				<h3 style="text-align: center; font-weight:200; text-shadow: .7px .7px #042C84; font-size:18px; padding:0 0 10px 0; margin: 0;">(<? echo $schedDate; ?>)</h3>
				<? echo $retrieved; ?>
        <br>
		
			<? } else if ($schedDate)  { 
			?>
		
				<h2 style="margin-bottom:29px;"><span class="glyphicon glyphicon-calendar"></span> <? echo $schedDate; ?></h2>
				<? echo $retrieved; ?>
		
			<? } else if ($_GET['year'])  {
			?>
		
				<h2><? echo $monthH2; ?>/<? echo $yearH2; ?></h2>
		
			<? } else { 
			?>
		
				<h2 style="margin-top: 35px;"><span class="glyphicon glyphicon-calendar"></span></h2>
				
			<? } ?>
  		
  		</div>
  	
  <?php
  	
  /**
  * @author  Xu Ding
  * @email   thedilab@gmail.com
  * @website http://www.StarTutorial.com
  * @revised by Alessandro Marinuzzi
  * @website http://www.alecos.it/
  * @revised by Toby Russell
  * @website http://www.postpostism.net
  **/
  class Calendar {
    /**
    ** Constructor
    **/
    public function __construct() {
      $this->naviHref = htmlentities($_SERVER['PHP_SELF']);
    }
    /********************* PROPERTY ********************/
    private $dayLabels = array("Mo", "Di", "Mi", "Do", "Fr", "Sa", "So");
    private $currentYear = 0;
    private $currentMonth = 0;
    private $currentDay = 0;
    private $currentDate = null;
    private $daysInMonth = 0;
    private $naviHref = null;
    /********************* PUBLIC **********************/
    /**
    ** print out the calendar
    **/
    public function show() {
      $year = null;
      $month = null;
      if (null == $year && isset($_GET['year'])) {
        $year = $_GET['year'];
      } elseif (null == $year) {
        $year = date("Y", time());
      }
      if (null == $month && isset($_GET['month'])) {
        $month = $_GET['month'];
      } elseif (null == $month) {
        $month = date("m", time());
      }
      //Make sure we retrieve data from correct database...
      if ($_POST['doctor'] == "jmu" || $_POST['doctor'] == "xpa") {
        $doctor = $_POST['doctor'];
        if ($_POST['doctor'] == "jmu") {
          $doctorName = "Dr Müller";
        } else if ($_POST['doctor'] == "xpa") {
          $doctorName = "Dr Who";
        }
      } 
      if ($_POST['Müller']) {
        $doctor = "jmu";
        $doctorName = "Dr Müller";
      } 
      if ($_POST['Who']) {
        $doctor = "xpa";
        $doctorName = "Dr Who";
      }
      $this->currentYear = $year;
      $this->currentMonth = $month;
      $this->daysInMonth = $this->_daysInMonth($month, $year);
      $content = '<div class="form-group" id="calendar">' . "\r\n" . '<div class="calendar_box">' . "\r\n" . $this->_createNavi($doctor, $doctorName) . "\r\n" . '</div>' . "\r\n" . '<div class="calendar_content">' . "\r\n" . '<ul class="calendar_label">' . "\r\n" . $this->_createLabels() . '</ul>' . "\r\n";
      $content .= '<div class="calendar_clear"></div>' . "\r\n";
      $content .= '<ul class="calendar_dates">' . "\r\n";
      $weeksInMonth = $this->_weeksInMonth($month, $year);
      // Create weeks in a month
      for ($i = 0; $i < $weeksInMonth; $i++) {
        //Create days in a week
        for ($j = 1; $j <= 7; $j++) {
          $content .= $this->_showDay($i * 7 + $j, $doctor);
        }
      }
      $content .= '</ul>' . "\r\n";
      $content .= '<div class="calendar_clear"></div>' . "\r\n";
      $content .= '</div>' . "\r\n";
      $content .= '</div>' . "\r\n";
      return $content;
    }
    /********************* PRIVATE **********************/ 
    /**
    ** create the li element for ul
    **/
    private function _showDay($cellNumber, $doctor) {
      if ($this->currentDay == 0) {
        $firstDayOfTheWeek = date('N', strtotime($this->currentYear . '-' . $this->currentMonth . '-01'));
        if (intval($cellNumber) == intval($firstDayOfTheWeek)) {
          $this->currentDay = 1;
        }
      }
      if (($this->currentDay != 0) && ($this->currentDay <= $this->daysInMonth)) {
        $this->currentDate = date('Y-m-d', strtotime($this->currentYear . '-' . $this->currentMonth . '-' . ($this->currentDay)));
        //Fetch day's busyness and assign background-color 
        $thisDate = $this->currentDate;
        $link = mysqli_connect("localhost", "root", "JhKM^1qayXSW8297", "chiroyoga");
        //$link = mysqli_connect("localhost", "cl17-chiroyoga", "JhKM^6Wzq", "cl17-chiroyoga");
    		$query = "SELECT * FROM `scheduler_" . $doctor . "` WHERE `Date` LIKE '" . $thisDate . "%' && `Typ` LIKE '%atient'";
    		$result = mysqli_query($link, $query);
    		if (!$result) die($link->error);
    		$row_cnt = mysqli_num_rows($result);
    		if ($row_cnt < 7) {
    			$color = "#B6D9EA";
    		} else if ($row_cnt < 14) {
    			$color = "#F8C95C";
    		} else {
    			$color = "#FF4848";
    		}
        $query = "SELECT * FROM `scheduler_" . $doctor . "` WHERE `Date` LIKE '" . $thisDate . "%' && `Name` LIKE '%U R L A U B%'";
        $result = mysqli_query($link, $query);
        if (!$result) die($link->error);
        $url_cnt = mysqli_num_rows($result);
        if ($url_cnt > 30) {
          $color = "white";
          $fontColor = "red";
        } else {
          $fontColor = "#000";
        }
        $query = "SELECT * FROM `scheduler_" . $doctor . "` WHERE `Date` LIKE '" . $thisDate . "%' && `Typ` = 'ChiroYogaEvent'";
        $result = mysqli_query($link, $query);
        if (!$result) die($link->error);
        $url_cnt = mysqli_num_rows($result);
        if ($url_cnt > 30) {
          $color = "#B8FAB4";
          $fontColor = "red";
        } else if ($fontColor != "red") {
          $fontColor = "#000";
        }
        $cellContent = $this->currentDay;
        $this->currentDay++;
      } else {
        $this->currentDate = null;
        $cellContent = null;
      }
      $today_day = date("d");
      $today_mon = date("m");
      $today_yea = date("Y");
      if (isset($_GET['month'])) {
      	$schedMon = $_GET['month'];
      } else {
      	$schedMon = date("m", time());
      }
      if (isset($_GET['year'])) {
      	$schedYear = $_GET['year'];
      } else {
      	$schedYear = date("Y", time());
      }
      $class_day = ($cellContent == $today_day && $this->currentMonth == $today_mon && $this->currentYear == $today_yea ? "calendar_today" : "calendar_days");
      /*if ($cellContent == $today_day && $this->currentMonth == $today_mon && $this->currentYear == $today_yea) {
        $class_day = "calendar_today";
      } else if ($dayPost && ($cellContent == $dayPost) {
        $borderColor = "#someColor";
      } else {
        $class_day = "calendar_days";
      }*/
      if ($cellContent && $cellContent < 10) {
        return '<li style="background-color: ' . $color . '; color: ' . $fontColor . '; opacity: 0.9;"  class="' . $class_day . '"><input type="submit" name="' . $schedYear . '-' . $schedMon . '-0' . $cellContent . '_' . $doctor . '" value="0' . $cellContent . '"/></li>' . "\r\n";
      } else  if ($cellContent && $cellContent > 9) {
        return '<li style="background-color: ' . $color . '; color: ' . $fontColor . '; opacity: 0.9;"  class="' . $class_day . '"><input type="submit" name="' . $schedYear . '-' . $schedMon . '-' . $cellContent . '_' . $doctor . '" value="' . $cellContent . '"/></li>' . "\r\n";
      } else {
        return '<li class="' . $class_day . '">' . $cellContent . '</li>' . "\r\n";
      }
    }
    /**
    ** create navigation
    **/
    private function _createNavi($doctor, $doctorName) {
      $nextMonth = $this->currentMonth == 12 ? 1 : intval($this->currentMonth)+1;
      $nextYear = $this->currentMonth == 12 ? intval($this->currentYear)+1 : $this->currentYear;
      $preMonth = $this->currentMonth == 1 ? 12 : intval($this->currentMonth)-1;
      $preYear = $this->currentMonth == 1 ? intval($this->currentYear)-1 : $this->currentYear;
      return '<div class="calendar_header">' . "\r\n" . '<input class="calendar_prev" type="submit" name="' . $preYear . '-' . sprintf('%02d', $preMonth) . '-01_' . $doctor . '" value="☚"/>' . "\r\n" . '<span class="calendar_title">' . $doctorName . ' ' . date('Y M', strtotime($this->currentYear . '-' . $this->currentMonth . '-1')) . '</span>' . "\r\n" . '<input class="calendar_next" type="submit" name="' . $nextYear . '-' . sprintf('%02d', $nextMonth) . '-01_' . $doctor . '" value="☛"/>' . "\r\n"  . '</div>';
    }
    /**
    ** create calendar week labels
    **/
    private function _createLabels() {
      $content = '';
      foreach ($this->dayLabels as $index => $label) {
        $content .= '<li class="calendar_names">' . $label.'</li>' . "\r\n";
      }
      return $content;
    }
    /**
    ** calculate number of weeks in a particular month
    **/
    private function _weeksInMonth($month = null, $year = null) {
      if (null == ($year)) {
        $year = date("Y", time());
      }
      if (null == ($month)) {
        $month = date("m", time());
      }
      // find number of days in this month
      $daysInMonths = $this->_daysInMonth($month, $year);
      $numOfweeks = ($daysInMonths % 7 == 0 ? 0 : 1) + intval($daysInMonths / 7);
      $monthEndingDay = date('N',strtotime($year . '-' . $month . '-' . $daysInMonths));
      $monthStartDay = date('N',strtotime($year . '-' . $month . '-01'));
      if ($monthEndingDay < $monthStartDay) {
        $numOfweeks++;
      }
      return $numOfweeks;
    }
    /**
    ** calculate number of days in a particular month
    **/
    private function _daysInMonth($month = null, $year = null) {
      if (null == ($year)) $year = date("Y",time());
      if (null == ($month)) $month = date("m",time());
      return date('t', strtotime($year . '-' . $month . '-01'));
    }
  }

  if ($doctor != "") {
    $calendar = new Calendar();
    echo $calendar->show();
  }
?>

    </form>
  	
  		<?php

      if ($doctor != "") {

        $patSearch = '<div id="patSearch">
          <span style="display:inline-block">
            <label for="patSearch" style="display:block;">Patientensuche</label>
            <input id="pSearch" type="text" style="width: 180px; background-color: white; border:solid .4px gray;"/>
          </span>
        </div>';

        $numSearch = '<div id="numSearch">
          <span style="display:inline-block">
            <label for="numSearch" style="display:block;">Per Nummer suchen</label>
            <input id="nSearch" type="text" style="width: 180px; background-color: white; border:solid .4px gray;"/>
          </span>
        </div>';

        echo $kommentar;
        echo $patSearch;
        echo $numSearch;

      }

      ?>
		
  	</div>
  	
  	</div>
    
    <script>
    	
    	$(document).ready(function($){
    		
			$('.name').autocomplete({
				source:'schedAutocomp.php', 
				minLength:2
			});

      $('#pSearch').autocomplete({
        source:'patSearch.php', 
        minLength:4
      });

      $('#nSearch').autocomplete({
        source:'numSearch.php', 
        minLength:6
      });

      $('ul.ui-autocomplete.ui-menu').css({"font-family": "'Ubuntu Condensed'", "font-size": "12px"});
				
		});
		
	</script>
	
	<script>
  //Add numbers that need adding on document load when new number entered...
	
	$('#myTable').change(function() {
	
		var els = $(document.getElementsByClassName("numberErsch"));
		var sumErsch = 0;
		var sumBar = 0;
		var sumEC = 0;
		var sumÜber = 0;
		var num = 0;
		Array.prototype.forEach.call(els, function(el) {
			if ( el.value == "1" ) { 
    			num = parseInt(el.value);
    			sumErsch += num;
    		}
		});
		
		var els = $(document.getElementsByClassName("numberBar"));
		Array.prototype.forEach.call(els, function(el) {
			if ( el.value ) { 
    			num = parseInt(el.value);
    			sumBar += num;
    		}
		});
		
		var els = $(document.getElementsByClassName("numberEC"));
		Array.prototype.forEach.call(els, function(el) {
			if ( el.value ) { 
    			num = parseInt(el.value);
    			sumEC += num;
    		}
		});
		
		var els = $(document.getElementsByClassName("numberÜber"));
		Array.prototype.forEach.call(els, function(el) {
			if ( el.value ) { 
    			num = parseInt(el.value);
    			sumÜber += num;
    		}
		});
		
		var dayTotal = sumBar + sumEC + sumÜber;
		dayTotal = "€" + dayTotal + ".00";
		
		document.getElementById("erschienen").innerHTML = sumErsch;
		document.getElementById("bar").innerHTML = sumBar;
		document.getElementById("EC").innerHTML = sumEC;
		document.getElementById("Überwiesen").innerHTML = sumÜber;
		document.getElementById("dayTotal").innerHTML = dayTotal;
	
	});
	
	</script>
	
	<script>
  //Add numbers when page loads
	
	$(document).ready(function($){
	
		var els = $(document.getElementsByClassName("numberErsch"));
		var sumErsch = 0;
		var sumBar = 0;
		var sumEC = 0;
		var sumÜber = 0;
		var num = 0;
		Array.prototype.forEach.call(els, function(el) {
			if ( el.value == "1" ) { 
    			num = parseInt(el.value);
    			sumErsch += num;
    		}
		});
		
		var els = $(document.getElementsByClassName("numberBar"));
		Array.prototype.forEach.call(els, function(el) {
			if ( el.value ) { 
    			num = parseInt(el.value);
    			sumBar += num;
    		}
		});
		
		var els = $(document.getElementsByClassName("numberEC"));
		Array.prototype.forEach.call(els, function(el) {
			if ( el.value ) { 
    			num = parseInt(el.value);
    			sumEC += num;
    		}
		});
		
		var els = $(document.getElementsByClassName("numberÜber"));
		Array.prototype.forEach.call(els, function(el) {
			if ( el.value ) { 
    			num = parseInt(el.value);
    			sumÜber += num;
    		}
		});
		
		var dayTotal = sumBar + sumEC + sumÜber;
		dayTotal = "€" + dayTotal + ".00";
		
		document.getElementById("erschienen").innerHTML = sumErsch;
		document.getElementById("bar").innerHTML = sumBar;
		document.getElementById("EC").innerHTML = sumEC;
		document.getElementById("Überwiesen").innerHTML = sumÜber;
		document.getElementById("dayTotal").innerHTML = dayTotal;
	
	});
	
	</script>
	
	<script>
		
		$('select').change(function() {
		//This function sets background colour of "Name" table cell in affected row
			for (var j = 1 ; j <= 39 ; ++j) {
		
				var typeOptions = document.getElementById('row' + j + 'label');

    			//Identify changed option and assign to variable
    			$('option:selected', this).attr('selected',true).siblings().removeAttr('selected');
    			var type = $('#row' + j + 'label option:selected').text();
				for (var i = 0; i < typeOptions.length; i++) {
					if (typeOptions[i].text === type) {
					typeOptions[i].setAttribute('selected', 'selected');
					break;
					}
				}
    			//Remove class that begins with a capital letter before adding
    			var element = document.getElementById('row' + j + 'name');
    			for (var i = 0 ; i <= element.classList.length ; ++i) {
    				var word = element.classList.item(i);
    				if (/[A-Z]/.test(word)) {
    					element.classList.remove(word);
    					element.classList.add(type);
    					break;
    				}
    			}
    		}
			
		});
	
	</script>
	
	<script>
	//autosave section for input elements
		$(document).ready(function($){
    		
			$('input').blur(function(){
    		var content = $(this).val();
    		var dbRow = this.id;
    		if (/name/.test(dbRow)) {
    			dbRow = 'Name';
    		} else if (/ersch/.test(dbRow)) {
    			dbRow = 'Ersch';
    		} else if (/bar/.test(dbRow)) {
    			dbRow = 'Bar';
    		} else if (/EC/.test(dbRow)) {
    			dbRow = 'EC';
    		} else if (/weisung/.test(dbRow)) {
    			dbRow = 'Ueberw';
    		} else if (/bezahlt/.test(dbRow)) {
    			dbRow = 'Nicht_bezahlt';
    		}
    		var myDate = "<?php echo $schedDate2 ?>";
    		var that = $(this),
    		tableRow = that.closest('tr');
    		var r = tableRow.index() + 1;
    		var table = document.getElementById('myTable');
    		var time = table.rows[r].cells[0].innerHTML;
        var doctor = "<?php echo $doctor ?>";
    		var toSend = [myDate, time, content, dbRow, doctor];
    		var jsonString = JSON.stringify(toSend);
    		//var data = "<?php echo $data ?>";
        //console.log(jsonString);
  			$.ajax({
  				url: 'schedAutosave.php',
  				type: 'POST',
  				data: {data: jsonString},
  				cache: false
          //success: function(req, query){ console.log(query); }
  			});
			});
		});
				
	</script>
	
	<script>
	//autosave code for comment field
		$(document).ready(function($){
    		//NOTE TO SELF: have existing data appear in Kommentar box on doc ready with ajax
			$('textarea').blur(function(){
        		var content = $(this).val();
        		var dbRow = this.id;
        		var myDate = "<?php echo $schedDate2 ?>";
        		var time = "23:00";
            var doctor = "<?php echo $doctor ?>";
        		var toSend = [myDate, time, content, doctor];
        		//var jsonString = JSON.stringify(toSend);
    			$.ajax({
    				url: 'schedAutosave.php', 
    				type: 'POST',
    				data: {comment: toSend},
    				cache: false
    			});
			});
		});
				
	</script>
	
	<script>
	
		$(document).ready(function($){
		
			$('select').change(function() {
		
				var val = $(this).val();
				if (val == 0) {
					var type = 'Nichts';
				} else if (val == 1) {
					type = 'Neupatient';
				} else if (val == 2) {
					type = 'NeupatientTBC';
				} else if (val == 3) {
					type = 'Patient';
				} else if (val == 4) {
					type = 'PatientTBC';
				} else if (val == 5) {
					type = 'Pause';
				} else if (val == 6) {
					type = 'Puffer';
				} else if (val == 7) {
					type = 'ChiroYogaEvent';
				} else if (val == 8) {
					type = 'PersonalEvent';
				} else {
          type = 'doNothing';
        }
				if (type != 'doNothing') {
          var myDate = "<?php echo $schedDate2 ?>";
  				var that = $(this),
      		tableRow = that.closest('tr');
      		var r = tableRow.index() + 1;
      		var table = document.getElementById('myTable');
      		var time = table.rows[r].cells[0].innerHTML;
          var doctor = "<?php echo $doctor ?>";
  				var toSend = [myDate, time, type, doctor];
  				var jsonString2 = JSON.stringify(toSend);
      		var Typ = "<?php echo $Typ ?>";
      		//console.log(jsonString2);
      		$.ajax({
  				url: 'schedAutosave.php', 
  				type: 'POST',
  				data: {Typ: jsonString2},
  				cache: false
      		});
        }
			
			});
		
		});
	
	</script>

  <script type="text/javascript">
    
    var jq = jQuery.noConflict();
    jq(window).load(function(){
      jq('#error').modal('show');
    });

  </script>
    
   	 <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
   	 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
   	 <!-- Include all compiled plugins (below), or include individual files as needed -->
    	<script src="js/bootstrap.min.js"></script>	
    	<!-- Latest compiled and minified JavaScript -->
		
			
	</body>
	
</html>