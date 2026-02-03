$(function () {

    $('.datepickertwo').tDatePicker({

        // auto close after selection
        autoClose: true,

        // animation speed in milliseconds
        durationArrowTop: 200,

        // the number of calendars
        numCalendar: 2,

        // localization
        titleCheckIn: 'Add Dates',
        titleCheckOut: 'Add Dates',
        titleToday: 'Today',
        titleDateRange: 'night',
        titleDateRanges: 'nights',
        titleDays: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
        titleMonths: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'Septemper', 'October', 'November', "December"],

        // the max length of the title
        titleMonthsLimitShow: 3,

        // replace moth names
        replaceTitleMonths: null,

        // e.g. 'dd-mm-yy'
        showDateTheme: null,

        // icon options
        iconArrowTop: false,
        iconDate: '&#x279C;',
        arrowPrev: '&#x276E;',
        arrowNext: '&#x276F;',
        // https://fontawesome.com/v4.7.0/icons/
        // iconDate: '<i class="li-calendar-empty"></i><i class="li-arrow-right"></i>',
        // arrowPrev: '<i class="fa fa-chevron-left"></i>',
        // arrowNext: '<i class="fa fa-chevron-right"></i>',

        // shows today title
        toDayShowTitle: true,

        // showss dange range title
        dateRangesShowTitle: true,

        // highlights today
        toDayHighlighted: false,

        // highlights next day
        nextDayHighlighted: false,

        // an array of days
        daysOfWeekHighlighted: [0, 6],

        // custom date format
        formatDate: 'yyyy-mm-dd',

        // dateCheckIn: '25/06/2018',  // DD/MM/YY
        // dateCheckOut: '26/06/2018', // DD/MM/YY
        dateCheckIn: null,
        dateCheckOut: null,
        startDate: null,
        endDate: null,

        // limits the number of months
        limitPrevMonth: 0,
        limitNextMonth: 11,

        // limits the number of days
        limitDateRanges: 31,

        // true -> full days || false - 1 day
        showFullDateRanges: false,

        // DATA HOLIDAYS
        // Data holidays
        fnDataEvent: null

    });

});