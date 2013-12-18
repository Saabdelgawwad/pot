<div id="fullcalendar"></div>

<?php

Yii::app()->clientScript->registerScript('fullcalendar', '
    $("#fullcalendar").fullCalendar({
        // AJAX-Adresse für Events
        events: ' . CJavaScript::encode(Yii::app()->createUrl('appointment/calendarEvents')) . ',

        // Buttons im Header
        header: {
            left: "prev,next today",
            center: "title",
            right: "month,agendaWeek,agendaDay",
        },
        buttonText: {
            today: "Heute",
            month: "Monat",
            week: "Woche",
            day: "Tag"
        },

        // Erster Tag der Woche: Montag
        firstDay: 1,

        // Defaul-Ansicht
        defaultView : "agendaWeek",

        // Wochenenden verbergen
        weekends: false,

        // Zeitbereich, der angezeigt wird
        minTime: "6:00",
        maxTime: "20:00",

        // Zeitformat
        timeFormat: "H:mm",
        axisFormat: "H:mm",
        columnFormat: {
            month: "ddd",
            week: "ddd d.M.",
            day: "ddd d.M."
        },
        titleFormat: {
            month: "MMMM yyyy",
            week: "d.[ MMMM][ yyyy]{ - d. MMMM yyyy}",
            day: "dddd, d. MMMM yyyy"
        },

        // Keine Ganztagesevents
        allDayDefault: false,
        allDaySlot: false,

        // Linien-Interval in Tages- und Wochenansicht
        slotMinutes: 30,

        // Klick auf Tag in Monatsansicht -> Tagesansicht
        dayClick: function (date, allDay, jsEvent, view) {
            if (view.name == "month") {
                view.calendar.changeView("agendaDay");
                view.calendar.gotoDate(date);
            }

            $("#fullcalendar").trigger(allDay ? "dayClick" : "slotClick", date);
        },

        // Lade-Indikator
        loading: function (isLoading, view) {
            if (isLoading) {
                $("#fullcalendar-loading").addClass("loading");
            } else {
                $("#fullcalendar-loading").removeClass("loading");
            }
        },

        // Tages- und Monatsnamen
        dayNames: ' . CJavaScript::encode(Yii::app()->locale->weekDayNames) . ',
        dayNamesShort: ' . CJavaScript::encode(Yii::app()->locale->getWeekDayNames('abbreviated')) . ',
        monthNames: ' . CJavaScript::encode(array_values(Yii::app()->locale->monthNames)) . ',
        monthNamesShort: ' . CJavaScript::encode(array_values(Yii::app()->locale->getMonthNames('abbreviated'))) . '
    });
');

?>

<div id="fullcalendar-loading">Lade...</div>
