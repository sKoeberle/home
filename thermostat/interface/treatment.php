<?php


if ($_GET['action'] == 'getCurrentDate') {
    echo json_encode( getCurrentDate() );
}

if ($_GET['action'] == 'getCurrentTime') {
    echo json_encode( getCurrentTime() );
}

if ($_GET['action'] == 'setCurrentSensorData') {
    echo json_encode( setCurrentSensorData( $_GET['data'] ) );
}

if ($_GET['action'] == 'getTargetTemperature') {
    echo json_encode( getTargetTemperature() );
}

if ($_GET['action'] == 'setTargetTemperature') {
    echo json_encode( setTargetTemperature( $_GET['temp'] ) );
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

function setTargetTemperature( $temp )
{
    $mysqli = connectDB();

    $res = $mysqli->query( "INSERT INTO `target` SET `temperature` = '$temp', `demand` = NOW()" );

    return $res;

}

function setCurrentSensorData( $data )
{

    $mysqli = connectDB();

    $mysqli->query( "INSERT INTO `sensors` SET `type` = 'temperature', `location` = '$data[sensor]', value = $data[temperature]" );
    $mysqli->query( "INSERT INTO `sensors` SET `type` = 'humidity', `location` = '$data[sensor]', value = $data[humidity]" );

    return;

}