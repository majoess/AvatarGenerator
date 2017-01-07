<?php
include_once 'db_connect.php';
include_once 'functions.php';

sec_session_start();
if (isset($_POST['userid'])) {
    $userid = $_POST['userid'];
	$userid = str_replace(' ','',$userid); 
	
	header("Location: ../userlist.php?search=$userid");
} else {
    echo 'Fehler!!!!';
}
