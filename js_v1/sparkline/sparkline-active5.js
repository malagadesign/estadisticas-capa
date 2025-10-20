(function ($) {
 "use strict";

	/*----------------------------
	 jQuery sparkline Chart
	------------------------------ */
    if ($('.sparkline-bar-stats1')[0]) {
    var monthsLookup = {};
    datastats1.months.forEach(function(month, index) {
        monthsLookup[index] = month;
    });

    $('.sparkline-bar-stats1').sparkline(datastats1.values.split(','), {
        type: 'bar',
        height: 36,
        barWidth: 3,
        barColor: '#00c292',
        barSpacing: 2,
        tooltipFormat: '{{offset:months}}: {{value}}',
        tooltipValueLookups: {
            months: monthsLookup
        }
    });
}
    if($('.sparkline-bar-stats2')[0]) {
    var monthsLookup = {};
    datastats2.months.forEach(function(month, index) {
        monthsLookup[index] = month;
    });

    $('.sparkline-bar-stats2').sparkline(datastats2.values.split(','), {
        type: 'bar',
        height: 36,
        barWidth: 3,
        barColor: '#fb9678',
        barSpacing: 2,
        tooltipFormat: '{{offset:months}}: {{value}}',
        tooltipValueLookups: {
            months: monthsLookup
        }
    });
}
    if($('.sparkline-bar-stats3')[0]) {
    var monthsLookup = {};
    datastats3.months.forEach(function(month, index) {
        monthsLookup[index] = month;
    });

    $('.sparkline-bar-stats3').sparkline(datastats3.values.split(','), {
        type: 'bar',
        height: 36,
        barWidth: 3,
        barColor: '#01c0c8',
        barSpacing: 2,
        tooltipFormat: '{{offset:months}}: {{value}}',
        tooltipValueLookups: {
            months: monthsLookup
        }
    });
}
    if($('.sparkline-bar-stats4')[0]) {
    var monthsLookup = {};
    datastats4.months.forEach(function(month, index) {
        monthsLookup[index] = month;
    });

    $('.sparkline-bar-stats4').sparkline(datastats4.values.split(','), {
        type: 'bar',
        height: 36,
        barWidth: 3,
        barColor: '#ab8ce4',
        barSpacing: 2,
        tooltipFormat: '{{offset:months}}: {{value}}',
        tooltipValueLookups: {
            months: monthsLookup
        }
    });
}
 
})(jQuery); 
