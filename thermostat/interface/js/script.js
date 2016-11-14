/**
 * Created by Stéphane Koeberlé on 30/10/2016.
 */

var minTemp = 17;
var maxTemp = 25;
var write;

$(document).ready(function () {

    $('.setup-screen').hide();

    getSensor();
    getAmbianceMode();
    getCurrentAmbianceMode();

    window.loop = setInterval(function () {
        getSensor()
        getAmbianceMode()
        getCurrentAmbianceMode()
    }, 60000);

    $('.date').on('click', function () {
        window.location.reload();
    });

});


function getSensor() {

    $.getJSON('treatment.php', {
        action: 'getCurrentSensorData',
        sensor: 'living-room'
    }).done(function (json) {

        var temperature = json.temperature.split('.');

        $('.temperature span.unity').html(temperature[0]);
        $('.temperature span.float').html(temperature[1]);


        var humidity = json.humidity.split('.');

        $('.humidity span.unity').html(humidity[0]);
        $('.humidity span.float').html(humidity[1]);

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