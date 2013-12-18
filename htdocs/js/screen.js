$(function() {

    // Modal beim Neuaufruf resetten
    var modal = $('.reset').attr('href');
    var backupModal = $(modal).clone();

    $('body').on('click', '.reset', function() {
        $(modal).modal('hide').remove();
        var clonedModal = backupModal.clone();
        $('body').append(clonedModal);
    });

    /**
     * Datepicker - verwendet, weil er als Komponent eingebunden werden kann
     * und mit Twitter Bootstrap kompatibel ist.
     */
    $(".datepicker").datepicker({dateFormat: "dd.mm.yy", autoclose: true}).on('changeDate', function(e) {
        if (e.viewMode === 'days') {
            $(this).datepicker('hide');
        }
        /**
         *Dirty Hack: @link https://github.com/infektweb/traveladvice/issues/46 
         */
        if (e.viewMode === 'days' && $('#Closing-Form').length === 1) {
            $(this).datepicker('hide');
            $('.datepicker.dropdown-menu').remove();

            //Datum vor Reload neu setzen
            var dateFrom = $('#Bill_dateFrom').val();
            var dateTo = $('#Bill_dateTo').val();
            $('#hidden_dateFrom').attr('value', dateFrom);
            $('#hidden_dateTo').attr('value', dateTo);
            var dateFromHidden = $('#hidden_dateFrom').val();
            var dateToHidden = $('#hidden_dateTo').val();

            window.location.href = '/bill/closing?' + 'Bill[dateFrom]=' + window.fixedEncodeURIComponent(dateFromHidden) + '&Bill[dateTo]=' + window.fixedEncodeURIComponent(dateToHidden);
        }


    });

    // Twitter Bootstrap Tooltip für Links mit rel="tooltip" einblenden
    $('body').tooltip({
        selector: '[rel=tooltip]'
    });

    $.fn.replaceInAllAttributes = function(regex, replacement) {
        this.find('*').add(this).each(function() {
            $.each(this.attributes, function() {
                this.nodeValue = this.nodeValue.replace(regex, replacement);
            });
        });
        return this;
    };
    // Angepasstes URI encoding
    window.fixedEncodeURIComponent = function(s) {
        return encodeURIComponent(s).replace(/[!'()]/g, escape).replace(/\*/g, "%2A");
    }

    window.capitaliseFirstLetter = function(string)
    {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    /**
     * encodeHtml
     */
    window.encodeHtml = function(s) {
        s = s.toString();
        $.each({
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': '&quot;',
            "'": '&#39;'
        }, function(k, v) {
            s = s.replace(new RegExp(k, 'g'), v);
        });
        return s;
    };
});


// Diese Funktion wird vor JQuery-Ready ausgeführt
(function() {

    /*
     * Twitter Bootstrap Tooltips: Trigger nur bei Hover (nicht bei Focus)
     */
    $.fn.tooltip.defaults.trigger = 'hover';

    /*
     * Twitter Bootstrap Tooltips: Container
     */
    $.fn.tooltip.defaults.container = 'body';

})();
