var $ = jQuery.noConflict();

jQuery(function($){
    $.datepicker.regional['pl'] = {
        closeText: 'Zamknij',
        prevText: '&#x3c;Poprzedni',
        nextText: 'Następny&#x3e;',
        currentText: 'Dziś',
        monthNames: ['Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec',
            'Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'],
        monthNamesShort: ['Sty','Lu','Mar','Kw','Maj','Cze',
            'Lip','Sie','Wrz','Pa','Lis','Gru'],
        dayNames: ['Niedziela','Poniedziałek','Wtorek','Środa','Czwartek','Piątek','Sobota'],
        dayNamesShort: ['Nie','Pn','Wt','Śr','Czw','Pt','So'],
        dayNamesMin: ['N','Pn','Wt','Śr','Cz','Pt','So'],
        weekHeader: 'Tydz',
        dateFormat: 'dd.mm.yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''};
});




function shalert(txt) {
    var div = $('<div>').addClass('popup-overlay').append('\
        <div class="popup-wrapper">\
        <div class="popup-body">\
        <div class="popup-head">\
        <a href="#" class="cancel"><i class="icomoon icon-close"></i></a>\
        </div>\
        <div class="popup-content">\
        <h3>' + txt + '</h3>\
        <a class="button cancel" title="" href="#">ZAMKNIJ</a>\
        </div>\
        </div>\
        </div>\
    ');

    $('body').append(div);
    div.fadeIn();

    $('.popup-overlay .cancel').on('click', function(e) {
        $('.popup-overlay').fadeOut(function() { $(this).remove(); });
        e.preventDefault();
    });
}

function confirm(txt, href) {
    var div = $('<div>').addClass('popup-overlay').append('\
        <div class="popup-wrapper">\
        <div class="popup-body">\
        <div class="popup-head">\
        <a href="#" class="cancel"><i class="icomoon icon-close"></i></a>\
        </div>\
        <div class="popup-content">\
        <h3>' + txt + '</h3>\
        <a class="button cancel" title="" href="#">ANULUJ</a>\
        <a class="button okay" title="" href="' + href + '">TAK</a>\
        </div>\
        </div>\
        </div>\
    ');

    $('body').append(div);
    div.fadeIn();

    $('.popup-overlay .cancel').on('click', function(e) {
        $('.popup-overlay').fadeOut(function() { $(this).remove(); });
        e.preventDefault();
    });
}

function shconfirmforfunc( txt, cb) {
    var div = $('<div>').addClass('popup-overlay').append('\
        <div class="popup-wrapper">\
        <div class="popup-body">\
        <div class="popup-head">\
        <a href="#" class="cancel"><i class="icomoon icon-close"></i></a>\
        </div>\
        <div class="popup-content">\
        <h3>' + txt + '</h3>\
        <a class="button cancel" title="" href="#">ANULUJ</a>\
        <a class="button okay" title="" href="#">TAK</a>\
        </div>\
        </div>\
        </div>\
    ');

    $('body').append(div);
    div.fadeIn();

    $('.popup-overlay .cancel').on('click', function(e) {
        $('.popup-overlay').fadeOut(function() { $(this).remove(); });
        e.preventDefault();
    });

    $('.popup-overlay .okay').on('click', function(e) {
        e.preventDefault();
        if (typeof cb === "function") {
            cb.call();
        }
        $('.popup-overlay').fadeOut(function() { $(this).remove(); });
    });
}

function eHandler() {
    $('a').each(function() {
        if ($(this).attr('onclick') != undefined) {
            var pattern = /^return confirm/g;
            var onclick = $(this).attr('onclick');
            if (pattern.test(onclick)) {
                var href = $(this).attr('href');

                onclick = onclick.replace('return confirm(\'', '').replace('\');', '').replace('return confirm("', '').replace('");', '');
                $(this).removeAttr('onclick');
                $(this).off('click').on('click', function(e) { e.preventDefault(); confirm(onclick, href); });
            }
        }
    });
}

var initFormFields = function () {
    var inpt = $('input[type="text"], input[type="password"], textarea');
    $.each(inpt, function () {
        if ($(this).val() === '') {
            $(this).val($(this).attr('title'));
        }
    });

    $('input[type="text"], input[type="password"], textarea').focus(function () {
        if ($(this).val() === $(this).attr('title')) {
            $(this).val('');
        }
    });

    $('input[type="text"], input[type="password"], textarea').blur(function () {
        if ($(this).val() === '') {
            $(this).val($(this).attr('title'));
        }
    });
};

var isIE8 = false;

function equalHeight(parent, element) {
    var overall = $("body > .overall");
    var ovh = overall.height();
    overall.css("min-height", ovh);
    var children = $(parent).find(element);
    var highestBox = 0;
    children.css("min-height", "");
    children.each(function(){
        if($(this).height() > highestBox){
            highestBox = $(this).height();
        }
    });
    if (highestBox <= 600) {
        highestBox = 600;
    }
    highestBox = highestBox + 50;
    children.css("min-height", highestBox+"px");
    overall.css("min-height", "");
}
var custombind = function () {
    jQuery(".shelect.active").removeClass('active').children('ul').slideUp('fast');
    jQuery(document).off('click', custombind);
};

var dropdownbind = function () {
    jQuery(".kilowats.active").removeClass('active').children('.dropdown-menu').slideUp('fast');
    jQuery(document).off('click', custombind);
};

var FixedButtonsEnabled = false;
var FixedButtonsEl;

var windowEl;

var runFixedButtons = function () {
    if (FixedButtonsEl.length > 0) {
        if (FixedButtonsEnabled) {
            if (window.pageYOffset > 61) {
                FixedButtonsEl.addClass('active');
            } else {
                FixedButtonsEl.removeClass('active');
            }
        } else {
            FixedButtonsEl.removeClass('active');
        }
    }
};

var mobileMenuEnabled = false;
var mobileMenuTimeouts = {};

var clearSubMenu = function (target) {
    $("#desktop-subs ul[data-target='" + target + "']").removeClass('active');
};

var tilesScrollLock = false;
var tilesScrollInterval;

var tilesMoveLeft = function (amm) {
    if (amm > 0) {
        var el = $("#tiles-movable-content").parent();
        var csval = el['scrollLeft']();
        if (csval > 5) {
            var calc = csval - 5;
            el['scrollLeft'](calc);

            var namm = amm - 5;
            setTimeout(function () {
                tilesMoveLeft(namm);
            }, 10);
        } else {
            el['scrollLeft'](0);
            tilesScrollLock = false;
        }
        el.trigger('scroll');
    } else {
        tilesScrollLock = false;
    }
};

var tilesMoveRight = function (amm) {
    if (amm > 0) {
        var child = $("#tiles-movable-content");
        var el = child.parent();

        var maxScroll = child.width() - el.children('.tiles-wrap').width();

        var csval = el['scrollLeft']();
        if (csval < maxScroll){
            var calc = csval + 5;
            el['scrollLeft'](calc);
            el.trigger('scroll');

            var namm = amm - 5;
            setTimeout(function () {
                tilesMoveRight(namm);
            }, 10);
        } else {
            tilesScrollLock = false;
        }
    } else {
        tilesScrollLock = false;
    }
};

var notepadTimeout = null;

function setWeather(latlng){
    $.post(weatherUrl, {'latlng': latlng}, function(data) { });
}

var activeToggleTimeout;

var sbSaveEnabled = true;
var setSbStatus = function (mode, status) {
    if (sbSaveEnabled) {
        var data = mode + '=' + status;

        $.post(sbUrl, data, function (data) {});
        window[mode] = status;
    }
};
var iOS;
var mouseX;
var mouseY;

var tilesMouseoutTimeout;
var tilesMouseoutElement;

jQuery(document).ajaxComplete(function() {
    eHandler();
});

jQuery(document).ready(function () {
    $.mobiscroll.defaults.theme = 'wp-light';
    $.mobiscroll.defaults.lang = 'pl';
    var mobiscrollCurrYear = new Date().getFullYear();
    var mobiscrollMaxYear = mobiscrollCurrYear + 10;

    $('.accordion-trigger').on('click', function(e) {
        e.preventDefault();
        var accordionCnt = $(this).closest('.accordion-container');
        if (!accordionCnt.hasClass('active')) {
            $('.accordion-container').removeClass('active');
            accordionCnt.addClass('active');
            $('.accordion-content').slideUp('fast');
            $(this).parent().find('.accordion-content').slideDown('fast');
        }
        else {
            accordionCnt.removeClass('active');
            accordionCnt.find('.accordion-content').slideUp('fast');
      }
    });
    iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    if (iOS) {
        $("body").addClass('iamios');
    }
    $(document).on('click', '.options-trigger', function(e) {
        e.preventDefault();
    });
    FixedButtonsEl = $("#hidden-head");
    $.datepicker.setDefaults($.datepicker.regional['pl']);
    $('.wrapper').TrackpadScrollEmulator();
    $(document).on('click', '.calc-button', function(e) {
        e.preventDefault();
        var holder = $(this).closest('.calc-widget');

        var result = holder.attr('data-result');
        var element1 = holder.attr('data-element1');
        var inter = holder.attr('data-inter');
        var element2 = holder.attr('data-element2');

        var func = ['/', '*', '+', '-'];
        var dot = ['.'];
        var resultchar = ['='];

        var char = $(this).attr('href').replace(/#/g, '');

        if (in_array(char, func)) {
            if (element1 != '') {
                if (!inter) {
                    inter = char;
                    holder.attr('data-inter', char);
                    holder.find('.result').text(holder.find('.result').text() + ' ' + char + ' ');
                } else {
                    if (element2 != '') {
                        holder.find('.green').trigger('click');
                        inter = char;
                        holder.find('.result').text(element1 + ' ' + char + ' ');
                    } else {
                        element2 = '';
                        holder.attr('data-element2', element2);
                        inter = char;
                        holder.find('.result').text(element1 + ' ' + char + ' ');
                    }
                    holder.attr('data-inter', char);
                }
            }
        } else if (in_array(char, dot)) {
            var regex = /([.]{1})/g;

            if (inter == '') {
                if (!regex.test(element1)) {
                    if (element1 == '') {
                        element1 += '0.';
                        holder.find('.result').text(holder.find('.result').text() + '0.');
                    } else {
                        element1 += '.';
                        holder.find('.result').text(holder.find('.result').text() + '.');
                    }
                    holder.attr('data-element1', element1);
                }
            } else {
                if (!regex.test(element2)) {
                    if (element2 == '') {
                        element2 += '0.';
                        holder.find('.result').text(holder.find('.result').text() + '0.');
                    } else {
                        element2 += '.';
                        holder.find('.result').text(holder.find('.result').text() + '.');
                    }
                    holder.attr('data-element2', element2);
                }
            }
        } else if (in_array(char, resultchar)) {
            if (inter != '' && element2 != '') {
                var regex = /([.]{1})/g;

                var el1 = regex.test(element1) ? parseFloat(element1) : parseInt(element1);
                var el2 = regex.test(element2) ? parseFloat(element2) : parseInt(element2);

                var res = null;

                switch (inter) {
                    case '/':
                        if (el2 != 0) { res = el1 / el2; }
                        break;
                    case '*':
                        res = el1 * el2;
                        break;
                    case '+':
                        res = el1 + el2;
                        break;
                    case '-':
                        res = el1 - el2;
                        break;
                }

                if (regex.test(res)) {
                    res = Math.round(res * 100) / 100;
                } else {
                    res = parseInt(res);
                }

                if (res !== null) {
                    holder.find('.result').text(res);
                    element1 = res + '';
                    inter = '';
                    element2 = '';
                    holder.attr('data-element1', element1);
                    holder.attr('data-inter', inter);
                    holder.attr('data-element2', element2);
                }
            }
        } else if (char == '') {
            holder.find('.result').text('');
            result = '';
            element1 = '';
            inter = '';
            element2 = '';
            holder.attr('data-result', result);
            holder.attr('data-element1', element1);
            holder.attr('data-inter', inter);
            holder.attr('data-element2', element2);
        } else {
            if (inter == '') {
                element1 += char;
                holder.attr('data-element1', element1);
                holder.find('.result').text(holder.find('.result').text() + char);
            } else {
                element2 += char;
                holder.attr('data-element2', element2);
                holder.find('.result').text(holder.find('.result').text() + char);
            }
        }
    });
    $(document).on('click', ".selectPhotoAjax", function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        var params = $(this).attr('data-params');

        $.ajax({type: 'POST', url: href, data: {
            'cropper' : params
        }, success: function(data) {
            var div = $('<div>').addClass('popup-overlay').append(data);

            $('body').addClass('mmvisible').append(div);
            div.fadeIn();

            $('.popup-overlay .cancel').on('click', function(e) {
                e.preventDefault();
                if (!$(this).hasClass('disabled')) {
                    $('.popup-overlay').fadeOut(function () {
                        $('body').removeClass('mmvisible');
                        $(this).remove();
                    });
                }
            });
        }});
    });
    $(document).on('click', ".clearPhoto", function (e) {
        e.preventDefault();
        var fieldname = $(this).attr('data-fieldname');
        var container = $(this).attr('data-container');
        var button = $(this).attr('data-button');

        shconfirmforfunc('Czy na pewno usunąć zdjęcie?', function () {
            $('input[name="' + fieldname + '"]').val('');
            $("#" + container).hide().html('');
            $("#" + button).show();
        })
    });

    $('.fancy-sel').fancySelect({includeBlank: true});
    $('.fancy-sel-noblank').fancySelect({includeBlank: false});
    $('a[href="#"]').on('click', function (e) {
        e.preventDefault();
    });
    /*$('input[type=file]').change(function () {
        /!*var fileName =*!/
        var mfile = $(this).val().replace("C:\\fakepath\\", "");
        $(this).next().html(mfile);

        /!*return fileName;*!/
        /!*$(this).parent().find('p').text($(this).val());*!/
        if ($(this).parent().find('p').text() == 0) {
            $(this).parent().find('p').text('Nie wybrano pliku');
        } 
    });*/
    initFormFields();

    $("#tilesMoveLeft").on('click', function (e) {
        e.preventDefault();
        clearInterval(tilesScrollInterval);
        if (tilesScrollLock){
            tilesScrollInterval = setInterval(function () {
                if (tilesScrollLock == false){
                    clearInterval(tilesScrollInterval);
                    tilesScrollLock = true;
                    tilesMoveLeft(120);
                }
            }, 100);
        } else {
            tilesScrollLock = true;
            tilesMoveLeft(120);
        }
    });
    $("#tilesMoveRight").on('click', function (e) {
        e.preventDefault();
        clearInterval(tilesScrollInterval);
        if (tilesScrollLock){
            tilesScrollInterval = setInterval(function () {
                if (tilesScrollLock == false){
                    clearInterval(tilesScrollInterval);
                    tilesScrollLock = true;
                    tilesMoveRight(120);
                }
            }, 100);
        } else {
            tilesScrollLock = true;
            tilesMoveRight(120);
        }
    });

    $('.mobiscr-datetime').mobiscroll().datetime({
        description: 'Ustawienia daty i godziny',
        focusOnClose: false,
        mode: 'mixed',       // Specify scroller mode like: mode: 'mixed' or omit setting to use default
        display: 'bubble', // Specify display mode like: display: 'bottom' or omit setting to use default
        minDate: new Date(2000,3,10,9,22),  // More info about minDate: http://docs.mobiscroll.com/2-14-0/datetime#!opt-minDate
        maxDate: new Date(mobiscrollMaxYear,7,30,15,44),   // More info about maxDate: http://docs.mobiscroll.com/2-14-0/datetime#!opt-maxDate
        stepMinute: 1  // More info about stepMinute: http://docs.mobiscroll.com/2-14-0/datetime#!opt-stepMinute
    });

    $('.mobiscr-date').mobiscroll().date({
        description: 'Ustawienia daty',
        focusOnClose: false,
        mode: 'mixed',       // Specify scroller mode like: mode: 'mixed' or omit setting to use default
        display: 'bubble', // Specify display mode like: display: 'bottom' or omit setting to use default
        minDate: new Date(2000,3,10),  // More info about minDate: http://docs.mobiscroll.com/2-14-0/datetime#!opt-minDate
        maxDate: new Date(mobiscrollMaxYear,7,30,15,44)   // More info about maxDate: http://docs.mobiscroll.com/2-14-0/datetime#!opt-maxDate
    });

    $('.mobiscr-time').mobiscroll().time({
        description: 'Ustawienia godziny',
        focusOnClose: false,
        mode: 'mixed',       // Specify scroller mode like: mode: 'mixed' or omit setting to use default 
        display: 'bubble', // Specify display mode like: display: 'bottom' or omit setting to use default 
        stepMinute: 1  // More info about stepMinute: http://docs.mobiscroll.com/2-14-0/datetime#!opt-stepMinute
    });

    eHandler();

    if ($('html').hasClass('ie8')){
        isIE8 = true;
    }

    $( ".article-date" ).datepicker({
        defaultDate: +1,
        autoSize: true,
        appendText: '(yyyy-mm-dd)',
        dateFormat: 'yy-mm-dd'
    });

    $('.editor').tinymce({
        script_url: '/a/js/tinymce/tinymce.min.js',
        theme: 'modern',
        height : 500,
        plugins: [ "link image textcolor anchor table responsivefilemanager  code" ],
        /*file_browser_callback : elFinderBrowser,*/
        resize: false,
        menubar: false,
        toolbar_items_size: 'small',
        toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | formatselect fontselect fontsizeselect | bullist numlist | outdent indent blockquote | undo redo",
        toolbar2: "link unlink anchor image | forecolor backcolor | table | hr removeformat | subscript superscript | code",
        image_advtab: true ,
        relative_urls: false,
        remove_script_host : false,
        document_base_url : "http://api.cdoc.pl/",
        external_filemanager_path:"/filemanager/",
        filemanager_title:"Responsive Filemanager" ,
        external_plugins: { "filemanager" : "/filemanager/plugin.min.js"}
    });

    $(function(){
        var ink, d, x, y;
        $(".material").click(function(e){
            if($(this).find(".ink").length === 0){
                $(this).prepend("<span class='ink'></span>");
            }

            ink = $(this).find(".ink");
            ink.removeClass("animate");

            if(!ink.height() && !ink.width()){
                d = Math.max($(this).outerWidth(), $(this).outerHeight());
                ink.css({height: d, width: d});
            }

            x = e.pageX - $(this).offset().left - ink.width()/2;
            y = e.pageY - $(this).offset().top - ink.height()/2;

            ink.css({top: y+'px', left: x+'px'}).addClass("animate");
        });
    });

    $(document).on('click', ".position-setters", function (e) {
        if ($(".clear-filters").is(':visible')) {
            e.preventDefault();
            shalert('Aby ustawiać pozycję wyczyść filtry.');
        }
    });

    $(".listing-filters").on('keyup change.fs change', function (e) {
        var filtersSet = false;

        $(".listing-filters").not('.null').each(function () {
            if ($(this).is(':checkbox') || $(this).is(':radio')){
                if ($(this).is(':checked')) filtersSet = true;
            } else {
                if ($(this).val().length > 0) filtersSet = true;
            }
        });

        if (filtersSet){
            $(".clear-filters").css('display', 'inline-block');
        } /*else {
            $(".clear-filters").hide();
        }*/
    });

    $(".clear-filters").on('click', function (e) {
        e.preventDefault();
        var me = $(this);

        var clearstate = me.attr('data-clearstate');
        clearstate = typeof clearstate !== 'undefined' ? clearstate : false;
        var reloadstate = me.attr('data-reloadstate');
        reloadstate = typeof reloadstate !== 'undefined' ? reloadstate : false;
        reloadstate = (reloadstate == 'true');

        var inpid = me.attr('data-inpid');

        $("#" + inpid).val('').trigger('keyup');

        if (clearstate == 'true') {
            var inpidArr = inpid.split("-");
            var identifier = inpidArr[0];
            $('#' + identifier + '-listing').data('materialtable2').clearState(reloadstate);
        }

        var href = $(this).attr('href');
        if (href != '#' && !reloadstate) {
            setTimeout(function () {
                console.log('pupa');
                window.location = href;
                // me.off('click').trigger('click');
            }, 200);
        }

        me.hide();
    });

    $('#search-trigger').on('click', function() {
        $(this).toggleClass('active');
        $("#search-cnt").toggleClass('active');
    });
    $('.toggle-active').on('click', function() {
        clearTimeout(activeToggleTimeout);
        var what = $(this).attr('data-toggle');
        $(what).toggleClass('active');
        activeToggleTimeout = setTimeout(function () {
            equalHeight('#main-widgets-area', '.widget-row');
            equalHeight('#CONTENT', '.height-equalizer');
        }, 501);
    });
    $('.submenu-trigger').on('click', function() {
        $(this).parent().toggleClass('active');
    });
    $('.term-trigger').on('click', function() {
        $(this).parent().toggleClass('active');
    });
    $('.single-item .title').on('click', function() {
        $(this).parent().parent().parent().parent().toggleClass('active');
    });
    enquire.register("screen and (max-width: 1325px)", {
        match : function() {
            FixedButtonsEnabled = true;
        },
        unmatch : function() {
            FixedButtonsEnabled = false;
        }
    });
    enquire.register("screen and (max-width: 480px)", {
        match : function() {
            mobileMenuEnabled = true;
        },
        unmatch : function() {
            mobileMenuEnabled = false;
        }
    });
    enquire.register("screen and (max-height: 360px)", {
        match : function() {
            mobileMenuEnabled = true;
        },
        unmatch : function() {
            mobileMenuEnabled = false;
        }
    });
    $(".tiles-wrap").on('mouseenter', function (event) {
        clearTimeout(tilesMouseoutTimeout);
        mouseX = event.clientX;
        mouseY = event.clientY;
        var elementMouseIsOver = document.elementFromPoint(mouseX, mouseY);
        var elmL;
        if ($(elementMouseIsOver).hasClass('tile')){
            elmL = $(elementMouseIsOver).offset().left;
        } else {
            var findEl = $(elementMouseIsOver).closest('.tile');
            if (findEl.length <= 0){
                findEl = $(this).find('.tile').first();
            }
            elmL = findEl.offset().left;
        }
        var calcL = elmL - $(this).offset().left;
        $(this).find('.after').css('left', calcL).wait(5).addClass('active');
        $(this).on('mousemove', function (innerevent) {
            mouseX = innerevent.clientX;
            mouseY = innerevent.clientY;
            var elementMouseIsOver = document.elementFromPoint(mouseX, mouseY);
            var elmL;
            var moveMe = true;
            if ($(elementMouseIsOver).hasClass('tile')){
                elmL = $(elementMouseIsOver).offset().left;
            } else {
                var findEl = $(elementMouseIsOver).closest('.tile');
                if (findEl.length <= 0){
                    moveMe = false;
                } else {
                    elmL = findEl.offset().left;
                }
            }
            if (moveMe) {
                var calcL = elmL - $(this).offset().left;
                $(this).find('.after').css('left', calcL);
            }
        })
    }).on('mouseleave', function () {
        tilesMouseoutElement = $(this);
        tilesMouseoutTimeout = setTimeout(function () {
            tilesMouseoutElement.off('mousemove');
            tilesMouseoutElement.find('.after').removeClass('active');
        }, 50);
    });
    $("#mainMenu .submenu-trigger").on('mouseenter', function () {
        if (!mobileMenuEnabled){
            var myId = $(this).attr('id');
            if (!mobileMenuTimeouts.hasOwnProperty(myId)){
                mobileMenuTimeouts[myId] = null;
            }
            clearTimeout(mobileMenuTimeouts[myId]);

            var subel = $("#desktop-subs ul[data-target='" + myId + "']");

            if (!subel.hasClass('active')) {
                var myTop = $(this).offset().top - window.pageYOffset;
                var subTop = subel.offset().top;
                if (subel != subTop) {
                    subel.addClass('noanimation').css({'top': myTop, 'bottom': 'auto'});
                    var subBot = myTop + subel.outerHeight();
                    var windBot = $(window).height();
                    if (subBot > windBot){
                        subel.css({'top': 'auto', 'bottom': 0});
                    }
                    subel.removeClass('noanimation').wait(10).addClass('active');
                } else {
                    subel.addClass('active');
                }
            }
        }
    }).on('mouseleave', function () {
        if (!mobileMenuEnabled){
            var myId = $(this).attr('id');
            if (!mobileMenuTimeouts.hasOwnProperty(myId)){
                mobileMenuTimeouts[myId] = null;
            }
            clearTimeout(mobileMenuTimeouts[myId]);

            mobileMenuTimeouts[myId] = setTimeout(
                function () {
                    clearSubMenu(myId)
                }, 200);
        }
    });
    $("#desktop-subs .submenu").on('mouseenter', function () {
        var myId = $(this).attr('data-target');
        if (!mobileMenuTimeouts.hasOwnProperty(myId)){
            mobileMenuTimeouts[myId] = null;
        }
        clearTimeout(mobileMenuTimeouts[myId]);
    }).on('mouseleave', function () {
        var myId = $(this).attr('data-target');
        if (!mobileMenuTimeouts.hasOwnProperty(myId)){
            mobileMenuTimeouts[myId] = null;
        }
        clearTimeout(mobileMenuTimeouts[myId]);

        mobileMenuTimeouts[myId] = setTimeout(
            function () {
                clearSubMenu(myId)
            }, 200);
    });

    $(document).on('click', '.ajax-popup', function (e) {
        e.preventDefault();
        var href = $(this).attr('href');

        $.get(href, function(data) {
            var div = $('<div>').addClass('popup-overlay').html(data);

            $('body').append(div);
            div.fadeIn();

            $('.popup-overlay .cancel').on('click', function(e) {
                $('.popup-overlay').fadeOut(function() { $(this).remove(); });
                e.preventDefault();
            });
        });
    });

    $(document).on('keyup', '.notes-widget-txt', function() {
        var tv = $(this).val();
        clearTimeout(notepadTimeout);
        notepadTimeout = setTimeout(function() {
            $('.notes-widget-txt').val(tv);
            $.post(notepadUrl, 'content=' + tv, function() {});
        }, 500);
    });

    $(".tab-menu li").on('click', function (e) {
        e.preventDefault();
        if (!$(this).hasClass('active')){
            var target = $(this).attr('data-goto');
            $(".tab-menu li").removeClass('active');
            $(this).addClass('active');
            var tabsCnt = $(this).closest('.tabs');
            tabsCnt.find('.single-tab').removeClass('active');
            tabsCnt.find('.single-tab[data-target="' + target + '"]').addClass('active');
        }
    });
});
var icotable = {
    'clear-day': '01d',
    'clear-night': '01n',
    'rain': '09d',
    'snow': '13d',
    'sleet': '13d',
    'wind': 'windy',
    'fog': '50d',
    'cloudy': '03d',
    'partly-cloudy-day': '02d',
    'partly-cloudy-night': '02n'
};

function getWeather(city) {
    $.ajax({
        url: 'https://api.forecast.io/forecast/' + weatherKey + '/' + city,
        type: 'GET',
        crossDomain: true,
        dataType: 'jsonp',
        success: function(data) {
            if (data.hasOwnProperty('daily')){
                if (!jQuery.isEmptyObject(data.daily)){
                    $('.weather-cnt ul').html('');
                    var daily = data.daily['data'];
                    var index = 0;

                    /*daily.forEach(function(currd) {*/
                    $.each(daily, function (ind, currd) {
                        var dobj = new Date(currd.time * 1000);
                        var dm = dobj.getMonth() + 1;
                        if (dm < 10) {
                            dm = '0' + dm;
                        }
                        var dd = dobj.getDate();
                        if (dd < 10) {
                            dd = '0' + dd;
                        }
                        var day = dd + '-' + dm + '-' + dobj.getFullYear();

                        if (index == 0) {
                            $('.general-weather-date').text(day);
                            $('.general-weather-today').text(toCelsius(data.currently['temperature']) + '°');
                            $('.general-weather-rbl').html('<img src="/a/imgs/weather/' + icotable[data.currently['icon']] + '.png" />');
                        } else {
                            var li = '<li>' +
                                '<span class="lweat">' + day + '</span>' +
                                '<span class="rweat">' +
                                '<span class="weather-degrees">' + toCelsius(currd.temperatureMin) + '° - ' + toCelsius(currd.temperatureMax) + '°</span>' +
                                '<span class="weather-indicator m' + icotable[currd.icon] + '"></span>' +
                                '</span>' +
                                '<div class="clr"></div>' +
                                '</li>';
                            $('.weather-cnt ul').append(li);
                        }
                        index = index + 1;
                    });
                    equalHeight('#main-widgets-area', '.widget-row');
                    equalHeight('#CONTENT', '.height-equalizer');
                }
            }
        }
    });
}

var timerNotStarted = true;
var timerIntervall;

function startTime() {
    if ($('.widget-zegar').length > 0) {
        var today = new Date();
        var h = today.getHours();
        var m = today.getMinutes();
        m = checkNulls(m);
        var time = '<span class="firstnumber">' + h + '</span><span class="separ">:</span><span class="fourthnumber">' + m + '</span>';
        $('.digital-clock').html(time);
        if (timerNotStarted) {
            timerNotStarted = false;

            timerIntervall = setInterval(function () {
                startTime()
            }, 1000);
        }
    }
}

jQuery(window).load(function () {
    if ($('select[name=weather_city]').length > 0) {
        getWeather($('select[name=weather_city]').val());
        $('select[name=weather_city]').fancySelect().on('change.fs', function () {
            getWeather(this.value);
            setWeather(this.value);
        });
    }
    enquire.register("screen and (max-width: 1024px)", {
        match : function() {
            sbSaveEnabled = false;
            $('#page').removeClass('settings-active').removeClass('active-right').removeClass('active-left');
            if (iOS) {
                $("#LEFT-SIDEBAR").css('display', 'none');
                $("#RIGHT-SIDEBAR").css('display', 'none');
                setTimeout(function () {
                    $("#LEFT-SIDEBAR").css('display', '');
                    $("#RIGHT-SIDEBAR").css('display', '');
                }, 150);
            }

            var rf = $('.right-trigger .fa');
            if (rf.hasClass('fa-long-arrow-right')) {
                rf.removeClass('fa-long-arrow-right').addClass('fa-long-arrow-left');
            }

            var tnmf = $('.toggle-nav-menu .fa');
            if (tnmf.hasClass('fa-long-arrow-left')) {
                tnmf.removeClass('fa-long-arrow-left').addClass("fa-bars");
            }
        },
        unmatch : function() {
            sbSaveEnabled = true;
            var pg = $('#page');

            if (sbRight == 1) {
                pg.removeClass('settings-active').addClass('active-right');

                var rf = $('.right-trigger .fa');
                if (rf.hasClass('fa-long-arrow-left')) {
                    rf.addClass('fa-long-arrow-right').removeClass('fa-long-arrow-left');
                }
            }

            if (sbLeft == 1) {
                pg.addClass('active-left');

                var tnmf = $('.toggle-nav-menu .fa');
                if (tnmf.hasClass('fa-bars')) {
                    tnmf.addClass('fa-long-arrow-left').removeClass("fa-bars");
                }
            }
        }
    });

    $('.toggle-nav-menu').on('click', function () {
        var myFa = $(this).find('.fa');
        var allFa = $('.toggle-nav-menu .fa');
        $('#page').toggleClass('active-left');
        if (myFa.hasClass('fa-long-arrow-left')) {
            allFa.removeClass("fa-long-arrow-left");
            allFa.addClass("fa-bars");
            setSbStatus('sbLeft', 0);
        } else {
            allFa.removeClass("fa-bars");
            allFa.addClass("fa-long-arrow-left");
            setSbStatus('sbLeft', 1);
        }

        setTimeout(function() {
            $('.wrapper').TrackpadScrollEmulator('recalculate');
        }, 500);
    });
    $("#hidden-head .toggle-nav-menu").on('click', function () {
        if (mobileMenuEnabled){
            $('html, body').animate({
                'scrollTop': 0
            }, 900, 'swing', function () {

                return false;
            });
        }
    });
    $('.right-trigger').on('click', function () {
        var myFa = $(this).find('.fa');
        var allFa = $('.right-trigger .fa');
        if ($('#page').hasClass('settings-active')) {
            $('#page').removeClass('settings-active');
            setTimeout(function() {
                $('#page').toggleClass('active-right');
                if (myFa.hasClass('fa-long-arrow-left')) {
                    allFa.removeClass("fa-long-arrow-left");
                    allFa.addClass("fa-long-arrow-right");
                    setSbStatus('sbRight', 1);
                } else {
                    allFa.removeClass("fa-long-arrow-right");
                    allFa.addClass("fa-long-arrow-left");
                    setSbStatus('sbRight', 0);
                }

                $('.wrapper').TrackpadScrollEmulator('recalculate');
            }, 500);
        } else {
            $('#page').toggleClass('active-right');
            if (myFa.hasClass('fa-long-arrow-left')) {
                allFa.removeClass("fa-long-arrow-left");
                allFa.addClass("fa-long-arrow-right");
                setSbStatus('sbRight', 1);
            } else {
                allFa.removeClass("fa-long-arrow-right");
                allFa.addClass("fa-long-arrow-left");
                setSbStatus('sbRight', 0);
            }
            setTimeout(function() {
                $('.wrapper').TrackpadScrollEmulator('recalculate');
            }, 500);
        }
    });
    $('.user-info').on('click', function () {
        if ($('#page').hasClass('active-right')) {
            $('#page').removeClass('active-right');
            setTimeout(function() {
                $('#page').toggleClass('settings-active');
                if ($('.right-trigger').find('.fa').hasClass('fa-long-arrow-right')) {
                    ($('.right-trigger').find('.fa').removeClass('fa-long-arrow-right'));
                    ($('.right-trigger').find('.fa').addClass('fa-long-arrow-left'));
                }
            }, 500);
        } else {
            $('#page').toggleClass('settings-active');
            if ($('.right-trigger').find('.fa').hasClass('fa-long-arrow-right')) {
                ($('.right-trigger').find('.fa').removeClass('fa-long-arrow-right'));
                ($('.right-trigger').find('.fa').addClass('fa-long-arrow-left'));
            }
        }
    });

    equalHeight('#main-widgets-area', '.widget-row');

    equalHeight('#CONTENT', '.height-equalizer');
    equalHeight('#LOGIN', '.height-equalizer');

    /*windowEl = jQuery('body > .tse-scrollable.wrapper > .tse-scroll-content');*/
    windowEl = $(window);

    runFixedButtons();

    jQuery(window).resize(function () {
        if (!$('#page').hasClass('active-left')) {
            $('#LEFT-SIDEBAR').css('opacity', '0');
            waitForFinalEvent(function () {
                $('#LEFT-SIDEBAR').css('opacity', '1');
            }, 500, 'InvisRules');
        }
        waitForFinalEvent(function () {
            equalHeight('#main-widgets-area', '.widget-row');
            equalHeight('#CONTENT', '.height-equalizer');
            equalHeight('#LOGIN', '.height-equalizer');
            runFixedButtons();
        }, 500, "heightEqualizer");

        waitForFinalEvent(function () {
            $('.wrapper').TrackpadScrollEmulator('recalculate');
        }, 600, "TrackpadScrollEmulator");
    });
    windowEl.scroll(function () {
        runFixedButtons();
    });
});