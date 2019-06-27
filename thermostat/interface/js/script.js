/**
 * Created by Stéphane Koeberlé on 2016/10/30.
 */

const config = {id: '3037368', tokenId: 'b6907d289e10d714a6e88b30761fae22'};

var minTemp = 17;
var maxTemp = 25;
var write;
var sensorInactive = [];
var id = config.id;
var tokenId = config.tokenId;
var units = 'metric';
var lang = 'fr';
var weatherIcons = {
    '01d': 'wi-day-sunny',
    '02d': 'wi-day-cloudy',
    '03d': 'wi-cloud',
    '04d': 'wi-cloudy',
    '09d': 'wi-rain',
    '10d': 'wi-day-rain',
    '11d': 'wi-storm-showers',
    '13d': 'wi-snow',
    '50d': 'wi-day-fog',
    '01n': 'wi-night-clear',
    '02n': 'wi-night-alt-cloudy',
    '03n': 'wi-cloud',
    '04n': 'wi-cloudy',
    '09n': 'wi-rain',
    '10n': 'wi-night-alt-rain',
    '11n': 'wi-storm-showers',
    '13n': 'wi-snow',
    '50n': 'wi-night-fog'
};

blink = false;
interval = null;

$(document).ready(function () {

    $('.alert-screen').hide();
    $('.outside-screen').hide();
    $('.inside-screen').hide();
    $('.log-screen').hide();
    $('.setup-screen').hide();
    $('.ambiance-setup').hide();

    // getDateOfLastRecordedData('exterior', 1);
    getDateOfLastRecordedData('living-room', 1);

    getSensor('living-room');
    // getSensor('exterior');
    getAmbianceMode();
    getCurrentAmbianceMode();

    window.loop = setInterval(function () {
        // getSensor('exterior');
        getSensor('living-room');
        getAmbianceMode();
        getCurrentAmbianceMode();
        // getDateOfLastRecordedData('exterior', 1);
        getDateOfLastRecordedData('living-room', 1);
        getOpenWeatherMap(id, tokenId, units, lang);
    }, 60000);

    $('.date').on('click', function () {
        window.location.reload();
    });

    getOpenWeatherMap(id, tokenId, units, lang);
});


function getDateOfLastRecordedData(location, i) {

    $.getJSON('treatment.php', {
        action: 'getDateOfLastRecordedData',
        sensor: location
    }).done(function (json) {

        // record date
        var d = json;

        // current date
        var t = parseInt(Date.now() / 1000);

        // 15 minutes
        var quarter = 60 * 15;

        // calculate difference and verify
        if (t - d > quarter) {
            sensorInactive[i] = true;
        } else {
            sensorInactive[i] = false;
        }

        // manage display
        var inArray = $.inArray(true, sensorInactive);
        if (inArray !== -1) {
            if (!blink) {
                blink = true;
                openAlertScreen('(' + location + ')');
                blinkSensor($('.sensor-status'));
            }
        } else {
            closeAlertScreen();
            unblinkSensor(interval);
        }

    });
}


function getSensor(location) {

    $.getJSON('treatment.php', {
        action: 'getCurrentSensorData',
        sensor: location
    }).done(function (json) {

        if (location === 'exterior') {
            location = 'outside';
        }

        display(location, json.temperature, json.pressure, json.humidity);

    });

    $.getJSON('treatment.php', {
        action: 'getCurrentTime'
    }).done(function (json) {
        $('.time').html(json);
    });

    $.getJSON('treatment.php', {
        action: 'getCurrentDate'
    }).done(function (json) {
        $('.date').html(json);
    });

}


function getAmbianceMode() {

    $.getJSON('treatment.php', {
        action: 'getAmbianceMode'
    }).done(function (json) {
        $('.ambiance-mode .auto').html(json.toUpperCase());
        $('.ambiance-setup label').removeClass('active');
        $('.ambiance-setup input:checked').attr('checked', false);
        $('.ambiance-setup input#' + json).attr('checked', true).parent().addClass('active');
    });

}


function display(location, t, p, h) {

    t = String(t);
    h = parseInt(h);
    p = parseInt(p);

    console.log(location, t, p, h);
    var temperature = [];

    if (t !== '') {
        if (t.includes('.')) {
            temperature = t.split('.');
            temperature[1] = temperature[1].substr(0, 1);
        } else {
            temperature[0] = t;
            temperature[1] = 0;
        }
        if (temperature[1] <= 2) {
            // temperature[0]++;
            temperature[1] = 0;
        }
        if (temperature[1] > 2 && temperature[1] <= 7) {
            temperature[1] = 5;
        }
        if (temperature[1] > 7) {
            temperature[0]++;
            temperature[1] = 0;
        }

        $('.' + location + '.temperature span.unity').html(String(temperature[0]));
        $('.' + location + '.temperature span.float').html(String(temperature[1]));

    }

    if (p > 0) {
        var pressure = String(parseInt(p));
        $('.' + location + '.pressure span.unity').html(pressure);
    }

    if (h > 0) {
        var humidity = String(Math.floor(h));
        $('.' + location + '.humidity span.unity').html(humidity);
    }

    // $('.' + location + '.temperature').html(t);
    // $('.' + location + '.pressure').html(p);
    // $('.' + location + '.humidity').html(h);

    // console.log(location, temperature, pressure, humidity);
}


function getCurrentAmbianceMode() {

    $.getJSON('treatment.php', {
        action: 'getCurrentAmbianceMode'
    }).done(function (json) {

        if (json == 'reduced') {
            $('.ambiance-mode .sun').hide();
            $('.ambiance-mode .cold').show();
        }

        if (json == 'comfort') {
            $('.ambiance-mode .cold').hide();
            $('.ambiance-mode .sun').show();
        }

    });
}


function setup() {

    $('.setup-button').fadeOut();
    $('.setup-screen').slideLeftShow();
    $('.dashboard').slideLeftHide();

    readTargetTemperature();
    setAmbianceMode();
    getDailyProgrammingMode();
    getProgram();

    initDatetimeSetup();
    dailyProgrammingMode();
    setProgram();

}


function closeSetup() {

    $('.setup-screen').slideRightHide();
    $('.dashboard').slideLeftShow();
    $('.setup-button').fadeIn();

}


function readTargetTemperature() {

    $.getJSON('treatment.php', {
        action: 'getTargetTemperature'
    }).done(function (json) {
        displayTargetTemperature(json);
    });

}


function readTemperatureSettings() {

    $.getJSON('treatment.php', {
        action: 'getTemperatureSettings'
    }).done(function (json) {
        setTemperatureSettings(json);
    });

}


function decreaseTargetTemperature() {

    if (write !== undefined) {
        clearTimeout(write);
    }

    var targetTemperature = parseFloat($('.target-temperature span').text());

    if (targetTemperature > minTemp) {
        targetTemperature -= 0.5;
        var result = sprintf("%.1f", targetTemperature);

        displayTargetTemperature(result);

        write = setTimeout(function () {
            setTargetTemperature(result)
        }, 3000);
    }

}


function increaseTargetTemperature() {

    if (write !== undefined) {
        clearTimeout(write);
    }

    var targetTemperature = parseFloat($('.target-temperature span').text());

    if (targetTemperature < maxTemp) {
        targetTemperature += 0.5;
        var result = sprintf("%.1f", targetTemperature);

        displayTargetTemperature(result);

        write = setTimeout(function () {
            setTargetTemperature(result)
        }, 1000);

    }

}


function displayTargetTemperature(temp) {

    $('.target-temperature span').html(temp);

}


function setTemperatureSettings(settings) {

    console.log(settings);

    // $('#tempSetting').attr();

}


function setTargetTemperature(temp) {

    $.getJSON('treatment.php', {
        action: 'setTargetTemperature',
        temp: temp
    }).done(function (json) {
        if (json) {
            $('.target-temperature').addClass('validAction');

            setTimeout(function () {
                $('.target-temperature').removeClass('validAction');
            }, 3000);
        }
    });

}


function initDatetimeSetup() {


    $('.setup-type input').on('change', function (e) {

        var target = e.target.value;

        $('.datetime-setup > div').hide();
        $('.datetime-setup > div').removeClass('active');

        $('#' + target).addClass('active');
        $('#' + target).fadeIn();

    });

    $('.datetime-setup .button').on('click', function (event) {
        event.preventDefault();
        if ($(this).hasClass('active')) {
            $(this).find('input').attr('checked', false);
            $(this).removeClass('active');
        } else {
            $(this).find('input').attr('checked', true);
            $(this).addClass('active');
        }
    })

}


function openAlertScreen(sensor) {

    $('.alert-screen .sensor-name').html(sensor);
    $('.dashboard').css({
        filter: 'blur(12px)',
        webkitFilter: 'blur(12px)'
    });
    $('.alert-screen').fadeIn();

}


function closeAlertScreen() {

    $('.alert-screen').fadeOut(400, function () {
        $('.dashboard').css({
            filter: 'none',
            webkitFilter: 'none'
        });
    }).hide();
}


function openOutsideScreen() {

    $('.dashboard').css({
        filter: 'blur(12px)',
        webkitFilter: 'blur(12px)'
    });
    $('.alert-screen').fadeIn();


    getSensorHistory('exterior', true, true, false);

    window.outsideScreenTimeout = setTimeout(function () {
        closeOutsideScreen();
    }, 20000);
}


function closeOutsideScreen() {

    $('.outside-screen').fadeOut(400, function () {
        $('.dashboard').css({
            filter: 'none',
            webkitFilter: 'none'
        });
    }).hide();
}


function openInsideScreen() {
    $('.dashboard').css({
        filter: 'blur(12px)',
        webkitFilter: 'blur(12px)'
    });
    $('.inside-screen').fadeIn();

    getSensorHistory('living-room', true, false, true);

    window.outsideScreenTimeout = setTimeout(function () {
        closeInsideScreen();
    }, 20000);
}


function closeInsideScreen() {

    $('.inside-screen').fadeOut(400, function () {
        $('.dashboard').css({
            filter: 'none',
            webkitFilter: 'none'
        });
    }).hide();
}


function openLogScreen() {
    $('.dashboard').css({
        filter: 'blur(12px)',
        webkitFilter: 'blur(12px)'
    });
    $('.log-screen').fadeIn();

    getLogPage();

    // window.outsideScreenTimeout = setTimeout(function () {
    //     closeLogScreen();
    // }, 20000);
}


function closeLogScreen() {

    $('.log-screen').fadeOut(400, function () {
        $('.dashboard').css({
            filter: 'none',
            webkitFilter: 'none'
        });
    }).hide();
}


function getSensorHistory(location, t, p, h) {


    $.getJSON('treatment.php', {
        action: 'getSensorHistory',
        sensor: location,
        t: t,
        p: p,
        h: h
    }).done(function (json) {

        if (location === 'exterior') {
            location = 'outside';
        } else {
            location = 'inside';
        }


        if (json.temperature && t) {


            var options = {
                width: '99%',
                height: '170px',
                showArea: true,
                showPoint: false,
                fullWidth: true,
                lineSmooth: Chartist.Interpolation.cardinal({
                    fillHoles: true
                })
                // ,plugins: [
                //     Chartist.plugins.ctThreshold({
                //         threshold: 1
                //     })
                // ]
            };


            var data_temperature = json.temperature;

            var chart = new Chartist.Line('#chart-' + location + '-temperature', data_temperature, options);
            chart.on('draw', function (data) {

                if (data.type === 'line' || data.type === 'area') {

                    data.element.animate({
                        d: {
                            begin: 200 * data.index,
                            dur: 2000,
                            from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
                            to: data.path.clone().stringify(),
                            easing: Chartist.Svg.Easing.easeOutQuint
                        }
                    });
                }

            });
        }
        if (json.pressure && p) {


            var options = {
                width: '99%',
                height: '170px',
                high: 1040,
                low: 980,
                showArea: true,
                showPoint: false,
                fullWidth: true,
                lineSmooth: Chartist.Interpolation.cardinal({
                    fillHoles: true
                })
            };


            var data_pressure = json.pressure;

            var chart = new Chartist.Line('#chart-' + location + '-pressure', data_pressure, options);
            chart.on('draw', function (data) {

                if (data.type === 'line' || data.type === 'area') {

                    data.element.animate({
                        d: {
                            begin: 200 * data.index,
                            dur: 2000,
                            from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
                            to: data.path.clone().stringify(),
                            easing: Chartist.Svg.Easing.easeOutQuint
                        }
                    });

                }

            });
        }
        if (json.humidity && h) {


            var options = {
                width: '99%',
                height: '170px',
                high: 100,
                low: 0,
                showArea: true,
                showPoint: false,
                fullWidth: true,
                lineSmooth: Chartist.Interpolation.cardinal({
                    fillHoles: true
                })
            };


            var data_humidity = json.humidity;

            var chart = new Chartist.Line('#chart-' + location + '-humidity', data_humidity, options);
            chart.on('draw', function (data) {

                if (data.type === 'line' || data.type === 'area') {

                    data.element.animate({
                        d: {
                            begin: 200 * data.index,
                            dur: 2000,
                            from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
                            to: data.path.clone().stringify(),
                            easing: Chartist.Svg.Easing.easeOutQuint
                        }
                    });

                }

            });
        }
    });
}


function dailyProgrammingMode() {

    $('.dailyProgrammingMode input').on('change', function (event) {
        var mode = $(event)[0].currentTarget.id;

        $.getJSON('treatment.php', {
            action: 'setDailyProgrammingMode',
            mode: mode
        }).done(function (json) {
            // console.log(json);
        });

    });
}


function getDailyProgrammingMode() {

    $.getJSON('treatment.php', {
        action: 'getDailyProgrammingMode'
    }).done(function (json) {
        $('#' + json).click();
    });

}


function setAmbianceMode() {

    $('.ambiance-mode-setup input').on('change', function (e) {

        var value = $(e)[0].currentTarget.id;

        $.getJSON('treatment.php', {
            action: 'setAmbianceMode',
            value: value
        }).done(function (json) {

            $('.ambiance-mode .auto').html(json.toUpperCase());

        });

    });
}


function getProgram() {

    $.getJSON('treatment.php', {
        action: 'getProgram'
    }).done(function (json) {

        $(json).each(function (key, object) {

            var prefix = object.day;
            $.each(object, function (index, value) {

                if (index != 'day') {
                    if (value == 1) {
                        $('input[name="' + prefix + '_' + index + '"]').parent().addClass('active');
                        $('input[name="' + prefix + '_' + index + '"]').attr('checked', true);
                    } else {
                        $('input[name="' + prefix + '_' + index + '"]').parent().removeClass('active');
                        $('input[name="' + prefix + '_' + index + '"]').attr('checked', false);
                    }
                }

            });
        });
    });

}


function setProgram() {


    $('.datetime-setup label.button').on('click', function (event) {

        var array = $(this).find('input')[0].name.split('_');
        var name = array[0];
        var position = array[1];
        var value = $(this).hasClass('active');
        if (value == true) {
            value = 1;
        } else {
            value = 0;
        }

        console.log(name, position, value);

        $.getJSON('treatment.php', {
            action: 'setProgram',
            name: name,
            position: position,
            value: value
        }).done(function (json) {
            console.log(json);
        });

    });
}


function getLogPage() {

    $.getJSON('treatment.php', {
        action: 'getLogPage'
    }).done(function (json) {
        $('.log-content').html(json);
    });
}


function blinkSensor(sensor) {

    interval = setInterval(function () {
        sensor.animate({
            opacity: 0
        }, 500, "linear", function () {
            sensor.animate({opacity: 1}, 500);
        });
    }, 1000)

}


function unblinkSensor(interval) {
    clearInterval(interval);
}


function getOpenWeatherMap(id, tokenId, units, lang) {

    $.getJSON('https://openweathermap.org/data/2.5/weather/?appid=' + tokenId + '&id=' + id + '&units=' + units + '&lang=' + lang, {}).done(function (json) {
        display('outside', json.main.temp, json.main.pressure, json.main.humidity);
        displayWeather(json.weather[0].icon, json.main.temp_min, json.main.temp_max);
    });
}


function displayWeather(icon, tempMin, tempMax) {

    tempMin = tempMin.toFixed(1);
    tempMax = tempMax.toFixed(1);

    $('.weather span.icon').attr('class', 'icon wi ' + weatherIcons[icon]);
    $('.weather span.temp_min').html(tempMin + '°C');
    $('.weather span.temp_max').html(tempMax + '°C');
}

jQuery.fn.extend({

    slideRightShow: function () {
        return this.each(function () {
            $(this).show('slide', {direction: 'right'}, 500);
        });
    },
    slideLeftHide: function () {
        return this.each(function () {
            $(this).hide('slide', {direction: 'left'}, 500);
        });
    },
    slideRightHide: function () {
        return this.each(function () {
            $(this).hide('slide', {direction: 'right'}, 500);
        });
    },
    slideLeftShow: function () {
        return this.each(function () {
            $(this).show('slide', {direction: 'left'}, 500);
        });
    }

});