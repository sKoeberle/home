<html>
<head>
    <meta charset="UTF-8">
    <meta HTTP-EQUIV="refresh" CONTENT="60">
    <title>Thermostat Interface</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">


    <link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">

    <script src="http://code.jquery.com/jquery-3.1.1.min.js"
            integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
            crossorigin="anonymous"></script>

    <!-- Latest compiled and minified JavaScript -->
    <script
        src="https://code.jquery.com/jquery-3.1.1.min.js"
        integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
        crossorigin="anonymous"></script>

    <script src="js/script.js"></script>

</head>
<body>
<div class="container">
    <div class="row first-row">
        <div class="col-xs-6 col-md-6 date-time">
            <?php $date = new DateTime(); ?>
            <h2 class="time"><?php echo $date->format( "H:i" ); ?></h2>
            <h3 class="date"><?php echo strtoupper( $date->format( "F, jS D" ) ); ?></h3>
        </div>
        <div class="col-xs-3 col-md-3 home-values">
            <h3>Temperature (°C)</h3>
            <h2 class="temperature">00.0</h2>
        </div>
        <div class="col-xs-3 col-md-3 home-values">
            <h3>Humidity (%)</h3>
            <h2 class="humidity">00.0</h2>
        </div>
    </div>
</div>

</body>
</html>