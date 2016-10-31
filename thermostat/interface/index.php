<html>
<head>
    <meta charset="UTF-8">
    <!--<meta HTTP-EQUIV="refresh" CONTENT="60">-->
    <title>Thermostat Interface</title>

    <link rel="stylesheet" href="../../vendor/jquery-ui-1.12.1/jquery-ui.css">
<!--    <link rel="stylesheet" href="../../vendor/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.css">-->
    <link rel="stylesheet" href="../../vendor/bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../vendor/bootstrap-3.3.7-dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="css/style.css">

    <script src="../../vendor/jquery/jquery-3.1.1.min.js"></script>
    <script src="../../vendor/jquery-ui-1.12.1/jquery-ui.min.js"></script>
    <script src="../../vendor/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <script src="../../vendor/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
    <script src="../../vendor/sprintf/sprintf.min.js"></script>
    <script src="js/script.js"></script>

</head>
<body>
<div class="container">
    <div class="row dashboard">
        <div class="col-xs-6 col-md-6 date-time">
            <?php $date = new DateTime(); ?>
            <h2 class="time"><?php echo $date->format( "H:i" ); ?></h2>
            <h3 class="date ui-btn"><?php echo strtoupper( $date->format( "l, M j" ) ); ?></h3>
        </div>
        <div class="col-xs-6 col-md-6 home-values">
            <p class="temperature"><span>00.0</span>°C</p>
            <p class="humidity"><span>00.0</span>%</p>
        </div>
    </div>
    <div class="row setup-screen">
        <div class="col-xs-11 col-md-11">
            <p class="lead text-left">SETUP <span class="muted">Change the temperature by pressing on the arrows</span></p>
        </div>
        <div class="col-xs-1 col-md-1 pull-right ui-btn close-setup" onclick="closeSetup();">
           <i class="glyphicon glyphicon-remove"></i>
        </div>

        <div class="col-xs-3 col-md-3"></div>
        <div class="col-xs-6 col-md-6">
            <a class="ui-btn increase-temp text-center" onclick="increaseTargetTemperature();">
                <i class="glyphicon glyphicon-chevron-up"></i>
            </a>
            <p class="target-temperature text-center"><span>00.0</span>°C</p>
            <a class="ui-btn decrease-temp text-center" onclick="decreaseTargetTemperature();">
                <i class="glyphicon glyphicon-chevron-down"></i>
            </a>
        </div>
        <div class="col-xs-3 col-md-3"></div>
    </div>
</div>

<div class="setup-button ui-btn" onclick="setup();">
    <i class="glyphicon glyphicon-cog"></i>
</div>

</body>
</html>