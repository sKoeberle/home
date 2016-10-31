/**
 * Created by stephane on 30/10/2016.
 */

$(document).ready(function () {

    $('.setyp-screen').hide();

    getSensor();

    window.loop = setInterval(function () {
        getSensor()
    }, 30000);

    $('.date').on('click', function () {
        window.location.reload();
    });
});


function getSensor() {

    $.getJSON('http://192.168.1.19', {
        //
    }).done(function (json) {

        if (json.sensor == 'living-room') {
            $('.temperature span').html(json.temperature);
            $('.humidity span').html(json.humidity);
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

    var targetTemperature = parseFloat($('.target-temperature span').text());

    if (targetTemperature > 16) {
        targetTemperature -= 0.5;
        var result = sprintf("%.1f", targetTemperature);

        displayTargetTemperature(result);
    }
}


function increaseTargetTemperature() {

    var targetTemperature = parseFloat($('.target-temperature span').text());

    if (targetTemperature < 26) {
        targetTemperature += 0.5;
        var result = sprintf("%.1f", targetTemperature);

        displayTargetTemperature(result);
    }
}


function displayTargetTemperature(temp) {
    $('.target-temperature span').html(temp);
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