/**
 * Created by Stéphane Koeberlé on 30/10/2016.
 */

var minTemp = 17;
var maxTemp = 25;
var write;

$(document).ready(function () {

    getDateOfLastRecordedData('living-room');

    $('.outside-screen').hide();
    $('.setup-screen').hide();

    getSensor('living-room');
    getSensor('exterior');
    getAmbianceMode();
    getCurrentAmbianceMode();

    window.loop = setInterval(function () {
        getSensor('living-room');
        getSensor('exterior');
        getAmbianceMode();
        getCurrentAmbianceMode();
        getDateOfLastRecordedData('living-room');
    }, 60000);

    $('.date').on('click', function () {
        window.location.reload();
    });

});


function getDateOfLastRecordedData(location) {

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
            var color = 'rgb(255,200, 0)';
            $('.unity').css('color', color);
            $('.dot').css('color', color);
            $('.float').css('color', color);
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

        if (json.temperature) {
            var temperature = json.temperature.split('.');

            $('.' + location + '.temperature span.unity').html(temperature[0]);
            $('.' + location + '.temperature span.float').html(temperature[1]);
        }

        if (json.humidity) {
            var humidity = json.humidity.split('.');

            $('.' + location + '.humidity span.unity').html(humidity[0]);
            $('.' + location + '.humidity span.float').html(humidity[1]);
        }

        if (json.pressure) {
            var pressure = json.pressure.split('.');

            $('.' + location + '.pressure span.unity').html(pressure[0]);
            // $('.' + location + '.pressure span.float').html(pressure[1]);
        }

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
        if (json == 'auto') {
            $('.ambiance-mode .auto').css('opacity', 1);
        } else {
            $('.ambiance-mode .auto').css('opacity', 0.2);
        }
    });

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

    initDatetimeSetup();

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
        }, 3000);

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


function openOutsideScreen() {
    $('.dashboard').slideRightHide();
    $('.outside-screen').slideLeftShow();
    $('.setup-button').hide();

    getSensorHistory('exterior');
}


function closeOutsideScreen() {
    $('.dashboard').slideRightShow();
    $('.outside-screen').slideLeftHide();
    $('.setup-button').show();
}


function getSensorHistory(location) {


    $.getJSON('treatment.php', {
        action: 'getSensorHistory',
        sensor: location
    }).done(function (json) {

        if (location === 'exterior') {
            location = 'outside';
        }

        if (json.temperature) {


            var options = {
                width: '99%',
                height: '200px',
                showArea: true,
                showPoint: false,
                fullWidth: true,
                lineSmooth: Chartist.Interpolation.cardinal({
                    fillHoles: true
                })
            };


            var data_temperature = json.temperature;

            var chart = new Chartist.Line('#chart-' + location + '-temperature', data_temperature, options);
            chart.on('draw', function (data) {

                if (data.type === 'line' || data.type === 'area') {

                    data.element.animate({
                        d: {
                            begin: 4000 * data.index,
                            dur: 4000,
                            from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
                            to: data.path.clone().stringify(),
                            easing: Chartist.Svg.Easing.easeOutQuint
                        }
                    });
                }

            });
        }
        if (json.pressure) {


            var options = {
                width: '99%',
                height: '200px',
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
                            begin: 4000 * data.index,
                            dur: 4000,
                            from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
                            to: data.path.clone().stringify(),
                            easing: Chartist.Svg.Easing.easeOutQuint
                        }
                    });

                }

            });
        }

        //
        // if (json.humidity) {
        //
        // }
        //
        // if (json.pressure) {
        // var data_pressure = {
        //     labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
        //     series: [
        //         [20, 19, 18, 17, 16, 15, 15, 14, 16, 18, 20, 22]
        //     ]
        // };
        //     new Chartist.Line('#chart-' + location + '-pressure', data_pressure, options);
        // }

        // console.log(json.temperature);
        // console.log(json.humidity);
        // console.log(json.pressure);
        // console.log(data_pressure);


    });
}

jQuery.fn.extend({

    slideRightShow: function () {
        return this.each(function () {
            $(this).show('slide', {direction: 'right'}, 1000);
        });
    },
    slideLeftHide: function () {
        return this.each(function () {
            $(this).hide('slide', {direction: 'left'}, 1000);
        });
    },
    slideRightHide: function () {
        return this.each(function () {
            $(this).hide('slide', {direction: 'right'}, 1000);
        });
    },
    slideLeftShow: function () {
        return this.each(function () {
            $(this).show('slide', {direction: 'left'}, 1000);
        });
    }

});