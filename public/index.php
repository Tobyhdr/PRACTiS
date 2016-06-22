<?php require_once("login.php"); ?>

<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <title>Chiro-Yoga | Anmelden</title>
  
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

  </head>
  
  <body>
  
    <div class="container contentContainer" id="topContainer">
    
      <div class="row" id="einloggen">
      
          <div class="col-md-6 col-md-offset-3">
       
          <h3 class="marginTop">Einloggen</h3>
       
          <?php
       
            if ($error) {
        
              echo '<div class="alert alert-danger">'.addslashes($error).'</div>';
        
            }
       
          ?>
       
          <form class="marginTop" method="post"> 
       
            <div class="form-group">
          
                <label for="name">Email</label>
                <input type="text" name="loginemail" class="form-control" placeholder="Sie@Ihnen.de" value="<? echo addslashes($_POST['loginemail']); ?>"/>
                                   
            </div>
        
            <div class="form-group">
          
                <label for="password">Passwort</label>
                <input type="password" name="loginpassword" class="form-control" placeholder="Passwort" value="<? echo addslashes($_POST['loginpassword']); ?>" />
                                                
            </div>

            <input type="submit" name="submit" value="➙" class="btn btn-success btn-lg marginTop"/> 
        
          </form>
       
        </div>
     
      </div>
    
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
      $("#topContainer").css("height",$(window).height());
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