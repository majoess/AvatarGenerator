<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

sec_session_start();

if (login_check($mysqli) == true) {
    $logged = 'in';
} else {
    $logged = 'out';
}
?>
<html lang="en">
<head>
  <title>LogIn | AvatarGenerator by .Pioneer</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script type="text/JavaScript" src="js/sha512.js"></script>
  <script type="text/JavaScript" src="js/forms.js"></script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-centered" style="float: none; margin: 0 auto;">
                <img src="images/arma3.png" alt="Arma 3 Logo" style="width:100%;height:20%;">
                <div class="panel-group">
                    <div class="panel panel-primary">
                        <div class="panel-heading">AltisLife Signature Generator</div>
                        <div class="panel-body">
                            <?php
                                if (isset($_GET['error'])) {
                                    echo '<p class="error">Error Logging In!</p>';
                                }
                            ?>
                            <form action="includes/process_login.php" method="post" name="login_form">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">E-Mail-Adresse</label>
                                    <input type="email" name="email" id="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Email eingeben">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Passwort</label>
                                    <input type="email" name="password" id="password" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Passwort eingeben">
                                </div>
                                <button type="submit" class="btn btn-primary" onclick="formhash(this.form, this.form.password);">Einloggen</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
