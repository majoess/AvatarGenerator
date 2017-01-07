<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

sec_session_start();
?>
<html lang="en">
<head>
    <title>Einstellungen | AvatarGenerator by .Pioneer</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/JavaScript" src="js/sha512.js"></script>
    <script type="text/JavaScript" src="js/forms.js"></script>
</head>
<body>
<?php if (login_check($mysqli) == true) : 
$lines = file ('includes/settings.txt'); ?>

    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-centered" style="float: none; margin: 0 auto;"><br>
                <div class="panel-group">
                    <div class="panel panel-primary">
                        <div class="panel-heading">AltisLife Signature Generator</div>
                        <div class="panel-body">
						<?php if(isset($_GET['changed'])){
                                echo '<div class="alert alert-success"><strong>Erfolgreich!</strong> Die Einstellungen wurden gespeichert.</div>';
                            } ?>
						
							
							<form action="includes/process_settings.php" method="post" name="settings_form">
							  <div class="form-group">
								<label for="formGroupExampleInput">Servername</label>
								<input type="text" class="form-control" placeholder="Servername" value="<?php echo $lines[0]; ?>" name="servername" id="servername">
							  </div>
							  <div class="form-group">
								<label for="formGroupExampleInput2">Serverip</label>
								<input type="text" class="form-control" placeholder="ServerIP" value="<?php echo $lines[1]; ?>" name="serverip" id="serverip">
							  </div>
							  <button type="submit" class="btn btn-primary">Speichern</button>
							</form>

                        </div> 
                    </div>
                </div>
				<center><a href="userlist.php">Userliste</a></center>
            </div>
        </div>
    </div>
<?php
else :
  echo 'Du bist nicht eingeloggt.';
endif; ?>
</body>
</html>
