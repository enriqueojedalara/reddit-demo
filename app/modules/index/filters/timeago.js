define(['layout/module'], function(module) {
    'use strict';
    module.registerFilter('timeago', function() {
        return function(time, local){
            time = time * 1000;

            if (!time) return "never";

            if (!local){
                (local = Date.now())
            }

            if (angular.isDate(time)){
                time = time.getTime();
            } 
            else if (typeof time === 'string'){
                time = new Date(time).getTime();
            }

            if (angular.isDate(local)) {
                local = local.getTime();
            }
            else if (typeof local === 'string') {
                local = new Date(local).getTime();
            }

            if (typeof time !== 'number' || typeof local !== 'number') {
                return;
            }

            var offset = Math.abs((local - time) / 1000);
            var span = [];
            var minute = 60;
            var hour = 3600;
            var day = 86400;
            var week = 604800;
            var year = 31556926;
            var decade = 315569260;

            if (offset <= minute) 
                span = ['', 'now'];
            else if (offset < (minute * 60)) 
                span = [ Math.round(Math.abs(offset / minute)), 'min' ];
            else if (offset < (hour * 24))
                span = [ Math.round(Math.abs(offset / hour)), 'hr' ];
            else if (offset < (day * 7))
                span = [ Math.round(Math.abs(offset / day)), 'day' ];
            else if (offset < (week * 52))
                span = [ Math.round(Math.abs(offset / week)), 'week' ];
            else if (offset < (year * 10))
                span = [ Math.round(Math.abs(offset / year)), 'year' ];
            else if (offset < (decade * 100))
                span = [ Math.round(Math.abs(offset / decade)), 'decade' ];
            else
                span = ['', 'a long time'];

            span[1] += (span[0] === 0 || span[0] > 1) ? 's' : '';
            span = span.join(' ');
            return (time <= local) ? span + ' ago' : 'in ' + span;
        }
    });
});
