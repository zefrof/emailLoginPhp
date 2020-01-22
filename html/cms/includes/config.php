<?php
ob_start();
session_start();

//set timezone
date_default_timezone_set('America/Detroit');

//database credentials
define('DBHOST','localhost');
define('DBUSER','USR');
define('DBPASS','PASS');
define('DBNAME','users');

//application address
define('DIR','http://18.223.101.184/cms');
define('SITEEMAIL','noreply@18.191.250.14');

try {
	//create PDO connection
	$db = new PDO("mysql:host=" . DBHOST . "; charset=utf8mb4; dbname=" . DBNAME, DBUSER, DBPASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    //Connect to magic database
    $dbm = new PDO("mysql:host=" . DBHOST . "; charset=utf8mb4; dbname=magicTCG", DBUSER, DBPASS);
    $dbm->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    $dbm->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch(PDOException $e) {
	//show error
    echo '<p>'.$e->getMessage().'</p>';
    exit;
}

//include the user class, pass in the database connection
include(dirname(__DIR__) . '/classes/user.php');
include(dirname(__DIR__) . '/classes/phpmailer/mail.php');
$user = new User($db, $dbm);
?>
