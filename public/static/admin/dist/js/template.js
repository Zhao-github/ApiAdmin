/**
 * 模版引擎
 * ------------------
 * You should not use this file in production.
 * This file is for demo purposes only.
 */
$.AdminTemplate = {};
/**
 * 格式化时间戳（为了和PHP的date函数统一，这里的时间戳都是10位，不包含毫秒）
 * @param timestamp
 * @returns {string}
 */
var formatDate = function ( timestamp ) {
    timestamp *= 1000;
    var date = new Date(timestamp);
    var Y = date.getFullYear() + '-';
    var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
    var D = (date.getDate()+1 < 10 ? '0'+(date.getDate()+1) : date.getDate()+1) + ' ';
    var h = date.getHours() + ':';
    var m = date.getMinutes() + ':';
    var s = date.getSeconds();
    return Y+M+D+h+m+s;
};

(function ($, AdminTemplate) {

    "use strict";

    AdminTemplate.buildDom = function ( jsonStr ) {

    };

    var emptyList = function() {

    };

    var easyList = function( listObj ) {

    };

    var topButton = function( topObj ) {

    };

    var rightButton = function( rightObj ) {

    };

    var easyForm = function( formObj ) {

    };

    var input = function( inputObj ) {

    };

    var select = function( selectObj ) {

    };

    var button = function( buttonObj ) {

    };

    /**
     * 面包屑
     */
    var breadcrumb = function(  ) {

    };

    AdminTemplate.a = function () {

    };

    AdminTemplate.alertMsg = function( msg ){
        var dialog = bootbox.dialog({
            message: '<p class="text-center">'+msg+'</p>',
            closeButton: false
        });
        setTimeout(function(){
            dialog.modal('hide');
        }, 3000);
    }

})(jQuery, $.AdminTemplate);
