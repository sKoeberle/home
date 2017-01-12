<html>
<head>
    <meta charset="UTF-8">
    <title>Thermostat Interface</title>

    <link rel="stylesheet" href="../../vendor/jquery-ui-1.12.1/jquery-ui.css">
    <link rel="stylesheet" href="../../vendor/bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../vendor/bootstrap-3.3.7-dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../../vendor/rangeslider.js-2.3.0/rangeslider.css">
    <link rel="stylesheet" href="css/style.css">

    <script src="../../vendor/jquery/jquery-3.1.1.min.js"></script>
    <script src="../../vendor/jquery-ui-1.12.1/jquery-ui.min.js"></script>
    <script src="../../vendor/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <script src="../../vendor/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
    <script src="../../vendor/sprintf/sprintf.min.js"></script>
    <script src="../../vendor/rangeslider.js-2.3.0/rangeslider.js"></script>
    <script src="js/script.js"></script>

</head>
<body>
<div class="container">
    <div class="row exterior-screen">
        <div class="col-xs-1 col-md-1 pull-right ui-btn close-exterior-screen"
        ">
        <i class="glyphicon glyphicon-remove"></i>
    </div>
</div>
<div class="row dashboard">
    <div class="exterior-resume-display" onclick="exterior();">
        <div class="exterior temperature"><p><span class="unity">00</span><span class="dot">.</span><span class="float">0</span><span class="unit">°C</span></p></div>
        <div class="exterior separator"></div>
        <div class="exterior pressure"><p><span class="unity">0000</span><span class="unit">hPa</span></p></div>
    </div>
    <div class="ambiance-mode">
        <i class="auto">AUTO</i>
        <i class="sun"></i>
        <i class="cold"></i>
    </div>
    <div class="col-xs-6 col-md-6 date-time">
        <?php $date = new DateTime(); ?>
        <h2 class="time"><?php echo $date->format( "H:i" ); ?></h2>
        <h3 class="date ui-btn"><?php echo strtoupper( $date->format( "l, M j" ) ); ?></h3>
    </div>
    <div class="col-xs-6 col-md-6 home-values">
        <div class="living-room temperature"><p><span class="unity">00</span><span class="dot">.</span><span class="float">0</span><span class="unit">°C</span></p></div>
        <div class="living-room humidity"><p><span class="unity">00</span><span class="dot">.</span><span class="float">0</span><span class="unit">%</span></p></div>
    </div>
</div>
<div class="row setup-screen">
    <div class="col-xs-11 col-md-11">
        <p class="lead text-left">SETUP <span class="muted">Get the desired temperature by pressing the arrows</span></p>
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
    <!--        <div class="col-xs-6 col-md-6">-->
    <!--            <input id="tempSetting" type="range"-->
    <!--                   min="0"-->
    <!--                   max="0"-->
    <!--                   step="0"-->
    <!--                   value="0"-->
    <!--                   data-orientation="horizontal">-->
    <!--        </div>-->
    <div class="col-xs-3 col-md-3"></div>
</div>

<div class="setup-button ui-btn" onclick="setup();">
    <i class="glyphicon glyphicon-cog"></i>
</div>

</body>
</html>