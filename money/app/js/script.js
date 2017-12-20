var apiUrl = "https://api.coinmarketcap.com/v1/ticker/";

$(document).ready(function () {

    getInfos('bytecoin-bcn/?convert=EUR', '.money_1');
    getInfos('monero/?convert=EUR', '.money_2');
    getInfos('bitcoin/?convert=EUR', '.money_3');

    window.loop = setInterval(function () {
        getInfos('bytecoin-bcn/?convert=EUR', '.money_1');
        getInfos('monero/?convert=EUR', '.money_2');
        getInfos('bitcoin/?convert=EUR', '.money_3');
    }, 60000);
});


function getInfos(money, target) {
    $.ajax({
        url: apiUrl + money,
        method: "GET",
        type: "JSON",
        async: true,
        cache: false
    }).done(function (json) {

        // Prices
        var prices = [];
        prices[0] = json[0].price_usd;
        prices[1] = json[0].price_eur;
        // Format
        for (var i = 0; i < prices.length; i++) {
            if (parseInt(prices[i]) < 10) {
                prices[i] = parseFloat(prices[i]).toFixed(6);
            } else {
                prices[i] = parseFloat(prices[i]).toFixed(2);
            }
        }

        // Percentages
        var percents = [];
        var evolution = [];
        var arrow = [];
        percents[0] = parseFloat(json[0].percent_change_1h).toFixed(2);
        percents[1] = parseFloat(json[0].percent_change_24h).toFixed(2);
        percents[2] = parseFloat(json[0].percent_change_7d).toFixed(2);
        // Format
        for (i = 0; i < percents.length; i++) {
            if (percents[i] < 0) {
                evolution[i] = ' minus';
                arrow[i] = 'down';
            } else {
                evolution[i] = ' plus';
                arrow[i] = 'up';
            }
        }

        // Datetime
        var timestamp = json[0].last_updated;


        $(target).html(
            '<h3>' + json[0].name + ' (' + json[0].symbol + ')' + '</h3>' +
            '<div class="separator"></div>' +
            '<div class="price-global">' +
            '<p class="price">' + prices[0] + ' USD' + '</p>' +
            '<p class="price">' + prices[1] + ' EUR' + '</p>' +
            '</div>' +
            '<div class="separator"></div>' +
            '<div class="percent-global">' +
            '<p class="percent' + evolution[0] + '"><i class="' + arrow[0] + '"></i>' + percents[0] + '%' + '</p>' +
            '<p class="percent' + evolution[1] + '"><i class="' + arrow[1] + '"></i>' + percents[1] + '%' + '</p>' +
            '<p class="percent' + evolution[2] + '"><i class="' + arrow[2] + '"></i>' + percents[2] + '%' + '</p>' +
            '</div>' +
            '<div class="time">' +
            '<p>' + timeConverter(timestamp) + '</p>' +
            '</div>'
        );

    });
}


function timeConverter(UNIX_timestamp) {
    var a = new Date(UNIX_timestamp * 1000);
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var year = a.getFullYear();
    var month = months[a.getMonth()];
    var date = a.getDate();
    var hour = a.getHours();
    var min = a.getMinutes();
    var sec = a.getSeconds();
    var time = minTwoDigits(date) + ' ' + month + ' ' + year + ' ' + minTwoDigits(hour) + ':' + minTwoDigits(min) + ':' + minTwoDigits(sec);
    return time;
}

function minTwoDigits(n) {
    return (n < 10 ? '0' : '') + n;
}

Number.prototype.pad = function (n) {
    return new Array(n).join('0').slice((n || 2) * -1) + this;
}