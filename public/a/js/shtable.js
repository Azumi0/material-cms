// OBJECT PARAMS:
// shparams.table = table id (with #)
// shparams.href = url to ajax request (string) / for static tables set false
// shparams.order = default column and order ['date', 'desc']

function materialtable(params) {
    var table = params.table;
    var href = params.href;
    var order = (params.order === undefined) ? [0, 0] : params.order;

    var pagination = table + '-pagination';
    var search = table + '-search';
    var limits = table + '-limit';
    var counting = table + '-counting';

    var count = 0;
    var limit = 0;
    var page = 1;

    if (href != false) {
        var fdef = ['search', 'limit', 'offset', 'order', 'direction', '_csrf'];
        var vdef = ['', 0, 0, order[0], order[1], csrf];

        prepareDefault();
    }

    function prepareDefault() {
        prepareLimit();
        prepareSortable();
        restoreState(href);
        prepareAjaxTables();

        makeRequest();
    }

    // resize
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

    function resizeOp() {
        var ww = $(window).outerWidth();
        var hidden = false;

        $(table + ' th').each(function (i) {
            if ($(this).attr('data-hide') > ww) {
                $(this).css({display: 'none'});
                $(table + ' .materialtable-row').each(function () {
                    $($(this).find('td')[i]).css({display: 'none'});
                });
                hidden = true;
            } else {
                $(this).css({display: 'table-cell'});
                $(table + ' .materialtable-row').each(function () {
                    $($(this).find('td')[i]).css({display: 'table-cell'});
                });
            }
        });

        if (hidden) {
            $(table + ' .materialtable-row').addClass('clickable');
        } else {
            $(table + ' .materialtable-row').removeClass('clickable');
            $(table + ' .materialtable-row-ext').css({display: 'none'});
        }
    }

    $(document).on('click', table + ' .materialtable-row.clickable', function (e) {
        e.preventDefault();

        if ($(this).next().css('display') == 'none') {
            $(table + ' .materialtable-row.clickable').each(function() {
                if ($(this).next().css('display') == 'table-row') {
                    $(this).next().css({ display: 'none' });
                }
            });
            $(this).next().css({display: 'table-row'}).addClass('active');
        } else {
            $(this).next().removeClass('active').css({display: 'none'});
        }
    });

    resizeOp();
    // end resize

    function prepareLimit() {
        limit = parseInt($(limits).val());
        vdef[1] = limit;
    }

    function prepareSortable() {
        $(table + ' th').each(function() {
            if ($(this).attr('data-sort') != 'false') {
                $(this).addClass('materialtable-sortable');
                $(this).append($('<span>').addClass('materialtable-sort-indicator'));

                var sort = $(this).attr('data-sort');

                if (sort == order[0]) {
                    $(this).addClass('materialtable-sorted materialtable-sorted-' + order[1]);
                }
            }
        });
    }

    function preparePagination() {
        var pages = (count > 0) ? Math.ceil(count / limit) : 0;

        if (pages == 0) {
            $(pagination).hide();
        } else if (pages == 1) {
            $(pagination + ' .materialtable-limit-prev').hide();
            $(pagination + ' .materialtable-limit-next').hide();
            $(pagination + ' .materialtable-page-first').addClass('disabled');
            $(pagination + ' .materialtable-page-prev').addClass('disabled');
            $(pagination + ' .materialtable-page-next').addClass('disabled');
            $(pagination + ' .materialtable-page-last').addClass('disabled');

            var li = $('<li>').addClass('materialtable-page active').append($('<a>').attr({ href: '#' }).text(1));
            $(pagination + ' .materialtable-limit-next').before(li);
        } else if (pages > 1 && pages <= 3) {
            $(pagination).show();

            $(pagination + ' .materialtable-limit-prev').hide();
            $(pagination + ' .materialtable-limit-next').hide();
            $(pagination + ' .materialtable-page-first').addClass('disabled');
            $(pagination + ' .materialtable-page-prev').addClass('disabled');
            $(pagination + ' .materialtable-page-next').addClass('disabled');
            $(pagination + ' .materialtable-page-last').addClass('disabled');

            $(pagination + ' .materialtable-page-first a').attr({ 'data-goto': 1 });
            $(pagination + ' .materialtable-page-prev a').attr({ 'data-goto': page - 1 });
            $(pagination + ' .materialtable-page-next a').attr({ 'data-goto': page + 1 });
            $(pagination + ' .materialtable-page-last a').attr({ 'data-goto': pages });

            if (page == 1) {
                $(pagination + ' .materialtable-page-next').removeClass('disabled');
                $(pagination + ' .materialtable-page-last').removeClass('disabled');
            } else {
                $(pagination + ' .materialtable-page-first').removeClass('disabled');
                $(pagination + ' .materialtable-page-prev').removeClass('disabled');
            }

            for (var x = 1; x <= pages; x++) {
                var li = $('<li>').addClass('materialtable-page').append($('<a>').attr({ href: '#', 'data-goto': x }).text(x));

                if (x == page) {
                    li.addClass('active');
                }

                $(pagination + ' .materialtable-limit-next').before(li);
            }
        } else {
            $(pagination).show();

            $(pagination + ' .materialtable-limit-prev').hide();
            $(pagination + ' .materialtable-limit-next').hide();

            $(pagination + ' .materialtable-page-first a').attr({ 'data-goto': 1 });
            $(pagination + ' .materialtable-page-last a').attr({ 'data-goto': pages });

            var mPages = [];
            mPages[0] = 1;
            mPages[1] = 2;
            mPages[2] = 3;

            $(pagination + ' .materialtable-page-first').addClass('disabled');
            $(pagination + ' .materialtable-page-prev').addClass('disabled');
            $(pagination + ' .materialtable-page-next').removeClass('disabled');
            $(pagination + ' .materialtable-page-last').removeClass('disabled');

            $(pagination + ' .materialtable-page-next a').attr({ 'data-goto': mPages[1] });

            if (page > 1 && page < pages) {
                mPages[0] = page - 1;
                mPages[1] = page;
                mPages[2] = page + 1;

                $(pagination + ' .materialtable-page-first').removeClass('disabled');
                $(pagination + ' .materialtable-page-prev').removeClass('disabled');
                $(pagination + ' .materialtable-page-next').removeClass('disabled');
                $(pagination + ' .materialtable-page-last').removeClass('disabled');

                $(pagination + ' .materialtable-page-prev a').attr({ 'data-goto': mPages[0] });
                $(pagination + ' .materialtable-page-next a').attr({ 'data-goto': mPages[2] });
            } else if (page == pages) {
                mPages[0] = page - 2;
                mPages[1] = page - 1;
                mPages[2] = page;

                $(pagination + ' .materialtable-page-first').removeClass('disabled');
                $(pagination + ' .materialtable-page-prev').removeClass('disabled');
                $(pagination + ' .materialtable-page-next').addClass('disabled');
                $(pagination + ' .materialtable-page-last').addClass('disabled');

                $(pagination + ' .materialtable-page-prev a').attr({ 'data-goto': mPages[1] });
            }

            $.each(mPages, function(i, x) {
                var li = $('<li>').addClass('materialtable-page').append($('<a>').attr({ href: '#', 'data-goto': x }).text(x));

                if (x == page) {
                    li.addClass('active');
                }

                $(pagination + ' .materialtable-limit-next').before(li);
            });

            if (page > 2) {
                $(pagination + ' .materialtable-limit-prev').show();
            }

            if (page < pages - 1) {
                $(pagination + ' .materialtable-limit-next').show();
            }
        }
    }

    function makeRequest() {
        var params = '';

        $.each(fdef, function (i, v) {
            params += ((i == 0) ? '' : '&') + v + '=' + vdef[i];
        });

        $.post(href, params, function (data) {
            var json = $.parseJSON(data);

            $(table + ' tbody').html(json.html);

            changeOrder(vdef[3], vdef[4]);
            changePagination(json.count, json.page);
            changeCounting(json.count);

            saveState(href, vdef);

            resizeOp();
        });
    }

    function changeOrder(order, direction) {
        $(table + ' th').removeClass('materialtable-sorted').removeClass('materialtable-sorted-asc').removeClass('materialtable-sorted-desc');

        switch (direction) {
            case 'asc':
                $(table + ' th[data-sort="' + order + '"]').addClass('materialtable-sorted').addClass('materialtable-sorted-asc');
                break;
            case 'desc':
                $(table + ' th[data-sort="' + order + '"]').addClass('materialtable-sorted').addClass('materialtable-sorted-desc');
                break;
        }
    }

    function changePagination(nCount, nPage) {
        count = parseInt(nCount);
        page = parseInt(nPage);

        $(pagination + ' .materialtable-page').remove();

        preparePagination();
    }

    function changeCounting(nCount) {
        var count = parseInt(nCount);

        var from = vdef[2] + 1;
        var to = (vdef[1] + vdef[2]  < count) ? vdef[1] + vdef[2] : count;

        if (count > 0) {
            $(counting).show();
            $(counting + ' .count_from').text(from);
            $(counting + ' .count_to').text(to);
            $(counting + ' .count_all').text(count);
        } else {
            $(counting).hide();

            $(table + ' tbody').html('<tr class="materialtable-no-records"><td colspan="' + ($(table + ' thead th').length) + '" class="textC">Brak wyników do wyświetlenia</td></tr>');
        }
    }

    function prepareAjaxTables() {
        // sortable click
        $(document).on('click', table + ' th.materialtable-sortable', function (e) {
            e.preventDefault();

            var curr = null;
            var currth = null;

            $(table + ' th').each(function () {
                if ($(this).hasClass('materialtable-sorted')) {
                    curr = $(this).attr('data-sort');
                    currth = $(this);
                }
            });

            var sort = $(this).attr('data-sort');

            if (sort != curr) {
                currth.removeClass('materialtable-sorted').removeClass('materialtable-sorted-asc').removeClass('materialtable-sorted-desc');
            }

            vdef[3] = sort;

            if ($(this).hasClass('materialtable-sorted-asc')) {
                vdef[4] = 'desc';
            }

            if ($(this).hasClass('materialtable-sorted-desc')) {
                vdef[4] = 'asc';
            }

            makeRequest();
        });

        // pagination click
        $(document).on('click', pagination + ' a', function (e) {
            e.preventDefault();

            if ($(this).attr('data-page') != 'limit-next' && $(this).attr('data-page') != 'limit-prev') {
                var status = (!$(this).parent().hasClass('disabled') && !$(this).parent().hasClass('active')) ? true : false;

                if (status) {
                    vdef[2] = (limit * parseInt($(this).attr('data-goto'))) - limit;

                    makeRequest();
                }
            }
        });

        // search
        var keyUpTO = null;

        $(document).on('keyup', search, function () {
            var t = $(this);

            clearTimeout(keyUpTO);

            keyUpTO = setTimeout(function () {
                vdef[0] = t.val();
                vdef[2] = 0;

                makeRequest();
            }, 300);
        });

        // limit change
        $(document).on('change, change.fs', limits, function() {
            limit = parseInt($(this).val());
            vdef[1] = limit;
            vdef[2] = 0;

            makeRequest();
        });
    }

    function saveState(href, params) {
        if (typeof(Storage) !== "undefined") {
            localStorage.setItem("materialtable_" + href, JSON.stringify(params));
        }
    }

    function restoreState(href) {
        if (typeof(Storage) !== "undefined") {
            var stored = localStorage.getItem("materialtable_" + href);

            if (stored !== null) {
                var json = $.parseJSON(stored);

                vdef[0] = json[0];
                vdef[1] = parseInt(json[1]);
                vdef[2] = parseInt(json[2]);
                vdef[3] = json[3];
                vdef[4] = json[4];

                $(search).val(json[0]);
                $(limits).val(vdef[1]);

                if (json[0].length > 0){
                    $(".clear-filters").css('display', 'inline-block');
                }
            }
        }
    }

    function clearState(href) {
        if (typeof(Storage) !== "undefined") {
            var stored = localStorage.getItem("materialtable_" + href);

            if (stored !== null) {
                localStorage.removeItem("materialtable_" + href);
            }
        }
    }
}
