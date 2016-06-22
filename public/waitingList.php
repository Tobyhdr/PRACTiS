<?php 

	session_start();

	require_once("connection.php");
	
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
    <title>Chiro-Yoga | Software Palette</title>
	
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
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
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
    				<li> <a href="scheduler.php"><span class="glyphicon glyphicon-calendar large" aria-hidden="true"></span></a></li>
    				<li> <a href="waitingList.php"><span class="glyphicon glyphicon-hourglass" aria-hidden="true" style="color:#F11C1C;"></span></a></li>
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
  		
  		<div id="topContainer2">
  		
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
    
   	 <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
   	 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
   	 <!-- Include all compiled plugins (below), or include individual files as needed -->
    	<script src="js/bootstrap.min.js"></script>	
    	<!-- Latest compiled and minified JavaScript -->
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
			
	</body>
	
</html>