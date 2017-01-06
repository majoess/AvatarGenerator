<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

sec_session_start();
?>
<html lang="en">
<head>
    <title>UserListe | AvatarGenerator by .Pioneer</title>
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
<?php if (login_check($mysqli) == true) : ?>

    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-centered" style="float: none; margin: 0 auto;"><br>
                <div class="panel-group">
                    <div class="panel panel-primary">
                        <div class="panel-heading">AltisLife Signature Generator</div>
                        <div class="panel-body">
                            <?php
                            if(isset($_GET['deactivate'])){
                                $trash = $_GET["deactivate"];
                                deactivateuser($trash, $mysqli);
                            }

                            if(isset($_GET['activate'])){
                                $activate = $_GET["activate"];
                                activateuser($activate, $mysqli);
                            }
                            ?>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Suchen">
                                <span class="input-group-btn">
                                  <button class="btn btn-default" type="button">Go!</button>
                                </span>
                            </div><!-- /input-group -->
                            <table class="table">
                                <thead>
                                    <tr>
                                      <th>UserID</th>
                                      <th>Spielername</th>
                                      <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php getuser($mysqli); ?>
                                </tbody>
                            </table>

                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else :
  echo 'Du bist nicht eingeloggt.';
endif; ?>
</body>
</html>
