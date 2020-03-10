var waitForFinalEvent = (function () {
    var timers = {};
    return function (callback, ms, uniqueId) {
        if (!uniqueId) {
            uniqueId = "Don't call this twice without a uniqueId";
        }
        if (timers[uniqueId]) {
            clearTimeout(timers[uniqueId]);
        }
        timers[uniqueId] = setTimeout(callback, ms);
    };
})();

var getRandomInt = function (min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
};

var idGenerator = function (size) {
    if (size === undefined) {
        size = 5;
    }

    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < size; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
};

function in_array(needle, haystack) {
    for (var i in haystack) {
        if (haystack[i] == needle) {
            return true;
        }
    }
    return false;
}

function toCelsius(far){
    var c = (far -32) * 5 / 9;
    return Math.round(c);
}

function checkNulls(i) {
    if (i < 10) {
        i = "0" + i
    }
    return i;
}