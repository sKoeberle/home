<html>
<head>
    <meta charset="UTF-8">
    <title>Thermostat Interface</title>

    <link rel="stylesheet" href="../../vendor/jquery-ui-1.12.1/jquery-ui.css">
    <link rel="stylesheet" href="../../vendor/bootstrap-3.3.7-dist/css/bootstrap.css">
    <!--    <link rel="stylesheet" href="../../vendor/bootstrap-3.3.7-dist/css/bootstrap-theme.css">-->
    <link rel="stylesheet" href="../../vendor/chartist/chartist.min.css">
    <link rel="stylesheet" href="../../vendor/weather-icons-master/css/weather-icons.css">
    <link rel="stylesheet" href="css/style.css?<?php echo time(); ?>">

    <script src="../../vendor/jquery/jquery-3.1.1.min.js"></script>
    <script src="../../vendor/jquery-ui-1.12.1/jquery-ui.min.js"></script>
    <!--    <script src="../../vendor/jquery.mobile-1.4.5/jquery.mobile-1.4.5.min.js"></script>-->
    <script src="../../vendor/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
    <script src="../../vendor/sprintf/sprintf.min.js"></script>
    <script src="../../vendor/chartist/chartist.min.js"></script>
    <script src="../../vendor/chartist/chartist-plugin-threshold.min.js"></script>
    <script src="js/script.js?<?php echo time(); ?>"></script>
</head>
<body>
<div class="container-fluid">

    <!-- ALERT SCREEN -->
    <div class="row alert-screen">
        <div class="close-button">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <p class="lead">Sensor error detected!</p>
                <span class="sensor-name"></span>
            </div>
            <i class="glyphicon glyphicon-remove" onclick="closeAlertScreen();"></i>
        </div>
    </div>
    <!-- END ALERT SCREEN -->

    <!-- OUTSIDE SCREEN -->
    <div class="row outside-screen">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <p class="lead text-left">Outside history</p>
            <div class="ct-chart ct-perfect-fourth" id="chart-outside-temperature"></div>
            <p class="chart-outside-temperature-label">Temperature (°C)</p>
            <div class="ct-chart ct-perfect-fourth" id="chart-outside-pressure"></div>
            <p class="chart-outside-pressure-label">Pressure (hPa)</p>
        </div>
        <div class="close-button">
            <i class="glyphicon glyphicon-remove" onclick="closeOutsideScreen();"></i>
        </div>
    </div>
    <!-- END OUTSIDE SCREEN -->

    <!-- INSIDE SCREEN -->
    <div class="row inside-screen">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <p class="lead text-left">Inside history</p>
            <div class="ct-chart ct-perfect-fourth" id="chart-inside-temperature"></div>
            <p class="chart-inside-temperature-label">Temperature (°C)</p>
            <div class="ct-chart ct-perfect-fourth" id="chart-inside-humidity"></div>
            <p class="chart-inside-humidity-label">Humidity (%)</p>
        </div>
        <div class="close-button">
            <i class="glyphicon glyphicon-remove" onclick="closeInsideScreen();"></i>
        </div>
    </div>
    <!-- END INSIDE SCREEN -->

    <!-- LOG SCREEN -->
    <div class="row log-screen">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <p class="lead text-left">Daily log</p>
            <div class="log-content"></div>
        </div>
        <div class="close-button">
            <i class="glyphicon glyphicon-remove" onclick="closeLogScreen();"></i>
        </div>
    </div>
    <!-- END LOG SCREEN -->

    <!-- DASHBOARD -->
    <div class="row dashboard">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 _hidden-xs">
            <div class="outside-resume-display" onclick="openOutsideScreen();">
                <div class="outside pressure"><p><span class="unity">0000</span><span class="unit">hPa</span></p></div>
                <div class="outside separator"></div>
                <div class="outside temperature"><p><span class="unity">00</span><span class="dot">.</span><span class="float">0</span><span class="unit">°C</span></p></div>
                <div class="outside weather"><p><span class="icon wi wi-na"></span><span class="temp_min"></span><span class="separator"></span><span class="temp_max"></span></p></div>
            </div>
            <div class="state">
                <div class="ambiance-mode">
                    <i class="auto">AUTO</i>
                    <i class="sun"></i>
                    <i class="cold"></i>
                </div>
                <div class="sensor">
                    <i class="sensor-status"></i>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 date-time">
            <?php $date = new DateTime(); ?>
            <h2 class="time"><?php echo $date->format("H:i"); ?></h2>
            <h3 class="date ui-btn"><?php echo strtoupper($date->format("l, M j")); ?></h3>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 home-values" onclick="openInsideScreen();">
            <div class="living-room temperature"><p><span class="unity">00</span><span class="dot">.</span><span class="float">0</span><span class="unit">°C</span></p></div>
            <!--            <div class="living-room humidity"><p><span class="unity">00</span><span class="dot">.</span><span class="float">0</span><span class="unit">%</span></p></div>-->
            <div class="living-room humidity"><p><span class="unity">00</span><span class="unit">%</span></p></div>
        </div>
    </div>
    <!-- END DASHBOARD -->

    <!-- SETUP SCREEN -->
    <div class="row setup-screen">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <p class="lead text-left">SETUP <span class="muted">Get the desired temperature and program comfort/reduce mode</span> <span class="muted">[<?php echo $_SERVER['REMOTE_ADDR']; ?>]</span></p>
        </div>

        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"></div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
                <p class="target-temperature text-right"><span>00.0</span>°C</p>
            </div>
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                <a class="ui-btn increase-temp text-center" onclick="increaseTargetTemperature();">
                    <i class="glyphicon glyphicon-chevron-up"></i>
                </a>
                <a class="ui-btn decrease-temp text-center" onclick="decreaseTargetTemperature();">
                    <i class="glyphicon glyphicon-chevron-down"></i>
                </a>
            </div>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 ambiance-mode-setup">
            <div class="btn-group-vertical" data-toggle="buttons">
                <label class="btn ui-btn active">
                    <input type="radio" name="options" id="auto" value="auto" autocomplete="off" checked>AUTO
                </label>
                <label class="btn ui-btn">
                    <input type="radio" name="options" id="comfort" value="comfort" autocomplete="off">COMFORT
                </label>
                <label class="btn ui-btn">
                    <input type="radio" name="options" id="reduced" value="reduced" autocomplete="off">REDUCED
                </label>
            </div>
        </div>
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"></div>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 setup-type dailyProgrammingMode">
            <div class="btn-group" data-toggle="buttons">
                <label class="btn ui-btn active">
                    <input type="radio" name="dailyProgrammingMode" id="everyday" value="everydays" autocomplete="off" checked> Identical everyday
                </label>
                <label class="btn ui-btn">
                    <input type="radio" name="dailyProgrammingMode" id="weekday" value="weekdays" autocomplete="off"> Weekdays & weekend
                </label>
                <label class="btn ui-btn">
                    <input type="radio" name="dailyProgrammingMode" id="eachday" value="eachdays" autocomplete="off"> Each weekday
                </label>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 datetime-setup">
            <div id="everydays" class="active">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="everydayss" class="active">
                        <a class="ui-btn" href="#everydayss" aria-controls="everydayss" role="tab" data-toggle="tab">Every day is identical</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="day tab-pane active" role="tabpanel" id="everydayss">
                        <div class="hours">
                            <?php for ($h = 0; $h <= 23; $h++): ?>
                                <div class="hour">
                                    <label><?php echo "{$h}h"; ?></label>

                                    <div class="hour-half">
                                        <?php for ($m = 0; $m <= 30; $m += 30): ?>
                                            <div class="half">
                                                <label class="button ui-btn">
                                                    <?php if ($m == 0): ?>
                                                        <input type="checkbox" name="<?php echo "all_{$h}"; ?>">
                                                    <?php else: ?>
                                                        <input type="checkbox" name="<?php echo "all_{$h}.{$m}"; ?>">
                                                    <?php endif ?>
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
            <div id="weekdays">

                <ul class="nav nav-tabs" role="tablist">
                    <li role="weekdayss" class="active">
                        <a class="ui-btn" href="#weekdayss" aria-controls="weekdayss" role="tab" data-toggle="tab">Weekdays</a>
                    </li>
                    <li role="weekends" class="">
                        <a class="ui-btn" href="#weekends" aria-controls="weekends" role="tab" data-toggle="tab">Weekend</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="day tab-pane active" role="tabpanel" id="weekdayss">
                        <div class="hours">
                            <?php for ($h = 0; $h <= 23; $h++): ?>
                                <div class="hour">
                                    <label><?php echo "{$h}h"; ?></label>

                                    <div class="hour-half">
                                        <?php for ($m = 0; $m <= 30; $m += 30): ?>
                                            <div class="half">
                                                <label class="button ui-btn">
                                                    <?php if ($m == 0): ?>
                                                        <input type="checkbox" name="<?php echo "weekday_{$h}"; ?>">
                                                    <?php else: ?>
                                                        <input type="checkbox" name="<?php echo "weekday_{$h}.{$m}"; ?>">
                                                    <?php endif ?>
                                                </label>
                                            </div>
                                        <?php endfor ?>
                                    </div>
                                </div>
                            <?php endfor ?>
                        </div>
                    </div>
                    <div class="day tab-pane" role="tabpanel" id="weekends">
                        <div class="hours">
                            <?php for ($h = 0; $h <= 23; $h++): ?>
                                <div class="hour">
                                    <label><?php echo "{$h}h"; ?></label>

                                    <div class="hour-half">
                                        <?php for ($m = 0; $m <= 30; $m += 30): ?>
                                            <div class="half">
                                                <label class="button ui-btn">
                                                    <?php if ($m == 0): ?>
                                                        <input type="checkbox" name="<?php echo "weekend_{$h}"; ?>">
                                                    <?php else: ?>
                                                        <input type="checkbox" name="<?php echo "weekend_{$h}.{$m}"; ?>">
                                                    <?php endif ?>
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
            <div id="eachdays">
                <ul class="nav nav-tabs" role="tablist">
                    <?php $week_days = [1 => "Monday", 2 => "Tuesday", 3 => "Wednesday", 4 => "Friday", 5 => "Thursday", 6 => "Saturday", 7 => "Sunday"]; ?>
                    <?php foreach ($week_days as $day_num => $day_label): ?>
                        <li role="<?php echo $day_label; ?>" class="<?php echo($day_num == 1 ? 'active' : ''); ?>">
                            <a class="ui-btn" href="#<?php echo $day_label; ?>" aria-controls="<?php echo $day_label; ?>" role="tab" data-toggle="tab"><?php echo $day_label; ?></a>
                        </li>
                    <?php endforeach ?>
                </ul>
                <div class="tab-content">
                    <?php foreach ($week_days as $day_num => $day_label): ?>
                        <div class="day tab-pane<?php echo($day_num == 1 ? ' active' : ''); ?>" role="tabpanel" id="<?php echo $day_label; ?>">
                            <div class="hours">
                                <?php for ($h = 0; $h <= 23; $h++): ?>
                                    <div class="hour">
                                        <label><?php echo "{$h}h"; ?></label>

                                        <div class="hour-half">
                                            <?php for ($m = 0; $m <= 30; $m += 30): ?>
                                                <div class="half">
                                                    <label class="button ui-btn">
                                                        <?php if ($m == 0): ?>
                                                            <input type="checkbox" name="<?php echo "{$day_num}_{$h}"; ?>">
                                                        <?php else: ?>
                                                            <input type="checkbox" name="<?php echo "{$day_num}_{$h}.{$m}"; ?>">
                                                        <?php endif ?>
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
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 legend">
            <div class="comfort_mode"></div>
            <span>Comfort mode</span>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 legend">
            <div class="reduce_mode"></div>
            <span>Reduce mode</span>
        </div>
        <div class="close-setup" onclick="closeSetup();">
            <i class="glyphicon glyphicon-remove"></i>
        </div>
    </div>
    <!-- END SETUP SCREEN -->

    <div class="setup-button ui-btn" onclick="setup();">
        <i class="glyphicon glyphicon-cog"></i>
    </div>

    <div class="log-button ui-btn" onclick="openLogScreen();">
        <i class="glyphicon glyphicon-list-alt"></i>
    </div>
</div>
</body>
</html>