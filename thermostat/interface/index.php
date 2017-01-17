<html>
<head>
    <meta charset="UTF-8">
    <title>Thermostat Interface</title>

    <link rel="stylesheet" href="../../vendor/jquery-ui-1.12.1/jquery-ui.css">
    <link rel="stylesheet" href="../../vendor/bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../vendor/bootstrap-3.3.7-dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../../vendor/rangeslider.js-2.3.0/rangeslider.css">
    <link rel="stylesheet" href="../../vendor/chartist/chartist.min.css">
    <link rel="stylesheet" href="css/style.css">

    <script src="../../vendor/jquery/jquery-3.1.1.min.js"></script>
    <script src="../../vendor/jquery-ui-1.12.1/jquery-ui.min.js"></script>
    <script src="../../vendor/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <script src="../../vendor/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
    <script src="../../vendor/sprintf/sprintf.min.js"></script>
    <script src="../../vendor/rangeslider.js-2.3.0/rangeslider.js"></script>
    <script src="../../vendor/chartist/chartist.min.js"></script>
    <script src="../../vendor/chartist/chartist-plugin-threshold.min.js"></script>
    <script src="js/script.js"></script>

</head>
<body>
<div class="container">
    <div class="row outside-screen">
        <div class="col-xs-12 col-md-12">
            <p class="lead text-left">Outside history</p>
            <div class="ct-chart ct-perfect-fourth" id="chart-outside-temperature"></div>
            <p class="chart-outside-temperature-label">Temperature (°C)</p>
            <div class="ct-chart ct-perfect-fourth" id="chart-outside-pressure"></div>
            <p class="chart-outside-pressure-label">Pressure (hPa)</p>
        </div>
        <div class="close-outside-screen" onclick="closeOutsideScreen();">
            <i class="glyphicon glyphicon-remove"></i>
        </div>
    </div>
    <div class="row inside-screen">
        <div class="col-xs-12 col-md-12">
            <p class="lead text-left">Inside history</p>
            <div class="ct-chart ct-perfect-fourth" id="chart-inside-temperature"></div>
            <p class="chart-inside-temperature-label">Temperature (°C)</p>
            <div class="ct-chart ct-perfect-fourth" id="chart-inside-humidity"></div>
            <p class="chart-inside-humidity-label">Humidity (%)</p>
        </div>
        <div class="close-inside-screen" onclick="closeInsideScreen();">
            <i class="glyphicon glyphicon-remove"></i>
        </div>
    </div>
    <div class="row dashboard">
        <div class="outside-resume-display" onclick="openOutsideScreen();">
            <div class="outside temperature"><p><span class="unity">00</span><span class="dot">.</span><span class="float">0</span><span class="unit">°C</span></p></div>
            <div class="outside separator"></div>
            <div class="outside pressure"><p><span class="unity">0000</span><span class="unit">hPa</span></p></div>
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
        <div class="col-xs-6 col-md-6 home-values" onclick="openInsideScreen();">
            <div class="living-room temperature"><p><span class="unity">00</span><span class="dot">.</span><span class="float">0</span><span class="unit">°C</span></p></div>
            <div class="living-room humidity"><p><span class="unity">00</span><span class="dot">.</span><span class="float">0</span><span class="unit">%</span></p></div>
        </div>
    </div>
    <div class="row setup-screen">
        <div class="col-xs-12 col-md-12">
            <p class="lead text-left">SETUP <span class="muted">Get the desired temperature by pressing the arrows</span></p>
        </div>

        <div class="col-xs-3 col-md-3"></div>
        <div class="col-xs-4 col-md-4">
            <p class="target-temperature text-right"><span>00.0</span>°C</p>
        </div>
        <div class="col-xs-1 col-md-1">
            <a class="ui-btn increase-temp text-center" onclick="increaseTargetTemperature();">
                <i class="glyphicon glyphicon-chevron-up"></i>
            </a>
            <a class="ui-btn decrease-temp text-center" onclick="decreaseTargetTemperature();">
                <i class="glyphicon glyphicon-chevron-down"></i>
            </a>
        </div>
        <div class="col-xs-4 col-md-4"></div>

        <div class="col-xs-12 col-md-12 setup-type">
            <div class="btn-group" data-toggle="buttons">
                <label class="btn ui-btn active">
                    <input type="radio" name="options" value="everyday" autocomplete="off" checked> Identical everyday
                </label>
                <label class="btn ui-btn">
                    <input type="radio" name="options" value="weekday_weekend" autocomplete="off"> Weekdays & weekend
                </label>
                <label class="btn ui-btn">
                    <input type="radio" name="options" value="each_weekday" autocomplete="off"> Each weekday
                </label>
            </div>
        </div>
        <div class="col-xs-12 col-md-12 datetime-setup">
            <div id="everyday" class="active">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="everydays" class="active">
                        <a class="ui-btn" href="#everydays" aria-controls="everydays" role="tab" data-toggle="tab">Every day is identical</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="day tab-pane active" role="tabpanel" id="everydays">
                        <div class="hours">
                            <?php for ($h = 0; $h <= 23; $h++): ?>
                                <div class="hour">
                                    <label><?php echo "{$h}h"; ?></label>

                                    <div class="hour-half">
                                        <?php for ($m = 0; $m <= 30; $m += 30): ?>
                                            <div class="half">
                                                <label class="button ui-btn">
                                                    <input type="checkbox" name="<?php echo "{$h}_{$m}"; ?>">
                                                </label>
                                            </div>
                                        <?php endfor ?>
                                    </div>
                                </div>
                            <?php endfor ?>
                        </div>
                    </div>
                </div>
            </div>
            <div id="weekday_weekend">

                <ul class="nav nav-tabs" role="tablist">
                    <li role="Weekdays" class="active">
                        <a class="ui-btn" href="#Weekdays" aria-controls="Weekdays" role="tab" data-toggle="tab">Weekdays</a>
                    </li>
                    <li role="Weekend" class="">
                        <a class="ui-btn" href="#Weekend" aria-controls="Weekend" role="tab" data-toggle="tab">Weekend</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="day tab-pane active" role="tabpanel" id="weekday">
                        <div class="hours">
                            <?php for ($h = 0; $h <= 23; $h++): ?>
                                <div class="hour">
                                    <label><?php echo "{$h}h"; ?></label>

                                    <div class="hour-half">
                                        <?php for ($m = 0; $m <= 30; $m += 30): ?>
                                            <div class="half">
                                                <label class="button ui-btn">
                                                    <input type="checkbox" name="<?php echo "{$h}_{$m}"; ?>">
                                                </label>
                                            </div>
                                        <?php endfor ?>
                                    </div>
                                </div>
                            <?php endfor ?>
                        </div>
                    </div>
                    <div class="day tab-pane" role="tabpanel" id="weekend">
                        <div class="hours">
                            <?php for ($h = 0; $h <= 23; $h++): ?>
                                <div class="hour">
                                    <label><?php echo "{$h}h"; ?></label>

                                    <div class="hour-half">
                                        <?php for ($m = 0; $m <= 30; $m += 30): ?>
                                            <div class="half">
                                                <label class="button ui-btn">
                                                    <input type="checkbox" name="<?php echo "{$h}_{$m}"; ?>">
                                                </label>
                                            </div>
                                        <?php endfor ?>
                                    </div>
                                </div>
                            <?php endfor ?>
                        </div>
                    </div>
                </div>
            </div>
            <div id="each_weekday">
                <ul class="nav nav-tabs" role="tablist">
                    <?php $week_days = [ 1 => "Monday", 2 => "Tuesday", 3 => "Wednesday", 4 => "Friday", 5 => "Thursday", 6 => "Saturday", 7 => "Sunday" ]; ?>
                    <?php foreach ($week_days as $day_num => $day_label): ?>
                        <li role="<?php echo $day_label; ?>" class="<?php echo( $day_num == 1 ? 'active' : '' ); ?>">
                            <a class="ui-btn" href="#<?php echo $day_label; ?>" aria-controls="<?php echo $day_label; ?>" role="tab" data-toggle="tab"><?php echo $day_label; ?></a>
                        </li>
                    <?php endforeach ?>
                </ul>
                <div class="tab-content">
                    <?php foreach ($week_days as $day_num => $day_label): ?>
                        <div class="day tab-pane<?php echo( $day_num == 1 ? ' active' : '' ); ?>" role="tabpanel" id="<?php echo $day_label; ?>">
                            <div class="hours">
                                <?php for ($h = 0; $h <= 23; $h++): ?>
                                    <div class="hour">
                                        <label><?php echo "{$h}h"; ?></label>

                                        <div class="hour-half">
                                            <?php for ($m = 0; $m <= 30; $m += 30): ?>
                                                <div class="half">
                                                    <label class="button ui-btn">
                                                        <input type="checkbox" name="<?php echo "{$h}_{$m}"; ?>">
                                                    </label>
                                                </div>
                                            <?php endfor ?>
                                        </div>
                                    </div>
                                <?php endfor ?>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-12 legend">
            <div class="comfort_mode"></div>
            <span>Comfort mode</span>
        </div>
        <div class="col-xs-12 col-md-12 legend">
            <div class="reduce_mode"></div>
            <span>Reduce mode</span>
        </div>
        <div class="close-setup" onclick="closeSetup();">
            <i class="glyphicon glyphicon-remove"></i>
        </div>
    </div>

    <div class="setup-button ui-btn" onclick="setup();">
        <i class="glyphicon glyphicon-cog"></i>
    </div>
</div>
</body>
</html>