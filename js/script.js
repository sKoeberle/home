/**
 * Created by stephane on 30/10/2016.
 */

$('document').ready(function () {

    $.get('http://192.168.1.115/temp')
    //
        .done(function (json) {
            console.log(json);
            $('.temperature').html(json);
        }).fail(function (jqxhr, textStatus, error) {
        var err = textStatus + ", " + error;
        console.log("Request Failed: " + err)
    });


});