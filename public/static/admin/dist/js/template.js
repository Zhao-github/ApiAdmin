/**
 * 模版引擎
 * ------------------
 * You should not use this file in production.
 * This file is for demo purposes only.
 */
(function ($) {

    "use strict";

    function create( jsonStr ) {

    }

    function emptyList() {

    }

    function easyList( listObj ) {

    }

    function topButton( topObj ) {

    }

    function rightButton( rightObj ) {

    }

    function easyForm() {

    }

    function input( inputObj ) {

    }

    function select( selectObj ) {

    }

    function button( buttonObj ) {

    }

    /**
     * 格式化时间戳（为了和PHP的date函数统一，这里的时间戳都是10位，不包含毫秒）
     * @param timestamp
     * @returns {string}
     */
    function formatDate( timestamp ) {
        timestamp *= 1000;
        var date = new Date(timestamp);
        var Y = date.getFullYear() + '-';
        var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
        var D = (date.getDate()+1 < 10 ? '0'+(date.getDate()+1) : date.getDate()+1) + ' ';
        var h = date.getHours() + ':';
        var m = date.getMinutes() + ':';
        var s = date.getSeconds();
        return Y+M+D+h+m+s;
    }

})(jQuery);
