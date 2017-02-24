<?php


if ($_GET['action'] == 'getAmbianceMode') {
    echo json_encode(getAmbianceMode());
}

if ($_GET['action'] == 'getCurrentAmbianceMode') {
    echo json_encode(getCurrentAmbianceMode());
}

if ($_GET['action'] == 'getCurrentDate') {
    echo json_encode(getCurrentDate());
}

if ($_GET['action'] == 'getCurrentTime') {
    echo json_encode(getCurrentTime());
}

if ($_GET['action'] == 'getTargetTemperature') {
    echo json_encode(getTargetTemperature());
}

if ($_GET['action'] == 'setTargetTemperature') {
    echo json_encode(setTargetTemperature($_GET['temp']));
}

if ($_GET['action'] == 'getCurrentSensorData') {
    echo json_encode(getCurrentSensorData($_GET['sensor']));
}

if ($_GET['action'] == 'getDateOfLastRecordedData') {
    echo json_encode(getDateOfLastRecordedData($_GET['sensor']));
}

if ($_GET['action'] == 'getTemperatureSettings') {
    echo json_encode(getTemperatureSettings());
}

if ($_GET['action'] == 'getSensorHistory') {
    echo json_encode(getSensorHistory($_GET['sensor'], $_GET['t'], $_GET['p'], $_GET['h']));
}

if ($_GET['action'] == 'getDailyProgrammingMode') {
    echo json_encode(getDailyProgrammingMode());
}

if ($_GET['action'] == 'setDailyProgrammingMode') {
    echo json_encode(setDailyProgrammingMode($_GET['mode']));
}

if ($_GET['action'] == 'getProgram') {
    echo json_encode(getProgram());
}

if ($_GET['action'] == 'setProgram') {
    echo json_encode(setProgram($_GET['name'], $_GET['position'], $_GET['value']));
}

if ($_GET['action'] == 'setAmbianceMode') {
    echo json_encode(setAmbianceMode($_GET['value']));
}


function connectDB()
{

    $dbHost = 'localhost';
    $dbUser = 'home';
    $dbPass = '2DsNEPnDHH93WT2y';
    $dbName = 'home';


    $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    return $mysqli;
}

function getAmbianceMode()
{
    $mysqli = connectDB();

    $res = $mysqli->query("SELECT `value` FROM `general` WHERE `label` = 'ambianceMode' LIMIT 0,1");
    $row = $res->fetch_assoc();

    return $row['value'];

}

function getCurrentAmbianceMode()
{
    $mysqli = connectDB();

    $res = $mysqli->query("SELECT `value` FROM `general` WHERE `label` = 'currentAmbianceMode' LIMIT 0,1");
    $row = $res->fetch_assoc();

    return $row['value'];

}

function getCurrentDate()
{

    $date = new DateTime();
    return strtoupper($date->format("l, M j"));
}

function getCurrentTime()
{

    $time = new DateTime();
    return $time->format("H:i");
}

function getTargetTemperature()
{
    $mysqli = connectDB();

    $res = $mysqli->query("SELECT `temperature` FROM `target` ORDER BY `demand` DESC LIMIT 0,1");
    $row = $res->fetch_assoc();

    return sprintf("%01.1f", $row['temperature']);

}

function getTemperatureSettings()
{

    $mysqli = connectDB();

    $res = $mysqli->query("SELECT `value` FROM `general` WHERE `label` = 'maxTemperatureSetting' LIMIT 0,1");
    $row = $res->fetch_assoc();
    $result['maxTemperatureSetting'] = $row['value'];

    $res = $mysqli->query("SELECT `value` FROM `general` WHERE `label` = 'minTemperatureSetting' LIMIT 0,1");
    $row = $res->fetch_assoc();
    $result['minTemperatureSetting'] = $row['value'];

    $res = $mysqli->query("SELECT `value` FROM `general` WHERE `label` = 'stepTemperatureSetting' LIMIT 0,1");
    $row = $res->fetch_assoc();
    $result['stepTemperatureSetting'] = $row['value'];

    return $result;
}

function setTargetTemperature($temp)
{
    $mysqli = connectDB();

    $res = $mysqli->query("INSERT INTO `target` SET `temperature` = '$temp', `demand` = NOW()");

    return $res;

}

function getCurrentSensorData($sensor)
{
    $mysqli = connectDB();

    $res = $mysqli->query("SELECT `value` FROM `sensors` WHERE `location` = '$sensor' AND `type` = 'temperature' ORDER BY `recordTime` DESC LIMIT 0,1");
    $row = $res->fetch_assoc();
    $result['temperature'] = $row['value'];

    $res = $mysqli->query("SELECT `value` FROM `sensors` WHERE `location` = '$sensor' AND `type` = 'humidity' ORDER BY `recordTime` DESC LIMIT 0,1");
    $row = $res->fetch_assoc();
    $result['humidity'] = $row['value'];

    $res = $mysqli->query("SELECT `value` FROM `sensors` WHERE `location` = '$sensor' AND `type` = 'pressure' ORDER BY `recordTime` DESC LIMIT 0,1");
    $row = $res->fetch_assoc();
    $result['pressure'] = $row['value'];

    return $result;

}

function getDateOfLastRecordedData($sensor)
{
    $mysqli = connectDB();

    $res = $mysqli->query("SELECT UNIX_TIMESTAMP(`recordTime`) FROM `sensors` WHERE `location` = '$sensor' AND `type` = 'temperature' ORDER BY `recordTime` DESC LIMIT 0,1");
    $row = $res->fetch_row();
    $result = $row[0];

    return $result;
}

function getSensorHistory($sensor, $temperature = false, $pressure = false, $humidity = false)
{
    $mysqli = connectDB();

    /*
     * Les dernières 24 heures
     *
     * SELECT `value`,DATE_FORMAT(`recordTime`,'%d.%m-%h:%i') FROM `sensors` WHERE `recordTime` BETWEEN NOW() - INTERVAL 24 HOUR AND NOW() AND `type` = 'temperature' AND `location` = 'exterior'
     */

    /*
     * La dernière heure
     *
     *SELECT `value`,DATE_FORMAT(`recordTime`,'%h') FROM `sensors` WHERE `recordTime` BETWEEN NOW() - INTERVAL 1 HOUR AND NOW() AND `type` = 'temperature' AND `location` = 'exterior'
     */

    /*
     * Les dernières 48 heures / heure
     *
     * SELECT AVG(`value`),DATE_FORMAT(`recordTime`,'%d.%m-%H') FROM `sensors` WHERE `recordTime` BETWEEN NOW() - INTERVAL 48 HOUR AND NOW() AND `type` = 'temperature' AND `location` = 'exterior' GROUP BY DATE_FORMAT(`recordTime`,'%d;%m-%H')
     */

    // TEMPERATURE
    if ($temperature == 'true') {
//    $res = $mysqli->query( "SELECT TIME_FORMAT(`recordTime`,'%H:%i') FROM `sensors` WHERE `location` = '$sensor' AND `type` = 'temperature' ORDER BY `recordTime` DESC LIMIT 0,24" );
        $res = $mysqli->query("SELECT DATE_FORMAT(`recordTime`,'%H') FROM `sensors` WHERE `recordTime` BETWEEN NOW() - INTERVAL 48 HOUR AND NOW() AND `type` = 'temperature' AND `location` = '$sensor' GROUP BY DATE_FORMAT(`recordTime`,'%d;%m-%H')");
        $row = $res->fetch_all(MYSQLI_NUM);
        $tempArray = array();
        foreach ($row as $key => $value) {
            $val = implode("", $value);
            if ($val % 2 == 0) { //even only
                $tempArray[] = $val . "h";
            } else {
                $tempArray[] = '';
            }
        }
//    $result['temperature']['labels'] = array_reverse( $tempArray );
        $result['temperature']['labels'] = $tempArray;

//    $res = $mysqli->query( "SELECT `value` FROM `sensors` WHERE `location` = '$sensor' AND `type` = 'temperature' ORDER BY `recordTime` DESC LIMIT 0,24" );
        $res = $mysqli->query("SELECT AVG(`value`) FROM `sensors` WHERE `recordTime` BETWEEN NOW() - INTERVAL 48 HOUR AND NOW() AND `type` = 'temperature' AND `location` = '$sensor' GROUP BY DATE_FORMAT(`recordTime`,'%d;%m-%H')");
        $row = $res->fetch_all(MYSQLI_NUM);
        $tempArray = array();
        foreach ($row as $key => $value) {
            $tempArray[] = implode("", $value);
        }
//    $result['temperature']['series'] = array( array_reverse( $tempArray ) );
        $result['temperature']['series'] = array($tempArray);
    }


    // HUMIDITY
    if ($humidity == 'true') {
        $res = $mysqli->query("SELECT DATE_FORMAT(`recordTime`,'%H') FROM `sensors` WHERE `recordTime` BETWEEN NOW() - INTERVAL 48 HOUR AND NOW() AND `type` = 'humidity' AND `location` = '$sensor' GROUP BY DATE_FORMAT(`recordTime`,'%d;%m-%H')");
        $row = $res->fetch_all(MYSQLI_NUM);
        $tempArray = array();
        foreach ($row as $key => $value) {
            $val = implode("", $value);
            if ($val % 2 == 0) { //even only
                $tempArray[] = $val . "h";
            } else {
                $tempArray[] = '';
            }
        }
//    $result['humidity']['labels'] = array_reverse( $tempArray );
        $result['humidity']['labels'] = $tempArray;

//    $res = $mysqli->query( "SELECT `value` FROM `sensors` WHERE `location` = '$sensor' AND `type` = 'humidity' AND `value` > 0 ORDER BY `recordTime` DESC LIMIT 0,24" );
        $res = $mysqli->query("SELECT AVG(`value`) FROM `sensors` WHERE `recordTime` BETWEEN NOW() - INTERVAL 48 HOUR AND NOW() AND `type` = 'humidity' AND `location` = '$sensor' GROUP BY DATE_FORMAT(`recordTime`,'%d;%m-%H')");
        $row = $res->fetch_all(MYSQLI_NUM);
        $tempArray = array();
        foreach ($row as $key => $value) {
            $val = implode("", $value);
            $tempArray[] = $val;
        }
//    $result['humidity']['series'] = array( array_reverse( $tempArray ) );
        $result['humidity']['series'] = array($tempArray);
    }


    // PRESSURE
    if ($pressure == 'true') {
//    $res = $mysqli->query( "SELECT TIME_FORMAT(`recordTime`,'%H:%i') FROM `sensors` WHERE `location` = '$sensor' AND `type` = 'pressure' AND `value` > 0 ORDER BY `recordTime` DESC LIMIT 0,24" );
        $res = $mysqli->query("SELECT DATE_FORMAT(`recordTime`,'%H') FROM `sensors` WHERE `recordTime` BETWEEN NOW() - INTERVAL 48 HOUR AND NOW() AND `type` = 'pressure' AND `location` = '$sensor' GROUP BY DATE_FORMAT(`recordTime`,'%d;%m-%H')");
        $row = $res->fetch_all(MYSQLI_NUM);
        $tempArray = array();
        foreach ($row as $key => $value) {
            $val = implode("", $value);
            if ($val % 2 == 0) { //even only
                $tempArray[] = $val . "h";
            } else {
                $tempArray[] = '';
            }
        }
//    $result['pressure']['labels'] = array_reverse( $tempArray );
        $result['pressure']['labels'] = $tempArray;

//    $res = $mysqli->query( "SELECT `value` FROM `sensors` WHERE `location` = '$sensor' AND `type` = 'pressure' AND `value` > 0 ORDER BY `recordTime` DESC LIMIT 0,24" );
        $res = $mysqli->query("SELECT AVG(`value`) FROM `sensors` WHERE `recordTime` BETWEEN NOW() - INTERVAL 48 HOUR AND NOW() AND `type` = 'pressure' AND `location` = '$sensor' GROUP BY DATE_FORMAT(`recordTime`,'%d;%m-%H')");
        $row = $res->fetch_all(MYSQLI_NUM);
        $tempArray = array();
        foreach ($row as $key => $value) {
            $val = implode("", $value);
            $tempArray[] = $val;
        }
//    $result['pressure']['series'] = array( array_reverse( $tempArray ) );
        $result['pressure']['series'] = array($tempArray);
    }

    return $result;

}

function getDailyProgrammingMode()
{
    $mysqli = connectDB();

    $res = $mysqli->query("SELECT `value` FROM `general` WHERE `label` = 'dailyProgrammingMode' LIMIT 0,1");
    $row = $res->fetch_row();
    $result = $row[0];

    return $result;
}

function setDailyProgrammingMode($mode = "everyday|eachday|weekday")
{
    $mysqli = connectDB();

    $res = $mysqli->query("UPDATE `general` SET `value` = '$mode' WHERE `label` = 'dailyProgrammingMode'");

    return $res;

}

function getProgram()
{
    $mysqli = connectDB();

    $res = $mysqli->query("SELECT * FROM `program`");

    return $res->fetch_all(MYSQLI_ASSOC);
}

function setProgram($name, $period, $value)
{

    $mysqli = connectDB();

    $res = $mysqli->query("UPDATE `program` SET `$period` = '$value' WHERE `day` = '$name'");

    return "UPDATE `program` SET `$period` = '$value' WHERE `day` = '$name'";

}

function setAmbianceMode($value)
{

    $mysqli = connectDB();

    $res = $mysqli->query("UPDATE `general` SET `value` = '$value' WHERE `label` = 'ambianceMode'");

    if ($res) {
        return $value;
    }

}