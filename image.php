<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

$lines = file ('includes/settings.txt');

if (isset($_GET['userid'])) {
  $userid = $_GET['userid'];
  $sql = "SELECT aliases, cash, bankacc, coplevel, mediclevel, imgeditorstate FROM players WHERE pid = '$userid'";
  $result = $mysqli->query($sql);
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

      $servername = $lines[0];
      $serverip = $lines[1];

      $username = $row["aliases"];
      $bankacc = $row["bankacc"];
      $bankmoney = "Konto: $bankacc";
      //$bankmoney = 'Konto: " . $bankacc . "';
      $cash = $row["cash"];
      $handmoney = "Bargeld: $cash";
      $fraktion = 'Fraktion: Zivilist';

      $coplevel = $row["coplevel"];
      $mediclevel = $row["mediclevel"];
      $imgeditorstate = $row["imgeditorstate"];
      
      if($coplevel > 0)
      {
        switch($coplevel)
        {
          case 1: $frank = "( Azubi )"; break;
          case 2: $frank = "( Wachtmeister )"; break;
          case 3: $frank = "( stv. Chef )"; break;
          case 4: $frank = "( Polizeichef )"; break;
          default: $frank = "( Rekrut )";
        }
        $fraktion = 'Fraktion: Polizei (' . $frank . ')';
      } //ende coplevel
      if ($mediclevel > 0) 
      {
        switch($mediclevel)
        {
          case 1: $frank = "( Azubi )"; break;
          case 2: $frank = "( Arzt )"; break;
          case 3: $frank = "( Chefarzt )"; break;
          case 4: $frank = "( Leitung )"; break;
          default: $frank = "( Anwaerter )";
        }
        $fraktion = 'Fraktion: Sanitaeter (' . $frank . ')';
      } //ende mediclevel

      if($imgeditorstate == "true")
      {
      header("Content-type: image/png");
      $image = imagecreatefrompng("images/image.png");
      $color_black = ImageColorAllocate($image, 0, 0, 0);

      ImageString($image, "6", "25", "25", "$username", $color_black);
      ImageString($image, "6", "25", "50", "$bankmoney", $color_black);
      ImageString($image, "6", "25", "75", "$handmoney", $color_black);
      ImageString($image, "6", "25", "100", "$fraktion", $color_black);
      ImageString($image, "6", "1", "160", "$servername", $color_black);
      ImageString($image, "6", "240", "160", "$serverip", $color_black);
      ImagePNG($image);
      }
      else 
      { 
        echo 'Nicht vorhanden!';
      }
    }
  }
}
