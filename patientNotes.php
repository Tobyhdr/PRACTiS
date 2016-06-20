<?php 

	session_start();

	require_once("submitPatientenNeu.php");
	
	if (!$_SESSION['id']) {
	
		header("Location:denied.html");
	
	}
	
	global $heute;
  global $NewPatient;
  global $pastVisits1;
	
	$heute = date('d-m-Y');
  $NewPatient = 'no';
  $pastVisits = '<p>None</p>';

?>

<!DOCTYPE html>
<html lang="en">
  <head>
  	<meta charset="utf-8"/>
  	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title>Chiro-Yoga | PatientNotes</title>
	
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
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
  	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
  	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script><!--Needed for modals -->
  	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css"><!-- Needed for autofill jQuery stuff-->
    <script type="text/javascript" src="jQuery/jquery-ui.min.js"></script><!-- Needed for autofill jQuery stuff-->
  	<link rel="stylesheet" type="text/css" href="styles.css" />
  	<link rel="stylesheet" type="text/css"
            href="https://fonts.googleapis.com/css?family=Philosopher|Ubuntu+Condensed"/>

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
    				<li> <a href="scheduler.php"><span class="glyphicon glyphicon-calendar large" aria-hidden="true"></span></a></li>
    				<li> <a href="waitingList.php"><span class="glyphicon glyphicon-hourglass" aria-hidden="true"></span></a></li>
    				<li> <a href="patientNotes.php"><span class="glyphicon glyphicon-pencil large" style="color:#F11C1C;" aria-hidden="true"></span></a></li>
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
  	
  		<h2 style="text-align:center;">PatientNotes <span class="glyphicon glyphicon-pencil"></span><br></h2>
  		
  		<div id="daySched"></div>
  		
  		<form action="patientNotes.php" id="pNformTop" method="post">
  		
  			<input type="submit" name="submit0" id="clearForm" value="Nächster Patient..." class="btn btn-primary btn-md" style="margin:0 0 12px 12px;"/>	
			
    			<div>
            <? if (!empty($success)) { ?>
              <div class="modal fade" id="success" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">  &times;</button>
                      <h4 class="modal-title">Erfolg</h4>
                    </div>
                    <div class="modal-body">
                      <? echo $success; ?>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Schliessen</button>
                    </div>
                  </div>
                </div>
              </div>
            <? } ?>
        
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
  		
  			<div class="form-group" id="mainDetails1">
  				<span style="display:inline-block">
  					<label for="Nachname" style="display:block; margin-left:12px;">Nachname</label>
  					<input type="text" class="ui-autocomplete-input" name="Nachname" id="Nachname" value="<? echo addslashes($_POST['Nachname']); ?>" style="margin:0 11px 10px 12px; width: 122px;"/>
  				</span>
  				<span style="display:inline-block">
  					<label for="Vorname" style="display:block">Vorname</label>
  					<input type="text" name="Vorname" id="Vorname" value="<? echo addslashes($_POST['Vorname']); ?>" style="margin-right:11px; width: 94px;"/>
  				</span>
  				<span style="display:inline-block">
  					<label for="Anrede" style="display:block">Anrede</label>
  					<input type="text" name="Anrede" id="Anrede" value="<? echo addslashes($_POST['Anrede']); ?>" style="margin-right:11px; width: 45px;"/>
  				</span>
  				<span style="display:inline-block">
  					<label for="Email" style="display:block">Email</label>
  					<input type="email" name="Email" id="Email" value="<? echo addslashes($_POST['Email']); ?>" style="width: 180px;"/>
  				</span>
  				<span style="display:inline-block">
  					<label for="Anschrift" style="display:block; margin-left:12px;">Anschrift</label>
  					<textarea name="Anschrift" id="Anschrift" style="margin-right:11px; margin-left:12px; width:152px; height: 74px;"><?echo htmlspecialchars($_POST['Anschrift']);?></textarea>
  				</span>
  				<span style="display:inline-block">
  					<label for="Bemerkung" style="display:block;">Admin Bemerkung</label>
  					<textarea name="Bemerkung" id="Bemerkung" style="margin-right:11px; width:152px; height: 74px;"><?echo htmlspecialchars($_POST['Bemerkung']);?></textarea>
  				</span>
  				<span style="display:inline-block">
  					<label for="Taetigkeit" style="display:block;">Tätigkeit/Hobbies</label>
  					<textarea name="Taetigkeit" id="Taetigkeit" style="margin-right:11px; width:152px; height: 74px;"><?echo htmlspecialchars($_POST['Taetigkeit']);?></textarea>
  				</span>
  				<span style="display:inline-block">
  					<input type="hidden" id="PatIDhidden" name="PatID" value="<? echo $PATID; ?>"/>
  				</span>						   
			</div>
			
			<div class="form-group" id="mainDetails2">
  				<span style="display:inline-block">
  					<label for="Tel" style="display:block">Tel</label>
  					<input type="text" name="Tel" id="Tel" value="<? echo addslashes($_POST['Tel']); ?>" style="margin-right:12px; margin-bottom:10px; width: 100px;"/>
  				</span>
  				<span style="display:inline-block">
  					<label for="Handy" style="display:block">Handy</label>
  					<input type="text" name="Handy" id="Handy" value="<? echo addslashes($_POST['Handy']); ?>" style="width: 100px;"/>
  				</span>
  				<span style="display:inline-block">
  					<label for="Geburtstag" style="display:block">Geburtstag</label>
  					<input type="date" name="Geburtstag" id="Geburtstag" value="<? echo addslashes($_POST['Geburtstag']); ?>" style="vertical-align:15px; margin-bottom:0; width: 120px;"/>
  				</span>
  				<span style="display:inline-block">
  					<label for="AnmDatum" style="display:block; margin-top:-10px;">Erstbesuch</label>
  					<input type="date" name="AnmDatum" id="AnmDatum" value="<? echo addslashes($_POST['AnmDatum']); ?>" style=" margin-top:-7px; margin-right:15px; width: 120px; position:relative;"/>
  				</span>
  				<div style="margin:-99px 0 0 163px;">
  					<span style="display:inline-block">
  						<label for="PatID" style="display:block">Pat. ID</label>
  						<div id="PatID"><? echo $PATID; ?></div>
  					</span>
  				</div>
  				<input type="submit" name="submit1" value="Save" class="btn btn-success btn-sm" style="margin-left:172px; margin-top:25px;"/>	
			</div>
			
			<div class="form-group" id="fixedNotes">
				<span style="display:inline-block">
  					<label for="Hauptbeschw" style="display:block; margin-left:14px; color:#F11C1C;">Hauptbeschwerden</label>
  					<textarea name="Hauptbeschw" id="Hauptbeschw" style="margin-right:10px; margin-left:12px; width: 217px; height: 125px;"><?echo htmlspecialchars($_POST['Hauptbeschw']);?></textarea>
  				</span>
  				<span style="display:inline-block">
  					<label for="Kontraind" style="display:block;">Kontraindikationen</label>
  					<textarea name="Kontraind" id="Kontraind" style="margin-right:10px; width: 217px; height: 125px;"><?echo htmlspecialchars($_POST['Kontraind']);?></textarea>
  				</span>
  				<span style="display:inline-block">
  					<label for="Diagnose" style="display:block;">Diagnose</label>
  					<textarea name="Diagnose" id="Diagnose" style="margin-right:3px; width: 217px; height: 125px;"><?echo htmlspecialchars($_POST['Diagnose']);?></textarea>
  				</span>
  				<input type="submit" name="submit2" value="Save" class="btn btn-success btn-sm" style="vertical-align:11px; margin-right: 0;"/>
			</div>
  		
  			<div class="form-group" id="dynNotes">

				  <span style="display:inline-block">
  					<label for="BesDatum" style="display:block; margin-left:14px;">Besuchsdatum</label>
  					<input type="date" name="BesDatum" id="BesDatum" value="<? echo addslashes($_POST['BesDatum']); ?>" style="margin-right:13px; margin-left:12px; vertical-align:233px;"/>
  				</span>
  				<span style="display:inline-block">
  					<label for="Anamnese" style="display:block;">Anamnese</label>
  					<textarea name="Anamnese" id="Anamnese" rows="7" style="margin-right:10px; width: 260px; height: 250px;"><? echo htmlspecialchars($_POST['Anamnese']); ?></textarea>
  				</span>
  				<span style="display:inline-block">
  					<label for="Behandlung" style="display:block;">Behandlung</label>
  					<textarea name="Behandlung" id="Behandlung" style="width: 260px; height: 250px;"><? echo htmlspecialchars($_POST['Behandlung']); ?></textarea>
  				</span>
  				<input type="submit" name="submit3" value="Save" class="btn btn-success btn-sm" style="vertical-align:12px; margin-left: 3px;"/>
			
      </div>

        <? if ($pastVisits) {
          ?>

        <div id="pastVisits"><? echo nl2br($pastVisits1); ?></div>

        <? } ?>
  		
  		</form>

      <? if ($NewPatient == "yes") {
        ?>

          <form action="patientNotes.php" method="post" id="NewPatient">
          
            <h2 style="text-align:center;">New-patient form here.</h2>
          
          </form>

        <? } ?>
  	
      </div>

  	</div>
    
    <script>

		$("#success").click(function() {
		
			$("#success").hide();
			
		});
		
		$("#error").click(function() {
		
			$("#error").hide();
			
		});

	</script>
    
  <script>
    
    var patID = <?php echo json_encode($PATID); ?>;
    	
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
					patID = ui.item.PatID;
					this.form.Taetigkeit.value = ui.item.Taetigkeit;
					document.getElementById("PatID").innerHTML = patID;
          $.ajax({
            url: 'dynDataRetrieve.php', 
            type: 'POST',
            data: {data: patID},
            cache: false,
            success: function(response) {
              $('#pastVisits').html(response);
              $("#pastVisits").accordion({ collapsible: true, heightStyle: 'content', active: false });
            }
          });
          /*$(document).ajaxComplete(function(){
            $("#pastVisits").accordion({ collapsible: true, heightStyle: 'content', active: false });
          });*/
				}
			}); 

      if (patID) {
        $("#pastVisits").accordion({ collapsible: true, heightStyle: 'content', active: false });
      }
				
		});

    </script>
    
    <script type="text/javascript">
    
    	var jq = jQuery.noConflict();
  		jq(window).load(function(){
    		jq('#error').modal('show');
  		});
  		
  		jq(window).load(function(){
    		jq('#success').modal('show');
  		});

  	</script>

    <script>

      $('input').bind('keypress', function(e) {
        if(e.keyCode == 13)  {
          return false;
        }
      });

    </script>
  	
  	<script>
      //Fetch day's schedule data and print to div element
  		$(document).ready(function($){
      
      	var myDate = "<?php echo $heute ?>";
      	var toSend = [myDate];
      	var jsonString = JSON.stringify(toSend);
  		  $.ajax({
  			url: 'patNotesRetrieve.php', 
  			type: 'POST',
  			data: {data: jsonString},
  			cache: false,
  			success: function(response) {
                  $('#daySched').html(response);
              }
  		  });

  		});
				
	 </script>

   <script>
      //Confirm that form should be cleared
      document.getElementById("clearForm").onclick = function() {

        window.onbeforeunload = function() {
          return "Haben Sie Ihre neuen Daten gespeichert?";
        };

      };
        
   </script>
			
	</body>
	
</html>