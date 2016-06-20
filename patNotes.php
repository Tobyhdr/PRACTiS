<?php 

	session_start();

	require_once("connection.php");
	require_once("selectTest.php");
	//require_once("patNotesAutocomp.php");
	
	if (!$_SESSION['id']) {
	
		header("Location:denied.html");
	
	}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
	<meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title>Chiro-Yoga | PatNotes</title>
	
	<!-- Bootstrap -->
    <link href="Bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <link rel="stylesheet" type="text/css" href="styles.css" /> 
    <script type="text/javascript" src="jQuery/jquery.min.js"></script>
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
	<script type="text/javascript" src="jQuery/jquery-ui.min.js"></script>

	</head>
	
	<body>
	
	<div class="navbar navbar-default navbar-fixed-top">
  
  		<div class="container">
  	
  			<div class="navbar-header">
  			
  				<a class="navbar-brand">ChiroSoft</a>
  				
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
    				<li> <a href="scheduler.php"><span class="glyphicon glyphicon-calendar large" aria-hidden="true"></span></a></li>
    				<li> <a href="patientNotes.php"><span class="glyphicon glyphicon-pencil large" style="color:#F2E0FC;" aria-hidden="true"></span></a></li>
    				<li> <a href="invoiceX.php"><span class="glyphicon glyphicon-usd large" aria-hidden="true"></span></a></li>
    			
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
  	
  		<h2 style="text-align:center;">PatNotes test area!!!<br ></h2>
  		
  		<form action="patNotes.php" method="post" id="pNformTop">
  		
  			<div class="form-group" id="mainDetails1">
  				<span style="display:inline-block">
  					<label for="Nachname" style="display:block; margin-left:12px;">Nachname</label>
  					<input type="text" size="20" class="ui-autocomplete-input" name="Nachname" id="Nachname" value="<? echo addslashes($_POST['Nachname']); ?>" style="margin:0 11px 10px 12px;"/>
  				</span>
  				<span style="display:inline-block">
  					<label for="Vorname" style="display:block">Vorname</label>
  					<input type="text" size="14" name="Vorname" id="Vorname" value="<? echo addslashes($_POST['Vorname']); ?>" style="margin-right:11px;"/>
  				</span>
  				<span style="display:inline-block">
  					<label for="Anrede" style="display:block">Anrede</label>
  					<input type="text" size="7" name="Anrede" id="Anrede" value="<? echo addslashes($_POST['Anrede']); ?>" style="margin-right:11px;"/>
  				</span>
  				<span style="display:inline-block">
  					<label for="Email" style="display:block">Email</label>
  					<input type="email" size="26" name="Email" id="Email" value="<? echo addslashes($_POST['Email']); ?>"/>
  				</span>
  				<span style="display:inline-block">
  					<label for="Anschrift" style="display:block; margin-left:12px;">Anschrift</label>
  					<textarea name="Anschrift" id="Anschrift" rows="4" cols="22" style="margin-right:11px; margin-left:12px;"><?echo htmlspecialchars($_POST['Anschrift']);?></textarea>
  				</span>
  				<span style="display:inline-block">
  					<label for="Bemerkung" style="display:block;">Admin Bemerkung</label>
  					<textarea name="Bemerkung" id="Bemerkung" rows="4" cols="17" style="margin-right:11px;"><?echo htmlspecialchars($_POST['Bemerkung']);?></textarea>
  				</span>
  				<span style="display:inline-block">
  					<label for="Taetigkeit" style="display:block;">Tätigkeit/Hobbies</label>
  					<textarea name="Taetigkeit" id="Taetigkeit" rows="4" cols="17" style="margin-right:11px;"><?echo htmlspecialchars($_POST['Taetigkeit']);?></textarea>
  				</span>
  				<span style="display:inline-block">
  					<label for="PatID" style="display:block">Pat. ID</label>
  					<input type="text" size="6" name="PatID" id="PatID" value="<? echo addslashes($_POST['PatID']); ?>" style="vertical-align:61px;"/>
  				</span>						   
			</div>
			
			<div class="form-group" id="mainDetails2">
  				<span style="display:inline-block">
  					<label for="Tel" style="display:block">Tel</label>
  					<input type="text" size="15" name="Tel" id="Tel" value="<? echo addslashes($_POST['Tel']); ?>" style="margin-right:12px; margin-bottom:10px;"/>
  				</span>
  				<span style="display:inline-block">
  					<label for="Handy" style="display:block">Handy</label>
  					<input type="text" size="15" name="Handy" id="Handy" value="<? echo addslashes($_POST['Handy']); ?>"/>
  				</span>
  				<span style="display:inline-block">
  					<label for="Geburtstag" style="display:block">Geburtstag</label>
  					<input type="date" name="Geburtstag" id="Geburtstag" value="<? echo addslashes($_POST['Geburtstag']); ?>" style="margin-right:18px; vertical-align:15px;"/>
  				</span>
  				<span style="display:inline-block">
  					<label for="AnmDatum" style="display:block; margin-top:-5px;">Erstbesuch</label>
  					<input type="date" name="AnmDatum" id="AnmDatum" value="<? echo addslashes($_POST['AnmDatum']); ?>" style=" margin-top:-10px;"/>
  				</span>
  				<input type="submit" name="submit1" value="➙" class="btn btn-success btn-sm" style="margin-left:222px; margin-top:-48px;"/>	
			</div>
			
			<div class="form-group" id="fixedNotes">
				<span style="display:inline-block">
  					<label for="Hauptbeschw" style="display:block; margin-left:14px; color:#D20202;">Hauptbeschwerden</label>
  					<textarea name="Hauptbeschw" id="Hauptbeschw" rows="7" cols="35" style="margin-right:11px; margin-left:12px;"><?echo htmlspecialchars($_POST['Hauptbeschw']);?></textarea>
  				</span>
  				<span style="display:inline-block">
  					<label for="Kontraind" style="display:block;">Kontraindikationen</label>
  					<textarea name="Kontraind" id="Kontraind" rows="7" cols="35" style="margin-right:11px;"><?echo htmlspecialchars($_POST['Kontraind']);?></textarea>
  				</span>
  				<span style="display:inline-block">
  					<label for="Diagnose" style="display:block;">Diagnose</label>
  					<textarea name="Diagnose" id="Diagnose" rows="7" cols="35" style="margin-right:3px;"><?echo htmlspecialchars($_POST['Diagnose']);?></textarea>
  				</span>
  				<input type="submit" name="submit2" value="➙" class="btn btn-success btn-sm" style="vertical-align:11px;"/>
			</div>
  		
  		</form>
  		
  		<form action="patNotes.php" method="post" id="pNformBottom">
  		
  			<div class="form-group" id="dynNotes">
				<span style="display:inline-block">
  					<label for="BesDatum" style="display:block; margin-left:14px;">Besuchsdatum</label>
  					<input type="date" name="BesDatum" id="BesDatum" value="<? echo addslashes($_POST['BesDatum']); ?>" style="margin-right:98px; margin-left:12px; vertical-align:115px;"/>
  				</span>
				<span style="display:inline-block">
  					<label for="Beschwerden" style="display:block; margin-left:14px;">Beschwerden</label>
  					<textarea name="Beschwerden" id="Beschwerden" value="<? echo htmlspecialchars($_POST['Beschwerden']); ?>" rows="7" cols="35" style="margin-right:11px;"></textarea>
  				</span>
  				<span style="display:inline-block">
  					<label for="Anamnese" style="display:block;">Anamnese</label>
  					<textarea name="Anamnese" id="Anamnese" value="<? echo htmlspecialchars($_POST['Anamnese']); ?>" rows="7" cols="35" style="margin-right:11px;"></textarea>
  				</span>
  				<span style="display:inline-block">
  					<label for="Befund" style="display:block; margin-left:14px;">Befund</label>
  					<textarea name="Befund" id="Befund" value="<? echo htmlspecialchars($_POST['Befund']); ?>" rows="7" cols="35" style="margin-right:11px; margin-left:12px;"></textarea>
  				</span>
  				<span style="display:inline-block">
  					<label for="Behandlung" style="display:block;">Behandlung</label>
  					<textarea name="Behandlung" id="Behandlung" value="<? echo htmlspecialchars($_POST['Behandlung']); ?>" rows="7" cols="35" style="margin-right:11px;"></textarea>
  				</span>
  				<span style="display:inline-block">
  					<label for="Empfehlung" style="display:block;">Empfehlung</label>
  					<textarea name="Empfehlung" id="Empfehlung" value="<? echo htmlspecialchars($_POST['Empfehlung']); ?>" rows="7" cols="35" style="margin-right:4px;"></textarea>
  				</span>
  				<input type="submit" name="submit3" value="➙" class="btn btn-success btn-sm" style="vertical-align:10px;"/>
			</div>
  		
  		</form>
  		
  		<p>Current session id: <? echo $_SESSION['id'] ?> </p>
  		<p>Progress1: <? echo $error ?> </p>
  		<p>Progress2 (PATID): <? echo $PATID ?> </p>
  		<p>Progress3: <? echo $success ?> </p>
  	
  	</div>
  	
  	<!-- Only size #topContainer to window height if I'm not a mobile phone) -->
    <script>
    
    	if (navigator.userAgent.match(/Android/i)
			|| navigator.userAgent.match(/webOS/i)
			|| navigator.userAgent.match(/iPhone/i)
			|| navigator.userAgent.match(/iPod/i)
			|| navigator.userAgent.match(/BlackBerry/i)
			|| navigator.userAgent.match(/Windows Phone/i)
 		) {
    		var check = "do nothing";
		} else {
			$("#topContainer2").css("height",$(window).height());
		}
    
    </script>
    
    <script>
    
    	var PATID = 1;
    	
    	$(document).ready(function($){
    		
			$('#Nachname').autocomplete({
				source:'patNotesAutocomp.php', 
				minLength:2,
				select:function(evt, ui) {
				// when a Nachname is selected, populate related fields in this form
					this.form.Vorname.value = ui.item.Vorname;
					this.form.Anrede.value = ui.item.Anrede;
					this.form.AnmDatum.value = ui.item.AnmDatum;
					this.form.Geburtstag.value = ui.item.Geburtstag;
					this.form.Anschrift.value = ui.item.Anschrift;
					this.form.Tel.value = ui.item.Tel;
					this.form.Handy.value = ui.item.Handy;
					this.form.Email.value = ui.item.email;
					this.form.Hauptbeschw.value = ui.item.Hauptbeschw;
					this.form.Kontraind.value = ui.item.Kontraind;
					this.form.Diagnose.value = ui.item.Diagnose;
					this.form.Bemerkung.value = ui.item.Bemerkung;
					this.form.PatID.value = ui.item.PatID;
					PATID = ui.item.PatID;
					this.form.Taetigkeit.value = ui.item.Taetigkeit;
					alert("Sie müssen nun die PatientID der SessionID zuweisen. Nur dann ist die Seite bereit für Datenänderungen zu diesem Patient. Einfach den obersten Button klicken.");
				}
			
			}); 
				
		});
		
		$(document).ready(function($){
    		
			$('#BesDatum').autocomplete({
				source:'patJanAutocomp.php', 
				minLength:4,
				select:function(evt, ui) {
				// when a Nachname is selected, populate related fields in this form
					this.form.Beschwerden.value = ui.item.Beschwerden;
					this.form.Anamnese.value = ui.item.Anamnese;
					this.form.Befund.value = ui.item.Befund;
					this.form.Behandlung.value = ui.item.Behandlung;
					this.form.Empfehlung.value = ui.item.Empfehlung;
				}
			
			}); 
				
		});


    </script>
    
   	 <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
   	 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
   	 <!-- Include all compiled plugins (below), or include individual files as needed -->
    	<script src="js/bootstrap.min.js"></script>	
    	<!-- Latest compiled and minified JavaScript -->
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
			
	</body>
	
</html>