// jQuery Plugin Boilerplate
// A boilerplate for jumpstarting jQuery plugins development
// version 1.1, May 14th, 2011
// by Stefan Gabos

// remember to change every instance of "materialtable2" to the name of your plugin!
(function($) {

    // here we go!
    $.materialtable2 = function(element, options) {

        // plugin's default options
        // this is private property and is  accessible only from inside the plugin
        var defaults = {
            order: [0, 0]
        };

        // to avoid confusions, use "plugin" to reference the 
        // current instance of the object
        var plugin = this;

        // this will hold the merged default, and user-provided options
        // plugin's properties will be available through this object like:
        // plugin.settings.propertyName from inside the plugin or
        // element.data('materialtable2').settings.propertyName from outside the plugin,
        // where "element" is the element the plugin is attached to;
        plugin.settings = {};
        plugin.count = 0;
        plugin.limit = 0;
        plugin.page = 0;
        plugin.fdef = [];
        plugin.vdef = [];

        var $element = $(element), // reference to the jQuery version of DOM element
            element = element;    // reference to the actual DOM element

        // the "constructor" method that gets called when the object is created
        plugin.init = function() {

            // the plugin's final properties are the merged default and 
            // user-provided options (if any)
            var merge = $.extend({}, defaults, options);
            var tableid = '#' + $element.attr('id');

            plugin.settings = $.extend({}, {
                table: tableid,
                pagination: tableid + '-pagination',
                search: tableid + '-search',
                limits: tableid + '-limit',
                counting: tableid + '-counting'
            }, merge);
            // code goes here

            if (plugin.settings.href != false) {
                plugin.fdef = ['search', 'limit', 'offset', 'order', 'direction', '_csrf'];
                plugin.vdef = ['', 0, 0, plugin.settings.order[0], plugin.settings.order[1], csrf];

                prepareDefault();
            }

            var resizeCount = 0;
            var resizeTO = null;
            $(window).on('resize', function() {
                if (resizeCount >= 20) {
                    resizeOp();
                    resizeCount = 0;
                }

                resizeCount++;

                clearTimeout(resizeTO);
                resizeTO = setTimeout(function() {
                    resizeOp();
                    resizeCount = 0;
                }, 500);
            });

            $(document).on('click', plugin.settings.table + ' .materialtable-row.clickable', function (e) {
                e.preventDefault();

                if ($(this).next().css('display') == 'none') {
                    $(plugin.settings.table + ' .materialtable-row.clickable').each(function() {
                        if ($(this).next().css('display') == 'table-row') {
                            $(this).next().css({ display: 'none' });
                        }
                    });
                    $(this).next().css({display: 'table-row'}).addClass('active');
                } else {
                    $(this).next().removeClass('active').css({display: 'none'});
                }
            });
        };

        // public methods
        // these methods can be called like:
        // plugin.methodName(arg1, arg2, ... argn) from inside the plugin or
        // element.data('materialtable2').publicMethod(arg1, arg2, ... argn) from outside
        // the plugin, where "element" is the element the plugin is attached to;

        plugin.makeRequest = function() {
            var params = '';

            $.each(plugin.fdef, function (i, v) {
                params += ((i == 0) ? '' : '&') + v + '=' + plugin.vdef[i];
            });

            $.post(plugin.settings.href, params, function (data) {
                var json = $.parseJSON(data);

                $(plugin.settings.table + ' tbody').html(json.html);

                changeOrder(plugin.vdef[3], plugin.vdef[4]);
                changePagination(json.count, json.page);
                changeCounting(json.count);

                saveState(plugin.vdef);

                resizeOp();
            });
        };

        plugin.clearState = function(reload) {
            reload = typeof reload !== 'undefined' ? reload : false;

            if (typeof(Storage) !== "undefined") {
                var stored = localStorage.getItem("materialtable_" + plugin.settings.href);

                if (stored !== null) {
                    localStorage.removeItem("materialtable_" + plugin.settings.href);
                }
            }

            if (reload){
                window.location.reload(true);
            } else if (plugin.settings.href != false) {
                $(plugin.settings.search).val('');
                plugin.vdef = ['', 0, 0, plugin.settings.order[0], plugin.settings.order[1], csrf];
                prepareLimit();
                plugin.makeRequest();
            }
        };

        // private methods
        // these methods can be called only from inside the plugin like:
        // methodName(arg1, arg2, ... argn)

        var prepareDefault = function() {
            prepareLimit();
            prepareSortable();
            restoreState();
            prepareAjaxTables();

            plugin.makeRequest();
        };

        var resizeOp = function() {
            var ww = $(window).outerWidth();
            var hidden = false;

            $(plugin.settings.table + ' th').each(function (i) {
                if ($(this).attr('data-hide') > ww) {
                    $(this).css({display: 'none'});
                    $(plugin.settings.table + ' .materialtable-row').each(function () {
                        $($(this).find('td')[i]).css({display: 'none'});
                    });
                    hidden = true;
                } else {
                    $(this).css({display: 'table-cell'});
                    $(plugin.settings.table + ' .materialtable-row').each(function () {
                        $($(this).find('td')[i]).css({display: 'table-cell'});
                    });
                }
            });

            if (hidden) {
                $(plugin.settings.table + ' .materialtable-row').addClass('clickable');
            } else {
                $(plugin.settings.table + ' .materialtable-row').removeClass('clickable');
                $(plugin.settings.table + ' .materialtable-row-ext').css({display: 'none'});
            }
        };

        var prepareLimit = function() {
            plugin.limit = parseInt($(plugin.settings.limits).val());
            plugin.vdef[1] = plugin.limit;
        };

        var prepareSortable = function() {
            $(plugin.settings.table + ' th').each(function() {
                if ($(this).attr('data-sort') != 'false') {
                    $(this).addClass('materialtable-sortable');
                    $(this).append($('<span>').addClass('materialtable-sort-indicator'));

                    var sort = $(this).attr('data-sort');

                    if (sort == plugin.settings.order[0]) {
                        $(this).addClass('materialtable-sorted materialtable-sorted-' + plugin.settings.order[1]);
                    }
                }
            });
        };

        var preparePagination = function() {
            var pages = (plugin.count > 0) ? Math.ceil(plugin.count / plugin.limit) : 0;

            if (pages == 0) {
                $(plugin.settings.pagination).hide();
            } else if (pages == 1) {
                $(plugin.settings.pagination + ' .materialtable-limit-prev').hide();
                $(plugin.settings.pagination + ' .materialtable-limit-next').hide();
                $(plugin.settings.pagination + ' .materialtable-page-first').addClass('disabled');
                $(plugin.settings.pagination + ' .materialtable-page-prev').addClass('disabled');
                $(plugin.settings.pagination + ' .materialtable-page-next').addClass('disabled');
                $(plugin.settings.pagination + ' .materialtable-page-last').addClass('disabled');

                var li = $('<li>').addClass('materialtable-page active').append($('<a>').attr({ href: '#' }).text(1));
                $(plugin.settings.pagination + ' .materialtable-limit-next').before(li);
            } else if (pages > 1 && pages <= 3) {
                $(plugin.settings.pagination).show();

                $(plugin.settings.pagination + ' .materialtable-limit-prev').hide();
                $(plugin.settings.pagination + ' .materialtable-limit-next').hide();
                $(plugin.settings.pagination + ' .materialtable-page-first').addClass('disabled');
                $(plugin.settings.pagination + ' .materialtable-page-prev').addClass('disabled');
                $(plugin.settings.pagination + ' .materialtable-page-next').addClass('disabled');
                $(plugin.settings.pagination + ' .materialtable-page-last').addClass('disabled');

                $(plugin.settings.pagination + ' .materialtable-page-first a').attr({ 'data-goto': 1 });
                $(plugin.settings.pagination + ' .materialtable-page-prev a').attr({ 'data-goto': plugin.page - 1 });
                $(plugin.settings.pagination + ' .materialtable-page-next a').attr({ 'data-goto': plugin.page + 1 });
                $(plugin.settings.pagination + ' .materialtable-page-last a').attr({ 'data-goto': pages });

                if (plugin.page == 1) {
                    $(plugin.settings.pagination + ' .materialtable-page-next').removeClass('disabled');
                    $(plugin.settings.pagination + ' .materialtable-page-last').removeClass('disabled');
                } else {
                    $(plugin.settings.pagination + ' .materialtable-page-first').removeClass('disabled');
                    $(plugin.settings.pagination + ' .materialtable-page-prev').removeClass('disabled');
                }

                for (var x = 1; x <= pages; x++) {
                    var li = $('<li>').addClass('materialtable-page').append($('<a>').attr({ href: '#', 'data-goto': x }).text(x));

                    if (x == plugin.page) {
                        li.addClass('active');
                    }

                    $(plugin.settings.pagination + ' .materialtable-limit-next').before(li);
                }
            } else {
                $(plugin.settings.pagination).show();

                $(plugin.settings.pagination + ' .materialtable-limit-prev').hide();
                $(plugin.settings.pagination + ' .materialtable-limit-next').hide();

                $(plugin.settings.pagination + ' .materialtable-page-first a').attr({ 'data-goto': 1 });
                $(plugin.settings.pagination + ' .materialtable-page-last a').attr({ 'data-goto': pages });

                var mPages = [];
                mPages[0] = 1;
                mPages[1] = 2;
                mPages[2] = 3;

                $(plugin.settings.pagination + ' .materialtable-page-first').addClass('disabled');
                $(plugin.settings.pagination + ' .materialtable-page-prev').addClass('disabled');
                $(plugin.settings.pagination + ' .materialtable-page-next').removeClass('disabled');
                $(plugin.settings.pagination + ' .materialtable-page-last').removeClass('disabled');

                $(plugin.settings.pagination + ' .materialtable-page-next a').attr({ 'data-goto': mPages[1] });

                if (plugin.page > 1 && plugin.page < pages) {
                    mPages[0] = plugin.page - 1;
                    mPages[1] = plugin.page;
                    mPages[2] = plugin.page + 1;

                    $(plugin.settings.pagination + ' .materialtable-page-first').removeClass('disabled');
                    $(plugin.settings.pagination + ' .materialtable-page-prev').removeClass('disabled');
                    $(plugin.settings.pagination + ' .materialtable-page-next').removeClass('disabled');
                    $(plugin.settings.pagination + ' .materialtable-page-last').removeClass('disabled');

                    $(plugin.settings.pagination + ' .materialtable-page-prev a').attr({ 'data-goto': mPages[0] });
                    $(plugin.settings.pagination + ' .materialtable-page-next a').attr({ 'data-goto': mPages[2] });
                } else if (plugin.page == pages) {
                    mPages[0] = plugin.page - 2;
                    mPages[1] = plugin.page - 1;
                    mPages[2] = plugin.page;

                    $(plugin.settings.pagination + ' .materialtable-page-first').removeClass('disabled');
                    $(plugin.settings.pagination + ' .materialtable-page-prev').removeClass('disabled');
                    $(plugin.settings.pagination + ' .materialtable-page-next').addClass('disabled');
                    $(plugin.settings.pagination + ' .materialtable-page-last').addClass('disabled');

                    $(plugin.settings.pagination + ' .materialtable-page-prev a').attr({ 'data-goto': mPages[1] });
                }

                $.each(mPages, function(i, x) {
                    var li = $('<li>').addClass('materialtable-page').append($('<a>').attr({ href: '#', 'data-goto': x }).text(x));

                    if (x == plugin.page) {
                        li.addClass('active');
                    }

                    $(plugin.settings.pagination + ' .materialtable-limit-next').before(li);
                });

                if (plugin.page > 2) {
                    $(plugin.settings.pagination + ' .materialtable-limit-prev').show();
                }

                if (plugin.page < pages - 1) {
                    $(plugin.settings.pagination + ' .materialtable-limit-next').show();
                }
            }
        };

        var changeOrder = function(order, direction) {
            $(plugin.settings.table + ' th').removeClass('materialtable-sorted').removeClass('materialtable-sorted-asc').removeClass('materialtable-sorted-desc');

            switch (direction) {
                case 'asc':
                    $(plugin.settings.table + ' th[data-sort="' + order + '"]').addClass('materialtable-sorted').addClass('materialtable-sorted-asc');
                    break;
                case 'desc':
                    $(plugin.settings.table + ' th[data-sort="' + order + '"]').addClass('materialtable-sorted').addClass('materialtable-sorted-desc');
                    break;
            }
        };

        var changePagination = function(nCount, nPage) {
            plugin.count = parseInt(nCount);
            plugin.page = parseInt(nPage);

            $(plugin.settings.pagination + ' .materialtable-page').remove();

            preparePagination();
        };

        var changeCounting = function(nCount) {
            var count = parseInt(nCount);

            var from = plugin.vdef[2] + 1;
            var to = (plugin.vdef[1] + plugin.vdef[2]  < count) ? plugin.vdef[1] + plugin.vdef[2] : count;

            if (count > 0) {
                $(plugin.settings.counting).show();
                $(plugin.settings.counting + ' .count_from').text(from);
                $(plugin.settings.counting + ' .count_to').text(to);
                $(plugin.settings.counting + ' .count_all').text(count);
            } else {
                $(plugin.settings.counting).hide();

                $(plugin.settings.table + ' tbody').html('<tr class="materialtable-no-records"><td colspan="' + ($(plugin.settings.table + ' thead th').length) + '" class="textC">Brak wyników do wyświetlenia</td></tr>');
            }
        };

        var prepareAjaxTables = function() {
            // sortable click
            $(document).on('click', plugin.settings.table + ' th.materialtable-sortable', function (e) {
                e.preventDefault();

                var curr = null;
                var currth = null;

                $(plugin.settings.table + ' th').each(function () {
                    if ($(this).hasClass('materialtable-sorted')) {
                        curr = $(this).attr('data-sort');
                        currth = $(this);
                    }
                });

                var sort = $(this).attr('data-sort');

                if (sort != curr) {
                    currth.removeClass('materialtable-sorted').removeClass('materialtable-sorted-asc').removeClass('materialtable-sorted-desc');
                }

                plugin.vdef[3] = sort;

                if ($(this).hasClass('materialtable-sorted-asc')) {
                    plugin.vdef[4] = 'desc';
                }

                if ($(this).hasClass('materialtable-sorted-desc')) {
                    plugin.vdef[4] = 'asc';
                }

                plugin.makeRequest();
            });

            // pagination click
            $(document).on('click', plugin.settings.pagination + ' a', function (e) {
                e.preventDefault();

                if ($(this).attr('data-page') != 'limit-next' && $(this).attr('data-page') != 'limit-prev') {
                    var status = (!$(this).parent().hasClass('disabled') && !$(this).parent().hasClass('active')) ? true : false;

                    if (status) {
                        plugin.vdef[2] = (plugin.limit * parseInt($(this).attr('data-goto'))) - plugin.limit;

                        plugin.makeRequest();
                    }
                }
            });

            // search
            var keyUpTO = null;

            $(document).on('keyup', plugin.settings.search, function () {
                var t = $(this);

                clearTimeout(keyUpTO);

                keyUpTO = setTimeout(function () {
                    plugin.vdef[0] = t.val();
                    plugin.vdef[2] = 0;

                    plugin.makeRequest();
                }, 300);
            });

            // limit change
            $(document).on('change, change.fs', plugin.settings.limits, function() {
                plugin.limit = parseInt($(this).val());
                plugin.vdef[1] = plugin.limit;
                plugin.vdef[2] = 0;

                plugin.makeRequest();
            });
        };

        var saveState = function(params) {
            if (typeof(Storage) !== "undefined") {
                localStorage.setItem("materialtable_" + plugin.settings.href, JSON.stringify(params));
            }
        };

        var restoreState = function() {
            if (typeof(Storage) !== "undefined") {
                var stored = localStorage.getItem("materialtable_" + plugin.settings.href);

                if (stored !== null) {
                    var json = $.parseJSON(stored);

                    plugin.vdef[0] = json[0];
                    plugin.vdef[1] = parseInt(json[1]);
                    plugin.vdef[2] = parseInt(json[2]);
                    plugin.vdef[3] = json[3];
                    plugin.vdef[4] = json[4];

                    $(plugin.settings.search).val(json[0]);
                    $(plugin.settings.limits).val(plugin.vdef[1]);

                    if (json[0].length > 0){
                        $(".clear-filters").css('display', 'inline-block');
                    }
                }
            }
        };


        // fire up the plugin!
        // call the "constructor" method
        plugin.init();

    };

    // add the plugin to the jQuery.fn object
    $.fn.materialtable2 = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('materialtable2')) {

                // create a new instance of the plugin
                // pass the DOM element and the user-provided options as arguments
                var plugin = new $.materialtable2(this, options);

                // in the jQuery version of the element
                // store a reference to the plugin object
                // you can later access the plugin and its methods and properties like
                // element.data('materialtable2').publicMethod(arg1, arg2, ... argn) or
                // element.data('materialtable2').settings.propertyName
                $(this).data('materialtable2', plugin);

            }

        });

    }

})(jQuery);