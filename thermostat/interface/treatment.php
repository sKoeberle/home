<?php


if ($_GET['action'] == 'getAmbianceMode') {
    echo json_encode( getAmbianceMode() );
}

if ($_GET['action'] == 'getCurrentAmbianceMode') {
    echo json_encode( getCurrentAmbianceMode() );
}

if ($_GET['action'] == 'getCurrentDate') {
    echo json_encode( getCurrentDate() );
}

if ($_GET['action'] == 'getCurrentTime') {
    echo json_encode( getCurrentTime() );
}

if ($_GET['action'] == 'getTargetTemperature') {
    echo json_encode( getTargetTemperature() );
}

if ($_GET['action'] == 'setTargetTemperature') {
    echo json_encode( setTargetTemperature( $_GET['temp'] ) );
}

if ($_GET['action'] == 'getCurrentSensorData') {
    echo json_encode( getCurrentSensorData( $_GET['sensor'] ) );
}

if ($_GET['action'] == 'getDateOfLastRecordedData') {
    echo json_encode( getDateOfLastRecordedData( $_GET['sensor'] ) );
}

if ($_GET['action'] == 'getTemperatureSettings') {
    echo json_encode( getTemperatureSettings() );
}


function connectDB()
{

    $dbHost = 'localhost';
    $dbUser = 'home';
    $dbPass = '2DsNEPnDHH93WT2y';
    $dbName = 'home';


    $mysqli = new mysqli( $dbHost, $dbUser, $dbPass, $dbName );
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    return $mysqli;
}

function getAmbianceMode()
{
    $mysqli = connectDB();

    $res = $mysqli->query( "SELECT `value` FROM `general` WHERE `label` = 'ambianceMode' LIMIT 0,1" );
    $row = $res->fetch_assoc();

    return $row['value'];

}

function getCurrentAmbianceMode()
{
    $mysqli = connectDB();

    $res = $mysqli->query( "SELECT `value` FROM `general` WHERE `label` = 'currentAmbianceMode' LIMIT 0,1" );
    $row = $res->fetch_assoc();

    return $row['value'];

}

function getCurrentDate()
{

    $date = new DateTime();
    return strtoupper( $date->format( "l, M j" ) );
}

function getCurrentTime()
{

    $time = new DateTime();
    return $time->format( "H:i" );
}

function getTargetTemperature()
{
    $mysqli = connectDB();

    $res = $mysqli->query( "SELECT `temperature` FROM `target` ORDER BY `demand` DESC LIMIT 0,1" );
    $row = $res->fetch_assoc();

    return sprintf( "%01.1f", $row['temperature'] );

}

function getTemperatureSettings()
{

    $mysqli = connectDB();

    $res = $mysqli->query( "SELECT `value` FROM `general` WHERE `label` = 'maxTemperatureSetting' LIMIT 0,1" );
    $row = $res->fetch_assoc();
    $result['maxTemperatureSetting'] = $row['value'];

    $res = $mysqli->query( "SELECT `value` FROM `general` WHERE `label` = 'minTemperatureSetting' LIMIT 0,1" );
    $row = $res->fetch_assoc();
    $result['minTemperatureSetting'] = $row['value'];

    $res = $mysqli->query( "SELECT `value` FROM `general` WHERE `label` = 'stepTemperatureSetting' LIMIT 0,1" );
    $row = $res->fetch_assoc();
    $result['stepTemperatureSetting'] = $row['value'];

    return $result;
}

function setTargetTemperature( $temp )
{
    $mysqli = connectDB();

    $res = $mysqli->query( "INSERT INTO `target` SET `temperature` = '$temp', `demand` = NOW()" );

    return $res;

}

function getCurrentSensorData( $sensor )
{
    $mysqli = connectDB();

    $res = $mysqli->query( "SELECT `value` FROM `sensors` WHERE `location` = '$sensor' AND `type` = 'temperature' ORDER BY `recordTime` DESC LIMIT 0,1" );
    $row = $res->fetch_assoc();
    $result['temperature'] = $row['value'];

    $res = $mysqli->query( "SELECT `value` FROM `sensors` WHERE `location` = '$sensor' AND `type` = 'humidity' ORDER BY `recordTime` DESC LIMIT 0,1" );
    $row = $res->fetch_assoc();
    $result['humidity'] = $row['value'];

    return $result;

}

function getDateOfLastRecordedData( $sensor )
{
    $mysqli = connectDB();

    $res = $mysqli->query( "SELECT UNIX_TIMESTAMP(`recordTime`) FROM `sensors` WHERE `location` = '$sensor' AND `type` = 'temperature' ORDER BY `recordTime` DESC LIMIT 0,1" );
    $row = $res->fetch_row();
    $result = $row[0];

    return $result;
}
