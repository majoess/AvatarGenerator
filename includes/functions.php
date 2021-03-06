<?php
include_once 'psl-config.php';

function sec_session_start() {
    $session_name = 'sec_session_id';   // vergib einen Sessionnamen
    $secure = SECURE;
    // Damit wird verhindert, dass JavaScript auf die session id zugreifen kann.
    $httponly = true;
    // Zwingt die Sessions nur Cookies zu benutzen.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // Holt Cookie-Parameter.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"],
        $cookieParams["domain"],
        $secure,
        $httponly);
    // Setzt den Session-Name zu oben angegebenem.
    session_name($session_name);
    session_start();            // Startet die PHP-Sitzung
    session_regenerate_id();    // Erneuert die Session, löscht die alte.
}

function login($email, $password, $mysqli) {
    // Das Benutzen vorbereiteter Statements verhindert SQL-Injektion.
    if ($stmt = $mysqli->prepare("SELECT id, username, password, salt
        FROM members
       WHERE email = ?
        LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Führe die vorbereitete Anfrage aus.
        $stmt->store_result();

        // hole Variablen von result.
        $stmt->bind_result($user_id, $username, $db_password, $salt);
        $stmt->fetch();

        // hash das Passwort mit dem eindeutigen salt.
        $password = hash('sha512', $password . $salt);
        if ($stmt->num_rows == 1) {
            // Wenn es den Benutzer gibt, dann wird überprüft ob das Konto
            // blockiert ist durch zu viele Login-Versuche

            if (checkbrute($user_id, $mysqli) == true) {
                // Konto ist blockiert
                // Schicke E-Mail an Benutzer, dass Konto blockiert ist
                return false;
            } else {
                // Überprüfe, ob das Passwort in der Datenbank mit dem vom
                // Benutzer angegebenen übereinstimmt.
                if ($db_password == $password) {
                    // Passwort ist korrekt!
                    // Hole den user-agent string des Benutzers.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS-Schutz, denn eventuell wir der Wert gedruckt
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
                    // XSS-Schutz, denn eventuell wir der Wert gedruckt
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/",
                                                                "",
                                                                $username);
                    $_SESSION['username'] = $username;
                    $_SESSION['login_string'] = hash('sha512',
                              $password . $user_browser);
                    // Login erfolgreich.
                    return true;
                } else {
                    // Passwort ist nicht korrekt
                    // Der Versuch wird in der Datenbank gespeichert
                    $now = time();
                    $mysqli->query("INSERT INTO login_attempts(user_id, time)
                                    VALUES ('$user_id', '$now')");
                    return false;
                }
            }
        } else {
            //Es gibt keinen Benutzer.
            return false;
        }
    }
}

function checkbrute($user_id, $mysqli) {
    // Hole den aktuellen Zeitstempel
    $now = time();

    // Alle Login-Versuche der letzten zwei Stunden werden gezählt.
    $valid_attempts = $now - (2 * 60 * 60);

    if ($stmt = $mysqli->prepare("SELECT time
                             FROM login_attempts <code><pre>
                             WHERE user_id = ?
                            AND time > '$valid_attempts'")) {
        $stmt->bind_param('i', $user_id);

        // Führe die vorbereitet Abfrage aus.
        $stmt->execute();
        $stmt->store_result();

        // Wenn es mehr als 5 fehlgeschlagene Versuche gab
        if ($stmt->num_rows > 5) {
            return true;
        } else {
            return false;
        }
    }
}

function login_check($mysqli) {
    // Überprüfe, ob alle Session-Variablen gesetzt sind
    if (isset($_SESSION['user_id'],
                        $_SESSION['username'],
                        $_SESSION['login_string'])) {

        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];

        // Hole den user-agent string des Benutzers.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];

        if ($stmt = $mysqli->prepare("SELECT password
                                      FROM members
                                      WHERE id = ? LIMIT 1")) {
            // Bind "$user_id" zum Parameter.
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // Wenn es den Benutzer gibt, hole die Variablen von result.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);

                if ($login_check == $login_string) {
                    // Eingeloggt!!!!
                    return true;
                } else {
                    // Nicht eingeloggt
                    return false;
                }
            } else {
                // Nicht eingeloggt
                return false;
            }
        } else {
            // Nicht eingeloggt
            return false;
        }
    } else {
        // Nicht eingeloggt
        return false;
    }
}

function esc_url($url) {

    if ('' == $url) {
        return $url;
    }

    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;

    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }

    $url = str_replace(';//', '://', $url);

    $url = htmlentities($url);

    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);

    if ($url[0] !== '/') {
        // Wir wollen nur relative Links von $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}

function getuser($mysqli){
  $sql = "SELECT pid, aliases, imgeditorstate FROM players";
  	$result = $mysqli->query($sql);

  	if ($result->num_rows > 0) {
  		while($row = $result->fetch_assoc()) {
  				echo '<tr><td>' . $row["pid"]. '</td>';
  				echo '<td>' . $row["aliases"]. '</td>';
          if($row["imgeditorstate"] == "true"){ echo '<td><a href="userlist.php?deactivate=' . $row["pid"]. '"><i class="fa fa-check" aria-hidden="true"></i></a> <a href="image.php?userid=' . $row["pid"]. '"><i class="fa fa-picture-o" aria-hidden="true"></i></a></td></tr>'; } else { echo '<td><a href="userlist.php?activate=' . $row["pid"]. '"><i class="fa fa-times" aria-hidden="true"></i></a></td></tr>'; }
          //if($row["imgeditorstate"] == "true"){ echo '<td>geil</td></tr>'; } else { echo '<td>test</td></tr>'; }
          //echo '<td>' . $row["imgeditorstate"]. '</td></tr>';
  		}
  	} else {
  		echo "0 results";
  	}
  	return 1;
}

function deactivateuser($trash, $mysqli){
  //UPDATE links Set urlname = 'PHP f&uuml;r dich' WHERE id = '4'
  $sql = "UPDATE players Set imgeditorstate = 'false' WHERE pid = '$trash'";
  if($mysqli->query($sql) === TRUE) {
		echo '<div class="alert alert-success"><strong>Erfolgreich!</strong> Der Benutzer wurde verändert.</div>';
	} else {
		echo '<div class="alert alert-success"><strong>Fehler!</strong> Beim verändern ist ein Fehler aufgetreten.</div>';
	}
	return 1;
}

function activateuser($activate, $mysqli){
  //UPDATE links Set urlname = 'PHP f&uuml;r dich' WHERE id = '4'
  $sql = "UPDATE players Set imgeditorstate = 'true' WHERE pid = '$activate'";
  if($mysqli->query($sql) === TRUE) {
		echo '<div class="alert alert-success"><strong>Erfolgreich!</strong> Der Benutzer wurde verändert.</div>';
	} else {
		echo '<div class="alert alert-success"><strong>Fehler!</strong> Beim verändern ist ein Fehler aufgetreten.</div>';
	}
	return 1;
}

function createImage($userid, $mysqli){
  if ($stmt = $mysqli->prepare("SELECT aliases, cash, bankacc, coplevel, mediclevel FROM players WHERE uid = '$userid'")) {
      $stmt->bind_param('sssss', $username, $cash, $bankacc, $coplevel, $mediclevel);  // Bind "$email" to parameter.
      $stmt->execute();    // Führe die vorbereitete Anfrage aus.
      $stmt->store_result();

      // hole Variablen von result.
      $stmt->bind_result($username, $handmoney, $bankmoney, $coplevel, $mediclevel);
      $stmt->fetch();

  $servername = "bluetec-roleplay";
  $serverip = "server.bluetec.de";
  $bankmoney = "Konto: ' . $bankmoney . '";
  $handmoney = "Bargeld: ' . $handmoney . '";
  $fraktion = 'Fraktion: Zivilist';
  if($row["coplevel"] > 1)
  {
    switch($row["coplevel"]){
      case 1: $frank = "( Azubi )"; break;
      case 2: $frank = "( Wachtmeister )"; break;
      case 3: $frank = "( stllv. Chef )"; break;
      case 4: $frank = "( Polizeichef )"; break;
      default: $frank = "( Rekrut )";
    $fraktion = 'Fraktion: Polizei (' . $frank . ')';
    }
  }
  if ($row["mediclevel"] > 1) {
    switch($row["mediclevel"])
    {
      case 1: $frank = "( Azubi )"; break;
      case 2: $frank = "( Arzt )"; break;
      case 3: $frank = "( Chefarzt )"; break;
      case 4: $frank = "( Leitung )"; break;
      default: $frank = "( Anwaerter )";
    $fraktion = 'Fraktion: Sanitaeter (' . $frank . ')';
    }
  }
  }
  return array($servername, $serverip, $username, $bankmoney, $handmoney, $fraktion);
}
