<?php
include_once 'db_connect.php';
include_once 'functions.php';

sec_session_start();
if (isset($_POST['servername'], $_POST['serverip'])) {
    $servername = $_POST['servername'];
    $serverip = $_POST['serverip'];

    //funktionsaufruf
	save_settings($servername, $serverip);
	header("Location: ../settings.php?changed=1");
} else {
    echo '$servername, $serverip';
}
