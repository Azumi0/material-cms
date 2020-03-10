(function() {
    if (window.define) var e = window.define;
    if (window.require) var t = window.require;
    if (window.jQuery && jQuery.fn && jQuery.fn.select2 && jQuery.fn.select2.amd) var e = jQuery.fn.select2.amd.define,
        t = jQuery.fn.select2.amd.require;
    e("select2/i18n/pl", [], function() {
        var e = ["znak", "znaki", "znaków"],
            t = ["element", "elementy", "elementów"],
            n = function(t, n) {
                if (t === 1) return n[0];
                if (t > 1 && t <= 4) return n[1];
                if (t >= 5) return n[2]
            };
        return {
            errorLoading: function() {
                return "Nie można załadować wyników."
            },
            inputTooLong: function(t) {
                var r = t.input.length - t.maximum;
                return "Usuń " + r + " " + n(r, e)
            },
            inputTooShort: function(t) {
                var r = t.minimum - t.input.length;
                return "<label>Podaj przynajmniej " + r + " " + n(r, e) + '</label>'
            },
            loadingMore: function() {
                return "Trwa ładowanie…"
            },
            maximumSelected: function(e) {
                return "Możesz zaznaczyć tylko " + e.maximum + " " + n(e.maxiumum, t)
            },
            noResults: function() {
                return "Brak wyników"
            },
            searching: function() {
                return "Trwa wyszukiwanie…"
            }
        }
    }), t("jquery.select2"), jQuery.fn.select2.amd = {
        define: e,
        require: t
    }
})();