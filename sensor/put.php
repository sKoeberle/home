<?php

// Read configuration
$config = parse_ini_file('/home/sensor/config.php');


$p = $_GET['p'];
$a = $_GET['a'];
$t = $_GET['t'];
$h = $_GET['h'];
$s = $_GET['s'];

$mysqli = connectDB($config);
if ($p) {
    $res = $mysqli->query("INSERT INTO `home`.`sensors` SET `location` = '$s', `type` = 'pressure', `value` = '$p'");
}
if ($a) {
    $res = $mysqli->query("INSERT INTO `home`.`sensors` SET `location` = '$s', `type` = 'altitude', `value` = '$a'");
}
if ($t) {
    $res = $mysqli->query("INSERT INTO `home`.`sensors` SET `location` = '$s', `type` = 'temperature', `value` = '$t'");
}
if ($h) {
    $res = $mysqli->query("INSERT INTO `home`.`sensors` SET `location` = '$s', `type` = 'humidity', `value` = '$h'");
}


// Include class
include_once($_SERVER['DOCUMENT_ROOT'] . '/home/sensor/phpMailer/class.phpmailer.php');


// Writing logs
try {
    $handle = @fopen($_SERVER['DOCUMENT_ROOT'] . "/home/sensor/__log/log.txt", "a+");
    if ($handle) {
        fwrite($handle, $this->content);
        fclose($handle);
    }

} catch (Exception $e) {

    if (!defined('__BR__')) {
        define('__BR__', '<br>');
    }

    $date = new DateTime('now', new DateTimeZone('Europe/Paris'));
    $datetime = $date->format('Ymd-H:i:s');

    $message = "Hi," . __BR__ . __BR__
        . "Errors details:" . __BR__ . __BR__
        . "Date: <b>" . $datetime . "</b>" . __BR__
        . "Id session: <b>" . session_id() . "</b>" . __BR__
        . "Details: <b>" . stripslashes($e->getMessage()) . "</b>" . __BR__ . __BR__
        . "_________" . __BR__
        . "System";


    // Include class
    include_once('phpMailer/class.phpmailer.php');

    if (class_exists('PHPMailer')) {
        //Create a new PHPMailer instance
        $mail = new PHPMailer;


        //Set who the message is to be sent from
        $mail->setFrom('home');


        //Set an alternative reply-to address
        //Set who the message is to be sent to
        $mail->addAddress($config['mail'], 'Admin');


        //Set the subject line
        $mail->Subject = 'LOG';


        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $mail->msgHTML($message);


        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';


        //Attach the log file
//        $mail->addAttachment($_SERVER['DOCUMENT_ROOT'] . '__log/log.txt');

    }
}


function connectDB($config)
{

    $mysqli = new mysqli($config['dbHost'], $config['dbUser'], $config['dbPass'], $config['dbName'], $config['dbPort']);

    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    return $mysqli;
}